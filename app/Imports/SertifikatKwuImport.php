<?php

namespace App\Imports;

use App\Models\SertifikatKwu;
use App\Models\ProgramStudi;
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
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class SertifikatKwuImport implements WithMultipleSheets
{
    use Importable;
    
    /**
     * @var array Error messages
     */
    protected $errors = [];
    
    /**
     * @var int Count of successfully imported records
     */
    protected $importedCount = 0;
    
    /**
     * @var SertifikatKwuSheetImport
     */
    protected $sheetImport;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sheetImport = new SertifikatKwuSheetImport();
    }
    
    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            0 => $this->sheetImport,
        ];
    }
    
    /**
     * Get error messages
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->sheetImport->getErrors();
    }
    
    /**
     * Get count of imported records
     *
     * @return int
     */
    public function getImportedCount(): int
    {
        return $this->sheetImport->getImportedCount();
    }
    

}