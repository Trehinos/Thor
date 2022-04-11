<?php

namespace Thor\Tools\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

final class Builder
{

    public function __construct(
        private ?Spreadsheet $spreadsheet = null,
        public readonly FileType $type = FileType::XLSX,
        public readonly StyleCollection $styles = new StyleCollection()
    ) {
        $this->spreadsheet ??= new Spreadsheet();
    }

    public function spreadsheet(): Spreadsheet
    {
        return $this->spreadsheet;
    }

    /**
     * @throws PhpSpreadsheetException
     */
    public function cell(mixed $value, string $coordinates, ?int $sheetIndex = null, ?string $style = null): self
    {
        $worksheet = $sheetIndex === null
            ? $this->spreadsheet->getActiveSheet()
            : $this->spreadsheet->getSheet($sheetIndex);

        $cell = $worksheet->getCell($coordinates);
        $cell->setValue($value);
        if ($style !== null) {
            $this->styles->getStyle($style)?->apply($cell);
        }

        return $this;
    }

    public function write(string $filename): void
    {
        $this->type->getWriter($this)->save($filename);
    }

    public static function read(string $filename, FileType $type): self
    {
        return new self($type->getReader()->load($filename), $type);
    }

}
