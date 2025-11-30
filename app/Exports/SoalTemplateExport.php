<?php

namespace App\Exports;

use App\Models\Ujian;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;

class SoalTemplateExport implements FromView, WithTitle
{
    protected $ujian;

    public function __construct(Ujian $ujian)
    {
        $this->ujian = $ujian;
    }

    public function view(): View
    {
        return view('exports.soal_template', [
            'ujian' => $this->ujian
        ]);
    }

    public function title(): string
    {
        return 'Template Soal';
    }
}