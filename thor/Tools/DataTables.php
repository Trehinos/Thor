<?php

namespace Thor\Tools;

use Exception;
use Thor\Web\Node;
use Thor\Web\Html;
use ReflectionException;
use JetBrains\PhpStorm\ArrayShape;
use Thor\Database\PdoExtension\PdoHandler;
use DataTables\{Editor, Database, Editor\Field};
use Thor\Database\PdoTable\Attributes\{PdoTable, PdoColumn, PdoAttributesReader};

/**
 * Bridge for DataTables and PdoTable.
 *
 * @template T
 */
final class DataTables
{

    #[ArrayShape(['table' => PdoTable::class, 'columns' => 'array', 'indexes' => 'array', 'foreign_keys' => 'array'])]
    private array $attributes;

    /**
     * @param class-string<T> $className
     *
     * @throws ReflectionException
     */
    public function __construct(
        private string $className,
        private PdoHandler $handler,
        private array $columns = [],
        private array $formatters = []
    ) {
        $this->attributes = PdoAttributesReader::pdoTableInformation($this->className);
    }

    /**
     * @throws Exception
     */
    public function process(array $toProcess): string
    {
        $tableName = $this->attributes['table']->getTableName();
        $primaries = $this->attributes['table']->getPrimaryKeys();

        $noFormatter = fn($value) => $value;

        $editor = new Editor($this->getDatabase(), $tableName, $primaries);
        $editor->field(
            array_map(
                fn(PdoColumn $column) => (new Field($column->getName()))
                    ->setFormatter($this->formatters[$column->getName()]['set'] ?? $noFormatter)
                    ->getFormatter($this->formatters[$column->getName()]['get'] ?? $noFormatter),
                $this->getColumns()
            )
        );

        return $editor->process($toProcess)->json(false);
    }

    /**
     * @throws Exception
     */
    public function getDatabase(): Database
    {
        return new Database(
            [
                'type' => ucfirst($this->handler->getDriverName()),
                'pdo'  => $this->handler->getPdo(),
            ]
        );
    }

    public function getColumns(): array
    {
        return array_values(
            array_filter(
                $this->attributes['columns'],
                fn(PdoColumn $column) => empty($this->columns) || in_array($column->getName(), $this->columns)
            )
        );
    }

    #[ArrayShape(['table' => Node::class, 'data' => 'string'])]
    public function getDataTable(array $labels = []): array
    {
        return [
            'table' => Html::node(
                'table',
                [
                    'id' => $this->className . '-dt',
                    'class' => 'table table-bordered w-100',
                ],
                [
                    Html::node(
                        'thead',
                        [],
                        [
                            Html::node(
                                'tr',
                                [],
                                array_map(
                                    fn(PdoColumn $column) => Html::node(
                                        'th',
                                        ['class' => 'bg-dark text-light'],
                                        [
                                            $labels[$column->getName()] ?? $column->getName(),
                                        ]
                                    ),
                                    $this->getColumns()
                                )
                            ),
                        ]
                    ),
                    Html::node('tbody', content: ['']),
                ]
            ),
            'data'  => json_encode(
                array_map(
                    fn(PdoColumn $column) => ['data' => $column->getName()],
                    $this->getColumns()
                )
            ),
        ];
    }

}
