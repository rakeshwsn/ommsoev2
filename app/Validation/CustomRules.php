<?php

namespace App\Validation;

use Admin\Localisation\Models\GrampanchayatModel;
use Config\Database;

class CustomRules
{

    public function is_unique_gp(string $str, string $field, array $data): bool
    {
        $uri = service('uri');

        [$field, $ignoreField, $ignoreValue] = array_pad(explode(',', $field), 3, null);
        $ignoreValue = $uri->getSegment(4);
        // Break the table and field apart
        sscanf($field, '%[^.].%[^.]', $table, $field);

        $db = Database::connect($data['DBGroup'] ?? null);

        $builder = $db->table($table)
            ->select('1')
            ->like($field, $str, 'both')
            ->limit(1);
        if(isset($data['block_id'])){
            $builder->where('block_id', $data['block_id']);
        }

        if (!empty($ignoreField) && !empty($ignoreValue) && !preg_match('/^\{(\w+)\}$/', $ignoreValue)) {
            $builder->where("{$ignoreField} !=", $ignoreValue);
        }

        $query = $builder->get();

        return $query->getNumRows() === 0;
    }

    public function is_unique_village(string $str, string $field, array $data): bool
    {
        $uri = service('uri');

        [$field, $ignoreField, $ignoreValue] = array_pad(explode(',', $field), 3, null);
        $ignoreValue = $uri->getSegment(4);
        // Break the table and field apart
        sscanf($field, '%[^.].%[^.]', $table, $field);

        $db = Database::connect($data['DBGroup'] ?? null);

        $builder = $db->table($table)
            ->select('1')
            ->like($field, $str, 'both')
            ->limit(1);
        if(isset($data['gp_id'])){
            $builder->where('gp_id', $data['gp_id']);
        }

        if (!empty($ignoreField) && !empty($ignoreValue) && !preg_match('/^\{(\w+)\}$/', $ignoreValue)) {
            $builder->where("{$ignoreField} !=", $ignoreValue);
        }

        $query = $builder->get();

        return $query->getNumRows() === 0;
    }

    public function required_if_value(string $field, string $params, array $data) {
        $params = explode(',',$params);
        $key = $params[0];
        $value = $params[1];

        if ($data[$key] == $value && $field == '') {
            return false;
        }
        return true;
    }

}