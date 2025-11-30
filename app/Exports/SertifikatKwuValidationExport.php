<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class SertifikatKwuValidationExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    /**
     * @var array
     */
    protected $validationData;

    /**
     * Constructor
     *
     * @param array $validationData
     */
    public function __construct(array $validationData)
    {
        $this->validationData = $validationData;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $result = [];

        foreach ($this->validationData as $index => $data) {
            $result[] = [
                'no' => $index + 1,
                'nim' => $data['nim'],
                'nama' => $data['nama'],
                'status' => $data['status'] ? 'Lulus' : 'Tidak Lulus',
                'tahun_akademik' => $data['tahun'] . ' ' . $data['semester'] ?? '-'
            ];
        }

        return new Collection($result);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'NIM',
            'Nama',
            'Status Lulus',
            'Tahun Akademik',
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Hasil Validasi';
    }

    /**
     * @param Worksheet $sheet
     * @return void
     */
    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4B5563'], // Warna abu-abu gelap
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Style untuk seluruh data
        $dataRows = count($this->validationData);
        if ($dataRows > 0) {
            // Tambahkan border untuk semua sel
            $sheet->getStyle('A1:E' . ($dataRows + 1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ]);
            $sheet->getStyle('D2:D' . ($dataRows + 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Warna berbeda untuk status Lulus dan Tidak Lulus
            for ($i = 2; $i <= $dataRows + 1; $i++) {
                $status = $sheet->getCell('D' . $i)->getValue();
                if ($status === 'Lulus') {
                    $sheet->getStyle('D' . $i)->applyFromArray([
                        'font' => ['color' => ['rgb' => '28A745']], // Warna hijau success Bootstrap
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'C6F6D5'], // Warna hijau muda
                        ],
                    ]);
                } else {
                    $sheet->getStyle('D' . $i)->applyFromArray([
                        'font' => ['color' => ['rgb' => 'DC3545']], // Warna merah danger Bootstrap
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FED7D7'], // Warna merah muda
                        ],
                    ]);
                }
            }

            // Auto-fit rows
            $sheet->getDefaultRowDimension()->setRowHeight(-1);
            $sheet->getRowDimension(1)->setRowHeight(20);
        }
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 8,  // No
            'B' => 15, // Nim
            'C' => 20, // Nama
            'D' => 15, // Status Lulus
            'E' => 15, // Tahun Akademik
        ];
    }
}
