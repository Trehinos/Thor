<?php

namespace Thor\Database\PdoTable;

use JetBrains\PhpStorm\ArrayShape;

/**
 * Describes a where SQL statement.
 *
 * @package          Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class Criteria
{
    public const GLUE_AND = 'AND';
    public const GLUE_OR = 'OR';

    private ?string $sql = null;
    private ?array $params = null;

    /**
     * **An example of criteria** :
     *
     * ['SOCIETY' => 'Xerox', 'OR' => ['EMPLOYEE_ID' => [1, 14, 999], 'MANAGER_ID' => 4]]
     *
     * @param array $criteria criteria in the form of :
     *
     *                          'fieldName' => value  -> DIRECT VALUE
     *
     *                          'fieldName' => []  -> IN
     *
     *                          'and' => [[], []]
     *
     *                          'or' => [[], []]
     *
     *                         value can be prefixed by one of these operators :
     *
     *                         =, !=, <>, >=, <=, >, <
     *
     *                         % (LIKE '%value%')
     *
     *                         !% (NOT LIKE '%value%')
     *
     *                         %* (LIKE '%value')
     *
     *                         *% (LIKE 'value%')
     *
     *                         if value === null, it will be 'IS NULL' in the SQL statement.
     *
     *                         if value === '!', it will be 'IS NOT NULL' in the SQL statement.
     *
     *                         if is_array(value), it will generate an IN statement.
     *
     *                         Write '=operator' to test the equality with one of these special operators.
     */
    public function __construct(private array $criteria = [], private string $glue = self::GLUE_AND)
    {
    }

    /**
     * Returns a WHERE statement for the specified Criteria.
     *
     * @return string SQL with 'WHERE' if there is any criteria or ''.
     */
    public static function getWhere(Criteria $criteria): string
    {
        return (($t_sql = $criteria->getSql()) === '') ? '' : "WHERE $t_sql";
    }

    /**
     * Get the SQL statement. Without "WHERE".
     */
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
     * Static method to generate SQL without a Criteria instantiation.
     *
     * @param array $criteria
     *                      'fieldName' =>  ''  -> DIRECT VALUE
     *
     *                      'fieldName' => []  -> IN
     *
     *                      'and' => [[], []]
     *
     *                      'or' => [[], []]
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

    /**
     * Returns the parameters of the SQL statement.
     */
    public function getParams(): array
    {
        if ($this->params === null) {
            $this->make();
        }

        return $this->params;
    }

}
