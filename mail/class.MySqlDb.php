<?php

//defined('PATH_LIB') or die("Restricted Access");

#############################################################################################################################
#												Simple Database Class to access MySql										#
#	@file	:	class.database.php																							#
#	@author	:	Mragank Shekhar																								#
#	@email	:	wecare@mssinfotech.com																						#
#############################################################################################################################

class MySqlDb {

    //private variables
    private $dbCon; //connection
    private $db_host; //database host
    private $db_user; //database user
    private $db_pswd; //database password
    private $db_name; //database name
    private $recSet; //recordset
    private $last_query; //last query
    private $msgEr; // error message
    private $magic_quotes_active; //boolean variable
    private $real_escape_string; //boolean variable

    //construtor, needs 4 essential parameters to connect to DB

    function __construct($dbHost, $dbUser, $dbPswd, $dbName) {
        $this->setDbHost($dbHost);
        $this->setDbUser($dbUser, $dbPswd);
        $this->setDbName($dbName);
        $this->connect();
    }

    //destructor
    function __destruct() {
        $this->disconnect();
    }

    public function setErAry($aryEr) {
        $this->aryErMsg = $aryEr;
    }

    public function setDbHost($host) {
        $this->db_host = $host;
    }

    public function setDbUser($dbUser, $dbPswd) {
        $this->db_user = $dbUser;
        $this->db_pswd = $dbPswd;
    }

    public function setDbName($dbName) {
        $this->db_name = $dbName;
    }

    //funciton to close connection
    public function disconnect() {
        if (isset($this->dbCon) && $this->dbCon) {
            //$this->dbCon = null;
        }
    }

    public function lq() {
        return $this->last_query;
    }

    //database connection establish
    public function connect() {
        //check if any connection already exists, close that connection
        $this->disconnect();
        try {
            $this->dbCon = new PDO("mysql:host=" . $this->db_host . "; dbname=" . $this->db_name, $this->db_user, $this->db_pswd, array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        } catch (PDOException $e) {
            die("Database Connection Failed : " . $e->getMessage());
        }
        if (!$this->dbCon) {
            die("Database Connection Failed ");
        }
    }

    //function to set Error Msg
    private function setErMsg($myErr, $myErrNo = 0) {
        if (isset($this->aryErMsg[$myErrNo]))
            $myErr = $this->aryErMsg[$myErrNo];

        $this->msgEr = "<pre>Query Exceution Failed :" . $myErr;
        $this->msgEr .= "<br />Last Query : " . $this->last_query . "</pre>";
    }

    //public funtion to send error to user
    public function em() {
        return $this->msgEr;
    }

    //method to get id of last inserted record
    public function getLastId() {
        return $this->LastInsertId;
    }

    //method to prepare string for various sql operation
    public function prepStr($sqlStr) {
        if (function_exists('POST')) {

            $sqlStr = POST($sqlStr);
        }
        return $sqlStr;
    }

    function query($sql, $param = array()) {
        try {
            $stmt = $this->dbCon->prepare($sql);
            $stmt->execute($param);
            $this->last_query = $stmt->queryString;
        } catch (PDOException $e) {
            $this->setErMsg($e->getMessage());
            return $e->getMessage();
        }
    }

    public function getRows($sql,$param = array()) {
        try {
            $this->last_query = $sql;
            $stmt = $this->dbCon->prepare($sql);
            if(is_array($param) && count($param)>0)
            $stmt->execute($param);
			else
			$stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->msgEr = $e->getMessage();
            return $e->getMessage() . "<br />Last Query : <strong>" . $this->last_query . "</strong>";
            ;
        }
    }

    public function getRow($sql, $param = array()) {

        try {
            $this->last_query = $sql;
            $stmt = $this->dbCon->prepare($sql);
			if(is_array($param) && count($param)>0)
            $stmt->execute($param);
			else
			$stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->msgEr = $e->getMessage();
            return $e->getMessage() . "<br />Last Query : <strong>" . $this->last_query . "</strong>";
            ;
        }
    }

    public function getVal($sql,$param = array()) {
        try {
            $this->last_query = $sql;
            $stmt = $this->dbCon->prepare($sql);
            if(is_array($param) && count($param)>0)
            $stmt->execute($param);
			else
			$stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            $this->msgEr = $e->getMessage();
            return $e->getMessage() . "<br />Last Query : <strong>" . $this->last_query . "</strong>";
            ;
        }
    }

    public function insertAry($table, $data) {
        try {
            $fields = '`' . implode('`, `', array_keys($data)) . '`';
            $placeholders = ':' . implode(', :', array_keys($data));
            $sql = "INSERT INTO {$table} ($fields) VALUES ({$placeholders})";
            $stmt = $this->dbCon->prepare($sql);
            foreach ($data as $placeholder => &$value) {
                $placeholder = ':' . $placeholder;
                $stmt->bindParam($placeholder, $value);
            }
            $this->last_query = $sql;
            $stmt->execute();
            return $this->dbCon->lastInsertId();
        } catch (PDOException $e) {
            $this->msgEr = $e->getMessage();
            //return $e->getMessage();
            return $e->getMessage() . "<br />Last Query : <strong>" . $this->last_query . "</strong>";
            ;
        }
    }

    public function updateAry($table, $data, $condition, $param = array()) {
        if ($condition == "")
            $condition = "1=1";

        $condition = str_replace("where", "", $condition);
        foreach ($data as $placeholder => $value) {
            $content .= $placeholder . "=" . "'" . $value . "',";
        }
        $content = rtrim($content, ",");
        $sql = "UPDATE " . $table . " SET " . $content . " WHERE " . $condition . " ";
        $this->last_query = $sql;
        try {
            $stmt = $this->dbCon->prepare($sql);
            $stmt->execute($param);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->msgEr = $e->getMessage();
            return $e->getMessage() . "<br />Last Query : <strong>" . $this->last_query . "</strong>";
            ;
        }
    }

    public function delete($sql, $param=array()) {
        try {
            $this->last_query = $sql;
            $stmt = $this->dbCon->prepare($sql);
            if(is_array($param) && count($param)>0)
            $stmt->execute($param);
			else
			$stmt->execute();
            if ($stmt->rowCount()) {
                return 1;
            } else {
                return NULL;
            }
        } catch (PDOException $e) {
            $this->msgEr = $e->getMessage();
            return $e->getMessage() . "<br />Last Query : <strong>" . $this->last_query . "</strong>";
            ;
        }
    }

}

?>