<?php
class Model{


    public function __construct(){
        $db = Db::getInstance();
        $this->conn = $db->getConnection();
    }

    /**
     * general query
     * @param string $statement
     * @return array result statment
     */
    public function query($statement){

        try {
            $sth = $this->conn->prepare($statement);
            $sth->execute();
            $result = $sth->fetchAll(PDO::FETCH_ASSOC);
            return $result;

        } catch (PDOException $e) {
            echo $statement. '<br/>';
            echo $e->getMessage();
        }
    }


    public function all(){
        $statement = "SELECT ".implode(',',$this->fields)." FROM ".$this->table;
        return $this->query($statement);
    }


    public function get($id){
        $statement = "SELECT ".implode(',',$this->fields)." FROM ".$this->table
            ."WHERE id=".$id;
        try {
            $sth = $this->conn->prepare($statement);
            $sth->execute();
            $result = $sth->fetch(PDO::FETCH_ASSOC);
            return $result;

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }


    public function getTable(){
        return $this->table;
    }

}