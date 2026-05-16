<?php

namespace App\Exports;

use App\Models\Aspect;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class AspekIndikatorExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function collection(): Collection
    {
        $rows = collect();
        $aspects = Aspect::with('indicators')->orderBy('order')->get();

        foreach ($aspects as $aspect) {
            // Baris aspek
            $rows->push([
                'nama_aspek'       => $aspect->name,
                'bobot_aspek'      => $aspect->weight,
                'urutan_aspek'     => $aspect->order,
                'kode_indikator'   => '',
                'nama_indikator'   => '',
                'bobot_indikator'  => '',
                'tipe_nilai'       => '',
                'link_storage'     => '',
                'catatan'          => '',
                'urutan'           => '',
            ]);

            // Baris indikator
            foreach ($aspect->indicators as $ind) {
                $rows->push([
                    'nama_aspek'       => '',
                    'bobot_aspek'      => '',
                    'urutan_aspek'     => '',
                    'kode_indikator'   => $ind->code,
                    'nama_indikator'   => $ind->name,
                    'bobot_indikator'  => $ind->weight,
                    'tipe_nilai'       => $ind->score_type,
                    'link_storage'     => $ind->storage_link ?? '',
                    'catatan'          => $ind->template_text ?? '',
                    'urutan'           => $ind->order,
                ]);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'nama_aspek',
            'bobot_aspek',
            'urutan_aspek',
            'kode_indikator',
            'nama_indikator',
            'bobot_indikator',
            'tipe_nilai',
            'link_storage',
            'catatan',
            'urutan',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 12,
            'C' => 12,
            'D' => 15,
            'E' => 50,
            'F' => 15,
            'G' => 15,
            'H' => 40,
            'I' => 30,
            'J' => 10,
        ];
    }
}