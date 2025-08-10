<?php

use Altahr\Database;
use Altahr\Fortnox;

class FortnoxEl
{
    private $DB;
    public $Fortnox;
    public $pdfFileName;

    function __construct()
    {
        $this->DB = new Database();
        $this->Fortnox = new Fortnox();
    }
    function insertDB()
    {
        $insert = ["date" => $_POST["invoiceDate"], "kwh" => $_POST["kwh"], "invoiceid" => $_POST["lastInvoice"], "clientid" => $_POST["clientid"]];
        $this->DB->insert("el_invoiced", $insert);
        //$this->ClientData->InvoiceList = $this->selectArray("el_invoiced", "id", "clientid = {$this->clientid} ORDER BY date DESC");
       // $this->createPDF();
    }
    function FortnoxUpdateInvoice(){
        $invoice = $this->updateInvoice($_POST["lastInvoice"]);
        $file = $this->upload("html/el/pdf/{$this->pdfFileName}");
        $this->attach($_POST["lastInvoice"], $file["File"]["ArchiveFileId"]);

        $this->insertDB();

        echo "<script>alert('Updated!');</script>";
    }
    function FortnoxInsertInvoice(){
        $invoice = $this->insertInvoice();
        $file = $this->upload("html/el/pdf/{$this->pdfFileName}");
        $this->attach($_POST["lastInvoice"], $file["File"]["ArchiveFileId"]);

        $this->insertDB();

        echo "<script>alert('Inserted!');</script>";
    }
    function getLastInvoice($fortnoxId)
    {
        return $this->Fortnox->curl("https://api.fortnox.se/3/invoices/?filter=unbooked&customernumber={$fortnoxId}");
    }

    public function upload($file)
    {
        $data = array();
        $data["file"] = curl_file_create($file);
        return ($this->Fortnox->curl("https://api.fortnox.se/3/inbox?path=inbox_kf", "UPLOAD", $data));
    }

    public function attach($invoice, $fileid)
    {
        $img1 = array();
        $img1["entityId"] = $invoice;
        $img1["entityType"] = "F";
        $img1["fileId"] = $fileid;       // Archive file id
        $img1["includeOnSend"] = true;
        $data = array();
        $data[] = $img1;
        return $this->Fortnox->curl("https://api.fortnox.se/api/fileattachments/attachments-v1/", "POST", $data);
    }

    public function updateInvoice($invoiceno)
    {
        $row = array();
        $row ["ArticleNumber"] = $_POST["vat"] == 25 ? 7 : 8;
        $row["DeliveredQuantity"] = $_POST["kwh"];
        $row["Description"] = "Elförbrukning";
        $row["Price"] = $_POST["price"];
        $row["Unit"] = "kWh";
        $invoice = $this->Fortnox->curl("https://api.fortnox.se/3/invoices/$invoiceno/");
        $invoice["Invoice"]["InvoiceRows"][] = $row;
        unset($invoice["Invoice"]["BasisTaxReduction"]);
        unset($invoice["Invoice"]["TaxReduction"]);
        unset($invoice["Invoice"]["TotalToPay"]);
        unset($invoice["Invoice"]["VoucherNumber"]);
        foreach ($invoice["Invoice"]["InvoiceRows"] as $key => $row) {
            unset($invoice["Invoice"]["InvoiceRows"][$key]["TotalExcludingVAT"]);
        }
        return  $this->Fortnox->curl("https://api.fortnox.se/3/invoices/$invoiceno/", "PUT", $invoice);
    }

    public function insertInvoice()
    {
        $invoice = array();
        $invoice["Invoice"] = array();
        $invoice["Invoice"]["Currency"] = "SEK";
        $invoice["Invoice"]["CustomerNumber"] = $_POST["customer"];
        $invoice["Invoice"]["InvoiceRows"] = array();
        $invoice["Invoice"]["InvoiceRows"]["0"] = array();
        $invoice["Invoice"]["InvoiceRows"]["0"] ["ArticleNumber"] = $_POST["vat"] == 25 ? 7 : 8;
        $invoice["Invoice"]["InvoiceRows"]["0"] ["DeliveredQuantity"] = $_POST["kwh"];
        $invoice["Invoice"]["InvoiceRows"]["0"] ["Description"] = "Elförbrukning";
        $invoice["Invoice"]["InvoiceRows"]["0"] ["Price"] = $_POST["price"];
        $invoice["Invoice"]["InvoiceRows"]["0"] ["Unit"] = "kWh";
        return ($this->Fortnox->curl("https://api.fortnox.se/3/invoices", "POST", $invoice));
    }
    function setFortnoxForm($array,$elavtal)
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

        return [$maxDocumentNumber, $invoiceDate];
    }
}

?>