<?php

namespace App\Exports;

use App\Models\ProgramStudi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class SertifikatKwuTemplateExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths, WithMapping
{
    /**
     * @var array Program studi data
     */
    protected $programStudis = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Load program studi data
        $this->programStudis = ProgramStudi::pluck('nama_prodi')->toArray();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Get valid program studi names for examples
        $prodiNames = $this->programStudis;
        $prodi1 = !empty($prodiNames) ? $prodiNames[0] : 'Teknik Informatika';
        $prodi2 = !empty($prodiNames) && count($prodiNames) > 1 ? $prodiNames[1] : 'Sistem Informasi';

        // Contoh data dengan nama program studi yang valid
        // CATATAN: Ini hanya contoh, silakan ganti dengan data yang sesuai
        return new Collection([
            [
                'SK001',       // no_sertifikat - Format: SKxxx (wajib diisi, unik)
                '2023-01-01',  // tgl_sertifikat - Format: YYYY-MM-DD (wajib diisi)
                '123456789',   // nim - NIM mahasiswa (wajib diisi, maks 9 karakter)
                'Nama Mahasiswa', // nama - Nama lengkap mahasiswa (wajib diisi)
                $prodi1,       // program_studi - Harus sesuai dengan daftar di sheet kedua (wajib diisi)
                'Ganjil',      // semester - Harus 'Ganjil' atau 'Genap' (wajib diisi)
                '2023/2024',   // tahun - Format: YYYY/YYYY (wajib diisi)
                'Lulus dengan baik', // keterangan - Informasi tambahan (opsional)
            ],
            [
                'SK002',       // no_sertifikat - Format: SKxxx (wajib diisi, unik)
                '2023-02-15',  // tgl_sertifikat - Format: YYYY-MM-DD (wajib diisi)
                '987654321',   // nim - NIM mahasiswa (wajib diisi, maks 9 karakter)
                'Nama Mahasiswa Lain', // nama - Nama lengkap mahasiswa (wajib diisi)
                $prodi2,       // program_studi - Harus sesuai dengan daftar di sheet kedua (wajib diisi)
                'Genap',       // semester - Harus 'Ganjil' atau 'Genap' (wajib diisi)
                '2023/2024',   // tahun - Format: YYYY/YYYY (wajib diisi)
                'Lulus',       // keterangan - Informasi tambahan (opsional)
            ],
        ]);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No Sertifikat', // Wajib diisi, maksimal 10 karakter
            'Tanggal Sertifikat', // Wajib diisi, format: YYYY-MM-DD
            'NIM', // Wajib diisi, maksimal 9 karakter
            'Nama', // Wajib diisi
            'Program Studi', // Wajib diisi, harus sesuai dengan daftar di sheet kedua
            'Semester', // Wajib diisi, harus 'Ganjil' atau 'Genap'
            'Tahun', // Wajib diisi, format: 2023/2024
            'Keterangan', // Opsional
        ];
    }
    
    /**
     * Map data to standardized format
     *
     * @param array $row
     * @return array
     */
    public function map($row): array
    {
        return [
            'no_sertifikat' => $row[0],
            'tgl_sertifikat' => $row[1],
            'nim' => $row[2],
            'nama' => $row[3],
            'program_studi' => $row[4],
            'semester' => $row[5],
            'tahun' => $row[6],
            'keterangan' => $row[7],
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Data Sertifikat KWU';
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Tambahkan komentar pada baris pertama untuk panduan pengisian
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A1:H1')->getFont()->setSize(12);
        $sheet->getStyle('A1:H1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A1:H1')->getFill()->getStartColor()->setARGB('FFD9EAD3'); // Light green background
        
        // Tambahkan komentar pada sel untuk panduan
        $sheet->getComment('A1')->getText()->createTextRun('Nomor sertifikat harus unik dan tidak boleh kosong.');
        $sheet->getComment('B1')->getText()->createTextRun('Format tanggal: YYYY-MM-DD (contoh: 2023-01-01).');
        $sheet->getComment('E1')->getText()->createTextRun('Program studi harus sesuai dengan daftar di sheet kedua.');
        $sheet->getComment('F1')->getText()->createTextRun('Semester harus berisi "Ganjil" atau "Genap".');
        
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15, // no_sertifikat
            'B' => 15, // tgl_sertifikat
            'C' => 15, // nim
            'D' => 30, // nama
            'E' => 25, // program_studi
            'F' => 12, // semester
            'G' => 15, // tahun
            'H' => 30, // keterangan
        ];
    }
}

class ProgramStudiListExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Get all program studi
        $programStudis = ProgramStudi::select('nama_prodi')->orderBy('nama_prodi')->get();
        
        return $programStudis;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Daftar Program Studi',
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Daftar Program Studi';
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Tambahkan komentar pada baris pertama untuk panduan pengisian
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setSize(12);
        $sheet->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A1')->getFill()->getStartColor()->setARGB('FFFCE5CD'); // Light orange background
        
        // Tambahkan komentar pada sel untuk panduan
        $sheet->getComment('A1')->getText()->createTextRun('Gunakan nama program studi yang terdaftar di sini untuk mengisi kolom Program Studi pada sheet pertama.');
        
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 40,
        ];
    }
}