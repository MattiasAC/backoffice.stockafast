<?php
if (!defined("stammisen")) {
    die("unauthorized");
}

class MeterData
{
    public $List;
    public $All;
    public $Name;
    public $Internal;
    public $MeterId;
    public $Brand;
    public $Branch;
    public $Rooms;
    public $ShowFrom;
    public $FirstDate;
    public $LastDate;
    public $FirstValue;
    public $LastValue;
    public $Kwh;
}

class ClientData
{
    public $InvoiceList;
    public $ClientName;

    public $Measured = 0;
    public $Invoiced = 0;
    public $ToInvoice = 0;

    public $FirstDate;
    public $LastDate;
    public $Kwh;
}

class Elmeters extends db
{
    private $toInvoice = 0;
    public $clientid, $client, $clients;
    public $invoiced, $Metersdata, $showTo, $spot, $dataExist, $ClientData, $pdfFileName;
    public $clientMeters = array();

    function __construct()
    {
        parent::__construct();
        $this->clientid = isset($_GET["b"]) ? $_GET["b"] : 60;
        $this->crud();
        $this->clients = $this->selectArray("sf_hyreslista", "clientid", "rooms !='' ORDER BY name");
        foreach($this->clients as $clientid => $client){
            $rooms = explode(",",$client["rooms"]);
            $autoreading = false;
            foreach($rooms as $room){
                $meters = $this->selectArray("el_meters","meterid", "rooms ='$room' OR rooms LIKE '{$room},%' OR rooms like '%,{$room}' OR rooms like '%,{$room},%'");
                foreach($meters as $meterid => $meter){
                    $autoreading = $meter["reading"];
                    if(in_array($meter["reading"],["Elvaco","Logger1010"])){
                        $autoreading = true;
                    }

                }

            }
            $this->clients[$clientid]["autoreading"] = $autoreading;
        }

        $this->client = $this->clients[$this->clientid];
        $this->pdfFileName = "el_{$this->client["name"]}.pdf";
        $search = array();
        if (!empty($this->client["rooms"])) {
            foreach (explode(",", $this->client["rooms"]) as $lokal) {
                $search[] = $lokal;
            }
        } else {
            return;
        }
        $conditions = array_map(fn($val) => "FIND_IN_SET('$val', rooms)", $search);
        $init = date("d") > 15 ?
            date("Y-m-d", mktime(0, 0, 0, date("m") + 1, 1, date("Y"))) :
            date("Y-m-d", mktime(0, 0, 0, date("m"), 1, date("Y")));
        $this->showTo = isset($_POST["showTo"]) ? $_POST["showTo"] : (isset($_SESSION["showTo"]) ? $_SESSION["showTo"] : $init);
        $_SESSION["showTo"] = $this->showTo;
        $this->ClientData = new ClientData();
        $this->ClientData->ClientName = $this->client["name"];
        $this->ClientData->InvoiceList = $this->selectArray("el_invoiced", "id", "clientid = {$this->clientid} ORDER BY date DESC");
        if(count($this->ClientData->InvoiceList) > 0){
            $this->ClientData->FirstDate = end($this->ClientData->InvoiceList)["date"];
            $this->ClientData->LastDate = reset($this->ClientData->InvoiceList)["date"];
            $this->ClientData->Invoiced = array_sum(array_column($this->ClientData->InvoiceList, 'kwh'));

        }
        $meters = $this->selectArray("el_meters", "meterid", implode(" OR ", $conditions));
        if (empty($meters)) {
            return;
        }
        foreach ($meters as $meterid => $meter) {
            $list = $this->selectArray("el_measured", "id", "datetime >= '{$meter["showfrom"]}' AND datetime <= '{$this->showTo} 23:59:59' AND meterid = {$meterid} ORDER BY datetime DESC");
            $all= $this->selectArray("el_measured", "id", "datetime >= '{$meter["showfrom"]}' AND meterid = {$meterid} ORDER BY datetime DESC");
            $this->Metersdata[$meterid] = new MeterData();
            $this->Metersdata[$meterid]->Name = $meter["name"];
            $this->Metersdata[$meterid]->MeterId = $meter["meterid"];
            $this->Metersdata[$meterid]->Brand = $meter["brand"];
            $this->Metersdata[$meterid]->Internal = $meter["name_internal"];
            $this->Metersdata[$meterid]->Branch = $meter["branch"];
            $this->Metersdata[$meterid]->Rooms = $meter["rooms"];
            $this->Metersdata[$meterid]->ShowFrom = $meter["showfrom"];
            $this->Metersdata[$meterid]->List = $list;
            $this->Metersdata[$meterid]->All = $all;
            $this->Metersdata[$meterid]->FirstDate = empty($list) ? "0000-00-00":end($list)["datetime"];
            $this->Metersdata[$meterid]->FirstValue = empty($list) ? 0: end($list)["value"];
            $this->Metersdata[$meterid]->LastDate = empty($list) ? "0000-00-00": reset($list)["datetime"];
            $this->Metersdata[$meterid]->LastValue = empty($list) ? 0: reset($list)["value"];
            $this->Metersdata[$meterid]->Kwh = round($this->Metersdata[$meterid]->LastValue - $this->Metersdata[$meterid]->FirstValue);
            $this->ClientData->Measured += $this->Metersdata[$meterid]->Kwh;
            //echo "<pre>";print_r($this->Metersdata[$meterid]->List);
        }

        $this->ClientData->ToInvoice += round($this->ClientData->Measured - $this->ClientData->Invoiced);
        $y = date("Y", strtotime($this->client["contract_from"]));
        $m = date("m", strtotime($this->client["contract_from"]));
        $this->spot = $this->selectArray("sf_el_spot", "id", "(year > $y) OR (year = $y AND month >= $m) ORDER BY year DESC, month DESC LIMIT 10");
        $this->createPDF();
    }

    function crud()
    {
        print_r($_GET);
        if (isset($_POST["addMeasure"])) {
            $core = ["datetime" => "", "value" => "", "type" => "", "meterid" => ""];
            $this->insert("el_measured", array_intersect_key($_POST, $core));
            $this->uploadImage($_POST["meterid"], $this->insertid(), $_POST["datetime"]);
        } elseif (isset($_POST["updateMeasure"])) {
            $core = ["datetime" => "", "value" => "", "type" => "", "meterid" => ""];
            $this->update("el_measured", array_intersect_key($_POST, $core), "id={$_POST["id"]}");
            $this->uploadImage($_POST["meterid"], $_POST["id"], $_POST["datetime"]);
        } elseif (isset($_GET["c"]) && $_GET["c"] == "delete_i") {
            $this->delete("el_invoiced", "id='{$_GET["d"]}'");
        } elseif (isset($_POST["deleteMeasure"])) {
            $this->delete("el_measured", "id='{$_POST["id"]}'");
        } elseif (isset($_POST["deleteImage"])) {
            unlink("./storage/el_reported/{$_POST["meterid"]}/{$_POST["del_img"]}");
        } else if (isset($_POST["addSPOT"])) {
            $this->query("INSERT  INTO sf_el_spot(year,month,se4) VALUES('{$_POST["year"]}','{$_POST["month"]}','{$_POST["se4"]}') ON DUPLICATE KEY UPDATE se4 = '{$_POST["se4"]}'");
        }
    }

    function setImages()
    {
        $images = array();
        foreach ($this->Metersdata as $meterid => $Meter) {
            $directory = "storage/el_reported/" . $meterid;
            $files = file_exists($directory) ? array_diff(scandir($directory), array('..', '.')) : array();
            foreach ($files as $image) {
                list($id, $nr, $date) = explode("_", $image);
                $date = substr($date, 0, strpos($date, "."));
                $date = date("ymd", strtotime($date));

$images[$date . base64_encode(uniqid())] = "
<div class='card' style='width:110px;float:left;border:1px solid #CCC;margin:2px;'>
<div class='card-header'>
<input type='hidden' name='meterid' value='{$meterid}'>
<input type='hidden' name='del_img' value='{$image}'>
$date <input type='submit' class='btn-danger' name='deleteImage' value='X'> 
</div>
<div class='body'>
<a href='https://admin.altahr.se/storage/el_reported/{$meterid}/{$image}' target='_blank'>
<img src='https://admin.altahr.se/storage/el_reported/{$meterid}/{$image}' class='img-thumbnail' style='border:1px solid #000;width: 100px; height:100px; object-fit: cover'></a>
</div>
</div>";
            }
            krsort($images);
        }
        return $images;
    }

    function spot()
    {
        return $this->selectArray("sf_el_spot", "id", "1=1 ORDER BY year DESC,month DESC");
    }

    function insertInvoice()
    {
        $insert = ["date" => $_POST["invoiceDate"], "kwh" => $_POST["kwh"], "invoiceid" => $_POST["lastInvoice"], "clientid" => $_POST["clientid"]];
        $this->insert("el_invoiced", $insert);
        $this->ClientData->InvoiceList = $this->selectArray("el_invoiced", "id", "clientid = {$this->clientid} ORDER BY date DESC");
        $this->createPDF();
    }

    function uploadImage($meterid, $measureid, $date)
    {
        if (isset($_FILES["image"])) {
            $filename = $_FILES["image"]["name"];
            if (strpos($filename, ".") !== false) {
                $ext = substr($filename, strpos($filename, "."));
                if (!file_exists("storage/el_reported/" . $meterid)) {
                    mkdir("storage/el_reported/" . $meterid);
                }
                $count = 1;
                $new_name = $measureid . "_{$count}_" . $date . $ext;
                while (file_exists("storage/el_reported/" . $meterid . "/" . $new_name)) {
                    $count++;
                    $new_name = $measureid . "_{$count}_" . $date . $ext;
                }
                $destination = "storage/el_reported/$meterid/$new_name";
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $destination)) {
                    echo "Image uploaded " . $destination;
                }
            }
        }
    }

    function setFortnoxForm($array)
    {
        $price = 0;
        $maxDocumentNumber = 0;
        $invoiceDate = date("Y-m-d");
        if (isset($array["Invoices"]) && !empty($array["Invoices"])) {
            foreach ($array["Invoices"] as $invoice) {
                if ($invoice["DocumentNumber"] > $maxDocumentNumber) {
                    $maxDocumentNumber = $invoice["DocumentNumber"];
                    // $invoiceDate = $invoice["InvoiceDate"];
                }
            }
        }
        if ($this->client["elavtal"] == 1.50) {
            $price = 1.50;
        } elseif (strpos($this->client["elavtal"], "spot") !== false) {
            foreach ($this->spot as $spot) {
                if ($spot["year"] == date("Y", strtotime($this->showTo) - 86400 * 15) && $spot["month"] == date("m", strtotime($this->showTo) - 86400 * 15)) {
                    $price = number_format(($spot["se4"] + str_replace("spot+", "", $this->client["elavtal"])) / 100, 2, ".", "");
                }
            }
        }
        return [$maxDocumentNumber, $invoiceDate, $price];
    }

    function createPDF()
    {
        $pdf = new PDF();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $numItems = count($this->Metersdata);
        if ($numItems == 1) {
            $w = 35;
            $col = 67;
        } elseif ($numItems == 2) {
            $w = 18;
            $col = 67;
        }else{
            $w = 18;
            $col = 67;
        }
        $meterCount = 1;
        $maxY = 0;
        foreach ($this->Metersdata as $meterid => $Meter) {
            if($meterCount == 4){
                $pdf->setLeftMargin(10);
                $pdf->setY($maxY);
                $pdf->ln();
            }
            $pdf->printEl(
                array_reverse($Meter->List),
                //Altahr::latin($Meter->Name),
                $Meter->Name,
                [$w, $w, $w, $w]);
            $maxY = max($maxY, $pdf->getY());
            if ($meterCount !== $numItems) {
                $pdf->setY(10);
                $pdf->setLeftMargin($meterCount * $col + 10);
                $pdf->setX($meterCount * $col + 10);
            } else {
                $pdf->setLeftMargin(10);
                $pdf->setY($maxY);
                $pdf->ln();
            }
            $meterCount++;
        }
        $pdf->printSummery();
        $pdf->ln();
        $pdf->printInvoiced($this->ClientData->InvoiceList);
        foreach ($this->Metersdata as $meter) {
            if (strpos($this->client["elavtal"], "spot") !== false && !isset($one)) {

                $pdf->ln();
                $pdf->printSpot($this->spot, str_replace("spot+", "", $this->client["elavtal"]));
                $one = true;
            }
        }
        $pdf->Output("./html/elmeters/pdf/{$this->pdfFileName}", "F");
    }
}