<?php

namespace App\Exports;

use App\Models\ProgramStudi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class MahasiswaExportTemplate implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Return empty collection for template
        return new Collection();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'NIM',
            'Nama',
            'Program Studi',
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Template Import Mahasiswa';
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Style for header row
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
        ]);

        // Add program studi list in a separate sheet
        $sheet->getParent()->createSheet()->setTitle('Daftar Program Studi');
        $prodiSheet = $sheet->getParent()->getSheetByName('Daftar Program Studi');
        
        // Add header for program studi list
        $prodiSheet->setCellValue('A1', 'Nama Program Studi');
        $prodiSheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
        ]);
        
        // Add program studi list
        $programStudis = ProgramStudi::orderBy('nama_prodi')->pluck('nama_prodi')->toArray();
        $row = 2;
        foreach ($programStudis as $prodi) {
            $prodiSheet->setCellValue('A' . $row, $prodi);
            $row++;
        }
        
        // Auto size columns for program studi sheet
        $prodiSheet->getColumnDimension('A')->setAutoSize(true);
        
        // Add data validation for program studi column
        $dataValidation = $sheet->getCell('C2')->getDataValidation();
        $dataValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $dataValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $dataValidation->setAllowBlank(false);
        $dataValidation->setShowInputMessage(true);
        $dataValidation->setShowErrorMessage(true);
        $dataValidation->setShowDropDown(true);
        $dataValidation->setErrorTitle('Input error');
        $dataValidation->setError('Nilai tidak ada dalam daftar.');
        $dataValidation->setPromptTitle('Pilih dari daftar');
        $dataValidation->setPrompt('Pilih program studi dari daftar yang tersedia.');
        $dataValidation->setFormula1('\''.'Daftar Program Studi'.'\''.'!$A$2:$A$'.($row-1));
        
        // Apply validation to the entire column
        for ($i = 2; $i <= 1000; $i++) {
            $sheet->getCell('C' . $i)->setDataValidation(clone $dataValidation);
        }
        
        // Add instructions
        $sheet->setCellValue('A1001', 'Petunjuk:');
        $sheet->setCellValue('A1002', '1. Jangan mengubah format template ini.');
        $sheet->setCellValue('A1003', '2. NIM harus unik dan tidak boleh kosong.');
        $sheet->setCellValue('A1004', '3. Nama tidak boleh kosong.');
        $sheet->setCellValue('A1005', '4. Program Studi harus dipilih dari daftar yang tersedia.');
        $sheet->setCellValue('A1006', '5. Lihat sheet "Daftar Program Studi" untuk daftar program studi yang tersedia.');
        
        $sheet->getStyle('A1001')->applyFromArray(['font' => ['bold' => true]]);
        
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}