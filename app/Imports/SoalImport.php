<?php

namespace App\Imports;

use App\Models\Ujian;
use App\Models\Soal;
use App\Models\Pilihan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use DB;
use Illuminate\Support\Facades\Log;

class SoalImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    protected $id_ujian;
    protected $currentNomor = 1;

    public function __construct($id_ujian)
    {
        $this->id_ujian = $id_ujian;

        // Get the last question number for this exam
        $lastSoal = Soal::where('id_ujian', $id_ujian)
            ->orderBy('nomor_soal', 'desc')
            ->first();

        if ($lastSoal) {
            $this->currentNomor = $lastSoal->nomor_soal + 1;
        }
    }

    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                // Convert row to array if it's an object and ensure string values
                $rowData = $row instanceof \Illuminate\Support\Collection ? $row->toArray() : (array) $row;

                // Skip empty rows
                if (empty($rowData['pertanyaan'])) {
                    continue;
                }

                // Get the question text and convert to string
                $teksSoal = $this->convertToString($rowData['pertanyaan']);

                if (empty($teksSoal)) {
                    continue;
                }

                // **Manual Validation for this row**
                $this->validateRow($rowData, $index + 2); // +2 because Excel rows start from 1 and header is row 1

                // Check if question number already exists for this exam
                if (Soal::where('id_ujian', $this->id_ujian)
                    ->where('nomor_soal', $this->currentNomor)
                    ->exists()) {
                    // Skip this question and continue with next
                    $this->currentNomor++;
                    continue;
                }

                // Create the question with correct field names
                $soal = Soal::create([
                    'id_ujian' => $this->id_ujian,
                    'nomor_soal' => $this->currentNomor,
                    'teks_soal' => $teksSoal,
                    'tipe' => 'pilihan_ganda' // Use correct enum value from database
                ]);

                // Create options for multiple choice questions
                $options = [
                    'A' => $this->convertToString($rowData['pilihan_a']),
                    'B' => $this->convertToString($rowData['pilihan_b']),
                    'C' => $this->convertToString($rowData['pilihan_c']),
                    'D' => $this->convertToString($rowData['pilihan_d']),
                    'E' => $this->convertToString($rowData['pilihan_e'])
                ];

                $correctAnswer = $this->convertToString($rowData['jawaban_benar']);

                $optionIndex = 0;
                foreach ($options as $huruf => $text) {
                    if (!empty($text)) {
                        Pilihan::create([
                            'id_soal' => $soal->id,
                            'huruf_pilihan' => $huruf,
                            'teks_pilihan' => $text,
                            'is_benar' => strtoupper($huruf) === strtoupper(trim($correctAnswer))
                        ]);
                        $optionIndex++;
                    }
                }

                // Ensure at least 2 options were created
                if ($optionIndex < 2) {
                    throw new \Exception("Baris " . ($index + 2) . ": Minimal harus ada 2 pilihan jawaban");
                }

                $this->currentNomor++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Import Soal Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Validate individual row data
     */
    private function validateRow($rowData, $rowNumber)
    {
        $errors = [];

        // Validate pertanyaan (question text) - accept any data type, convert to string
        $pertanyaanValue = $this->convertToString($rowData['pertanyaan']);
        if (empty($pertanyaanValue)) {
            $errors[] = "Baris {$rowNumber}: Pertanyaan wajib diisi";
        } elseif (strlen(trim($pertanyaanValue)) < 3) {
            $errors[] = "Baris {$rowNumber}: Pertanyaan minimal 3 karakter";
        }

        // Validate required options (A-D) - accept any data type, convert to string
        $requiredOptions = ['pilihan_a', 'pilihan_b', 'pilihan_c', 'pilihan_d'];
        foreach ($requiredOptions as $option) {
            $optionValue = $this->convertToString($rowData[$option]);
            if (empty($optionValue)) {
                $errors[] = "Baris {$rowNumber}: " . ucfirst(str_replace('_', ' ', $option)) . " wajib diisi";
            }
        }

        // Validate optional option E if exists
        if (isset($rowData['pilihan_e']) && !empty($rowData['pilihan_e'])) {
            $optionEValue = $this->convertToString($rowData['pilihan_e']);
            // Accept any data type, no need to validate if empty since it's optional
        }

        // Validate jawaban_benar (correct answer) - accept any data type, convert to string
        $jawabanValue = $this->convertToString($rowData['jawaban_benar']);
        if (empty($jawabanValue)) {
            $errors[] = "Baris {$rowNumber}: Jawaban benar wajib diisi";
        } else {
            $jawabanClean = strtoupper(trim($jawabanValue));
            if (!in_array($jawabanClean, ['A', 'B', 'C', 'D', 'E'])) {
                $errors[] = "Baris {$rowNumber}: Jawaban benar harus berupa A, B, C, D, atau E";
            }
        }

        // Check if correct answer corresponds to existing option
        $correctAnswer = strtoupper(trim($jawabanValue));
        $optionsExist = [
            'A' => !empty($this->convertToString($rowData['pilihan_a'])),
            'B' => !empty($this->convertToString($rowData['pilihan_b'])),
            'C' => !empty($this->convertToString($rowData['pilihan_c'])),
            'D' => !empty($this->convertToString($rowData['pilihan_d'])),
            'E' => !empty($this->convertToString($rowData['pilihan_e']))
        ];

        if (!$optionsExist[$correctAnswer]) {
            $errors[] = "Baris {$rowNumber}: Jawaban benar '{$correctAnswer}' tidak ada dalam pilihan yang tersedia";
        }

        // If there are errors, throw exception with all error messages
        if (!empty($errors)) {
            throw new \Exception(implode('; ', $errors));
        }
    }

    /**
     * Convert any data type to string
     * Handles numbers, booleans, objects, arrays, etc.
     */
    private function convertToString($value)
    {
        // Handle null values
        if ($value === null || $value === '') {
            return '';
        }

        // Handle arrays
        if (is_array($value)) {
            return '';
        }

        // Handle objects with __toString method
        if (is_object($value) && method_exists($value, '__toString')) {
            return trim((string) $value);
        }

        // Handle booleans
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        // Handle numbers (int, float, double)
        if (is_numeric($value)) {
            return (string) $value;
        }

        // Handle other types - cast to string
        return trim((string) $value);
    }

    /**
     * Helper method to get string value from array/object
     */
    private function getStringValue($data, $key)
    {
        $value = $data[$key] ?? '';

        // Convert to string and trim
        if (is_object($value) && method_exists($value, '__toString')) {
            return trim((string) $value);
        } elseif (is_array($value)) {
            return '';
        } else {
            return trim((string) $value);
        }
    }

    public function rules(): array
    {
        return [
            'pertanyaan' => 'required|string',
            'pilihan_a' => 'required|string',
            'pilihan_b' => 'required|string',
            'pilihan_c' => 'required|string',
            'pilihan_d' => 'required|string',
            'pilihan_e' => 'nullable|string',
            'jawaban_benar' => 'required|string|in:A,B,C,D,E'
        ];
    }

    public function customValidationMessages()
    {
        return [
            'pertanyaan.required' => 'Pertanyaan wajib diisi',
            'pertanyaan.string' => 'Pertanyaan harus berupa teks',
            'pilihan_a.required' => 'Pilihan A wajib diisi',
            'pilihan_a.string' => 'Pilihan A harus berupa teks',
            'pilihan_b.required' => 'Pilihan B wajib diisi',
            'pilihan_b.string' => 'Pilihan B harus berupa teks',
            'pilihan_c.required' => 'Pilihan C wajib diisi',
            'pilihan_c.string' => 'Pilihan C harus berupa teks',
            'pilihan_d.required' => 'Pilihan D wajib diisi',
            'pilihan_d.string' => 'Pilihan D harus berupa teks',
            'pilihan_e.string' => 'Pilihan E harus berupa teks',
            'jawaban_benar.required' => 'Jawaban benar wajib diisi',
            'jawaban_benar.string' => 'Jawaban benar harus berupa teks',
            'jawaban_benar.in' => 'Jawaban benar harus berupa A, B, C, D, atau E'
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}