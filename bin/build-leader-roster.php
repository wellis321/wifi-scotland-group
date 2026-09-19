#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * One-off builder: cross-references data/council-leader-names.csv (Leader names,
 * researched fresh) against data/councillors-roster.csv (already-verified emails) to
 * produce data/council-leader-roster.csv for bin/send-council-leader-campaign.php.
 *
 * Not part of the regular send pipeline — run manually whenever the Leader name list
 * needs rebuilding, then review the output (especially anything under "NEEDS REVIEW")
 * before it's used to send anything.
 */

if (php_sapi_name() !== 'cli') {
    exit("CLI only.\n");
}

$namesPath = __DIR__ . '/../data/council-leader-names.csv';
$rosterPath = __DIR__ . '/../data/councillors-roster.csv';
$outPath = __DIR__ . '/../data/council-leader-roster.csv';

function read_csv(string $path): array
{
    $fh = fopen($path, 'r');
    $header = fgetcsv($fh, 0, ',', '"', '\\');
    $headerCount = count($header);
    $rows = [];
    while (($line = fgetcsv($fh, 0, ',', '"', '\\')) !== false) {
        if (count($line) < $headerCount) continue;
        if (count($line) > $headerCount) {
            $overflow = array_splice($line, $headerCount - 1);
            $line[] = implode(',', $overflow);
        }
        $rows[] = array_combine($header, $line);
    }
    fclose($fh);
    return $rows;
}

/** Extract clean candidate name(s) to look up from a raw leader_name field. */
function extract_candidate_names(string $raw): array
{
    // Eilean Siar-style: "X (Council Leader); Y (Council Convener)" — only want the Leader.
    if (str_contains($raw, 'Council Convener')) {
        $parts = explode(';', $raw);
        foreach ($parts as $part) {
            if (str_contains($part, 'Council Leader')) {
                $raw = $part;
                break;
            }
        }
    }

    // Co-leader style: "X (PARTY) and Y (PARTY), Co-Leaders"
    $raw = preg_replace('/,?\s*Co-Leaders?\s*$/i', '', $raw);
    $raw = preg_replace('/\s*\(Council Leader\)\s*$/i', '', $raw);

    if (preg_match('/^(.+?)\s+and\s+(.+)$/i', $raw, $m)) {
        $candidates = [$m[1], $m[2]];
    } else {
        $candidates = [$raw];
    }

    // Strip trailing " (PARTY)" parentheticals from each candidate.
    return array_map(static function (string $name): string {
        $name = preg_replace('/\s*\([^)]*\)\s*$/', '', $name);
        return trim($name);
    }, $candidates);
}

function normalize(string $name): string
{
    return strtolower(trim(preg_replace('/\s+/', ' ', $name)));
}

$leaderRows = read_csv($namesPath);
$councillorRows = read_csv($rosterPath);

// Index councillor roster by council, with normalized-name lookup.
$byCouncil = [];
foreach ($councillorRows as $r) {
    $byCouncil[$r['council']][] = $r;
}

$out = [];
$needsReview = [];

foreach ($leaderRows as $lr) {
    $council = $lr['council_area'];
    $candidates = extract_candidate_names($lr['leader_name']);
    $pool = $byCouncil[$council] ?? [];

    foreach ($candidates as $candidateName) {
        $target = normalize($candidateName);
        $match = null;

        foreach ($pool as $c) {
            if (normalize($c['full_name']) === $target) {
                $match = $c;
                break;
            }
        }

        // Fallback: match on surname + first initial if exact full match failed
        // (handles middle names/initials like "Paul F Steele" vs "Paul Steele").
        if ($match === null) {
            $targetParts = explode(' ', $target);
            $targetFirst = $targetParts[0] ?? '';
            $targetLast = end($targetParts);
            foreach ($pool as $c) {
                $cParts = explode(' ', normalize($c['full_name']));
                $cFirst = $cParts[0] ?? '';
                $cLast = end($cParts);
                if ($cFirst === $targetFirst && $cLast === $targetLast) {
                    $match = $c;
                    break;
                }
            }
        }

        if ($match !== null) {
            $out[] = [
                'council_area' => $council,
                'leader_name' => $candidateName,
                'leader_email' => $match['email'],
                'confidence' => $lr['confidence'],
                'source_url' => $lr['source_url'],
                'notes' => $lr['notes'],
            ];
        } else {
            $needsReview[] = "$council: '$candidateName' — no match found in councillor roster (pool size: " . count($pool) . ")";
        }
    }
}

$fh = fopen($outPath, 'w');
fputcsv($fh, ['council_area', 'leader_name', 'leader_email', 'confidence', 'source_url', 'notes'], ',', '"', '\\');
foreach ($out as $row) {
    fputcsv($fh, $row, ',', '"', '\\');
}
fclose($fh);

fwrite(STDOUT, "Wrote " . count($out) . " matched rows to $outPath\n");
if (!empty($needsReview)) {
    fwrite(STDOUT, "\nNEEDS REVIEW (" . count($needsReview) . "):\n");
    foreach ($needsReview as $line) {
        fwrite(STDOUT, "  - $line\n");
    }
}
