<?php

namespace Thor\Tools\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Cell\Cell;

/**
 *
 */

/**
 *
 */
interface StyleApplier
{

    /**
     * @param Cell $cell
     *
     * @return void
     */
    public function apply(Cell $cell): void;

}
