<?php

abstract class ObjectModelExt extends ObjectModel
{
    const FICTIVE_AND_CONDITION = '1 ';

    public static function getCount($where = '')
    {
        $query = (new DbQuery())
            ->select("COUNT(*)")
            ->from(static::$definition['table']);
        if($where)
            $query->where($where);

        $count = Db::getInstance()->getValue($query);
        if (!$count || $count <= 0) {
            return 0;
        }
        return $count;
    }

    public static function checkIfExists($column, $value, $id_lang = false)
    {
        if(!isset(static::$definition['fields'][$column]))
            return false;

        $value = pSQL($value);
        $query = (new DbQuery())
            ->select(static::$definition['primary'])
            ->from(static::$definition['table'] . ($id_lang ? '_lang' : ''))
            ->where($column . " = '{$value}'" . ($id_lang ? " AND id_lang = {$id_lang}" : ''));

        return (bool) Db::getInstance()->getValue($query);
    }

    /*
        1 condition = [column => value]
    */
    public static function getObjectIds($conditions)
    {
        $columns = array_keys($conditions);
        foreach($columns as $column)
        {
            if(!isset(static::$definition['fields'][$column]))
                return false;
        }

        $query = (new DbQuery())
            ->select(static::$definition['primary'])
            ->from(static::$definition['table']);

        $where = self::FICTIVE_AND_CONDITION;
        foreach($conditions as $column => $value)
        {
            if(self::columnValueNeedsQuotes($column))
            {
                $where .= ' AND ' . $column . " = '{$value}'";
            }
            else
            {
                $where .= ' AND ' . $column . " = {$value}";
            }
        }
        $query->where($where);

        return array_map(function($object) {
            return ($object[static::$definition['primary']] ?? 0);
        }, Db::getInstance()->executeS($query));
    }

    private function columnValueNeedsQuotes($column)
    {
        if(static::$definition['fields'][$column]['type'] == self::TYPE_INT || static::$definition['fields'][$column]['type'] == self::TYPE_FLOAT
            || static::$definition['fields'][$column]['type'] == self::TYPE_BOOL)
        {
            return false;
        }
        return true;
    }
}
