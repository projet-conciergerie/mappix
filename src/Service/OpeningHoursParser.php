<?php

namespace App\Service;

class OpeningHoursParser
{
    private const DAYS = [
        'Mo' => 'mo',
        'Tu' => 'tu',
        'We' => 'we',
        'Th' => 'th',
        'Fr' => 'fr',
        'Sa' => 'sa',
        'Su' => 'su',

        'lundi' => 'mo',
        'mardi' => 'tu',
        'mercredi' => 'we',
        'jeudi' => 'th',
        'vendredi' => 'fr',
        'samedi' => 'sa',
        'dimanche' => 'su',
    ];

    public function parse(?string $input): array
    {
        $result = $this->emptyWeek();

        if (!$input) {
            return $result;
        }

        $input = trim($input);
        $result['meta']['raw'] = $input;

        if (preg_match('/24\s*\/\s*7|toujours/i', $input)) {
            foreach ($this->getDaysOrder() as $day) {
                $result[$day][] = ['start' => '00:00', 'end' => '23:59'];
            }
            $result['meta']['always_open'] = true;
            return $result;
        }

        if (strtolower($input) === 'off') {
            return $result;
        }

        $input = str_replace(['closed', 'fermé'], 'off', $input);
        $blocks = preg_split('/;/', $input);

        foreach ($blocks as $block) {
            $this->parseBlock(trim($block), $result);
        }

        return $result;
    }

    private function parseBlock(string $block, array &$result): void
    {
        if (!$block) return;

        if (preg_match('/^([A-Za-z,\-\s]+)\s+(.*)$/', $block, $matches)) {
            $daysPart = trim($matches[1]);
            $timePart = trim($matches[2]);
        } else {
            $daysPart = 'Mo-Su';
            $timePart = $block;
        }

        $days = $this->expandDays($daysPart);

        if (stripos($timePart, 'off') !== false) {
            foreach ($days as $day) {
                $result[$day] = [];
            }
            return;
        }

        $timeRanges = preg_split('/,/', $timePart);

        foreach ($timeRanges as $range) {
            if (preg_match('/(\d{1,2}:\d{1,2})\s*-\s*(\d{1,2}:\d{1,2})/', trim($range), $m)) {

                $start = $this->normalizeTime($m[1]);
                $end   = $this->normalizeTime($m[2]);

                foreach ($days as $day) {
                    $result[$day][] = [
                        'start' => $start,
                        'end'   => $end
                    ];
                }
            }
        }
    }

    private function expandDays(string $input): array
    {
        $days = [];
        $order = $this->getDaysOrder();

        foreach (explode(',', $input) as $part) {
            $part = trim($part);

            if (strpos($part, '-') !== false) {
                [$start, $end] = array_map('trim', explode('-', $part));

                $startKey = self::DAYS[$start] ?? null;
                $endKey   = self::DAYS[$end] ?? null;

                if (!$startKey || !$endKey) continue;

                $startIndex = array_search($startKey, $order);
                $endIndex   = array_search($endKey, $order);

                if ($startIndex <= $endIndex) {
                    for ($i = $startIndex; $i <= $endIndex; $i++) {
                        $days[] = $order[$i];
                    }
                }
            } else {
                if (isset(self::DAYS[$part])) {
                    $days[] = self::DAYS[$part];
                }
            }
        }

        return array_unique($days);
    }

    private function normalizeTime(string $time): string
    {
        [$h, $m] = explode(':', $time);

        $h = (int)$h;
        $m = (int)$m;

        if ($h >= 24) {
            $h = $h % 24;
        }

        return sprintf('%02d:%02d', $h, $m);
    }

    private function getDaysOrder(): array
    {
        return ['mo', 'tu', 'we', 'th', 'fr', 'sa', 'su'];
    }

    private function emptyWeek(): array
    {
        return [
            'mo' => [],
            'tu' => [],
            'we' => [],
            'th' => [],
            'fr' => [],
            'sa' => [],
            'su' => [],
            'meta' => [
                'raw' => null,
                'always_open' => false
            ]
        ];
    }

    private function compressDays(array $days): string
    {
        $order = $this->getDaysOrder();
        $indexes = array_map(fn($d) => array_search($d, $order), $days);
        sort($indexes);

        $ranges = [];
        $start = $prev = array_shift($indexes);

        foreach ($indexes as $i) {
            if ($i === $prev + 1) {
                $prev = $i;
                continue;
            }

            $ranges[] = [$start, $prev];
            $start = $prev = $i;
        }

        $ranges[] = [$start, $prev];

        $result = [];

        foreach ($ranges as [$s, $e]) {
            if ($s === $e) {
                $result[] = ucfirst($order[$s]);
            } else {
                $result[] = ucfirst($order[$s]) . '-' . ucfirst($order[$e]);
            }
        }

        return implode(',', $result);
    }

    public function toStandardString(array $parsed): string
    {
        if ($parsed['meta']['always_open'] ?? false) {
            return '24/7';
        }

        $order = $this->getDaysOrder();

        // Grouper les jours ayant les mêmes horaires
        $groups = [];

        foreach ($order as $day) {
            $slots = $parsed[$day] ?? [];

            if (empty($slots)) {
                $key = 'off';
            } else {
                $ranges = array_map(
                    fn($s) => $s['start'] . '-' . $s['end'],
                    $slots
                );
                sort($ranges);
                $key = implode(',', $ranges);
            }

            $groups[$key][] = $day;
        }

        $parts = [];

        foreach ($groups as $time => $days) {

            $dayString = $this->compressDays($days);

            if ($time === 'off') {
                $parts[] = $dayString . ' off';
            } else {
                $parts[] = $dayString . ' ' . $time;
            }
        }

        return implode('; ', $parts);
    }
}
