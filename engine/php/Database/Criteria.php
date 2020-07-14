<?php

namespace Thor\Database;

class Criteria
{

    public static function compile(array $criteria): array
    {
        $sqlArray = '';
        $params = [];

        foreach ($criteria as $lOp => $rOp) {
            $sql = '';
            if (in_array(strtolower($lOp), ['or', 'and'])) {
                $sql = self::compile($rOp);
            } elseif (strpos($lOp, 'in') === 0) {
                $values = implode(', ', $rOp);
                $sql = "IN ($values)";
            } else {
                $sql = "$lOp = '";
            }

            $sqlArray[] = $sql;
        }

        return [
            'sql' => $sqlArray,
            'params' => $params
        ];
    }

}
