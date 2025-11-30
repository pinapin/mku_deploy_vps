<?php

namespace App\Imports;

use App\Models\SertifikatKwu;
use App\Models\ProgramStudi;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class SertifikatKwuSheetImport extends DefaultValueBinder implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure,
    WithEvents,
    WithCustomValueBinder,
    WithMapping
{
    use Importable, SkipsFailures;

    /**
     * @var array Program studi data keyed by name
     */
    protected $programStudis = [];

    /**
     * @var array Error messages
     */
    protected $errors = [];

    /**
     * @var int Count of successfully imported records
     */
    protected $importedCount = 0;

    /**
     * @var array Existing certificate numbers in database
     */
    protected $existingCertificates = [];

    /**
     * @var array Certificate numbers in current import batch
     */
    protected $importBatchCertificates = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Load program studi data
        $this->programStudis = ProgramStudi::pluck('id', 'nama_prodi')->toArray();

        // Load existing certificate numbers
        $this->existingCertificates = SertifikatKwu::pluck('no_sertifikat')->toArray();
    }

    /**
     * Custom value binder to ensure all values are properly converted
     * but preserves numeric values for date handling
     *
     * @param Cell $cell
     * @param mixed $value
     * @return bool
     */
    public function bindValue(Cell $cell, $value)
    {
        // Keep original value for date cells to allow proper date conversion later
        if ($value !== null) {
            // Get the column letter
            $column = $cell->getColumn();
            $row = $cell->getRow();

            // For numeric values that might be dates, preserve the numeric type
            if (is_numeric($value)) {
                // Check if this might be a date value (Excel dates are typically large numbers)
                // Excel dates start from December 30, 1899 (value 1)
                if ($value > 1) {
                    try {
                        // Try to convert as Excel date to see if it's valid
                        $dateObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);

                        // If we're in a reasonable date range (e.g., not year 5000), it's likely a date
                        $year = (int)$dateObj->format('Y');
                        if ($year >= 1900 && $year <= 2100) {
                            // Let parent handle it to preserve the numeric value
                            return parent::bindValue($cell, $value);
                        }
                    } catch (\Exception $e) {
                        // Not a valid date, continue with string conversion
                    }
                }
            }

            // For other values, convert to string
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    /**
     * Define the heading row in the Excel file
     * 
     * @return int
     */
    public function headingRow(): int
    {
        return 1;
    }

    /**
     * Map Excel column headers to standardized names
     *
     * @param array $row
     * @return array
     */
    public function map($row): array
    {
        $result = [];

        // Define column mappings (original column name => normalized column name)
        $columnMappings = [
            'no sertifikat' => 'no_sertifikat',
            'no. sertifikat' => 'no_sertifikat',
            'nomor sertifikat' => 'no_sertifikat',
            'no_sertifikat' => 'no_sertifikat',
            'tgl sertifikat' => 'tgl_sertifikat',
            'tanggal sertifikat' => 'tgl_sertifikat',
            'tgl_sertifikat' => 'tgl_sertifikat',
            'nim' => 'nim',
            'nama' => 'nama',
            'program studi' => 'program_studi',
            'prodi' => 'program_studi',
            'program_studi' => 'program_studi',
            'semester' => 'semester',
            'tahun' => 'tahun',
            'keterangan' => 'keterangan',
        ];

        // Process each column in the row
        foreach ($row as $columnName => $value) {
            // Convert column name to lowercase and trim for more flexible comparison
            $lowerColumnName = trim(strtolower($columnName));

            // Check if this column has a mapping
            $normalizedColumnName = null;
            foreach ($columnMappings as $originalName => $normalizedName) {
                // Use more flexible comparison (trim and case-insensitive)
                if ($lowerColumnName === trim(strtolower($originalName))) {
                    $normalizedColumnName = $normalizedName;
                    break;
                }
            }

            // If no exact match found, try partial matching for common column names
            if (!$normalizedColumnName) {
                // Special handling for tanggal sertifikat variations
                if (strpos($lowerColumnName, 'tanggal') !== false && strpos($lowerColumnName, 'sertifikat') !== false) {
                    $normalizedColumnName = 'tgl_sertifikat';
                }
                // Special handling for tgl sertifikat variations
                else if (strpos($lowerColumnName, 'tgl') !== false && strpos($lowerColumnName, 'sertifikat') !== false) {
                    $normalizedColumnName = 'tgl_sertifikat';
                }
                // If still no mapping found, use the original column name
                else {
                    $normalizedColumnName = $columnName;
                }
            }

            // Add to normalized row and ensure string type for specific fields
            if (($normalizedColumnName === 'nim' || $normalizedColumnName === 'no_sertifikat') && $value !== null) {
                $result[$normalizedColumnName] = (string) $value;
            }
            // Convert Excel date to proper date format
            else if ($normalizedColumnName === 'tgl_sertifikat' && $value !== null) {

                // Try to convert numeric or string representation of Excel date
                try {
                    // If it's already a formatted date string in Y-m-d format
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                        $result[$normalizedColumnName] = $value;
                    }
                    // If it's a numeric value (Excel date)
                    else if (is_numeric($value)) {
                        $dateObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                        $result[$normalizedColumnName] = $dateObj->format('Y-m-d');
                    }
                    // If it's a date in another format, try to parse it
                    else {
                        // Try various common date formats
                        $dateObj = \DateTime::createFromFormat('d/m/Y', $value) ?:
                            \DateTime::createFromFormat('d-m-Y', $value) ?:
                            \DateTime::createFromFormat('Y/m/d', $value) ?:
                            \DateTime::createFromFormat('m/d/Y', $value) ?:
                            \DateTime::createFromFormat('j/n/Y', $value) ?:
                            \DateTime::createFromFormat('j-n-Y', $value) ?:
                            \DateTime::createFromFormat('d.m.Y', $value) ?:
                            \DateTime::createFromFormat('Y.m.d', $value) ?:
                            \DateTime::createFromFormat('j F Y', $value) ?:
                            \DateTime::createFromFormat('F j, Y', $value);

                        if ($dateObj) {
                            $result[$normalizedColumnName] = $dateObj->format('Y-m-d');
                        } else {
                            // If all parsing attempts fail, keep the original value
                            $result[$normalizedColumnName] = $value;
                        }
                    }
                } catch (\Exception $e) {
                    // Keep original if conversion fails
                    $result[$normalizedColumnName] = $value;
                }
            } else {
                $result[$normalizedColumnName] = $value;
            }
        }

        return $result;
    }

    /**
     * Register import events
     *
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {},
            AfterImport::class => function (AfterImport $event) {},
        ];
    }

    /**
     * Process each row from the Excel file
     *
     * @param array $row
     * @return \App\Models\SertifikatKwu|null
     */
    public function model(array $row)
    {
        // Skip completely empty rows
        if (
            empty($row['no_sertifikat']) && empty($row['nim']) && empty($row['nama']) && empty($row['program_studi']) &&
            empty($row['tgl_sertifikat']) && empty($row['semester']) && empty($row['tahun'])
        ) {
            return null;
        }

        // Normalize certificate number
        $certificateNumber = (string) trim($row['no_sertifikat'] ?? '');

        // Skip if certificate number is empty
        if (empty($certificateNumber)) {
            $this->errors[] = "No Sertifikat tidak boleh kosong.";
            return null;
        }

        // Check for duplicate in current import batch
        if (in_array($certificateNumber, $this->importBatchCertificates)) {
            $this->errors[] = "No Sertifikat '{$certificateNumber}' duplikat dalam file impor.";
            return null;
        }

        // Check for duplicate in database
        if (in_array($certificateNumber, $this->existingCertificates)) {
            $this->errors[] = "No Sertifikat '{$certificateNumber}' sudah digunakan.";
            return null;
        }

        // Add to batch certificates to prevent duplicates
        $this->importBatchCertificates[] = $certificateNumber;

        // Validate program studi field exists
        if (empty($row['program_studi'])) {
            $this->errors[] = "Program Studi tidak boleh kosong untuk No Sertifikat '{$certificateNumber}'.";
            return null;
        }

        // Find program studi ID
        $prodiId = null;
        $prodiName = trim($row['program_studi']);

        // Exact match
        $prodiId = $this->programStudis[$prodiName] ?? null;

        // Try partial match if exact match fails
        if (!$prodiId) {
            foreach ($this->programStudis as $name => $id) {
                if (stripos($name, $prodiName) !== false || stripos($prodiName, $name) !== false) {
                    $prodiId = $id;
                    break;
                }
            }
        }

        // Validate program studi exists in database
        if (empty($prodiId)) {
            $this->errors[] = "Program Studi '{$prodiName}' tidak ditemukan.";
            return null;
        }

        // Validate each required field individually for better error messages
        if (empty($row['nim'])) {
            $this->errors[] = "NIM tidak boleh kosong untuk No Sertifikat '{$certificateNumber}'.";
            return null;
        }

        if (empty($row['nama'])) {
            $this->errors[] = "Nama tidak boleh kosong untuk No Sertifikat '{$certificateNumber}'.";
            return null;
        }

        // Special handling for date validation to ensure it's not considered empty
        if (!isset($row['tgl_sertifikat']) || (is_string($row['tgl_sertifikat']) && trim($row['tgl_sertifikat']) === '')) {
            $this->errors[] = "Tanggal Sertifikat tidak boleh kosong untuk No Sertifikat '{$certificateNumber}'.";
            return null;
        }

        if (empty($row['semester'])) {
            $this->errors[] = "Semester tidak boleh kosong untuk No Sertifikat '{$certificateNumber}'.";
            return null;
        }

        if (empty($row['tahun'])) {
            $this->errors[] = "Tahun tidak boleh kosong untuk No Sertifikat '{$certificateNumber}'.";
            return null;
        }

        // Validate semester
        if (!in_array($row['semester'], ['Ganjil', 'Genap'])) {
            $this->errors[] = "Semester harus 'Ganjil' atau 'Genap' untuk No Sertifikat '{$certificateNumber}'.";
            return null;
        }

        // Increment imported count
        $this->importedCount++;

        // Ensure NIM is always a string
        $nim = (string) $row['nim'];

        // Create and return the model
        return new SertifikatKwu([
            'no_sertifikat' => (string) $certificateNumber,
            'tgl_sertifikat' => (string) $row['tgl_sertifikat'],
            'nim' => (string) $nim,
            'nama' => (string) $row['nama'],
            'prodi_id' => $prodiId,
            'semester' => (string) $row['semester'],
            'tahun' => (string) $row['tahun'],
            'keterangan' => $row['keterangan'] ? (string) $row['keterangan'] : null,
        ]);
    }

    /**
     * Define validation rules
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            // Use a more flexible date validation
            'tgl_sertifikat' => ['required', function ($attribute, $value, $fail) {
                // Skip validation if already validated in the model method
                if (empty($value)) {
                    $fail('Tanggal Sertifikat tidak boleh kosong');
                    return;
                }

                // Try to parse the date to validate it
                try {
                    if (is_string($value)) {
                        // Check if it's already in Y-m-d format
                        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                            return;
                        }

                        // Try to parse various date formats
                        $formats = ['d/m/Y', 'd-m-Y', 'Y/m/d', 'm/d/Y', 'j/n/Y', 'j-n-Y', 'd.m.Y', 'Y.m.d'];
                        foreach ($formats as $format) {
                            $date = \DateTime::createFromFormat($format, $value);
                            if ($date && $date->format($format) == $value) {
                                return;
                            }
                        }
                    }

                    // If it's a numeric value, it might be an Excel date
                    if (is_numeric($value)) {
                        return;
                    }

                    // If we get here, the date is invalid
                    $fail('Format Tanggal Sertifikat tidak valid');
                } catch (\Exception $e) {
                    $fail('Format Tanggal Sertifikat tidak valid: ' . $e->getMessage());
                }
            }],
            'no_sertifikat' => 'required|string|max:20',
            'nim' => 'required|string|max:9',
            'nama' => 'required|string|max:255',
            'program_studi' => 'required|string',
            'semester' => 'required|in:Ganjil,Genap',
            'tahun' => 'required|string|max:10',
            'keterangan' => 'nullable|string',
        ];
    }

    /**
     * Prepare the data for validation
     *
     * @param array $row
     * @param int $index
     * @return array
     */
    public function prepareForValidation(array $row, $index)
    {
        // Special handling for date field
        if (isset($row['tgl_sertifikat'])) {
            $dateValue = $row['tgl_sertifikat'];

            // If it's a numeric value, try to convert it as an Excel date
            if (is_numeric($dateValue)) {
                try {
                    $dateObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue);
                    $row['tgl_sertifikat'] = $dateObj->format('Y-m-d');
                } catch (\Exception $e) {
                    // Keep original if conversion fails
                    Log::debug("Failed to convert Excel date in prepareForValidation", [
                        'value' => $dateValue,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return $row;
    }

    /**
     * Define custom validation messages
     *
     * @return array
     */
    public function customValidationMessages(): array
    {
        return [
            'no_sertifikat.required' => 'No Sertifikat tidak boleh kosong',
            'no_sertifikat.max' => 'No Sertifikat maksimal 20 karakter',
            'tgl_sertifikat.required' => 'Tanggal Sertifikat tidak boleh kosong',
            'tgl_sertifikat.date' => 'Format Tanggal Sertifikat tidak valid',
            'nim.required' => 'NIM tidak boleh kosong',
            'nim.max' => 'NIM maksimal 9 karakter',
            'nama.required' => 'Nama tidak boleh kosong',
            'nama.max' => 'Nama maksimal 255 karakter',
            'program_studi.required' => 'Program Studi tidak boleh kosong',
            'semester.required' => 'Semester tidak boleh kosong',
            'semester.in' => 'Semester harus Ganjil atau Genap',
            'tahun.required' => 'Tahun tidak boleh kosong',
            'tahun.max' => 'Tahun maksimal 10 karakter',
        ];
    }

    /**
     * Get error messages
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get count of imported records
     *
     * @return int
     */
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    /**
     * Handle validation failures
     *
     * @param Failure[] $failures
     * @return void
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $row = $failure->row();
            $rowNumber = $row; // Adjust for heading row

            foreach ($failure->errors() as $error) {
                $attribute = $failure->attribute();
                $this->errors[] = "Baris {$rowNumber}: {$error}";
            }
        }
    }
}
