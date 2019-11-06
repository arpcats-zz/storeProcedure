<?php
/**
 * Purpose: Databse Connector
 * File Name: DBConnector.php
 * Class Name: DBConnector
 * Author: Anthony Payumo
 * Email: 1010payumo@gmail.com
 * Git Repo: github.com/arpcats
 * 
 * PHP Version 7
*/

Class DBConnector{

    var $conn;

    public function __construct(){

        $this->host = "localhost";
        $this->port = "3306";
        $this->username = "root";
        $this->password = "";
        $this->database = "storeprocedure";

        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);

        if($this->conn->connect_error){
            die("Connection Failed: ". $this->conn->connect_error);
        }
    }

    //STORE PROCEDURE 
    public function storeProcSelect($userId = false){

        $sqlDrop = "DROP PROCEDURE IF EXISTS getProcRecord";
        $sqlCreate = "
        CREATE PROCEDURE getProcRecord(IN userId INT)
        READS SQL DATA 
        BEGIN 
            IF(userId > 0) THEN
                SELECT * FROM users WHERE id = userId;
            ELSE
                SELECT * FROM users;
            END IF;
        END;";

        if(!$this->conn->query($sqlDrop) || !$this->conn->query($sqlCreate)){
            return "Store Procedure Creation Failed: ".$this->conn->connect_error;
        }

        if(!$this->conn->multi_query("CALL getProcRecord($userId)")){
            return "CALL Failed: ".$this->conn->connect_error;
        }

        do{
            if($result = $this->conn->store_result()){
                $row = $result->fetch_all();
                return $row;
            }else{
                if($this->conn->connect_error){
                    return "Store Failed: ".$this->conn->connect_error;
                }
            }
        } while($this->conn->more_results() && $this->conn->next_result());
    }

    public function storeProcSave($data = array()){
        
        if(is_array($data)){

            $sqlDrop = "DROP PROCEDURE IF EXISTS saveProcRecord";
            $sqlCreate = "
            CREATE PROCEDURE saveProcRecord(IN userId INT, IN uname VARCHAR(50), IN fname VARCHAR(100), IN lname VARCHAR(100), IN email VARCHAR(50) )
            READS SQL DATA 
            BEGIN
                IF(userId = 0) THEN
                    INSERT INTO users(username,firstname,lastname,email) VALUES(uname,fname,lname,email);
                ELSEIF(userId > 0) THEN
                    UPDATE users SET username = uname, firstname = fname, lastname = lname, email = email WHERE id = userId;
                END IF;
            END;";

            if(!$this->conn->query($sqlDrop) || !$this->conn->query($sqlCreate)){
                return "Store Procedure Creation Failed: ".$this->conn->connect_error;
            }

            $splitData = "'".implode($data,"', '")."'";
            if(!$this->conn->multi_query("CALL saveProcRecord($splitData)")){
                return "CALL Failed: ".$this->conn->connect_error;
            }

            do{
                if($result = $this->conn->store_result()){
                    $row = $result->fetch_all();
                    return $row;
                }else{
                    if($this->conn->connect_error){
                        return "Store Failed: ".$this->conn->connect_error;
                    }
                }
            } while($this->conn->more_results() && $this->conn->next_result());
        }else{
            return "Data is not array";
        }
    }

    public function storeProcDelete($userId){

        if($userId){

            $sqlCreate = "
            CREATE PROCEDURE deleteProcRecord(IN userId INT)
            BEGIN 
                DELETE FROM users WHERE id = userId;
            END;";

            if($this->conn->query("DROP PROCEDURE IF EXISTS deleteProcRecord")){
                if($this->conn->query($sqlCreate)){
                    if($this->conn->multi_query("CALL deleteProcRecord($userId)")){
                        return true;
                    }else{
                        return "CALL Failed: ".$this->conn->connect_error;
                    }
                }else{
                    return "Store Procedure Creation Failed: ".$this->conn->connect_error;
                }
            }

            /*
            $sqlDrop = "DROP PROCEDURE IF EXISTS deleteProcRecord";
            $sqlCreate = "
            CREATE PROCEDURE deleteProcRecord(IN userId INT)
            BEGIN 
                DELETE FROM users WHERE id = userId;
            END;";

            if(!$this->conn->query($sqlCreate) || !$this->conn->query($sqlDrop)){
                return "Store Procedure Creation Failed: ". $this->conn->connect_error;
            }

            if(!$this->conn->multi_query("CALL deleteProcRecord($userId)")){
                return "CALL Failed: ".$this->conn->connect_error;
            }
            */

            /*
            if($result = $this->conn->store_result()){
                return true; //data deleted
            }else{
                return false; //not data deleted
            }
            */
            
        }else{
            return "Add integer parameter";
        }
    }
    //END STORE PROCEDURE

    //SQL QUERY
    public function getRecord($table, $cond = "", $field = ""){
        
        $field = $field ? $field : "*";
        $sql = "SELECT {$field} FROM {$table} {$cond}";
        $result = $this->conn->query($sql);

        if($result->num_rows > 0){
            $row = $result->fetch_object();
        }else{
            $row = "";
        }

        return $row;
    }

    public function updateRecord($table, $data, $cond = "insert"){

        $field = "";
        $value = "";
        $set = "";
        $countData = count($data);
        
        foreach($data as $key => $val){
            if($count > 1){
                $field .= ",{$key}";
                $value .= ",{$val}";
                $set .= ",{$key} = '{$val}'";
            }else{
                $field .= " {$key}";
                $value .= " {$val}";
                $set .= " {$key} = '{$val}'";
            }
        }

        if($cond == "update"){
            $sql = "UPDATE {$table} SET ".substr($set,1)." {$cond}";
        }else{
            $sql = "INSERT INTO {$table} (".substr($field,1).") VALUES (".substr($value, 1).") ";
        }

        $result = $this->conn->query($sql);
        $row = $result->fetch_object();
        return $row;
    }

    public function deleteRecord($table, $cond = ""){

        $sql = "DELETE FROM {$table} {$cond}";
        $result = $this->conn->query($sql);
        if($result == true){
            return true;
        }else{
            return false;
        }
    }

}

?>