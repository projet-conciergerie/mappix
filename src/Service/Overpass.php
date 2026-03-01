<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class Overpass
{
    private const ENDPOINT = 'https://overpass-api.de/api/interpreter';

    private const CATEGORIES = [
        'hotels' => ['tourism', 'hotel'],
        'bars' => ['amenity', 'bar'],
        'pubs' => ['amenity', 'pub'],
        'restaurants' => ['amenity', 'restaurant'],
        'fontaines' => ['amenity', 'drinking_water'],
        'toilettes' => ['amenity', 'toilets'],
        'musees' => ['tourism', 'museum'],
        'monuments' => ['historic', 'monument'],
        'parcs' => ['leisure', 'park'],
        'monuments_historiques' => ['historic', 'monument'],
        'attractions' => ['tourism', 'attraction'],
    ];

    public function __construct(
        private HttpClientInterface $client
    ) {
    }

    /**
     * Récupère tous les bars dans une zone géographique définie.
     *
     * @param string $areaName Nom de la zone (ex: "Rouen", "Normandie")
     * @return array Liste des bars (nom, adresse, lat, lon)
     */

    public function getInArea(string $areaName, string $category): array
    {
        $data = [];

        if (!isset(self::CATEGORIES[$category])) {
            return [];
        }

        [$overpassCategory, $overpassType] = self::CATEGORIES[$category];

        // Directory to save cache files
        $filedir = __DIR__ . '/../../public/data';

        if (!file_exists($filedir)) {
            mkdir($filedir, 0777);
        }

        // File to save / restore cache files 
        $filename = $filedir . '/overpass_' . $areaName . '_' . $category . '.json';

        if (file_exists($filename)) {
            // Return a pre loaded request
            $jsondata = file_get_contents($filename);
            $data = json_decode($jsondata, true);
        } else {
            // Requête Overpass
            $query = <<<OVERPASS
[out:json][timeout:25];
area["name"="$areaName"]->.a;
(
  node["$overpassCategory"="$overpassType"](area.a);
  way["$overpassCategory"="$overpassType"](area.a);
  relation["$overpassCategory"="$overpassType"](area.a);
);
out center;
OVERPASS;

            try {
                $response = $this->client->request('POST', self::ENDPOINT, [
                    'body' => ['data' => $query],
                ]);

                $data = $response->toArray();

                $jsondata = json_encode($data);
                file_put_contents($filename, $jsondata);
            } catch (\RuntimeException $e) {
                $data['error'] = 'Datas collect from Overpass was rejected';
                $data['elements'] = [];
            }
        }

        $results = [];

        foreach ($data['elements'] as $el) {
            $lat = $el['lat'] ?? $el['center']['lat'] ?? null;
            $lon = $el['lon'] ?? $el['center']['lon'] ?? null;

            $tags = $el['tags'] ?? [];

            $address = "";
            if (isset($tags['addr:city']) || isset($tags['addr:postcode'])) {
                $housename = $tags['addr:housename'] ?? '';

                $address = ($tags['addr:housenumber'] ?? '') . ' '
                    . ($tags['addr:street'] ?? '') . ', '
                    . ($housename ? $housename . ', ' : '')
                    . ($tags['addr:postcode'] ?? '') . ' '
                    . ($tags['addr:city'] ?? '');
            } else if (isset($tags['contact:city']) || isset($tags['contact:postcode'])) {
                $address = ($tags['contact:housenumber'] ?? '') . ' '
                    . ($tags['contact:street'] ?? '') . ', '
                    . ($tags['contact:postcode'] ?? '') . ' '
                    . ($tags['contact:city'] ?? '');
            }

            // Liste des tags utilisés
            $usedTags = [
                'name',
                'email',
                'website',
                'facebook',
                'contact:facebook',
                'instagram',
                'contact:instagram',

                // address
                'addr:housenumber',
                'addr:street',
                'addr:housename',
                'addr:postcode',
                'addr:city',

                // contact address
                'contact:housenumber',
                'contact:street',
                'contact:postcode',
                'contact:city',

                // Phone number
                'phone',
            ];

            $remainingTags = array_diff_key(
                $tags,
                array_flip($usedTags)
            );

            // Uniformisation des liens Facebook
            $facebook = $tags['contact:facebook'] ?? $tags['facebook'] ?? null;
            if ($facebook) {
                if (!str_starts_with($facebook, 'http')) {
                    $facebook = 'https://www.facebook.com/' . ltrim($facebook, '/');
                }
            }

            // Uniformisation des liens Instagram
            $instagram = $tags['contact:instagram'] ?? $tags['instagram'] ?? null;
            if ($instagram) {
                if (!str_starts_with($instagram, 'http')) {
                    $instagram = 'https://www.instagram.com/' . ltrim($instagram, '/');
                }
            }

            $results[] = [
                'name' => $tags['name'] ?? $category . ' sans nom',
                'address' => trim($address),
                'phone' => $tags['phone'] ?? null,
                'email' => $tags['email'] ?? null,
                'website' => $tags['website'] ?? null,
                'instagram' => $instagram,
                'facebook' => $facebook,
                'lat' => $lat,
                'lon' => $lon,
                'tags' => $remainingTags
            ];
        }

        return $results;
    }

    public function getInAreaShort(string $areaName, string $category): array
    {
        $data = $this->getInArea($areaName, $category);

        $results = [];

        foreach ($data as $item) {
            $results[] = [
                'name' => $item['name'],
                'address' => $item['address'],
                'lat' => $item['lat'],
                'lon' => $item['lon']
            ];
        }

        return $results;
    }

}
