<?php

namespace Thor\Tools\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Borders;

final class Style
{

    public function __construct(
        public readonly ?Font $font = null,
        public readonly ?Fill $fill = null,
        public readonly ?Borders $borders = null,
    ) {
    }

    public function apply(Cell $cell): void
    {
        $style = $cell->getStyle();
        if (null !== $this->font) {
            $style->setFont($this->font);
        }
        if (null !== $this->fill) {
            $style->getFill()
                  ->setFillType($this->fill->getFillType())
                  ->setStartColor($this->fill->getStartColor())
                  ->setEndColor($this->fill->getEndColor());
        }
        if (null !== $this->borders) {
            $style->getBorders()->applyFromArray($this->borders->getAllBorders()->exportArray());
        }
    }

}
