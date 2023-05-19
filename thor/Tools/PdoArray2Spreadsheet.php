<?php

namespace Thor\Tools;

use Thor\Tools\Spreadsheet\Builder;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Thor\Tools\Structures\Collection\Collection;
use Thor\Database\PdoTable\PdoTable\PdoRowInterface;

/**
 *
 */

/**
 *
 */
final class PdoArray2Spreadsheet
{

    const OPTION_NO_HEADERS = 0b0001;

    /**
     * @param \Thor\Tools\Structures\Collection\Collection<PdoRowInterface> $pdoArray
     * @param Builder|null                                                  $builder
     */
    public function __construct(private Collection $pdoArray, private ?Builder $builder = null)
    {
        $this->builder ??= new Builder();
    }

    /**
     * @return Spreadsheet
     */
    public function toXlsx(): Spreadsheet
    {
        $this->build();
        return $this->builder->spreadsheet();
    }

    /**
     * @param int $options Can take one of this parameters (combinable with |) :<br>
     * <ul>
     *      <li>OPTION_NO_HEADERS</li>
     * </ul>
     *
     * @return void
     */
    public function build(int $options = 0): void
    {
        if ($this->pdoArray->count() === 0) {
            return;
        }
        $headers = [];
        $arr = $this->pdoArray->toArray();
        if (!($options & self::OPTION_NO_HEADERS)) {
            $headers = array_keys($arr[0]);
        }
        foreach ($headers as $header) {
            // TODO
        }
        foreach ($arr as $rowIndex => $pdoRow) {
            // TODO
        }
    }

}
