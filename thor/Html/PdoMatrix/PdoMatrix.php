<?php

/**
 * @package          Trehinos/Thor/Html
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Html\PdoMatrix;

use Thor\Http\Request\Request;
use Thor\Database\PdoExtension\PdoRequester;
use Thor\Html\{HtmlTag, Form\TextType, Form\InputType};
use Thor\Database\PdoTable\{Criteria,
    CrudHelper,
    PdoRowInterface,
    Attributes\PdoColumn,
    Attributes\PdoAttributesReader
};
use Thor\Http\Request\ServerRequestInterface;

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
     * @param MatrixColumn[] $columns
     *
     * @return string
     */
    public function getTableHtmlFromRequest(ServerRequestInterface $request, array $columns): string
    {
        $search = [];
        $queryParams = $request->getQueryParams();
        foreach ($columns as $columnName => $c) {
            $querySearch = $queryParams["search_$columnName"] ?? null;
            if (null !== $querySearch && '' !== $querySearch) {
                $search[$columnName] = $querySearch;
            }
        }

        return $this->generateTableTag(
            $this->crudHelper->readMultipleBy(new Criteria($search, Criteria::GLUE_OR)),
            $columns
        )->toHtml();
    }

    /**
     * @param PdoRowInterface[] $rows
     * @param MatrixColumn[]    $columns
     *
     * @return HtmlTag
     */
    public function generateTableTag(array $rows, array $columns): HtmlTag
    {
        $thead = HtmlTag::tag('thead', ['style' => 'background: #246; color: #fff']);
        $this->createHeadLine($thead, $columns);
        $this->createHeadLine($thead, $columns, true);

        $tbody = HtmlTag::tag('tbody');
        foreach ($rows as $row) {
            $this->createBodyLine($tbody, $row, $columns);
        }

        return HtmlTag::tag(
            'table',
            ['class' => 'table-pdo-matrix table table-bordered table-sm table-hover'],
            [$thead, $tbody]
        );
    }

    /**
     * @param HtmlTag        $thead
     * @param MatrixColumn[] $columns
     * @param bool           $withSearchTag
     */
    protected function createHeadLine(HtmlTag $thead, array $columns, bool $withSearchTag = false): void
    {
        $thead_tr = new HtmlTag('tr', false);
        $th = new HtmlTag('th', false);
        $th->setContent('');
        $thead_tr->addChild($th);
        $th = new HtmlTag('th', false);
        if ($withSearchTag) {
            $th->addChild($this->searchTag('primary', 'Primary'));
        } else {
            $th->setContent('Primary');
        }
        $thead_tr->addChild($th);
        foreach ($columns as $columnName => $column) {
            $th = new HtmlTag('th', false);
            if ($withSearchTag) {
                $th->addChild($this->searchTag($columnName, $column->label));
            } else {
                $th->setContent($column->label);
            }
            $thead_tr->addChild($th);
        }
        $thead_tr->addChild(new HtmlTag('th', false));
        $thead->addChild($thead_tr);
    }

    private function searchTag(string $columnName, string $label): HtmlTag
    {
        $tag = new TextType(htmlClass: 'form-control form-control-sm');
        $tag->setAttr('name', "search_$columnName");
        $tag->setAttr('placeholder', "$label search string");
        return $tag;
    }

    /**
     * @param HtmlTag         $tbody
     * @param PdoRowInterface $row
     * @param MatrixColumn[]  $columns
     */
    protected function createBodyLine(HtmlTag $tbody, PdoRowInterface $row, array $columns): void
    {
        $tag = new HtmlTag('tr', false);
        $pdoArray = $row->toPdoArray();

        $td = new HtmlTag('td', false, ['class' => 'text-center']);
        $checkbox = new InputType('checkbox', htmlClass: 'form-check-input');
        $checkbox->setAttr('name', 'select_' . $row->getPrimaryString());
        $checkbox->setAttr('style', 'height: 1.5em;');
        $formCheck = HtmlTag::div(
            ['class' => 'form-check'],
            [$checkbox]
        );
        $td->addChild($formCheck);
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
        $tdAction = HtmlTag::tag(
                      'td',
            children: [
                          HtmlTag::button(HtmlTag::icon('edit'), '', ['class' => 'btn btn-sm btn-primary']),
                          HtmlTag::button(HtmlTag::icon('trash'), '', ['class' => 'btn btn-sm btn-danger']),
                      ]
        );
        $tag->addChild($tdAction);
        $tbody->addChild($tag);
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


}
