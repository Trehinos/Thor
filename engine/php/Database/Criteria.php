<?php

namespace Thor\Database;

class Criteria
{

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
    public static function compile(array $criteria, bool $andGlue = false): array
    {
        $sqlArray = [];
        $params = [];

        foreach ($criteria as $key => $value) {
            if (in_array($keyword = strtolower($key), ['and', 'or'])) {
                if ($keyword === 'and') {
                    ['sql' => $t_sql, 'params' => $t_params] = self::compile($value, true);
                } else {
                    ['sql' => $t_sql, 'params' => $t_params] = self::compile($value, false);
                }
                $sqlArray[] = "($t_sql)";
                $params += $t_params;
            } else {
                if (is_array($value)) {
                    $params = $params + $value;
                    $sqlArray[] = '"' . $key . '" IN (' .
                        implode(',', array_fill(0, count($value), '?')) .
                        ')';
                } else {
                    ['op' => $op, 'value' => $value] = self::parseValue($value);

                    $params += [$value];
                    $sqlArray[] = "$key $op ?";
                }
            }
        }

        return [
            'sql' => implode($andGlue ? ' AND ' : ' OR ', $sqlArray),
            'params' => $params
        ];
    }

    private static function parseValue(string $value): array
    {
        $op = '=';
        switch ($first = substr($value, 0, 1)) {
            case '>':
            case '<':
                $op = $first;
                $value = substr($value, 1);
        }
        switch ($twoFirsts = substr($value, 0, 2)) {
            case '!=':
            case '<>':
                $op = '<>';
                $value = substr($value, 2);
                break;

            case '>=':
            case '<=':
                $op = $twoFirsts;
                $value = substr($value, 2);
        }

        return [
            'op' => $op,
            'value' => $value
        ];
    }

}
