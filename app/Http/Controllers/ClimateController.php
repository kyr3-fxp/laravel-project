<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class ClimateController extends Controller
{
    private const NASA_API_URL = 'https://power.larc.nasa.gov/api/temporal/daily/point';

    private const DEFAULT_SITE = [
        'name' => 'Riohacha, La Guajira',
        'latitude' => 11.5444,
        'longitude' => -72.9069,
        'pv_capacity_kwp' => 20.0,
        'battery_capacity_kwh' => 40.0,
        'critical_load_kw' => 5.5,
        'daily_load_kwh' => 58.0,
        'tariff_cop_kwh' => 943,
        'performance_ratio' => 0.78,
        'self_consumption_ratio' => 0.82,
        'co2_factor_kg_kwh' => 0.42,
    ];

    public function index(): View
    {
        return view('dashboard.index', [
            'defaultSite' => self::DEFAULT_SITE,
        ]);
    }

    public function getWeatherData(Request $request)
    {
        return $this->getSolarData($request);
    }

    public function getSolarData(Request $request)
    {
        $site = $this->resolveSite($request);

        $parameters = [
            'start' => now()->subDays(29)->format('Ymd'),
            'end' => now()->format('Ymd'),
            'latitude' => $site['latitude'],
            'longitude' => $site['longitude'],
            'community' => 're',
            'parameters' => 'ALLSKY_SFC_SW_DWN,CLRSKY_SFC_SW_DWN,T2M,RH2M,WS2M',
            'format' => 'json',
            'units' => 'metric',
        ];

        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->acceptJson()
                ->get(self::NASA_API_URL, $parameters);

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo obtener la radiacion desde NASA POWER.',
                    'status' => $response->status(),
                ], 500);
            }

            $raw = $response->json();
            $processed = $this->processDailyData($raw, $site);

            if (empty($processed)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La API respondio, pero no llegaron datos procesables.',
                    'raw_keys' => array_keys($raw ?? []),
                ], 502);
            }

            $statistics = $this->buildStatistics($processed, $site);

            return response()->json([
                'success' => true,
                'site' => $site,
                'data' => $processed,
                'statistics' => $statistics,
                'recommendations' => $this->buildRecommendations($statistics, $site),
                'alerts' => $this->buildAlerts($statistics, $site),
                'meta' => [
                    'latitude' => $site['latitude'],
                    'longitude' => $site['longitude'],
                    'date_range' => [
                        'start' => $parameters['start'],
                        'end' => $parameters['end'],
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud solar.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    private function resolveSite(Request $request): array
    {
        $site = self::DEFAULT_SITE;

        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $siteName = $request->input('site_name');

        if (is_numeric($latitude) && is_numeric($longitude)) {
            $site['latitude'] = (float) $latitude;
            $site['longitude'] = (float) $longitude;
        }

        if (is_string($siteName) && trim($siteName) !== '') {
            $site['name'] = trim($siteName);
        }

        return $site;
    }

    private function processDailyData(array $data, array $site): array
    {
        if (!isset($data['properties']['parameter']) || !is_array($data['properties']['parameter'])) {
            return [];
        }

        $parameters = $data['properties']['parameter'];
        $radiationSeries = $parameters['ALLSKY_SFC_SW_DWN'] ?? null;

        if (!is_array($radiationSeries) || empty($radiationSeries)) {
            return [];
        }

        $processed = [];

        foreach (array_keys($radiationSeries) as $date) {
            $radiation = $this->sanitizeNumber($parameters['ALLSKY_SFC_SW_DWN'][$date] ?? null, 2);
            $clearSky = $this->sanitizeNumber($parameters['CLRSKY_SFC_SW_DWN'][$date] ?? null, 2);
            $temperature = $this->sanitizeNumber($parameters['T2M'][$date] ?? null, 1);
            $humidity = $this->sanitizeNumber($parameters['RH2M'][$date] ?? null, 1);
            $wind = $this->sanitizeNumber($parameters['WS2M'][$date] ?? null, 2);

            if ($radiation === null && $temperature === null && $humidity === null && $wind === null) {
                continue;
            }

            $generation = $radiation !== null
                ? round($radiation * $site['pv_capacity_kwp'] * $site['performance_ratio'], 1)
                : 0.0;

            $demand = round(
                $site['daily_load_kwh'] * (1 + max(0, (($temperature ?? 0) - 30) * 0.01)),
                1
            );

            $savings = round(min($generation, $demand) * $site['tariff_cop_kwh'] * $site['self_consumption_ratio'], 0);
            $co2Avoided = round($generation * $site['co2_factor_kg_kwh'], 1);

            $processed[] = [
                'date' => $this->formatDate($date),
                'radiation' => $radiation,
                'clear_sky_radiation' => $clearSky,
                'temperature' => $temperature,
                'humidity' => $humidity,
                'wind_speed' => $wind,
                'estimated_generation_kwh' => $generation,
                'estimated_demand_kwh' => $demand,
                'estimated_savings_cop' => $savings,
                'co2_avoided_kg' => $co2Avoided,
            ];
        }

        return $processed;
    }

    private function buildStatistics(array $data, array $site): array
    {
        $radiations = $this->validNumbers(array_column($data, 'radiation'));
        $temperatures = $this->validNumbers(array_column($data, 'temperature'));
        $humidities = $this->validNumbers(array_column($data, 'humidity'));
        $generations = $this->validNumbers(array_column($data, 'estimated_generation_kwh'));
        $savings = $this->validNumbers(array_column($data, 'estimated_savings_cop'));
        $co2 = $this->validNumbers(array_column($data, 'co2_avoided_kg'));

        $peakDay = $this->peakDay($data, 'estimated_generation_kwh');
        $latest = end($data) ?: null;
        $averageGeneration = !empty($generations) ? array_sum($generations) / count($generations) : 0;

        $coverage = $site['daily_load_kwh'] > 0
            ? round(($averageGeneration / $site['daily_load_kwh']) * 100, 1)
            : 0;

        $batteryCharge = (int) max(18, min(100, 42 + ($coverage * 0.45)));
        $solarWindow = $this->solarWindowFromAverage(!empty($radiations) ? array_sum($radiations) / count($radiations) : 0);
        $solarScore = $this->solarScore($radiations, $coverage);

        return [
            'avg_radiation' => !empty($radiations) ? round(array_sum($radiations) / count($radiations), 2) : 0,
            'max_radiation' => !empty($radiations) ? max($radiations) : 0,
            'min_radiation' => !empty($radiations) ? min($radiations) : 0,
            'avg_temperature' => !empty($temperatures) ? round(array_sum($temperatures) / count($temperatures), 1) : 0,
            'avg_humidity' => !empty($humidities) ? round(array_sum($humidities) / count($humidities), 1) : 0,
            'total_generation_kwh' => round(array_sum($generations), 1),
            'estimated_monthly_savings_cop' => round(array_sum($savings), 0),
            'co2_avoided_kg' => round(array_sum($co2), 1),
            'coverage_ratio' => $coverage,
            'battery_autonomy_hours' => round($site['battery_capacity_kwh'] / max(0.1, $site['critical_load_kw']), 1),
            'battery_charge_percent' => $batteryCharge,
            'battery_usage_text' => $coverage >= 85 ? 'Excedente para carga' : ($coverage >= 60 ? 'Carga balanceada' : 'Priorizar respaldo'),
            'solar_window' => $solarWindow,
            'solar_score' => $solarScore,
            'peak_day' => $peakDay['date'] ?? '--',
            'peak_day_generation_kwh' => $peakDay['value'] ?? 0,
            'latest_radiation' => $latest['radiation'] ?? 0,
            'latest_generation_kwh' => $latest['estimated_generation_kwh'] ?? 0,
            'latest_savings_cop' => $latest['estimated_savings_cop'] ?? 0,
            'latest_date' => $latest['date'] ?? '--',
            'tags' => [
                $solarScore >= 80 ? 'Alta oportunidad solar' : 'Estrategia solar en ajuste',
                $coverage >= 80 ? 'Cobertura FV alta' : 'Cobertura FV moderada',
                $batteryCharge >= 70 ? 'Batería saludable' : 'Refuerzo de almacenamiento',
            ],
        ];
    }

    private function buildRecommendations(array $stats, array $site): array
    {
        $items = [];

        $items[] = [
            'icon' => 'fa-clock',
            'title' => 'Mover cargas a la franja solar',
            'message' => 'Programa refrigeración, bombeo y procesos intensivos entre ' . $stats['solar_window'] . ' para aprovechar la radiación disponible.',
        ];

        if ($stats['coverage_ratio'] < 75) {
            $items[] = [
                'icon' => 'fa-battery-three-quarters',
                'title' => 'Aumentar respaldo',
                'message' => 'La cobertura fotovoltaica aún es moderada. Conviene priorizar baterías o gestión de cargas en horas de baja radiación.',
            ];
        } else {
            $items[] = [
                'icon' => 'fa-bolt',
                'title' => 'Aprovechar excedentes',
                'message' => 'Hay suficiente margen solar para cargar baterías y desplazar consumos no críticos al mediodía.',
            ];
        }

        if ($stats['avg_temperature'] >= 32) {
            $items[] = [
                'icon' => 'fa-fan',
                'title' => 'Ajustar climatización',
                'message' => 'El calor incrementa la demanda. Ajusta aire acondicionado y ventilación para evitar picos de consumo.',
            ];
        }

        $items[] = [
            'icon' => 'fa-shield-heart',
            'title' => 'Mantener reserva operativa',
            'message' => 'Reserva al menos una fracción de la batería para interrupciones de red y eventos de alta demanda.',
        ];

        return $items;
    }

    private function buildAlerts(array $stats, array $site): array
    {
        $alerts = [];

        if ($stats['avg_radiation'] < 5.5) {
            $alerts[] = [
                'level' => 'warning',
                'title' => 'Radiación por debajo del potencial esperado',
                'message' => 'La media reciente está por debajo de Riohacha. Revisa nubosidad, mantenimiento o suciedad en paneles.',
            ];
        } else {
            $alerts[] = [
                'level' => 'success',
                'title' => 'Condición solar favorable',
                'message' => 'La radiación promedio está alineada con el potencial de la ciudad y permite optimizar consumo diurno.',
            ];
        }

        if ($stats['coverage_ratio'] < 70) {
            $alerts[] = [
                'level' => 'critical',
                'title' => 'Cobertura fotovoltaica limitada',
                'message' => 'El sistema no cubre toda la demanda base. Considera desplazar cargas, ampliar paneles o aumentar batería.',
            ];
        }

        if ($stats['battery_charge_percent'] < 50) {
            $alerts[] = [
                'level' => 'warning',
                'title' => 'Batería en nivel medio',
                'message' => 'Conviene reservar energía para continuidad operativa en caso de interrupciones de red.',
            ];
        }

        return $alerts;
    }

    private function solarWindowFromAverage(float $avgRadiation): string
    {
        if ($avgRadiation >= 6.5) {
            return '10:00 - 14:00';
        }

        if ($avgRadiation >= 5.0) {
            return '10:30 - 13:30';
        }

        return '11:00 - 13:00';
    }

    private function solarScore(array $radiations, float $coverage): int
    {
        $radiationScore = !empty($radiations)
            ? min(60, (array_sum($radiations) / count($radiations)) * 8)
            : 0;

        $coverageScore = min(40, max(0, $coverage * 0.4));

        return (int) min(100, round($radiationScore + $coverageScore));
    }

    private function peakDay(array $data, string $field): array
    {
        $best = ['date' => '--', 'value' => 0];

        foreach ($data as $row) {
            $value = (float) ($row[$field] ?? 0);

            if ($value >= $best['value']) {
                $best = [
                    'date' => $row['date'] ?? '--',
                    'value' => round($value, 1),
                ];
            }
        }

        return $best;
    }

    private function formatDate(string $dateString): string
    {
        try {
            return Carbon::createFromFormat('Ymd', $dateString)->format('d/m/Y');
        } catch (\Throwable) {
            return $dateString;
        }
    }

    private function sanitizeNumber(mixed $value, int $precision): ?float
    {
        if ($value === null || $value === '' || !is_numeric($value)) {
            return null;
        }

        $numeric = round((float) $value, $precision);

        if (abs($numeric + 999) < 0.01) {
            return null;
        }

        return $numeric;
    }

    private function validNumbers(array $values): array
    {
        return array_values(array_filter($values, function ($value) {
            return $value !== null && is_numeric($value) && abs((float) $value + 999) >= 0.01;
        }));
    }
}
