<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CountryController extends Controller
{
    // GET /api/countries
    // Obtener lista de países desde REST Countries API
    public function index()
    {
        try {
            // Usar cache para no hacer request cada vez (24 horas)
            $countries = Cache::remember('countries_list', 86400, function () {
                $response = Http::get('https://restcountries.com/v3.1/all');
                
                if (!$response->successful()) {
                    return null;
                }

                $data = $response->json();

                // Procesar y formatear los datos
                $countries = array_map(function ($country) {
                    return [
                        'name' => $country['name']['common'] ?? 'N/A',
                        'official_name' => $country['name']['official'] ?? 'N/A',
                        'flag' => $country['flag'] ?? '🌍',
                        'region' => $country['region'] ?? 'N/A',
                        'subregion' => $country['subregion'] ?? 'N/A',
                        'population' => $country['population'] ?? 0,
                        'area' => $country['area'] ?? 0,
                        'capital' => isset($country['capital']) ? $country['capital'][0] : 'N/A',
                        'languages' => $country['languages'] ?? [],
                        'currencies' => $country['currencies'] ?? [],
                        'timezones' => $country['timezones'] ?? [],
                        'code' => $country['cca2'] ?? 'XX'
                    ];
                }, $data);

                // Ordenar alfabéticamente
                usort($countries, function ($a, $b) {
                    return strcmp($a['name'], $b['name']);
                });

                return $countries;
            });

            if (!$countries) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener países'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'count' => count($countries),
                'data' => $countries
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // GET /api/countries/{code}
    // Obtener detalles de un país específico
    public function show($code)
    {
        try {
            $response = Http::get("https://restcountries.com/v3.1/alpha/{$code}");

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'País no encontrado'
                ], 404);
            }

            $country = $response->json()[0] ?? null;

            if (!$country) {
                return response()->json([
                    'success' => false,
                    'message' => 'País no encontrado'
                ], 404);
            }

            $formatted = [
                'name' => $country['name']['common'] ?? 'N/A',
                'official_name' => $country['name']['official'] ?? 'N/A',
                'flag' => $country['flag'] ?? '🌍',
                'region' => $country['region'] ?? 'N/A',
                'subregion' => $country['subregion'] ?? 'N/A',
                'population' => $country['population'] ?? 0,
                'area' => $country['area'] ?? 0,
                'capital' => isset($country['capital']) ? $country['capital'][0] : 'N/A',
                'languages' => $country['languages'] ?? [],
                'currencies' => $country['currencies'] ?? [],
                'timezones' => $country['timezones'] ?? [],
                'code' => $country['cca2'] ?? 'XX',
                'borders' => $country['borders'] ?? [],
                'continents' => $country['continents'] ?? []
            ];

            return response()->json([
                'success' => true,
                'data' => $formatted
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // GET /api/countries/region/{region}
    // Obtener países por región
    public function byRegion($region)
    {
        try {
            $response = Http::get("https://restcountries.com/v3.1/region/{$region}");

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Región no encontrada'
                ], 404);
            }

            $data = $response->json();

            $countries = array_map(function ($country) {
                return [
                    'name' => $country['name']['common'] ?? 'N/A',
                    'flag' => $country['flag'] ?? '🌍',
                    'region' => $country['region'] ?? 'N/A',
                    'code' => $country['cca2'] ?? 'XX'
                ];
            }, $data);

            usort($countries, function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            });

            return response()->json([
                'success' => true,
                'region' => $region,
                'count' => count($countries),
                'data' => $countries
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
