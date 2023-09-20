<?php

namespace {{NAMESPACE}}\MVC;

use PDO;
use PDOException;
use DateTime;
use DateTimeObject;

class MVCDataBase
{
    # @object, The PDO object
    private $pdo;

    # @object, PDO statement object
    private $sQuery;

    # @array,  The database settings
    private $settings;

    # @bool ,  Connected to the database
    private $bConnected = false;

    # @path for logging exceptions
    private $logPath;

    # @array, The parameters of the SQL query
    private $parameters;

    /**
     *   Default Constructor
     *
     *  1. Instantiate Log class.
     *  2. Connect to database.
     *  3. Creates the parameter array.
     */
    public function __construct()
    {
        #$this->log = new DataBaseLog();
        if (defined('DIR_TMPFILES')) {
            $this->logPath  = DIR_TMPFILES  . '/logs/';
        } else {
            $this->logPath  = dirname(__FILE__)  . '/logs/';
        }
        $this->Connect();
        $this->parameters = array();
    }

    public function getParameters()
    {
        return $this->parameters;
    }
    /**
     *  This method makes connection to the database.
     *
     *  1. Reads the database settings from a ini file.
     *  2. Puts  the ini content into the settings array.
     *  3. Tries to connect to the database.
     *  4. If connection failed, exception is displayed and a log file gets created.
     */
    private function Connect()
    {
        # $this->settings = parse_ini_file("settings.ini.php");
        # Túnel
        # $dsn            = 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST . ';port='.DB_PORT;
        $dsn            = 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST . '';
        try {
            # Read settings from INI file, set UTF8
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, array(
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ));

            # We can now log any exceptions on Fatal error.
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            # Disable emulation of prepared statements, use REAL prepared statements instead.
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            # Connection succeeded, set the boolean to true.
            $this->bConnected = true;
        } catch (PDOException $e) {
            # Write into log
            # echo $this->ExceptionLog($e->getMessage());
            $this->ExceptionLog($e->getMessage());
            $this->bConnected = false;
            return $this->bConnected ;
        }
    }
    /*
     *   You can use this little method if you want to close the PDO connection
     *
     */
    public function CloseConnection()
    {
        # Set the PDO object to null to close the connection
        # http://www.php.net/manual/en/pdo.connections.php
        $this->pdo = null;
    }

    /**
     *  Every method which needs to execute a SQL query uses this method.
     *
     *  1. If not connected, connect to the database.
     *  2. Prepare Query.
     *  3. Parameterize Query.
     *  4. Execute Query.
     *  5. On exception : Write Exception into the log + SQL query.
     *  6. Reset the Parameters.
     */
    private function Init($query, $parameters = "")
    {
        # Connect to database
        if (!$this->bConnected) {
            $this->Connect();
        }
        try {
            # Prepare query
            $this->sQuery = $this->pdo->prepare($query);

            # Add parameters to the parameter array
            $this->bindMore($parameters);

            # Bind parameters
            if (!empty($this->parameters)) {
                foreach ($this->parameters as $param => $value) {
                    $type = PDO::PARAM_STR;

                    if (is_int($value[1])) {
                        $type = PDO::PARAM_INT;
                    } elseif (is_bool($value[1])) {
                        $type = PDO::PARAM_BOOL;
                    } elseif (is_null($value[1])) {
                            $type = PDO::PARAM_NULL;
                    }
                    // Add type when binding the values to the column
                    $this->sQuery->bindValue($value[0], $value[1], $type);
                }
            }

            # Reset the parameters
            $this->parameters = array();
            # Execute SQL
            $this->sQuery->execute();
            return true;
        } catch (PDOException $e) {
            # Reset the parameters
            $parameterstxt = '';
            foreach ($this->parameters as $parameter) {
                foreach ($parameter as $key => $value) {
                    $parameterstxt .= "\n$key = $value";
                }
            }
            $this->parameters = array();
            # Write into log and display Exception
            $this->ExceptionLog($parameterstxt . " \n\n " . $e->getMessage(), $query);
           return false;
        }
    }

    /**
     *  @void
     *
     *  Add the parameter to the parameter array
     *  @param string $para
     *  @param string $value
     */
    public function bind($para, $value)
    {
        $this->parameters[sizeof($this->parameters)] = [":" . $para , $value];
    }
    /**
     *  @void
     *
     *  Add more parameters to the parameter array
     *  @param array $parray
     */
    public function bindMore($parray)
    {
        if (empty($this->parameters) && is_array($parray)) {
            $columns = array_keys($parray);
            foreach ($columns as $i => &$column) {
                $this->bind($column, $parray[$column]);
            }
        }
    }

    public function getErrorCode()
    {
        return $this->pdo->errorCode();
    }

    /**
     *  If the SQL query  contains a SELECT or SHOW statement it returns an array containing all of the result set row
     *  If the SQL statement is a DELETE, INSERT, or UPDATE statement it returns the number of affected rows
     *
     *      @param  string $query
     *  @param  array  $params
     *  @param  int    $fetchmode
     *  @return mixed
     */
    public function query($query, $params = null, $fetchmode = PDO::FETCH_ASSOC)
    {
        $query = trim(str_replace("\r", " ", $query));

        $result = $this->Init($query, $params);

        $rawStatement = explode(" ", preg_replace("/\s+|\t+|\n+/", " ", $query));

        # Which SQL statement is used
        $statement = strtolower($rawStatement[0]);

        if ($result && ($statement === 'select' || $statement === 'show')) {
            return $this->sQuery->fetchAll($fetchmode);
        } elseif ($result && ($statement === 'insert' || $statement === 'update' || $statement === 'delete')) {
            return $this->sQuery->rowCount();
        } else {
            return null;
        }
    }


public function queryObject($query, $params = null, $className )
    {
        $query = trim(str_replace("\r", " ", $query));

        $result = $this->Init($query, $params);

        $rawStatement = explode(" ", preg_replace("/\s+|\t+|\n+/", " ", $query));

        # Which SQL statement is used
        $statement = strtolower($rawStatement[0]);

        if ($result && ($statement === 'select' || $statement === 'show')) {
            return $this->sQuery->fetchAll( PDO::FETCH_CLASS, $className );
        } elseif ($result && ($statement === 'insert' || $statement === 'update' || $statement === 'delete')) {
            return $this->sQuery->rowCount();
        } else {
            return null;
        }
    }

    /**
     *  Returns the last inserted id.
     *  @return string
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Starts the transaction
     * @return boolean, true on success or false on failure
     */
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    public function inTransaction()
    {
        return $this->pdo->inTransaction();
    }

    /**
     *  Execute Transaction
     *  @return boolean, true on success or false on failure
     */
    public function executeTransaction()
    {
        return $this->pdo->commit();
    }

    /**
     *  Rollback of Transaction
     *  @return boolean, true on success or false on failure
     */
    public function rollBack()
    {
        return $this->pdo->rollBack();
    }

    /**
     *  Returns an array which represents a column from the result set
     *
     *  @param  string $query
     *  @param  array  $params
     *  @return array
     */
    public function column($query, $params = null)
    {
        $this->Init($query, $params);
        $Columns = $this->sQuery->fetchAll(PDO::FETCH_NUM);

        $column = null;

        foreach ($Columns as $cells) {
            $column[] = $cells[0];
        }

        return $column;
    }
    /**
     *  Returns an array which represents a row from the result set
     *
     *  @param  string $query
     *  @param  array  $params
     *      @param  int    $fetchmode
     *  @return array
     */
    public function row($query, $params = null, $fetchmode = PDO::FETCH_ASSOC)
    {
        $this->Init($query, $params);
        $result = $this->sQuery->fetch($fetchmode);
        $this->sQuery->closeCursor(); // Frees up the connection to the server so that other SQL statements may be issued,
        return $result;
    }
    /**
     *  Returns the value of one single field/column
     *
     *  @param  string $query
     *  @param  array  $params
     *  @return string
     */
    public function single($query, $params = null)
    {
        $this->Init($query, $params);
        $result = $this->sQuery->fetchColumn();
        $this->sQuery->closeCursor(); // Frees up the connection to the server so that other SQL statements may be issued
        return $result;
    }
    /**
     * Writes the log and returns the exception
     *
     * @param  string $message
     * @param  string $sql
     * @return string
     */
    private function ExceptionLog($message, $sql = "")
    {
        error_log($message);
        $exception = 'Unhandled Exception. <br />';
        $exception .= $message;
        $exception .= "<br /> You can find the error back in the log.";

        if (!empty($sql)) {
            # Add the Raw SQL to the Log
            $message .= "\r\nRaw SQL : " . $sql;
        }
        # Write into log
        $this->writeLog($message);

        return $exception;
    }

       /**
        *   @void
        *   Creates the log
        *
        *   @param string $message the message which is written into the log.
        *   @description:
        *    1. Checks if directory exists, if not, create one and call this method again.
            *    2. Checks if log already exists.
        *    3. If not, new log gets created. Log is written into the logs folder.
        *    4. Logname is current date(Year - Month - Day).
        *    5. If log exists, edit method called.
        *    6. Edit method modifies the current log.
        */
    public function writeLog($message)
    {
        $date = new DateTime();
        $log = $this->logPath . $date->format('Y-m-d') . ".txt";

        if (is_dir($this->logPath)) {
            if (!file_exists($log)) {
                $fh  = fopen($log, 'a+') or die("Fatal Error !");
                $logcontent = "Time : " . $date->format('H:i:s') . "\r\n" . $message . "\r\n";
                fwrite($fh, $logcontent);
                fclose($fh);
            } else {
                $this->editLog($log, $date, $message);
            }
        } else {
            if (mkdir($this->logPath, 0777, true) === true) {
                   $this->writeLog($message);
            }
        }
    }

        /**
         *  @void
         *  Gets called if log exists.
         *  Modifies current log and adds the message to the log.
         *
         * @param string $log
         * @param DateTimeObject $date
         * @param string $message
         */
    private function editLog($log, $date, $message)
    {
        $logcontent = "Time : " . $date->format('H:i:s') . "\r\n" . $message . "\r\n\r\n";
        $logcontent = $logcontent . file_get_contents($log);
        file_put_contents($log, $logcontent);
    }
}
