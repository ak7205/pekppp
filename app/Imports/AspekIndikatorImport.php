<?php

namespace App\Imports;

use App\Models\Aspect;
use App\Models\Indicator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class AspekIndikatorImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $currentAspek = null;

        foreach ($rows as $row) {
            // Helper konversi nilai numerik
            $toFloat = function($val) {
                if (is_null($val)) return 0;
                if (is_numeric($val)) return (float) $val;
                // Handle formula string seperti "=1/5"
                $val = str_replace('=', '', $val);
                if (str_contains($val, '/')) {
                    $parts = explode('/', $val);
                    if (count($parts) === 2 && is_numeric($parts[0]) && is_numeric($parts[1]) && $parts[1] != 0) {
                        return (float)$parts[0] / (float)$parts[1];
                    }
                }
                return 0;
            };

            // Baris aspek
            if (empty($row['kode_indikator']) && !empty($row['nama_aspek'])) {
                $currentAspek = Aspect::firstOrCreate(
                    ['name' => $row['nama_aspek']],
                    [
                        'weight' => $toFloat($row['bobot_aspek']),
                        'order'  => (int) ($row['urutan_aspek'] ?? 0),
                    ]
                );
                continue;
            }

            // Baris indikator
            if ($currentAspek && !empty($row['kode_indikator'])) {
                Indicator::firstOrCreate(
                    [
                        'aspect_id' => $currentAspek->id,
                        'code'      => $row['kode_indikator'],
                    ],
                    [
                        'name'          => trim($row['nama_indikator'] ?? ''),
                        'weight'        => $toFloat($row['bobot_indikator']),
                        'score_type'    => $row['tipe_nilai'] ?? 'likert',
                        'storage_link'  => $row['link_storage'] ?? null,
                        'template_text' => $row['catatan'] ?? null,
                        'order'         => (int) ($row['urutan'] ?? 0),
                    ]
                );
            }
        }
    }
}