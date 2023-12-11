<?php


class  RYMDatabase {

		var $bd;
		var $server;
		var $userDB;
		var $passwordDB;
		var $database;

	function getCnx(){
		return $this->bd;
	}
	
	function __construct() {
		$cfg=include __DIR__.'/../config.inc.php';
		$this->setConfig($cfg['hostDB'],$cfg['usernameDB'],$cfg['passwordDB'],$cfg['nameDB']);
	}

	function setConfig($_server, $_userDB, $_passwordDB, $_database){
		$this->server = $_server;
		$this->userDB = $_userDB;
		$this->passwordDB = $_passwordDB;
		$this->database = $_database;
		$this->cnxOn();
	}

//###################### CONEXI�N A LA BD ###################### //

    function cnxOn(){
		$this->bd = new PDO('mysql:host='.$this->server.';dbname='.$this->database.';charset=utf8;', $this->userDB, $this->passwordDB) or die (PDO::errorInfo());
	}

//###################### DESCONEXI�N A LA BD ###################### //

	function cnxOff(){
		$this->bd = null;
	}

	function getTables(){
		$query = "SHOW TABLES ; ";
		$rquery = $this->bd->prepare($query);
		$rquery->execute();
	    $table_fields = $rquery->fetchAll(PDO::FETCH_COLUMN);
		return $table_fields;
	}

	function getColumns( $_tableName ){
		$query = "SELECT c.COLUMN_NAME, c.DATA_TYPE,c.COLUMN_TYPE, c.CHARACTER_MAXIMUM_LENGTH , c.COLUMN_KEY, IFNULL(c.COLUMN_DEFAULT,'') AS COLUMN_DEFAULT
			FROM information_schema.COLUMNS c 
			WHERE c.TABLE_SCHEMA LIKE '".$this->database."' AND c.TABLE_NAME LIKE '".$_tableName."'; ";
		//echo '<br>'.$query.'<br>';
		$rquery = $this->bd->prepare($query);
		$rquery->execute();
	    $table_fields = $rquery->fetchAll();
		return $table_fields;
	}

	function getPrimaryKeys($_table){
		$query = "SHOW KEYS FROM  $_table  WHERE Key_name='PRIMARY'; ";
		$rquery = $this->bd->prepare($query);
		$rquery->execute();
	    $table_fields = $rquery->fetchAll(PDO::FETCH_COLUMN);
		/*foreach ($table_fields as $column ) {
			  echo $column['Column_name'];
	    }*/
		return $table_fields;
	}
	function getForeingeys($_table){
		$query = "SELECT 
			RefCons.table_name, 
			KeyCol.table_name, 
			KeyCol.column_name,
			KeyCol.referenced_table_name, 
			KeyCol.referenced_column_name, 
			RefCons.constraint_name 
			FROM information_schema.referential_constraints RefCons 
			JOIN information_schema.key_column_usage KeyCol 
				ON RefCons.constraint_schema = KeyCol.table_schema 
					AND RefCons.table_name = KeyCol.table_name 
					AND RefCons.constraint_name = KeyCol.constraint_name 
			WHERE RefCons.constraint_schema = '".$this->database."'
				AND KeyCol.table_name LIKE '".$_table."' 
			ORDER BY KeyCol.table_name ASC, KeyCol.column_name ASC; ";
		$rquery = $this->bd->prepare($query);
		$rquery->execute();
	    $table_fields = $rquery->fetchAll();
		/*foreach ($table_fields as $column ) {
			  echo $column['Column_name'];
	    }*/
		return $table_fields;
	}
	public static function getArrayToHTMLTable($_arrayInfo)
    {
        $header = "<tr>";
        $body = "";
        $idx = 0 ;

        foreach ($_arrayInfo as $row) {
            $body .= '<tr>';
            foreach ($row as $column => $colValue) {
                if ($idx == 0) {
                    $header .= '<th>' . $column . '</th>';
                }
                $body .= '<td>' . $colValue . '</td>';
            }
            $body .= '</tr>';
            $idx++;
        }
        return '<table border="1"><tr>' . $header . '</tr>' . $body . '</table>';
    }
//###################### Cuantos Registros  TENGO en mi tabla ###################### //
}

