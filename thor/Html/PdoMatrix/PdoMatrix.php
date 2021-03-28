<?php

namespace Thor\Html\PdoMatrix;

use Thor\Html\HtmlTag;
use Thor\Http\Request;
use Thor\Database\PdoTable\Criteria;
use Thor\Database\PdoTable\CrudHelper;
use Thor\Database\PdoTable\PdoRowInterface;

/**
 * Class PdoMatrix
 *      Request <-> CrudHelper(PdoRow) <-> Form
 *
 * Request -> select -> html table
 * Request -> create -> DB
 * Request -> update -> DB
 * Request -> delete -> DB
 *
 * @package Thor\Html\PdoMatrix
 */
final class PdoMatrix
{

    public function __construct(
        private CrudHelper $crudHelper,
        private array $columns,
        private Request $request,
    ) {
    }

    public function getTableHtml(Request $request): string
    {
        $search = [];
        foreach ($this->columns as $columnName => $c) {
            $querySearch = $this->request->queryGet("search_$columnName");
            if (null !== $querySearch) {
                $search[$columnName] = $querySearch;
            }
        }

        return $this->generateTableTag($this->crudHelper->readMultipleBy(new Criteria($search)))->toHtml();
    }

    private function generateTableTag(array $rows): HtmlTag
    {
        $table = new HtmlTag('table', false, ['class' => 'table']);

        $thead = new HtmlTag('thead', false);
        /**
         * @var MatrixColumn $column
         */
        foreach ($this->columns as $columnName => $column) {
            $th = new HtmlTag('th', false);
            $th->setContent($column->label);
            $thead->addChild($th);
        }
        $table->addChild($thead);

        $tbody = new HtmlTag('tbody', false);
        foreach ($rows as $row) {
            $tbody->addChild($this->trFromRow($row));
        }
        $table->addChild($tbody);

        return $table;
    }

    private function trFromRow(PdoRowInterface $row): HtmlTag
    {
        $tag = new HtmlTag('tr', false);
        $pdoArray = $row->toPdoArray();
        /**
         * @var MatrixColumn $column
         */
        foreach ($this->columns as $columnName => $column) {
            $td = new HtmlTag('td', false);
            $td->setContent($column->fromDb($pdoArray[$columnName]));
            $tag->addChild($td);
        }
        return $tag;
    }

}
