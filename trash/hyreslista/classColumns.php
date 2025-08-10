<?php
global $contracts;
use Altahr\Database;

class Column
{
    public $count;
    public $head = "";
    public $headStyle = "text-align:center";
    public $display = "";
    public $class = "";
    public $style = "";
    public $uploads = array();
    public $sums = [];
    public $displaySum = false;
    public $sumSuffix = "";
    public $sumPerYear = false;
    private $db,$lokaler;

    public function __construct($head, $displaySum = false, $class = "", $headStyle = "", $style = "text-align:right", $sumSuffix = "", $sumPerYear = false, $uploads = false)
    {
        //$this->db = new database();
        $this->count = 0;
        $this->head = ucfirst($head);
        $this->displaySum = $displaySum;
        $this->class = $class;
        if (!empty($headStyle)) {
            $this->headStyle = $headStyle;
        }
        $this->style = $style;
        $this->sumSuffix = $sumSuffix;
        $this->sumPerYear = $sumPerYear;
        $this->uploads = $uploads;
        //$this->lokaler = $this->db->selectArray("lokaler","room","1=1");
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

    function setFiles()
    {
        $files = scandir("storage/uploads/");
        foreach ($files as $file) {
            if (strpos($file, "_") !== false) {
                $parts = explode("_", $file);
                $this->uploads[$parts[0]][] = $file;
            }
        }
    }

    function num($number, $decimals = 2)
    {
        return number_format($number, $decimals, ".", " ");
    }

    function set($row, $key)
    {
        switch ($key) {
            case "clientid":
                $this->class = "edit text-bg-header\" clientid=\"{$row["clientid"]}";
                $this->style = "background-color:{$this->col($row["active"])} !important;ext-align:center;padding:0px 2px 0px 2px;";
                $this->display = "{$this->count}";
                $this->count++;
                break;
            case "name":
                if ($row["information"] == "") {
                    $this->display = $row["name"];
                    $this->class = "edit\" clientid=\"" . $row["clientid"];
                } else {

                    $this->display = "<i class='fas fa-info-circle'></i> " . $row["name"] . "";
                    $this->class = "edit\" title=\"{$row["information"]}\" clientid=\"" . $row["clientid"];
                }
                break;
            case "size":
                $kvmpris = $row["size"] > 0 ? $this->num(12 * $row["monthly_exvat"] / $row["size"], 0) . " kr/kvm" : 0;
                $this->display = "{$row["size"]} kvm ({$kvmpris})";
                break;
            case "yearly_fee_exvat":
                $this->display = "hej";
                break;
            case "monthly_exvat":
                $sumValue = $row["monthly_exvat"] + $row["yearly_fee_exvat"] / 12;
                $this->display = $this->num($sumValue, 0) . " kr";
                break;
            case "inc":
                $multiplier = ($row["vat"] + 100) / 100;
                $sumValue = $multiplier * ($row["monthly_exvat"] + $row["yearly_fee_exvat"] / 12);
                $this->display = $this->num($sumValue, 0) . " kr";
                break;
            case "contract_from":
                $f = !empty($row["contract_from"]) && strtotime($row["contract_from"]) > strtotime("2001-01-01") ? date("M y", strtotime($row["contract_from"])) : "";
                $t = !empty($row["contract_to"]) &&  strtotime($row["contract_to"]) > strtotime("2001-01-01") ? (strtotime($row["contract_to"]) < time() ? "Ext." : date("M y", strtotime($row["contract_to"]))) : "";
                $this->display = "$f - $t";
                break;
            case "next_index":
                $this->class =   $row["next_index"] == "0000-00-00" ? "" : (strtotime($row["next_index"]) < time() + 86400 * 90 ? "text-bg-error" : "");
                $this->display = $row["next_index"] == "0000-00-00" ? "" : $row["next_index"];
                break;
            case "elavtal":
                $this->display = "<a href='/elmeters/{$row["clientid"]}/'>{$row["elavtal"]}</a>";
                $this->display = "<a href='/elmeters/{$row["clientid"]}/'>{$row["elavtal"]}</a>";
                break;
            case "files";
                $this->class = $row["valid_contract"] == 1 ? "text-bg-success" : ($row["valid_contract"] == 2 ? "text-bg-info" : "");
                $files = array();
                if (!empty($this->uploads[$row["clientid"]])) {
                    foreach ($this->uploads[$row["clientid"]] as $file) {
                        $files[] = "<a href='/storage/uploads/{$file}' target='_blank' title='{$file}'>" . substr($file, -3) . "</a>";
                    }
                    $this->display = implode(", ", $files);
                } else {
                    $this->display = "";
                }
                break;
            case "el_avg":
                $days = (strtotime($row["elinvoice_last"]) - strtotime($row["contract_from"])) / 86400;
                $avg = $row["elinvoice_kwh"] > 0 ? number_format($row["elinvoice_kr"] / $row["elinvoice_kwh"], 2, ".", "") : 0;
                $sumValue = $days > 0 ? number_format($avg * 365 * $row["elinvoice_kwh"] / $days, 2, ".", "") : "0";
                $this->display = $avg > 0 ? $this->num($avg, 2) . " kr" : "";
                break;
            case "elinvoice_last";
                $kr = $row["elinvoice_kr"];
                $old = strtotime($row["elinvoice_last"]) < (time() - 86400 * 60);
                $this->display = $kr == -1 ? "* Ingen mätare" : ($kr == -2 ? "* Avtal saknas" : ($kr == -3 ? "* Oklart" : $row["elinvoice_last"]));
                $this->class = $kr == -1 ? "text-bg-success" : ($kr == -2 ? "text-bg-info" : ($kr == -3 ? "text-bg-error" : ($old ? "text-bg-error" : "")));
                break;
            case "elinvoice_kwh";
                $days = (strtotime($row["elinvoice_last"]) - strtotime($row["contract_from"])) / 86400;
                $sumValue = $days > 0 ? number_format(365 * $row["elinvoice_kwh"] / $days, 2, ".", "") : "0";
                $this->display = $sumValue > 0 ? $this->num($sumValue, 0) . " kWh" : "";
                break;
            case "elinvoice_updated";
                $this->display = $row["elinvoice_kr"] >= 0 ? "<a href='/hyreslista/update_el/{$row["fortnox"]}/{$row["contract_from"]}/'>{$row["elinvoice_updated"]}</a>" : "";
                break;
            default:
                $this->display = $row[$key];
                break;
        }
        if ($this->displaySum && isset($sumValue)) {
            $this->sums[$row["active"]] = ($this->sums[$row["active"]] ?? 0) + $sumValue;
        } elseif ($this->displaySum) {
            $this->sums[$row["active"]] = ($this->sums[$row["active"]] ?? 0) + $row[$key];
        }
    }
}

$db = new Database();
$oneRow = $db->selectOne("hyreslista","id","1=1 LIMIT 1");
$columns = array();
foreach($oneRow as $col => $val){
    $columns[$col] = new Column(ucfirst($col), false, "", "", "", "", false);
}

$columns["clientid"] = new Column("#", false, "", "width:40px");
$columns["size"] = new Column("Yta", true, "", "", "text-align:right", " kvm", false);
$columns["monthly_exvat"] = new Column("Ex", true, "", "", "text-align:right", " kr", true);
$columns["inc"] = new Column("Inc", true, "", "", "text-align:right", " kr", true);
$columns["files"] = new Column("files", "", "", "", "", "", false, $contracts);
$columns["el_avg"] = new Column("Avg", true, "", "", "", " kr/år");
$columns["elinvoice_kwh"] = new Column("Kwh/år", true, "", "", "text-align:right", " kWh");
$columns["elinvoice_updated"] = new Column("Updated");
$columns["invoicefrequency"] = new Column("Freq");
$columns["yearly_fee_exvat"] = new Column("Ytor");
echo "<pre>";
//print_r($Hyreslista->uploads);
echo "</pre>";
?>