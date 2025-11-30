<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SertifikatKwuExportTemplate implements WithMultipleSheets
{
    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            new SertifikatKwuTemplateExport(),
            new ProgramStudiListExport(),
        ];
    }
}