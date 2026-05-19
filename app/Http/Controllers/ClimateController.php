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
        'name' => 'Riohacha, Colombia',
        'latitude' => 11.5444,
        'longitude' => -72.9069,
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
            $processed = $this->processDailyData($raw);

            if (empty($processed)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La API respondio, pero no llegaron datos procesables.',
                    'raw_keys' => array_keys($raw ?? []),
                ], 502);
            }

            $statistics = $this->buildStatistics($processed);

            return response()->json([
                'success' => true,
                'site' => $site,
                'data' => $processed,
                'statistics' => $statistics,
                'recommendations' => $this->buildRecommendations($statistics),
                'alerts' => $this->buildAlerts($statistics),
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

    private function processDailyData(array $data): array
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

            $solarRatio = ($radiation !== null && $clearSky !== null && $clearSky > 0)
                ? round(($radiation / $clearSky) * 100, 1)
                : null;

            $processed[] = [
                'date' => $this->formatDate($date),
                'radiation' => $radiation,
                'clear_sky_radiation' => $clearSky,
                'temperature' => $temperature,
                'humidity' => $humidity,
                'wind_speed' => $wind,
                'solar_ratio' => $solarRatio,
            ];
        }

        return $processed;
    }

    private function buildStatistics(array $data): array
    {
        $radiations = $this->validNumbers(array_column($data, 'radiation'));
        $clearSky = $this->validNumbers(array_column($data, 'clear_sky_radiation'));
        $temperatures = $this->validNumbers(array_column($data, 'temperature'));
        $humidities = $this->validNumbers(array_column($data, 'humidity'));
        $windSpeeds = $this->validNumbers(array_column($data, 'wind_speed'));
        $ratios = $this->validNumbers(array_column($data, 'solar_ratio'));

        $latest = end($data) ?: null;
        $peakDay = $this->peakDay($data, 'radiation');
        $lowDay = $this->lowDay($data, 'radiation');
        $stabilityIndex = $this->stabilityIndex($radiations);

        return [
            'avg_radiation' => $this->average($radiations),
            'max_radiation' => !empty($radiations) ? max($radiations) : 0,
            'min_radiation' => !empty($radiations) ? min($radiations) : 0,
            'avg_clear_sky' => $this->average($clearSky),
            'avg_temperature' => $this->average($temperatures, 1),
            'avg_humidity' => $this->average($humidities, 1),
            'avg_wind_speed' => $this->average($windSpeeds, 2),
            'avg_solar_ratio' => $this->average($ratios, 1),
            'stability_index' => $stabilityIndex,
            'latest_date' => $latest['date'] ?? '--',
            'latest_radiation' => $latest['radiation'] ?? 0,
            'latest_temperature' => $latest['temperature'] ?? 0,
            'latest_humidity' => $latest['humidity'] ?? 0,
            'latest_wind_speed' => $latest['wind_speed'] ?? 0,
            'peak_day' => $peakDay['date'] ?? '--',
            'peak_day_radiation' => $peakDay['value'] ?? 0,
            'low_day' => $lowDay['date'] ?? '--',
            'low_day_radiation' => $lowDay['value'] ?? 0,
            'solar_score' => $this->solarScore($radiations, $ratios),
            'tags' => [
                !empty($radiations) && $this->average($radiations) >= 6.5 ? 'Alta radiación' : 'Radiación variable',
                $stabilityIndex >= 75 ? 'Patrón estable' : 'Patrón cambiante',
                !empty($ratios) && $this->average($ratios) >= 80 ? 'Cielo favorable' : 'Cobertura mixta',
            ],
        ];
    }

    private function buildRecommendations(array $stats): array
    {
        $items = [];

        $items[] = [
            'icon' => 'fa-sun',
            'title' => 'Aprovechar la ventana de mayor radiación',
            'message' => 'La franja con mejores condiciones solares debe priorizar cargas intensivas y procesos que dependan de energía estable.',
        ];

        if ($stats['avg_radiation'] < 5.5) {
            $items[] = [
                'icon' => 'fa-cloud-sun',
                'title' => 'Vigilar días de radiación baja',
                'message' => 'Conviene postergar consumos no críticos cuando la radiación promedio cae por debajo del umbral esperado.',
            ];
        } else {
            $items[] = [
                'icon' => 'fa-bolt',
                'title' => 'Optimizar autoconsumo',
                'message' => 'El comportamiento reciente sugiere una buena oportunidad para absorber más energía solar en sitio.',
            ];
        }

        if ($stats['avg_temperature'] >= 32) {
            $items[] = [
                'icon' => 'fa-temperature-three-quarters',
                'title' => 'Prever mayor carga térmica',
                'message' => 'Las temperaturas elevadas pueden aumentar la demanda de refrigeración y afectar la eficiencia del sistema.',
            ];
        }

        $items[] = [
            'icon' => 'fa-chart-line',
            'title' => 'Monitorear estabilidad',
            'message' => 'La consistencia entre radiación real y cielo despejado ayuda a detectar días con nubosidad o pérdidas por suciedad.',
        ];

        return $items;
    }

    private function buildAlerts(array $stats): array
    {
        $alerts = [];

        if ($stats['avg_radiation'] < 5.5) {
            $alerts[] = [
                'level' => 'warning',
                'title' => 'Radiación inferior a la referencia reciente',
                'message' => 'Revisa nubosidad, polvo en paneles o cambios en la condición atmosférica.',
            ];
        } else {
            $alerts[] = [
                'level' => 'success',
                'title' => 'Radiación favorable',
                'message' => 'Las mediciones recientes muestran un comportamiento solar saludable para la operación fotovoltaica.',
            ];
        }

        if ($stats['stability_index'] < 65) {
            $alerts[] = [
                'level' => 'critical',
                'title' => 'Variabilidad alta',
                'message' => 'El patrón diario cambió bastante en el periodo analizado. Conviene ajustar planificación operativa.',
            ];
        }

        if ($stats['avg_solar_ratio'] > 0 && $stats['avg_solar_ratio'] < 75) {
            $alerts[] = [
                'level' => 'warning',
                'title' => 'Cobertura solar moderada',
                'message' => 'La radiación real está por debajo del cielo despejado en buena parte del periodo.',
            ];
        }

        return $alerts;
    }

    private function average(array $values, int $precision = 2): float
    {
        if (empty($values)) {
            return 0;
        }

        return round(array_sum($values) / count($values), $precision);
    }

    private function stabilityIndex(array $values): int
    {
        if (count($values) < 2) {
            return 100;
        }

        $avg = array_sum($values) / count($values);

        if ($avg == 0.0) {
            return 100;
        }

        $variance = 0.0;

        foreach ($values as $value) {
            $variance += ($value - $avg) ** 2;
        }

        $stdDev = sqrt($variance / count($values));
        $index = 100 - min(100, ($stdDev / max($avg, 0.1)) * 120);

        return (int) max(0, round($index));
    }

    private function solarScore(array $radiations, array $ratios): int
    {
        $radiationScore = !empty($radiations)
            ? min(60, ($this->average($radiations) / 7.0) * 60)
            : 0;

        $ratioScore = !empty($ratios)
            ? min(40, ($this->average($ratios, 1) / 100) * 40)
            : 0;

        return (int) min(100, round($radiationScore + $ratioScore));
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

    private function lowDay(array $data, string $field): array
    {
        $lowest = ['date' => '--', 'value' => PHP_FLOAT_MAX];

        foreach ($data as $row) {
            $value = (float) ($row[$field] ?? 0);

            if ($value <= $lowest['value']) {
                $lowest = [
                    'date' => $row['date'] ?? '--',
                    'value' => round($value, 1),
                ];
            }
        }

        return $lowest['value'] === PHP_FLOAT_MAX ? ['date' => '--', 'value' => 0] : $lowest;
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
