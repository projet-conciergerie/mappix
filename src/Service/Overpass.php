<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class Overpass
{
    private const ENDPOINT = 'https://overpass-api.de/api/interpreter';

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

        $overpassCategory = "";
        if ($category === "hotels") {
            $overpassCategory = "tourism";
            $overpassType = "hotel";
        } else if ($category === "bars") {
            $overpassCategory = "amenity";
            $overpassType = "bar";
        } else if ($category === "restaurants") {
            $overpassCategory = "amenity";
            $overpassType = "restaurant";
        } else if ($category === "fontaines") {
            $overpassCategory = "amenity";
            $overpassType = "drinking_water";
        } else if ($category === "toilettes") {
            $overpassCategory = "amenity";
            $overpassType = "toilets";
        } else {
            return [];
        }

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

            $address = "";
            if (isset($tags['addr:city']) || isset($tags['addr:postcode'])) {
                $address = ($tags['addr:housenumber'] ?? '') . ' '
                    . ($tags['addr:street'] ?? '') . ', '
                    . ($tags['addr:postcode'] ?? '') . ' '
                    . ($tags['addr:city'] ?? '');
            } else if (isset($tags['contact:city']) || isset($tags['contact:postcode'])) {
                $address =  ($tags['contact:housenumber'] ?? '') . ' '
                    . ($tags['contact:street'] ?? '') . ', '
                    . ($tag['contact:postcode'] ?? '') . ' '
                    . ($tags['contact:city'] ?? '');
            }

            $results[] = [
                'name' => $tags['name'] ?? $category . ' sans nom',
                'address' => trim($address),
                'lat' => $lat,
                'lon' => $lon,
                'tags' => $tags
            ];
        }

        return $results;
    }
}
