<?php

/**
 * @package          Trehinos/Thor/Html
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Html\PdoMatrix;

use Thor\Html\HtmlTag;
use Thor\Http\Request;
use Thor\Html\Form\TextType;
use Thor\Html\Form\InputType;
use Thor\Database\PdoTable\Criteria;
use Thor\Database\PdoTable\CrudHelper;
use Thor\Database\PdoTable\PdoRowInterface;
use Thor\Database\PdoExtension\PdoRequester;
use Thor\Database\PdoTable\Attributes\PdoColumn;
use Thor\Database\PdoTable\Attributes\PdoAttributesReader;

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

    private CrudHelper $crudHelper;
    private PdoAttributesReader $attributesReader;

    public function __construct(
        private string $className,
        private PdoRequester $requester
    ) {
        $this->crudHelper = new CrudHelper($this->className, $this->requester);
        $this->attributesReader = new PdoAttributesReader($this->className);
    }

    /**
     * @param Request        $request
     * @param MatrixColumn[] $columns
     *
     * @return string
     */
    public function getTableHtml(Request $request, array $columns): string
    {
        $search = [];
        foreach ($columns as $columnName => $c) {
            $querySearch = $request->queryGet("search_$columnName");
            if (null !== $querySearch && '' !== $querySearch) {
                $search[$columnName] = $querySearch;
            }
        }

        return $this->generateTableTag(
            $this->crudHelper->readMultipleBy(new Criteria($search)),
            $columns
        )->toHtml()
            ;
    }

    /**
     * @param PdoRowInterface[] $rows
     * @param MatrixColumn[]    $columns
     *
     * @return HtmlTag
     */
    public function generateTableTag(array $rows, array $columns): HtmlTag
    {
        $table = new HtmlTag('table', false, ['class' => 'table table-bordered table-sm table-hover']);

        $thead = new HtmlTag('thead', false, ['style' => 'background: #246; color: #fff']);

        $thead_tr = new HtmlTag('tr', false);
        $th = new HtmlTag('th', false);
        $th->setContent('');
        $thead_tr->addChild($th);
        $th = new HtmlTag('th', false);
        $th->setContent('Primary');
        $thead_tr->addChild($th);
        foreach ($columns as $column) {
            $th = new HtmlTag('th', false);
            $th->setContent($column->label);
            $thead_tr->addChild($th);
        }
        $thead_tr->addChild(new HtmlTag('th', false));
        $thead->addChild($thead_tr);

        $thead_tr = new HtmlTag('tr', false);
        $th = new HtmlTag('th', false);
        $th->setContent('');
        $thead_tr->addChild($th);
        $th = new HtmlTag('th', false);
        $th->addChild($this->searchTag('primary', 'Primary'));
        $thead_tr->addChild($th);
        foreach ($columns as $columnName => $column) {
            $th = new HtmlTag('th', false);
            $th->addChild($this->searchTag($columnName, $column->label));
            $thead_tr->addChild($th);
        }
        $thead->addChild($thead_tr);
        $table->addChild($thead);

        $tbody = new HtmlTag('tbody', false);
        foreach ($rows as $row) {
            $tbody->addChild($this->trFromRow($row, $columns));
        }
        $table->addChild($tbody);

        return $table;
    }

    private function searchTag(string $columnName, string $label): HtmlTag
    {
        $tag = new TextType(htmlClass: 'form-control form-control-sm');
        $tag->setAttr('name', "search_$columnName");
        $tag->setAttr('placeholder', "$label search string");
        return $tag;
    }

    /**
     * @param PdoRowInterface $row
     * @param MatrixColumn[]  $columns
     *
     * @return HtmlTag
     */
    private function trFromRow(PdoRowInterface $row, array $columns): HtmlTag
    {
        $tag = new HtmlTag('tr', false);
        $pdoArray = $row->toPdoArray();

        $td = new HtmlTag('td', false, ['class' => 'text-center']);
        $label = new HtmlTag('label', false, ['class' => 'checkbox', 'style' => 'padding: 0']);
        $textTag = new InputType('checkbox');
        $textTag->setAttr('name', 'select_' . $row->getPrimaryString());
        $textTag->setAttr('style', 'height: 1.5em;');
        $label->addChild($textTag);
        $td->addChild($label);
        $tag->addChild($td);
        $td = new HtmlTag('td', false);
        $td->setContent($row->getPrimaryString());
        $tag->addChild($td);
        foreach ($columns as $columnName => $column) {
            $attr = [];
            if ($this->isInteger($columnName)) {
                $attr['class'] = 'text-right';
            }
            $td = new HtmlTag('td', false, $attr);
            $td->setContent($column->fromDb($pdoArray[$columnName] ?? '<em class="text-warning">invalid</em>'));
            $tag->addChild($td);
        }
        $tdAction = new HtmlTag('td', false);
        $tdAction->addChild(
            $this->buttonTag(
                HtmlTag::icon('') . " ",
                ''
            )
        );
        $tag->addChild($tdAction);
        return $tag;
    }

    private function isInteger(string $columnName): ?bool
    {
        $columns = $this->attributesReader->getAttributes()['columns'];
        /** @var PdoColumn $column */
        foreach ($columns as $column) {
            if ($column->getName() === $columnName) {
                return $column->getPhpType() === 'integer';
            }
        }

        return null;
    }

    // TODO
    private function buttonTag(string $buttonLabel, string $action): HtmlTag
    {
        $tag = new HtmlTag('button', false, ['onclick' => $action]);
        return $tag;
    }

}
