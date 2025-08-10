<?php


class output extends db
{
    private $count = 1;
    private $uploads = array();
    private $meters;
    public $list;

    function __construct()
    {
        parent::__construct();
        $this->actions();
        $this->meters = $this->selectArray("el_meters", "clientid", "1=1");
        $files = scandir("storage/uploads/");
        foreach ($files as $file) {
            if (strpos($file, "_") !== false) {
                $parts = explode("_", $file);
                $this->uploads[$parts[0]][] = $file;
            }
        }
        $this->list = $this->selectArray("sf_hyreslista", "clientid", "1=1");
        uasort($this->list, function ($a, $b) {
            return $a['name'] > $b['name'] ? 1 : -1;
        });
    }

    function actions()
    {
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
        } elseif (isset($_POST["addasnew"])) {
            unset($_POST["newval"]["clientid"]);
            $this->insert("sf_hyreslista", $_POST["newval"]);
        } elseif (isset($_POST["insert"])) {
            $insert = array();
            $insert["name"] = "AAA Ny hyresgäst";
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
            if (file_exists("storage/uploads/" . $fileToDelete)) {
                unlink("storage/uploads/" . $fileToDelete);
                echo "Filen $fileToDelete har tagits bort.";
            } else {
                echo "Filen $fileToDelete existerar inte.";
            }
        }
    }

    function set($row, $cell, $key)
    {
        switch ($key) {
            case "clientid":
                $cell->class = "edit\" clientid=\"{$row["clientid"]}";
                $cell->style = "cursor:pointer;background-color:{$this->col($row["active"])};color:white;text-align:center;padding:0px 2px 0px 2px;";
                $cell->display = "{$this->count}";
                $this->count++;
                break;
            case "name":
                $cell->class = "edit\" clientid=\"" . $row["clientid"];
                if ($row["information"] == "") {
                    $cell->style = "cursor:pointer;text-decoration:underline;";
                } else {
                    $cell->style = "cursor:pointer;text-decoration:underline;color:red;\" title=\"{$row["information"]}";
                }
                $cell->display = $row["name"];
                break;
            case "size":
                $kvmpris = $row["size"] > 0 ? $this->num($row["monthly_exvat"] / $row["size"]) : 0;
                $cell->display = "{$row["size"]} kvm ({$kvmpris})";
                break;
            case "monthly_exvat":
                $sumValue = $row["monthly_exvat"] + $row["yearly_fee_exvat"] / 12;
                $cell->display = $this->num($sumValue, 0) . " kr";
                break;
            case "inc":
                $multiplier = ($row["vat"] + 100) / 100;
                $sumValue = $multiplier * ($row["monthly_exvat"] + $row["yearly_fee_exvat"] / 12);
                $cell->display = $this->num($sumValue, 0) . " kr";
                break;
            case "contract_from":
                $f = strtotime($row["contract_from"]) > strtotime("2001-01-01") ? date("M y", strtotime($row["contract_from"])) : "";
                $t = strtotime($row["contract_to"]) > strtotime("2001-01-01") ? (strtotime($row["contract_to"]) < time() ? "Ext." : date("M y", strtotime($row["contract_to"]))) : "";
                $cell->display = "$f - $t";
                break;
            case "next_index":
                $cell->style = $row["next_index"] == "0000-00-00" ? "" : (strtotime($row["next_index"]) < time() + 86400 * 90 ? "border:2px solid red" : "");
                $cell->display = $row["next_index"] == "0000-00-00" ? "" : $row["next_index"];
                break;
            case "elavtal":
                $cell->display = isset($this->meters[$row["clientid"]]) && ($this->meters[$row["clientid"]]["reading"] == "Elvaco" || $this->meters[$row["clientid"]]["reading"] == "Logger1010") ? " <a style='color:red' target='_blank' href='/el/client/{$this->meters[$row["clientid"]]["meterid"]}/'>{$row["elavtal"]}</a>" : "{$row["elavtal"]}";
                break;
            case "cancellation";
                $cell->display = "{$row["cancellation"]}";
                break;
            case "files";
                if (!empty($this->uploads[$row["clientid"]])) {
                    $files = array();
                    foreach ($this->uploads[$row["clientid"]] as $file) {
                        $files[] = "<a href='/storage/uploads/{$file}' target='_blank' title='{$file}'>" . substr($file, -3) . "</a>";
                    }
                    $cell->display = implode(", ", $files);
                }
                break;
            case "el_avg":
                $days = (strtotime($row["elinvoice_last"]) - strtotime($row["contract_from"])) / 86400;


                $avg = $row["elinvoice_kwh"] > 0 ? number_format($row["elinvoice_kr"] / $row["elinvoice_kwh"], 2, ".", "") : 0;
                $sumValue = $days > 0 ? number_format($avg*365 * $row["elinvoice_kwh"] / $days, 2, ".", "") : "0";


                $cell->display = $avg > 0 ? $this->num($avg, 2) . " kr" : "";
                break;
            case "elinvoice_last";
                $kr = $row["elinvoice_kr"];
                $old = strtotime($row["elinvoice_last"]) < (time() - 86400 * 60);
                $cell->display = $kr == -1 ? "* Ingen mätare" : ($kr == -2 ? "* Avtal saknas" : ($kr == -3 ? "* Oklart" : $row["elinvoice_last"]));
                $cell->style = $kr == -1 ? "background-color: #d4edda !important;" : ($kr == -2 ? "background-color: #f8d7da !important;" : ($kr == -3 ? "background-color: #fff3cd !important;" : ($old ? "border:2px solid red;" : "")));
                break;
            case "elinvoice_kwh";
                $days = (strtotime($row["elinvoice_last"]) - strtotime($row["contract_from"])) / 86400;
                $sumValue = $days > 0 ? number_format(365 * $row["elinvoice_kwh"] / $days, 2, ".", "") : "0";
                $cell->display = $sumValue > 0 ? $this->num($sumValue, 0) . " kWh" : "";
                break;
            case "elinvoice_updated";
                $cell->display = $row["elinvoice_kr"] >= 0 ? "<a href='/hyreslista/update_el/{$row["fortnox"]}/{$row["contract_from"]}/'>{$row["elinvoice_updated"]}</a>" : "";
                break;
            default:
                $cell->display = $row[$key];
                break;
        }
        if ($cell->displaySum && isset($sumValue)) {
            $cell->sums[$row["active"]] = ($cell->sums[$row["active"]] ?? 0) + $sumValue;
        } elseif ($cell->displaySum) {
            $cell->sums[$row["active"]] = ($cell->sums[$row["active"]] ?? 0) + $row[$key];
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

    function num($number, $decimals = 2)
    {
        return number_format($number, $decimals, ".", " ");
    }

    function col($num)
    {
        switch ($num) {
            case 0:
                return "#000";
            case 1:
                return "#090";
            case 2:
                return "#009";
            case 3:
                return "orange";
            case 4:
                return "red";
            case 5:
                return "grey";
            default:
                return "grey";
        }
    }

    public function displayRow($active, $area)
    {
        if (
            (empty($_POST["active"]) || in_array($active, array_keys($_POST["active"])))
            &&
            (empty($_POST["area"]) || in_array($area, array_keys($_POST["area"])))) {
            return true;
        }
        return false;
    }

    public function selectArea()
    {
        $human["area"]["Butik"] = true;
        $human["area"]["Fristående"] = true;
        $human["area"]["Förråd"] = true;
        $human["area"]["Gamla fabriken"] = true;
        $human["area"]["Kontor"] = true;
        $human["area"]["Nya fabriken"] = true;
        $return = array();
        foreach ($human["area"] as $key => $display) {
            $selected = isset($_POST["area"][$key]) ? "checked='checked'" : "";
            $return[] = "<input style='' name='area[{$key}]' type='checkbox' value=\"1\" onclick='submit();' {$selected}> $key</span>";
        }
        return implode(", ", $return);
    }

    public function selectActivity()
    {
        if (sizeof($_POST) == 0) {
            $_POST["active"]["1"] = 1;
        }
        $human["active"]["0"] = "Avslutad";
        $human["active"]["1"] = "Aktiv";
        $human["active"]["2"] = "På väg bort";
        $human["active"]["3"] = "På väg in";
        $human["active"]["4"] = "Extra";
        $human["active"]["5"] = "Gratis";
        $return = array();
        foreach ($human["active"] as $key => $display) {
            $selected = isset($_POST["active"][$key]) ? "checked='checked'" : "";
            $return[] = "<input style='' name='active[{$key}]' type='checkbox' value=\"1\" onclick='submit();' {$selected}> $display</span>";
        }
        return implode(", ", $return);
    }
}

?>