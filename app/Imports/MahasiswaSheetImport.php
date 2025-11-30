<?php

namespace App\Imports;

use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class MahasiswaSheetImport extends DefaultValueBinder implements 
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
     * @var array Existing NIM in database
     */
    protected $existingNims = [];
    
    /**
     * @var array NIMs in current import batch
     */
    protected $importBatchNims = [];
    
    /**
     * @var int Tahun Akademik ID
     */
    protected $tahunAkademikId;
    
    /**
     * Constructor
     */
    public function __construct($tahunAkademikId = null)
    {
        // Set tahun akademik ID
        $this->tahunAkademikId = $tahunAkademikId;
        
        // Load program studi data
        $this->programStudis = ProgramStudi::pluck('id', 'nama_prodi')->toArray();
        
        // Load existing NIMs
        $this->existingNims = Mahasiswa::pluck('nim')->toArray();
    }
    
    /**
     * Custom value binder to ensure all values are properly converted
     *
     * @param Cell $cell
     * @param mixed $value
     * @return bool
     */
    public function bindValue(Cell $cell, $value)
    {
        // Convert all values to strings except null values
        if ($value !== null) {
            // Ensure the value is explicitly converted to string
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
            'nim' => 'nim',
            'nama' => 'nama',
            'program studi' => 'program_studi',
            'prodi' => 'program_studi',
            'program_studi' => 'program_studi',
        ];
        
        // Process each column in the row
        foreach ($row as $columnName => $value) {
            // Convert column name to lowercase for case-insensitive comparison
            $lowerColumnName = strtolower($columnName);
            
            // Check if this column has a mapping
            $normalizedColumnName = null;
            foreach ($columnMappings as $originalName => $normalizedName) {
                if ($lowerColumnName === $originalName) {
                    $normalizedColumnName = $normalizedName;
                    break;
                }
            }
            
            // If no mapping found, use the original column name
            if (!$normalizedColumnName) {
                $normalizedColumnName = $columnName;
            }
            
            // Add to normalized row and ensure string type for specific fields
            if ($normalizedColumnName === 'nim' && $value !== null) {
                $result[$normalizedColumnName] = (string) $value;
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
            BeforeImport::class => function(BeforeImport $event) {
            },
            AfterImport::class => function(AfterImport $event) {
            },
        ];
    }
    
    /**
     * Process each row from the Excel file
     *
     * @param array $row
     * @return \App\Models\Mahasiswa|null
     */
    public function model(array $row)
    {
        // Skip completely empty rows
        if (empty($row['nim']) && empty($row['nama']) && empty($row['program_studi'])) {
            return null;
        }
        
        // Normalize NIM
        $nim = (string) trim($row['nim'] ?? '');
        
        // Skip if NIM is empty
        if (empty($nim)) {
            $this->errors[] = "NIM tidak boleh kosong.";
            return null;
        }
        
        // Check for duplicate in current import batch
        if (in_array($nim, $this->importBatchNims)) {
            $this->errors[] = "NIM '{$nim}' duplikat dalam file impor.";
            return null;
        }
        
        // Check for duplicate in database
        // if (in_array($nim, $this->existingNims)) {
        //     $this->errors[] = "NIM '{$nim}' sudah ada dalam database.";
        //     return null;
        // }
        
        // Add to batch NIMs to prevent duplicates
        $this->importBatchNims[] = $nim;
        
        // Validate program studi field exists
        if (empty($row['program_studi'])) {
            $this->errors[] = "Program Studi tidak boleh kosong untuk NIM '{$nim}'.";
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
        
        // Validate nama field
        if (empty($row['nama'])) {
            $this->errors[] = "Nama tidak boleh kosong untuk NIM '{$nim}'.";
            return null;
        }
        
        // Validate tahun akademik ID
        if (empty($this->tahunAkademikId)) {
            $tahunAkademik = TahunAkademik::getActive();
            if (!$tahunAkademik) {
                $this->errors[] = "Tidak ada tahun akademik aktif. Silakan pilih tahun akademik secara manual.";
                return null;
            }
            $this->tahunAkademikId = $tahunAkademik->id;
        }
        
        // Increment imported count
        $this->importedCount++;
        
        // Create and return the model
        return new Mahasiswa([
            'nim' => $nim,
            'nama' => (string) $row['nama'],
            'prodi_id' => $prodiId,
            'tahun_akademik_id' => $this->tahunAkademikId,
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
            'nim' => 'required|string|max:9',
            'nama' => 'required|string|max:255',
            'program_studi' => 'required|string',
        ];
    }
    
    /**
     * Define custom validation messages
     *
     * @return array
     */
    public function customValidationMessages(): array
    {
        return [
            'nim.required' => 'NIM tidak boleh kosong',
            'nim.max' => 'NIM maksimal 9 karakter',
            'nama.required' => 'Nama tidak boleh kosong',
            'nama.max' => 'Nama maksimal 255 karakter',
            'program_studi.required' => 'Program Studi tidak boleh kosong',
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