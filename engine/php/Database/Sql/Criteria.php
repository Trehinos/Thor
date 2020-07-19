<?php

namespace Thor\Database\Sql;

final class Criteria
{

    private array $criteria;
    private bool $andGlue;

    const GLUE_AND = true;
    const GLUE_OR = false;

    private ?string $sql = null;
    private ?array $params = null;

    public function __construct(array $criteria = [], bool $glue = self::GLUE_AND)
    {
        $this->criteria = $criteria;
        $this->andGlue = $andGlue;
    }

    /**
     * @param array $criteria
     *      'fieldName' =>  ''  -> DIRECT VALUE
     *                      []  -> IN
     *      'and' => [...]
     *      'or' => [...]
     *
     * @param bool $andGlue
     *
     * @return array ['sql' => ..., 'params' => ...]
     */
    public static function compile(array $criteria, bool $glue = self::GLUE_AND): array
    {
        $sqlArray = [];
        $params = [];

        foreach ($criteria as $key => $value) {
            if (in_array($keyword = strtolower($key), ['and', 'or'])) {
                if ($keyword === 'and') {
                    ['sql' => $t_sql, 'params' => $t_params] = self::compile($value, self::GLUE_AND);
                } else {
                    ['sql' => $t_sql, 'params' => $t_params] = self::compile($value, self::GLUE_OR);
                }
                $sqlArray[] = "($t_sql)";
                $params = array_merge($params, $t_params);
            } else {
                if (is_array($value)) {
                    $params = array_merge($params, $value);
                    $sqlArray[] = '"' . $key . '" IN (' .
                        implode(',', array_fill(0, count($value), '?')) .
                        ')';
                } else {
                    ['op' => $op, 'value' => $value] = self::parseValue($value);

                    $params[] = $value;
                    $sqlArray[] = "$key $op ?";
                }
            }
        }

        return [
            'sql' => implode($glue ? ' AND ' : ' OR ', $sqlArray),
            'params' => $params
        ];
    }

    public function getSql(): string
    {
        if ($this->sql === null) {
            $this->make();
        }

        return $this->sql;
    }

    public function getParams(): array
    {
        if ($this->params === null) {
            $this->make();
        }

        return $this->params;
    }

    private function make()
    {
        ['sql' => $this->sql, 'params' => $this->params] = self::compile($this->criteria, $this->andGlue);
    }

    private static function parseValue(string $value): array
    {
        $op = '=';
        switch ($twoFirsts = substr($value, 0, 2)) {
            case '!=':
            case '<>':
            case '>=':
            case '<=':
                $op = $twoFirsts;
                $value = substr($value, 2);
                break;

            default:
                switch ($first = substr($value, 0, 1)) {
                    case '=':
                    case '>':
                    case '<':
                        $op = $first;
                        $value = substr($value, 1);
                }
        }

        return [
            'op' => $op,
            'value' => $value
        ];
    }

}
