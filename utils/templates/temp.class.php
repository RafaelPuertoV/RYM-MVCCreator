<?php

namespace SIPI;

use SIPI\DataBase as Db;

class LevelCatalog
{
    private $id;
    private $name;

    public function __construct()
    {
        $this->id = 0;
        $this->name = '';
    }

    public function get_id()
    {
        return $this->id;
    }
    public function set_id($_id)
    {
        $this->id = $_id;
    }
    public function get_name()
    {
        return $this->name;
    }
    public function set_name($_name)
    {
        $this->name = $_name;
    }

    private function find($column, $value)
    {
        $db = new Db();
        $db->bind('ColValue', $value);
        $lvls = $db->query("SELECT id, name FROM levelCatalog WHERE " . $column . " = :ColValue;");

        if (count($lvls) > 0) {
            $this->id = $lvls[0]["id"] ;
            $this->name = $lvls[0]["name"] ;
            return true;
        }
        return false;
    }

    public function findById($id)
    {
        return $this->find('id', $id);
    }

    public function getLevelList(): array
    {
        $result = array();
        $db = new Db();
        $lvlCatalog = $db->query("SELECT id, name FROM levelCatalog ORDER BY name ASC;");
        foreach ($lvlCatalog as $value) {
            $lvl = new LevelCatalog();
            $lvl->set_id($value["id"]) ;
            $lvl->set_name($value["name"]) ;
            $result[] = $lvl;
        }
        return $result;
    }
}
