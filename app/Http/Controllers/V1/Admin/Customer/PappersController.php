<?php

namespace Crater\Http\Controllers\V1\Admin\Customer;

use Crater\Http\Controllers\Controller;
use Crater\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PappersController extends Controller
{
    const BASE_URL = 'https://api.pappers.fr/v2';

    /**
     * Search French companies on Pappers by name, SIREN or SIRET.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $this->authorize('create', Customer::class);

        $request->validate(['q' => 'required|string|min:2|max:100']);

        $query = preg_replace('/\s+/', '', $request->q);

        // A SIREN (9 digits) or SIRET (14 digits) identifies one company directly
        if (preg_match('/^\d{9}$|^\d{14}$/', $query)) {
            $param = strlen($query) === 9 ? 'siren' : 'siret';
            $response = $this->call('/entreprise', [$param => $query]);

            if ($response === null) {
                return $this->unavailable();
            }

            return response()->json([
                'results' => $response->successful() ? [$this->normalize($response->json())] : [],
            ]);
        }

        $response = $this->call('/recherche', ['q' => $request->q, 'par_page' => 10]);

        if ($response === null) {
            return $this->unavailable();
        }

        if (! $response->successful()) {
            return response()->json(['results' => []]);
        }

        $results = collect($response->json('resultats', []))
            ->map(fn ($company) => $this->normalize($company))
            ->values();

        return response()->json(['results' => $results]);
    }

    /**
     * Get the full details of one company by SIREN.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function company(Request $request)
    {
        $this->authorize('create', Customer::class);

        $request->validate(['siren' => 'required|digits:9']);

        $response = $this->call('/entreprise', ['siren' => $request->siren]);

        if ($response === null) {
            return $this->unavailable();
        }

        if (! $response->successful()) {
            return response()->json(['company' => null], 404);
        }

        return response()->json(['company' => $this->normalize($response->json())]);
    }

    /**
     * @return \Illuminate\Http\Client\Response|null  null when no API key is configured
     */
    private function call(string $path, array $params)
    {
        $token = config('services.pappers.key');

        if (! $token) {
            return null;
        }

        return Http::timeout(10)
            ->acceptJson()
            ->get(self::BASE_URL.$path, array_merge($params, ['api_token' => $token]));
    }

    private function unavailable()
    {
        return response()->json([
            'error' => 'pappers_not_configured',
            'message' => 'PAPPERS_API_KEY is not set.',
        ], 503);
    }

    /**
     * Map a Pappers company (search result or full record) to the customer form fields.
     */
    private function normalize(array $company): array
    {
        $siege = $company['siege'] ?? [];
        $siren = $company['siren'] ?? null;

        return [
            'name' => $company['nom_entreprise'] ?? $company['denomination'] ?? null,
            'siren' => $siren,
            'siret' => $siege['siret'] ?? null,
            'vat_number' => $company['numero_tva_intracommunautaire'] ?? $this->frenchVatNumber($siren),
            'legal_form' => $company['forme_juridique'] ?? null,
            'website' => $company['site_internet'] ?? null,
            'phone' => $company['telephone'] ?? $siege['telephone'] ?? null,
            'address_street_1' => $siege['adresse_ligne_1'] ?? null,
            'address_street_2' => $siege['adresse_ligne_2'] ?? null,
            'zip' => $siege['code_postal'] ?? null,
            'city' => $siege['ville'] ?? null,
            'country_code' => $siege['code_pays'] ?? 'FR',
        ];
    }

    /**
     * French intra-EU VAT number derived from the SIREN: FR + key + SIREN,
     * with key = (12 + 3 * (SIREN mod 97)) mod 97.
     */
    private function frenchVatNumber(?string $siren): ?string
    {
        if (! $siren || ! preg_match('/^\d{9}$/', $siren)) {
            return null;
        }

        $key = (12 + 3 * ((int) $siren % 97)) % 97;

        return 'FR'.str_pad((string) $key, 2, '0', STR_PAD_LEFT).$siren;
    }
}
