    
    private function find($_column, $value)
    {
        $db->bind('ColValue', $value);
        $lvls = $db->query("SELECT id, name , level_id FROM applicationCatalog WHERE " . $_column . " = :ColValue;");

        if (count($lvls) > 0) {
            $this->id = $lvls[0]["id"] ;
            $this->name = $lvls[0]["name"] ;
            $this->level_id = $lvls[0]["level_id"] ;
            return true;
        }
        return false;
    }