<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class Overpass
{
    private const ENDPOINT = 'https://overpass-api.de/api/interpreter';

    public function __construct(
        private HttpClientInterface $client
    ) {}

    /**
     * Récupère tous les bars dans une zone géographique définie.
     *
     * @param string $areaName Nom de la zone (ex: "Rouen", "Normandie")
     * @return array Liste des bars (nom, adresse, lat, lon)
     */

    public function getInArea(string $areaName, string $category): array
    {
        $data = [];

        $filename = __DIR__ . '/../../public/data/overpass_' . $areaName . '_' . $category . '.json';

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
  node["amenity"="bar"](area.a);
  way["amenity"="bar"](area.a);
  relation["amenity"="bar"](area.a);
);
out center;
OVERPASS;

            $response = $this->client->request('POST', self::ENDPOINT, [
                'body' => ['data' => $query],
            ]);

            $data = $response->toArray();

            $jsondata = json_encode($data);
            file_put_contents($filename, $jsondata);
        }

        $results = [];

        foreach ($data['elements'] as $el) {
            $lat = $el['lat'] ?? $el['center']['lat'] ?? null;
            $lon = $el['lon'] ?? $el['center']['lon'] ?? null;

            $tags = $el['tags'] ?? [];

            $results[] = [
                'name' => $tags['name'] ?? 'Bar sans nom',
                'address' => trim(
                    ($tags['addr:housenumber'] ?? '') . ' ' .
                        ($tags['addr:street'] ?? '') . ', ' .
                        ($tags['addr:city'] ?? '')
                ),
                'lat' => $lat,
                'lon' => $lon
            ];
        }

        return $results;
    }
}
