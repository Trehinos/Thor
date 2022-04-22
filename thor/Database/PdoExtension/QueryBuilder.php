<?php

namespace Thor\Database\PdoExtension;

use Thor\Database\PdoTable\Criteria;

final class QueryBuilder
{

    private array $selects = [];
    private string $table = '';
    private array $joins = [];
    private ?Criteria $criteria = null;

    public function __construct(private PdoRequester $requester)
    {
    }

    public function build(): string
    {
        $select = 'SELECT ' . (
            empty($this->selects)
                ? '*'
                : implode(', ', $this->selects)
            );
        $from = "FROM {$this->table}";
        if ($this->criteria !== null) {
            $where = Criteria::getWhere($this->criteria);
        } else {
            $where = '';
        }
        return "$select $from $where";
    }

    /**
     * @param string|array ...$columns
     *
     * @return $this
     */
    public function select(string|array ...$columns): self
    {
        foreach ($columns as $column) {
            if (is_array($column)) {
                $this->selects[] = "{$column[0]} AS {$column[1]}";
            } else {
                $this->selects[] = $column;
            }
        }

        return $this;
    }

    public function from(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    public function where(Criteria $criteria): self
    {
        if ($this->criteria === null) {
            $this->criteria = $criteria;
        } else {
            $this->criteria = new Criteria(array_merge($this->criteria->criteria, $criteria->criteria));
        }

        return $this;
    }

}
