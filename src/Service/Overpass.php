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
            if (isset($tags['addr:full'])) {
                $address = $tags['addr:full'];
            } else if (isset($tags['contact:full'])) {
                $address = $tags['contact:full'];
            } else if (isset($tags['addr:city']) || isset($tags['addr:postcode'])) {
                $housename = $tags['addr:housename'] ?? '';
                $housenumber = $tags['addr:housenumber'] ?? '';
                $street = $tags['addr:street'] ?? '';
                $postcode = $tags['addr:postcode'] ?? '';
                $city = $tags['addr:city'] ?? '';

                $address = ($housenumber ? $housenumber . ' ' : '')
                    . ($street ? $street . ', ' : '')
                    . ($housename ? $housename . ', ' : '')
                    . ($postcode ? $postcode . ' ' : '')
                    . ($city ? $city : '');
            } else if (isset($tags['contact:city']) || isset($tags['contact:postcode'])) {
                $housenumber = $tags['contact:housenumber'] ?? '';
                $street = $tags['contact:street'] ?? '';
                $postcode = $tags['contact:postcode'] ?? '';
                $city = $tags['contact:city'] ?? '';

                $address = ($housenumber ? $housenumber . ' ' : '')
                    . ($street ? $street . ', ' : '')
                    . ($postcode ? $postcode . ' ' : '')
                    . ($city ? $city : '');
            }

            $website = $tags['website'] ??
                $tags['url'] ??
                $tags['brand:website'] ??
                $tags['contact:website'] ?? null;

            $name = $tags['name'] ?? $tags['official_name'] ?? null;

            // Liste des tags utilisés
            $usedTags = [
                // category
                'tourism',
                'amenity',
                'historic',
                'leisure',

                'name',
                'official_name',
                'operator',

                'email',

                // website
                'website',
                'url',
                'brand:website',
                'contact:website',

                // social media
                'facebook',
                'contact:facebook',
                'instagram',
                'contact:instagram',
                'wikipedia',
                'brand:wikipedia',
                'twitter',
                'contact:twitter',

                // address
                'addr:housenumber',
                'addr:street',
                'addr:housename',
                'addr:postcode',
                'addr:city',
                'addr:full',

                // contact address
                'contact:housenumber',
                'contact:street',
                'contact:postcode',
                'contact:city',
                'contact:full',

                // Phone number
                'phone',

                // Thumbnail
                'image',
                'contact:image',
                'brand:image',
                'logo',
                'contact:logo',
                'brand:logo',

                'wikidata',
                'brand:wikidata',
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

            $twitter = $tags['contact:twitter'] ?? $tags['twitter'] ?? null;
            if ($twitter) {
                if (!str_starts_with($twitter, 'http')) {
                    $twitter = 'https://www.twitter.com/' . ltrim($twitter, '/');
                }
            }

            // Uniformisation des liens vers Wikipedia
            $wikipedia = null;
            if (isset($tags['wikipedia'])) {
                $wikipedia = $tags['wikipedia'];
                if (!str_starts_with($wikipedia, 'http')) {
                    $wikipedia = 'https://en.wikipedia.org/wiki/' . ltrim($wikipedia, '/');
                }
            } else if (isset($tags['brand:wikipedia'])) {
                $wikipedia = $tags['brand:wikipedia'];
                if (!str_starts_with($wikipedia, 'http')) {
                    $wikipedia = 'https://en.wikipedia.org/wiki/' . ltrim($wikipedia, '/');
                }
            }

            $thumbnail = null;
            if (isset($tags['image'])) {
                $thumbnail = $tags['image'];
            } else if (isset($tags['contact:image'])) {
                $thumbnail = $tags['contact:image'];
            } else if (isset($tags['brand:image'])) {
                $thumbnail = $tags['brand:image'];
            } else if (isset($tags['logo'])) {
                $thumbnail = $tags['logo'];
            } else if (isset($tags['contact:logo'])) {
                $thumbnail = $tags['contact:logo'];
            } else if (isset($tags['brand:logo'])) {
                $thumbnail = $tags['brand:logo'];
            }

            $wikidata = $tags['wikidata'] ?? $tags['brand:wikidata'] ?? null;

            $description = null;
            if (isset($tags['description:fr'])) {
                $description = $tags['description:fr'];
            } else if (isset($tags['description'])) {
                $description = $tags['description'];
            } else if (isset($tags['description:en'])) {
                $description = $tags['description:en'];
            }

            $results[] = [
                'description' => $description,
                'name' => $name,
                'address' => trim($address),
                'phone' => $tags['phone'] ?? null,
                'email' => $tags['email'] ?? null,
                'website' => $website,
                'instagram' => $instagram,
                'facebook' => $facebook,
                'twitter' => $twitter,
                'wikipedia' => $wikipedia,
                'thumbnail' => $thumbnail,
                'wikidata' => $wikidata,
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

    public function getWikidataThumbnail(string $wikidataId): ?string
    {
        $endpoint = 'https://www.wikidata.org/w/api.php';

        try {
            $response = $this->client->request('GET', $endpoint, [
                'query' => [
                    'action' => 'wbgetentities',
                    'ids' => $wikidataId,
                    'format' => 'json',
                    'props' => 'claims'
                ]
            ]);

            $data = $response->toArray();

            if (isset($data['entities'][$wikidataId]['claims']['P18'][0]['mainsnak']['datavalue']['value'])) {
                $url = 'https://commons.wikimedia.org/wiki/Special:FilePath/';
                $url .= $data['entities'][$wikidataId]['claims']['P18'][0]['mainsnak']['datavalue']['value'];
                return $url;
            }
        } catch (\RuntimeException $e) {
            // Handle error if needed
        }

        return null;
    }
}
