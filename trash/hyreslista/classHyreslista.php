<?php
use Altahr\Database;
class Hyreslista
{
    private $count = 1;
    private $db;
    public $uploads = array();
    private $meters, $orderby, $order;
    public $list,$active,$area;

    function __construct()
    {
        //parent::__construct();

        $this->db = new Database();
        $this->actions();
        $this->setHyresLista();

        $this->active = [];
        $this->active["0"] = "Avslutad";
        $this->active["1"] = "Aktiv";
        $this->active["2"] = "Vakant";
        $this->active["4"] = "Extra";
        $this->active["5"] = "Gratis";

        $this->area["Butik"] = "Butik";
        $this->area["Fristående"] = "Fristående";
        $this->area["Förråd"] = "Förråd";
        $this->area["Gamla fabriken"] = "Gamla fabriken";
        $this->area["Kontor"] = "Kontor";
        $this->area["Nya fabriken"] = "Nya fabriken";
    }

    function __destruct(){
        $this->db->close();
    }
    function setHyresLista()
    {
        $this->orderby = $_POST["orderby"] ?? "name";
        $this->order = $_POST["order"] ?? "ASC";
        try {
            $this->list = $this->db->selectArray("hyreslista", "clientid", "1=1 ORDER BY {$this->orderby} {$this->order}");
        } catch (Exception) {
            $this->list = $this->db->selectArray("hyreslista", "clientid", "1=1");
        }
    }

    function actions()
    {
        if (isset($_POST["update"])) {
            echo "update";
            $this->db->update("hyreslista", $_POST["newval"], "clientid = {$_POST["clientid"]}");
            echo "_";
            if (!empty($_FILES['file']['name'])) {
                $filename = $_POST["clientid"] . "_" . $_FILES['file']['name'];
                $path = "storage/uploads/" . $filename;
                $count = 1;
                while (file_exists($path)) {
                    $filename = "{$_POST["clientid"]}_{$count}_{$_FILES["file"]["name"]}";
                    $path = "storage/uploads/" . $filename;
                    $count++;
                }
                if (move_uploaded_file($_FILES['file']['tmp_name'], $path)) {
                    echo "The file {$path} has been uploaded";
                } else {
                    echo "There was an error uploading the file.";
                }
            }
        } elseif (isset($_POST["addasnew"])) {
            unset($_POST["newval"]["clientid"]);
            $this->db->insert("hyreslista", $_POST["newval"]);
        } elseif (isset($_POST["insert"])) {
            $insert = array();
            $insert["name"] = "AAA Ny hyresgäst";
            $insert["active"] = 1;
            $this->db->insert("sf_hyreslista", $insert);
        } elseif (isset($_POST["pdf"])) {
            $this->setHyresLista();
            require_once("pdf.php");
            $pdf = new PDF($this);
        } elseif (isset($_POST["clean"])) {
            $ids = implode(",", $_POST["field"]["active"]);
            echo "<script>window.open('/html/hyreslista/clean_hyreslista.php?ids={$ids}', '_blank');</script>";
        } elseif (isset($_POST["delete_file"])) {
            $fileToDelete = array_key_first($_POST["delete_file"]);
            if (file_exists("storage/uploads/" . $fileToDelete)) {
                unlink("storage/uploads/" . $fileToDelete);
                echo "Filen $fileToDelete har tagits bort.";
            } else {
                echo "Filen $fileToDelete existerar inte.";
            }
        }
    }

    function sum($row, $key)
    {
        switch ($key) {
            case "a";
                return 0;
            case "size";
                return $row[$key];
            default:
                return is_numeric($row[$key]) ? $row[$key] : 0;
        }
    }

    public function displayRow($active, $area)
    {
        if (
            (empty($_POST["active"]) || in_array($active, array_keys($_POST["active"])))
            &&
            (empty($_POST["area"]) || in_array($area, array_keys($_POST["area"])))
        ) {
            return true;
        }
        return false;
    }

    public function displayColumn($column)
    {

        if (empty($_POST["column"]) || in_array($column, array_keys($_POST["column"]))
        ) {
            return true;
        }
        return false;
    }

    public function selectArea()
    {

        $return = ["<div class='row' style='width: 100%; margin: 0;'>"];
        foreach ($this->area as $key => $display) {
            $selected = isset($_POST["area"][$key]) ? "checked='checked'" : "";
            $return[] = "<div class='col-6' style=''>";
            $return[] = "<label class='list-group-item' style='padding: 5px 30px; margin: 0;'>";
            $return[] = "<input class='form-check-input me-2' name='area[{$key}]' type='checkbox' value='1' {$selected} onclick='this.form.submit();'> $key";
            $return[] = "</label>";
            $return[] = "</div>";
        }
        $return[] = "</div>";
        return implode("\n", $return);
    }

    public function selectColumns($columns)
    {
        asort($columns);
        $return = ["<div class='row' style='width: 100%; margin: 0;'>"];
        foreach ($columns as $key => $column) {
            $selected = empty($_POST["column"]) ? "checked='checked'" : (isset($_POST["column"][$key]) ? "checked='checked'" : "");
            $return[] = "<div class='col-4' style='white-space: nowrap;overflow: hidden'>";
            $return[] = "<label class='list-group-item' style='padding: 5px 30px; margin: 0;'>";
            $return[] = "<input class='form-check-input me-2' name='column[{$key}]' type='checkbox' value='1' {$selected} onclick='this.form.submit();'> $column->head";
            $return[] = "</label>";
            $return[] = "</div>";
        }
        $return[] = "</div>";
        return implode("", $return);
    }

    public function selectActivity()
    {


        $return = ["<div class='row' style='width: 100%; margin: 0;'>"];
        foreach ($this->active as $key => $display) {
            $selected = isset($_POST["active"][$key]) ? "checked='checked'" : "";
            $return[] = "<div class='col-6' style=''>";
            $return[] = "<label class='list-group-item' style='padding: 5px 30px; margin: 0;'>";
            $return[] = "<input class='form-check-input me-2' name='active[{$key}]' type='checkbox' value='1' {$selected} onclick='this.form.submit();'> $display";
            $return[] = "</label>";
            $return[] = "</div>";
        }
        $return[] = "</div>";
        return implode("\n", $return);
    }

}

?>