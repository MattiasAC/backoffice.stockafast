<?php
if (!defined("stammisen")) {
    die("unauthorized");
}

class Hyreslista extends db
{
    public $colors = array();
    public $hyreslista = array();
    public $list = array();
    public $uploads = array();
    public $meters = array();
    public $stats = array();

    public function __construct()
    {
        parent::__construct();
        $this->hyreslista = array();
        if (!isset($_POST["formsubmit"])) {
            //$_POST["field"]["active"][1] = 1;
        }
        if (isset($_POST["update"])) {
            $this->update("sf_hyreslista", $_POST["newval"], "clientid = {$_POST["clientid"]}");
            if (!empty($_FILES['file']['name'])) {
                $parts = explode(".", $_FILES['file']['name']);
                $filename = $_POST["clientid"] . "_" . $_POST["filename"] . "." . $parts[1];
                $path = "storage/uploads/" . $filename;
                if (move_uploaded_file($_FILES['file']['tmp_name'], $path)) {
                    echo "The file {$path} has been uploaded";
                }
            }
        } elseif (isset($_POST["update_elinvoice"])) {
            $this->update("sf_hyreslista", $_POST["updatedata"], "fortnox={$_POST["fortnox"]}");
            print_r($_POST["updatedata"]);
        } elseif (isset($_POST["addasnew"])) {
            $this->insert("sf_hyreslista", $_POST["newval"]);
        } elseif (isset($_POST["insert"])) {
            $insert = array();
            $insert["name"] = "Ny hyresgäst";
            $insert["active"] = 1;
            $this->insert("sf_hyreslista", $insert);
        } elseif (isset($_POST["pdf"])) {
            $ids = implode(",", $_POST["field"]["active"]);
            echo "<script>window.open('/html/hyreslista/pdf_hyreslista.php?ids={$ids}', '_blank');</script>";
        } elseif (isset($_POST["clean"])) {
            $ids = implode(",", $_POST["field"]["active"]);
            echo "<script>window.open('/html/hyreslista/clean_hyreslista.php?ids={$ids}', '_blank');</script>";
        } elseif (isset($_POST["delete_file"])) {
            $fileToDelete = array_key_first($_POST["delete_file"]);
            if (file_exists("storage/uploads/".$fileToDelete)) {
                unlink("storage/uploads/".$fileToDelete);
                echo "Filen $fileToDelete har tagits bort.";
            } else {
                echo "Filen $fileToDelete existerar inte.";
            }
        } elseif (isset($_GET["b"]) && $_GET["b"] == "deletefile") {
            unlink("storage/uploads/" . $_GET["c"]);
        }
        $this->setList();
        $this->meters = $this->selectArray("el_meters", "clientid", "1=1");
        //$this->stats = $this->selectArray("kundstatistik", "id", "1=1");
        $this->colors[0] = "#000";
        $this->colors[1] = "#090";
        $this->colors[2] = "#009";
        $this->colors[3] = "orange";
        $this->colors[4] = "red";
        $this->colors[5] = "grey";
        $this->uploads = array();
        $files = scandir("storage/uploads/");
        foreach ($files as $file) {
            if (strpos($file, "_") !== false) {
                $parts = explode("_", $file);
                $this->uploads[$parts[0]][] = $file;
            }
        }
    }

    public function saveStatistics($array)
    {
        foreach ($array as $custid => $dates) {
            foreach ($dates as $date => $rows) {
                foreach ($rows as $index => $row) {
                    $data = array();
                    $data["date"] = $date;
                    $data["custid"] = $custid;
                    $data["custname"] = $row["namn"];
                    $data["article"] = $row["artikel"];
                    $data["name"] = $row["benamning"];
                    $data["antal"] = $row["antal"];
                    $data["enhet"] = $row["enhet"];
                    $data["sum"] = str_replace(",", ".", $row["summa"]);
                    $this->insert("kundstatistik", $data);
                }
            }
        }
    }

    public function stats($id)
    {
        $a = array();
        $a["raw"] = array();
        $a["maxdate"] = "1971-01-01";
        $a["sumkWh"] = 0;
        $a["sumKr"] = 0;
        foreach ($this->stats as $row) {
            if ($row["custid"] == $id && strpos(strtolower($row["name"]), "el") !== false) {
                $a["raw"][] = implode(";", $row);
                if ($row["date"] > $a["maxdate"]) {
                    $a["maxdate"] = $row["date"];
                }
                $a["sumkWh"] += $row["antal"];
                $a["sumKr"] += $row["sum"];
            }
        }
        return $a;
    }

    private function setList()
    {
        $this->list = $this->selectArray("sf_hyreslista", "clientid", "1=1");
        uasort($this->list, function ($a, $b) {
            return $a['name'] > $b['name'] ? 1 : -1;
        });
    }

    public function num($number, $decimals = 0)
    {
        return number_format($number, $decimals, ".", " ");
    }

    public function display($row)
    {
        if (!isset($_POST["field"])) {
            if ($row["active"] == 0) {
                //return false;
            }
            return true;
        }
        foreach ($_POST["field"] as $field => $values) {
            if (!in_array($row[$field], $values)) {
                return false;
            }
        }
        return true;
    }

    public function numToActivity($num)
    {
        switch ($num) {
            case 0:
                return "Avslutad";
            case 1:
                return "Aktiv";
            case 2:
                return "Vakant";
            case 3:
                return "Osäkra";
            case 4:
                return "Extra";
            case 5:
                return "Gratis";
            default:
                return "Unknown";
        }
    }

    public function select($field)
    {
        if (empty($_POST)) {
            //$_POST["field"]["active"][1] = 1;
        }
        $uniq = array();
        foreach ($this->list as $row) {
            $uniq[$row[$field]] = $row[$field];
        }
        $human["active"]["0"] = "Avslutad";
        $human["active"]["1"] = "Aktiv";
        $human["active"]["2"] = "På väg bort";
        $human["active"]["3"] = "På väg in";
        $human["active"]["4"] = "Extra";
        $human["active"]["5"] = "Gratis";
        asort($uniq);
        $return = array();
        foreach ($uniq as $val) {
            $display = is_numeric($val) ? $this->numToActivity($val) : $val;
            $selected = isset($_POST["field"][$field][$val]) ? "checked='checked'" : "";
            //echo "<pre>";
            //print_r($_POST);
            $style = $field == "active" ? "background-color:{$this->colors[$val]};padding:3px;padding-top:5px;color:#FFF" : "";
            $return[] = "<span style='$style'><input style='width:25px;height:25px;margin:0px;padding:0px;vertical-align: text-bottom;' name='field[{$field}][{$val}]' type='checkbox' $selected value=\"{$val}\" onclick='submit();'> $display</span>";
        }
        return "<b>" . ucfirst($field) . ":</b> " . implode(", ", $return);
    }

    public function number($number, $decimals = 0)
    {
        return number_format($number, $decimals, ".", " ");
    }
}

?>