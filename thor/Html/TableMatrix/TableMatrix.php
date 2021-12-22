<?php

/**
 * @package          Thor/Html
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Html\TableMatrix;

use Thor\Factories\Html;
use Thor\Database\PdoExtension\PdoRequester;
use Thor\Http\Request\ServerRequestInterface;
use Thor\Html\{Node, TextNode, Form\Field\TextField, Form\Field\InputField};
use Thor\Database\PdoTable\{Criteria,
    CrudHelper,
    PdoRowInterface,
    Attributes\PdoColumn,
    Attributes\PdoAttributesReader
};

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
final class TableMatrix
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
     * @param ServerRequestInterface $request
     * @param MatrixColumn[]         $columns
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
        )->getHtml();
    }

    /**
     * @param PdoRowInterface[] $rows
     * @param MatrixColumn[]    $columns
     *
     * @return Node
     */
    public function generateTableTag(array $rows, array $columns): Node
    {
        $thead = Html::node('thead', ['style' => 'background: #246; color: #fff']);
        $this->createHeadLine($thead, $columns);
        $this->createHeadLine($thead, $columns, true);

        $tbody = Html::node('tbody');
        foreach ($rows as $row) {
            $this->createBodyLine($tbody, $row, $columns);
        }

        return Html::node(
            'table',
            ['class' => 'table-pdo-matrix table table-bordered table-sm table-hover'],
            [$thead, $tbody]
        );
    }

    /**
     * @param Node           $thead
     * @param MatrixColumn[] $columns
     * @param bool           $withSearchTag
     */
    protected function createHeadLine(Node $thead, array $columns, bool $withSearchTag = false): void
    {
        $thead_tr = Html::node('tr', content: ['']);
        $th = Html::node('th', content: ['']);
        $thead_tr->addChild($th);
        $th = Html::node('th');
        if ($withSearchTag) {
            $th->addChild($this->searchTag('primary', 'Primary'));
        } else {
            $th->addChild(new TextNode('Primary'));
        }
        $thead_tr->addChild($th);
        foreach ($columns as $columnName => $column) {
            $th = Html::node('th');
            if ($withSearchTag) {
                $th->addChild($this->searchTag($columnName, $column->label));
            } else {
                $th->addChild(new TextNode($column->label));
            }
            $thead_tr->addChild($th);
        }
        $thead_tr->addChild(Html::node('th', content: ['']));
        $thead->addChild($thead_tr);
    }

    private function searchTag(string $columnName, string $label): Node
    {
        $tag = new TextField("search_$columnName", htmlClass: 'form-control form-control-sm');
        $tag->setAttribute('placeholder', "$label search string");
        return $tag;
    }

    /**
     * @param Node            $tbody
     * @param PdoRowInterface $row
     * @param MatrixColumn[]  $columns
     */
    protected function createBodyLine(Node $tbody, PdoRowInterface $row, array $columns): void
    {
        $tag = Html::node('tr');
        $pdoArray = $row->toPdoArray();

        $td = Html::node('td', ['class' => 'text-center']);
        $checkbox = new InputField('select_' . $row->getPrimaryString(), 'checkbox', htmlClass: 'form-check-input');
        $checkbox->setAttribute('style', 'height: 1.5em;');
        $formCheck = Html::div(
            ['class' => 'form-check'],
            [$checkbox]
        );
        $td->addChild($formCheck);
        $tag->addChild($td);
        $td = Html::node('td');
        $td->addChild(new TextNode($row->getPrimaryString()));
        $tag->addChild($td);
        foreach ($columns as $columnName => $column) {
            $attr = [];
            if ($this->isInteger($columnName)) {
                $attr['class'] = 'text-right';
            }
            $td = Html::node('td', $attr);
            $td->addChild(
                new TextNode(
                    $column->fromDb($pdoArray[$columnName] ?? '<em class="text-warning">invalid</em>')
                )
            );
            $tag->addChild($td);
        }
        $tdAction = Html::node(
            'td',
            [],
            [
                Html::button(Html::icon('edit'), '', ['class' => 'btn btn-sm btn-primary']),
                Html::button(Html::icon('trash'), '', ['class' => 'btn btn-sm btn-danger']),
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
