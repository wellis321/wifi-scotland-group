<?php

declare(strict_types=1);

/**
 * Curated evidence-led stories (JSON under data/). Works without MySQL.
 *
 * @return list<array{
 *   title: string,
 *   summary: string,
 *   when: string,
 *   primary_label: string,
 *   primary_url: string,
 *   secondary_label?: string,
 *   secondary_url?: string,
 *   caveat?: string
 * }>
 */
function load_scotland_positive_stories(): array
{
    $path = PROJECT_ROOT . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'scotland-positive-stories.json';
    if (!is_readable($path)) {
        return [];
    }
    $raw = file_get_contents($path);
    if ($raw === false) {
        return [];
    }
    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return [];
    }
    $out = [];
    foreach ($decoded as $row) {
        if (!is_array($row)) {
            continue;
        }
        $title = isset($row['title']) ? trim((string) $row['title']) : '';
        $summary = isset($row['summary']) ? trim((string) $row['summary']) : '';
        $when = isset($row['when']) ? trim((string) $row['when']) : '';
        $primaryLabel = isset($row['primary_label']) ? trim((string) $row['primary_label']) : '';
        $primaryUrl = isset($row['primary_url']) ? trim((string) $row['primary_url']) : '';
        if ($title === '' || $summary === '' || $primaryUrl === '' || !preg_match('#^https://#i', $primaryUrl)) {
            continue;
        }
        if ($primaryLabel === '') {
            $primaryLabel = 'Source';
        }
        $item = [
            'title' => $title,
            'summary' => $summary,
            'when' => $when,
            'primary_label' => $primaryLabel,
            'primary_url' => $primaryUrl,
        ];
        $secUrl = isset($row['secondary_url']) ? trim((string) $row['secondary_url']) : '';
        $secLabel = isset($row['secondary_label']) ? trim((string) $row['secondary_label']) : '';
        if ($secUrl !== '' && preg_match('#^https://#i', $secUrl) && $secLabel !== '') {
            $item['secondary_label'] = $secLabel;
            $item['secondary_url'] = $secUrl;
        }
        if (!empty($row['caveat']) && is_string($row['caveat'])) {
            $c = trim($row['caveat']);
            if ($c !== '') {
                $item['caveat'] = $c;
            }
        }
        $out[] = $item;
    }

    return $out;
}
