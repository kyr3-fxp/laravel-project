<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class ClimateController extends Controller
{
    private const NASA_API_URL = 'https://power.larc.nasa.gov/api/temporal/daily/point';

    /**
     * Mostrar el dashboard principal
     */
    public function index(): View
    {
        return view('dashboard.index');
    }

    /**
     * Obtener datos climáticos de NASA POWER API
     */
    public function getWeatherData()
    {
        $latitude = request('latitude');
        $longitude = request('longitude');

        if ($latitude === null || $longitude === null || $latitude === '' || $longitude === '') {
            return response()->json([
                'success' => false,
                'message' => 'Debes enviar latitude y longitude para consultar la API.'
            ], 422);
        }

        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return response()->json([
                'success' => false,
                'message' => 'Latitude y longitude deben ser valores numéricos.'
            ], 422);
        }

        $latitude = (float) $latitude;
        $longitude = (float) $longitude;

        $parameters = [
            'start' => now()->subDays(10)->format('Ymd'),
            'end' => now()->format('Ymd'),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'community' => 're',
            'parameters' => 'T2M,RH2M,WS2M,PRECTOTCORR',
            'format' => 'json',
            'units' => 'metric',
        ];

        try {
            $response = Http::withoutVerifying()
                ->timeout(10)
                ->acceptJson()
                ->get(self::NASA_API_URL, $parameters);

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener datos de NASA POWER API',
                    'status' => $response->status(),
                ], 500);
            }

            $data = $response->json();
            $processedData = $this->processWeatherData($data);

            if (empty($processedData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La API respondió, pero no llegaron datos climáticos procesables.',
                    'raw_keys' => array_keys($data ?? []),
                ], 502);
            }

            return response()->json([
                'success' => true,
                'data' => $processedData,
                'statistics' => $this->calculateStatistics($processedData),
                'meta' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'date_range' => [
                        'start' => $parameters['start'],
                        'end' => $parameters['end']
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Procesar datos crudos de la API
     */
    private function processWeatherData(array $data): array
    {
        if (!isset($data['properties']['parameter']) || !is_array($data['properties']['parameter'])) {
            return [];
        }

        $parameters = $data['properties']['parameter'];
        $temperatureSeries = $parameters['T2M'] ?? null;

        if (!is_array($temperatureSeries) || empty($temperatureSeries)) {
            return [];
        }

        $processedData = [];
        $dates = array_keys($temperatureSeries);

        foreach ($dates as $date) {
            $temperature = $parameters['T2M'][$date] ?? null;
            $humidity = $parameters['RH2M'][$date] ?? null;
            $windSpeed = $parameters['WS2M'][$date] ?? null;
            $precipitation = $parameters['PRECTOTCORR'][$date] ?? null;

            if ($temperature === null && $humidity === null && $windSpeed === null && $precipitation === null) {
                continue;
            }

            $processedData[] = [
                'date' => $this->formatDate($date),
                'temperature' => $this->sanitizeNumber($temperature, 1),
                'humidity' => $this->sanitizeNumber($humidity, 1),
                'wind_speed' => $this->sanitizeNumber($windSpeed, 2),
                'precipitation' => $this->sanitizeNumber($precipitation, 2),
            ];
        }

        return $processedData;
    }

    /**
     * Calcular estadísticas
     */
    private function calculateStatistics(array $data): array
    {
        if (empty($data)) {
            return [];
        }

        $temperatures = $this->filterValidNumbers(array_column($data, 'temperature'));
        $humidities = $this->filterValidNumbers(array_column($data, 'humidity'));
        $windSpeeds = $this->filterValidNumbers(array_column($data, 'wind_speed'));
        $precipitations = $this->filterValidNumbers(array_column($data, 'precipitation'));

        return [
            'avg_temperature' => !empty($temperatures) ? round(array_sum($temperatures) / count($temperatures), 1) : 0,
            'max_temperature' => !empty($temperatures) ? max($temperatures) : 0,
            'min_temperature' => !empty($temperatures) ? min($temperatures) : 0,
            'avg_humidity' => !empty($humidities) ? round(array_sum($humidities) / count($humidities), 1) : 0,
            'avg_wind_speed' => !empty($windSpeeds) ? round(array_sum($windSpeeds) / count($windSpeeds), 2) : 0,
            'total_precipitation' => !empty($precipitations) ? round(array_sum($precipitations), 2) : 0,
        ];
    }

    /**
     * Formatear fecha desde timestamp
     */
    private function formatDate($dateString): string
    {
        if (!$dateString) {
            return '';
        }

        try {
            return \Carbon\Carbon::createFromFormat('Ymd', $dateString)->format('d/m/Y');
        } catch (\Exception $e) {
            return $dateString;
        }
    }

    private function sanitizeNumber(mixed $value, int $precision): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            return null;
        }

        $numericValue = round((float) $value, $precision);

        if (abs($numericValue + 999) < 0.01) {
            return null;
        }

        return $numericValue;
    }

    private function filterValidNumbers(array $values): array
    {
        return array_values(array_filter($values, function ($value) {
            if ($value === null || !is_numeric($value)) {
                return false;
            }

            return abs((float) $value + 999) >= 0.01;
        }));
    }
}
