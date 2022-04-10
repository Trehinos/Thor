<?php

namespace Thor\Tools\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Writer\BaseWriter;
use PhpOffice\PhpSpreadsheet\Writer\Xls as XlsWriter;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\Reader\BaseReader;
use PhpOffice\PhpSpreadsheet\Reader\Xls as XlsReader;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;

enum FileType
{

    case XLS;
    case XLS2XLSX;
    case XLSX;
    case XLSX2XLS;

    public function getWriter(Builder $builder): BaseWriter
    {
        return match ($this) {
            self::XLS, self::XLSX2XLS  => new XlsWriter($builder->spreadsheet()),
            self::XLSX => new XlsxWriter($builder->spreadsheet())
        };
    }

    public function getReader(): BaseReader
    {
        return match ($this) {
            self::XLS, self::XLS2XLSX  => new XlsReader(),
            self::XLSX, self::XLSX2XLS => new XlsxReader()
        };
    }

}
