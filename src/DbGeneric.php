<?php
namespace Altahr;
class DbGeneric{
    private $conn;
    public function __construct($db = "")
    {
        $this->conn = new \mysqli("mysql684.loopia.se", "viktor@s380030", "stenbocksliden", "stockafast_se");
    }
    public function setDB($database){
        if($database == "hyrenmaskin"){
            $this->conn = new \mysqli("mysql685.loopia.se", "website@h362332", "h7878_90=iu&rtasd12hj23", "hyrenmaskin_se");
        }
    }
    public function close(){
        if ($this->conn) {
            $this->conn->close();
        }
    }
    public function __destruct()
    {
        if ($this->conn) {
           // $this->conn->close();
        }
    }

    public function query($sql){
        $res = mysqli_query($this->conn,$sql) or trigger_error(mysqli_error($this->conn)." SQL:".$sql);
        //
        return $res;
    }
    protected function insertid(){
        return mysqli_insert_id($this->conn);
    }
    protected function affectedrows(){
        return mysqli_affected_rows($this->conn);
    }
    protected function rowcount(){
        return mysqli_num_rows($this->conn);
    }
    public function escape($string){
        return mysqli_escape_string($this->conn,$string);
    }
    public function real_escape_string($string){
        return mysqli_escape_string($this->conn,$string);
    }

    public function insert($table,$data){
        $sql = "INSERT INTO {$table} SET ";

        $set = array();
        foreach($data as $column => $value){
            $set[] = "{$table}.{$column} = '".mysqli_escape_string($this->conn,$value)."'";
        }

        $sql .= implode(", ",$set);
        return $this->query($sql);
    }
    public function insertIgnore($table,$data){
        $sql = "INSERT IGNORE INTO {$table} SET ";

        $set = array();
        foreach($data as $column => $value){
            $set[] = "{$table}.{$column} = '".mysqli_escape_string($this->conn,$value)."'";
        }

        $sql .= implode(", ",$set);
        return $this->query($sql);
    }
    protected function getDB(){
        $sql = "SELECT DATABASE()";
        $row = mysqli_fetch_assoc($this->query($sql));
        return $row["DATABASE()"];
    }
    protected function getAutoIncrement($table){
        $sql ="SELECT `AUTO_INCREMENT`
            FROM  INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = '{$this->getDB()}'
                AND   TABLE_NAME   = '{$table}'";
        $row = mysqli_fetch_assoc($this->query($sql));
        return $row["AUTO_INCREMENT"];
    }
    public function update($table,$data,$where){
        echo "db";
        $sql = "UPDATE {$table} SET ";

        $set = array();
        foreach($data as $column => $value){
            $set[] = "{$table}.{$column} = '".mysqli_escape_string($this->conn,$value)."'";
        }

        $sql .= implode(", ",$set);
        $sql .= "WHERE {$where}";
        return $this->query($sql);
    }
        public function updateIgnore($table,$data,$where){
        $sql = "UPDATE IGNORE {$table} SET ";

        $set = array();
        foreach($data as $column => $value){
            $set[] = "{$table}.{$column} = '".mysqli_escape_string($this->conn,$value)."'";
        }

        $sql .= implode(", ",$set);
        $sql .= "WHERE {$where}";
        return $this->query($sql);
    }
    public function deleteOne($table,$where){
        $sql = "DELETE FROM {$table} WHERE {$where} LIMIT 1";
        return $this->query($sql);
    }
    protected function delete($table,$where){
        if(!empty($where)){
            $sql = "DELETE FROM {$table} WHERE {$where}";
            return $this->query($sql);
        }
    }
    public function count($table,$where){
        $sql = "SELECT count(*) AS c FROM {$table} WHERE {$where}";
        $row = mysqli_fetch_assoc($this->query($sql));
        return $row["c"];

    }
    public function selectArray($table,$key,$where){
        $sql = "SELECT * FROM {$table} WHERE {$where} ";
        $res = $this->query($sql);
        $return = array();
        while($row = mysqli_fetch_assoc($res)){
            if(empty($key)){
                $return[] = $row;
            }else{
                $return[$row[$key]] = $row;
            }
        }
        return $return;
    }
    public function selectOne($table,$where){
        $sql = "SELECT * FROM {$table} WHERE {$where} LIMIT 1";
        //echo $sql;
        return mysqli_fetch_assoc($this->query($sql));
    }
}
?>