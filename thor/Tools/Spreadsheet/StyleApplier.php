<?php

namespace Thor\Tools\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Cell\Cell;

interface StyleApplier
{

    public function apply(Cell $cell): void;

}
