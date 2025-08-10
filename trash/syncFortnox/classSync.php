<?php

use Altahr\Fortnox;
use Altahr\Database;

class Sync
{
    private $db, $fortnox;
    public $loginUrl;
    function __construct()
    {

        $this->db = new Database();
        $this->fortnox = new Fortnox();
        $this->loginUrl = $this->fortnox->loginUrl;
    }

    function hyreslista()
    {
        return $this->db->selectArray("sf_hyreslista", "clientid", "active=1 ORDER BY name");;
    }

    function getCustomerData()
    {
        $res = array();
        $array = [];
        $page = 1;
        while ($page < 3 && !isset($lastPage)) {
            $res = $this->fortnox->curl("https://api.fortnox.se/3/customers/?page=" . $page);
            foreach ($res["Customers"] as $customer) {
                $array[$customer["CustomerNumber"]] = $customer;
            }
            if ($res["MetaInformation"]["@TotalPages"] == $res["MetaInformation"]["@CurrentPage"]) {
                $lastPage = true;
            }
            $page++;
        }
        return [$res,$array];
    }
    function getLastInvoices()
    {
        $array = [];


        foreach ($this->db->selectArray("el_LastFortnoxInvoice","fortnoxid","1=1") as $fortnoxid => $invoice){
            $json = json_decode($invoice["json"],1);
            foreach($json["InvoiceRows"] as $row){
                //echo "<pre>";print_r($row);
                $temp = [];
                $temp["InvoiceDate"] = $json["InvoiceDate"];
                $temp["AccountNumber"] = $row["AccountNumber"];
                $temp["Description"] = $row["Description"];
                $temp["PriceExcludingVAT"] = $row["PriceExcludingVAT"];
                $temp["TotalExcludingVAT"] = $row["TotalExcludingVAT"];
                $temp["DeliveredQuantity"] = $row["DeliveredQuantity"];
                $temp["Unit"] = $row["Unit"];
                $temp["VAT"] = $row["VAT"];
                $array[$fortnoxid][] = $temp;
            };

        };
        //print_r($array);
        return [true,$array];
    }
    function crud()
    {
        if (isset($_POST["update_Email"])) {
            $row = $this->db->selectOne("sf_hyreslista", "clientid = {$_POST["clientid"]}");
            $emails = explode(",", $row["email"]);
            $emails[] = $_POST["update_Email"];
            foreach ($emails as $key => $email) {
                $emails[$key] = trim($email);
            }
            foreach ($emails as $key => $email) {
                if ($email == "") {
                    unset($emails[$key]);
                }
            }
            $emails = array_unique($emails);
            $update = ["email" => implode(",", $emails)];
            $this->db->update("sf_hyreslista", $update, " clientid = {$_POST["clientid"]} LIMIT 1");
        } else if (isset($_POST["update_Phone"])) {
            $row = $this->db->selectOne("sf_hyreslista", "clientid = {$_POST["clientid"]}");
            $telephones = explode(",", $row["telephone"]);
            $telephones[] = $_POST["update_Phone"];
            foreach ($telephones as $key => $telephone) {
                $telephones[$key] = trim($telephone);
            }
            foreach ($telephones as $key => $telephone) {
                if ($telephone == "") {
                    unset($telephones[$key]);
                }
            }
            $telephones = array_unique($telephones);
            $update = ["telephone" => implode(",", $telephones)];
            $this->db->update("sf_hyreslista", $update, " clientid = {$_POST["clientid"]} LIMIT 1");
        } else if (isset($_POST["updateLast"])) {
            $array = [];
            $show = "avtal";
            $one = $this->db->selectOne("el_LastFortnoxInvoice", "fortnoxid=" . $_POST["fortnox"]);
            if ($one !== false && isset($saved["InvoiceDate"])) {
                $saved = json_decode($one["json"], 1);
                $from = $saved["InvoiceDate"];
            } else {
                $from = date("Y-m-d", time() - 86400 * 100);
            }
            $f = "https://api.fortnox.se/3/invoices/?customernumber={$_POST["fortnox"]}&fromdate=" . $from;
            echo $f;
            $res = $this->fortnox->curl($f);
            $url = "";
            foreach ($res["Invoices"] as $invoice) {
                if ($invoice["InvoiceType"] == "AGREEMENTINVOICE" && $invoice["booked"] = 1) {
                    $url = $invoice["@url"];
                }
            }
            if (!empty($url)) {
                $res = $this->fortnox->curl($url);
                $json = json_encode($res["Invoice"]);
                $this->db->query("INSERT INTO el_LastFortnoxInvoice (fortnoxid, json) 
        VALUES ('{$_POST["fortnox"]}', '{$json}') 
        ON DUPLICATE KEY UPDATE json = '{$json}'");
            }
        }
    }
}

?>