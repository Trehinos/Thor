<?php

namespace Thor\Tools\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;

final class Builder
{

    /**
     * @param Spreadsheet|null $spreadsheet
     * @param FileType         $type
     * @param StyleCollection  $styles
     */
    public function __construct(
        private ?Spreadsheet $spreadsheet = null,
        public readonly FileType $type = FileType::XLSX,
        public readonly StyleCollection $styles = new StyleCollection()
    ) {
        $this->spreadsheet ??= new Spreadsheet();
    }

    /**
     * @return Spreadsheet
     */
    public function spreadsheet(): Spreadsheet
    {
        return $this->spreadsheet;
    }

    /**
     * @param mixed       $value
     * @param string      $coordinates "Xy" or "x:y"
     * @param int|null    $sheetIndex
     * @param string|null $style
     *
     * @return Builder
     *
     * @throws PhpSpreadsheetException
     */
    public function cell(mixed $value, string $coordinates, ?int $sheetIndex = null, ?string $style = null): self
    {
        $worksheet = $sheetIndex === null
            ? $this->spreadsheet->getActiveSheet()
            : $this->spreadsheet->getSheet($sheetIndex);

        if (str_contains(':', $coordinates)) {
            [$column, $row] = explode(':', $coordinates);
            $cell = $worksheet->getCell([$column, $row]);
        } else {
            $cell = $worksheet->getCell($coordinates);
        }
        $cell->setValue($value);
        if ($style !== null) {
            $this->styles->getStyle($style)?->apply($cell);
        }

        return $this;
    }

    /**
     * @param string $filename
     *
     * @return void
     *
     * @throws WriterException
     */
    public function write(string $filename): void
    {
        $this->type->getWriter($this)->save($filename);
    }

    /**
     * @param string   $filename
     * @param FileType $type
     *
     * @return static
     *
     * @throws ReaderException
     */
    public static function read(string $filename, FileType $type): self
    {
        return new self($type->getReader()->load($filename), $type);
    }

}
