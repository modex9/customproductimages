<?php

namespace CustomProductImages\Helper;

use DB, Exception;

class DbManager
{
    private $tables;

    private $module;

    public function __construct($tables, $module)
    {
        $this->tables = $tables;
        $this->module = $module;
    }

    /**
     * Create tables for module
     */
    public function createTables()
    {
        if(empty($this->tables))
        {
            return true;
        }
        $sql_path = _PS_ROOT_DIR_ . '/' . _MODULE_DIR_ . $this->module->name . '/sql/';
        $sql_files = scandir($sql_path);
        $sql_queries = [];
        foreach($sql_files as $sql_file)
        {
            $file_parts = pathinfo($sql_file);
            if($file_parts['extension'] == 'sql' && in_array($file_parts['filename'], $this->tables))
            {
                $sql_query = str_replace('_DB_PREFIX_', _DB_PREFIX_, file_get_contents($sql_path . $sql_file));
                $sql_queries[] = str_replace('_MYSQL_ENGINE_', _MYSQL_ENGINE_, $sql_query);
            }
        }
        foreach ($sql_queries as $query) {
            try {
                $res_query = Db::getInstance()->execute($query);

                if ($res_query === false) {
                    return false;
                }
            } catch (Exception $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * Delete module tables
     */
    public function deleteTables()
    {
        if(empty($this->tables))
        {
            return true;
        }
        foreach ($this->tables as $table) {
            try {
                $res_query = Db::getInstance()->execute("DROP TABLE IF EXISTS " . _DB_PREFIX_ . $table);
            } catch (Exception $e) {
            }
        }

        return true;
    }

}
