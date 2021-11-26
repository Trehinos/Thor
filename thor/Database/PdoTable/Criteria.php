<?php

/**
 * @package          Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Database\PdoTable;

use JetBrains\PhpStorm\ArrayShape;

final class Criteria
{
    public const GLUE_AND = 'AND';
    public const GLUE_OR = 'OR';

    private ?string $sql = null;
    private ?array $params = null;

    public function __construct(private array $criteria = [], private string $glue = self::GLUE_AND)
    {
    }

    /**
     * @param Criteria $criteria
     *
     * @return string SQL with 'WHERE' if there is any criteria or ''.
     */
    public static function getWhere(Criteria $criteria): string
    {
        return (($t_sql = $criteria->getSql()) === '') ? '' : "WHERE $t_sql";
    }

    public function getSql(): string
    {
        if ($this->sql === null) {
            $this->make();
        }

        return $this->sql;
    }

    private function make(): void
    {
        ['sql' => $this->sql, 'params' => $this->params] = self::compile($this->criteria, $this->glue);
    }

    /**
     * @param array  $criteria
     *                      'fieldName' =>  ''  -> DIRECT VALUE
     *                      'fieldName' => []  -> IN
     *                      'and' => [[], []]
     *                      'or' => [[], []]
     *
     * @param string $glue
     *
     * @return array
     */
    #[ArrayShape(['sql' => "string", 'params' => "array"])]
    public static function compile(
        array $criteria,
        string $glue = self::GLUE_AND
    ): array {
        $sqlArray = [];
        $params = [];

        foreach ($criteria as $key => $value) {
            if (in_array($keyword = strtoupper($key), [self::GLUE_AND, self::GLUE_OR])) {
                ['sql' => $t_sql, 'params' => $t_params] = self::compile($value, $keyword);
                $sqlArray[] = "($t_sql)";
                $params = array_merge($params, $t_params);
            } elseif (is_array($value)) {
                $params = array_merge($params, $value);
                $marks = implode(',', array_fill(0, count($value), '?'));
                $sqlArray[] = '"' . $key . '" IN (' . $marks . ')';
            } else {
                ['op' => $op, 'value' => $value] = self::parseValue($value);
                if ($value === null) {
                    $mark = 'NULL';
                } else {
                    $mark = '?';
                    $params[] = $value;
                }
                $sqlArray[] = "$key $op $mark";
            }
        }

        return [
            'sql'    => implode(" $glue ", $sqlArray),
            'params' => $params,
        ];
    }

    #[ArrayShape(['op' => "string", 'value' => "false|null|string"])]
    private static function parseValue(
        ?string $value
    ): array {
        $op = '=';
        switch ($twoFirsts = substr($value, 0, 2)) {
            case '!=':
            case '<>':
            case '>=':
            case '<=':
                $op = $twoFirsts;
                $value = substr($value, 2);
                break;

            case '%*':
                $op = 'LIKE';
                $value = '%' . substr($value, 2);
                break;

            case '*%':
                $op = 'LIKE';
                $value = substr($value, 2) . '%';
                break;

            case '!%';
                $op = 'NOT LIKE';
                $value = '%' . substr($value, 2) . '%';
                break;

            default:
                switch ($first = substr($value, 0, 1)) {
                    case '=':
                    case '>':
                    case '<':
                        $op = $first;
                        $value = substr($value, 1);
                        break;

                    case '%':
                        $op = 'LIKE';
                        $value = '%' . substr($value, 1) . '%';
                        break;

                    case '!':

                    default:
                        if ($value === null) {
                            $op = 'IS';
                        } elseif ($value === '!') {
                            $op = 'IS NOT';
                            $value = null;
                            break;
                        }
                }
        }

        return [
            'op'    => $op,
            'value' => $value,
        ];
    }

    public function getParams(): array
    {
        if ($this->params === null) {
            $this->make();
        }

        return $this->params;
    }

}
