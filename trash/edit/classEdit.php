<?php

use Altahr\Database;

class Edit
{
    private $db, $exclude;
    public $list, $lengths, $table, $primary;

    function __construct()
    {
        $this->db = new database();
        $this->crud();
        switch ($this->table) {
            case "lokaler":
                $this->Lokaler();
                break;
            case "el_meters":
                $this->Elmeters();
                break;
        }
        $this->setLengths();
    }

    private function Lokaler()
    {
        $this->list = $this->db->selectArray("lokaler", "room", "1=1");
        $this->primary = "room";
        $this->exclude = ["room"];
    }
    private function Elmeters()
    {
        $this->list = $this->db->selectArray("el_meters", "meterid", "1=1");
        $this->primary = "meterid";
        $this->exclude = ["room"];
    }

    private function crud()
    {
        if (isset($_POST["insert"])) {
            $primary = $_POST['primary'];
            $table = $_POST["table"];
            $_POST[$primary] = $_POST['id'];
            unset($_POST['id']);
            unset($_POST['table']);
            unset($_POST['primary']);
            unset($_POST['insert']);
            $this->db->insert($table, $_POST);
            $this->table = $table;
        } else if (isset($_POST["update"])) {
            $primary = $_POST['primary'];
            $table = $_POST["table"];
            $where = "{$primary} = '{$_POST["id"]}'";
            unset($_POST['id']);
            unset($_POST['table']);
            unset($_POST['primary']);
            unset($_POST['update']);
            $this->db->update($table, $_POST, "{$where} LIMIT 1");
            $this->table = $table;
        } else if (isset($_POST["delete"])) {
            $primary = $_POST['primary'];
            $table = $_POST["table"];
            $where = "{$primary} = '{$_POST["id"]}'";
            $this->db->deleteOne($table, $where);
            $this->table = $table;
        } else {
            $this->table = isset($_POST["table"]) ? $_POST["table"] : "lokaler";
        }
    }

    private function setLengths()
    {
        foreach ($this->list as $key => $row) {
            foreach ($this->exclude as $exclude) {
                unset($this->list[$key][$exclude]);
            }
            foreach ($row as $k => $v) {
                if (!isset($this->lengths[$k])) {
                    $this->lengths[$k] = 0;
                }
                $this->lengths[$k] = max($this->lengths[$k], strlen($v));
            }
        }
    }
}

?>