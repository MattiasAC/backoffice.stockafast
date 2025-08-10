<?php
class Kontrakt extends db
{
    public $contracts, $clients, $items;

    public function __construct()
    {
        parent::__construct();
        $this->setDB("hyrenmaskin");
        if (isset($_POST["add_client"])) {
            $this->addClient();
        } elseif (isset($_POST["update_client"])) {
            $this->addClient($_POST["clientid"]);
        }elseif (isset($_POST["add_kontrakt"])) {
            $this->addKontrakt();
        } elseif (isset($_POST["update_kontrakt"])) {
            $this->addKontrakt($_POST["contractid"]);
        } elseif (isset($_GET["c"]) && $_GET["c"] == "deleteClient") {
            $this->deleteOne("clients", "clientid=" . $_GET["d"]);
        } elseif (isset($_GET["c"]) && $_GET["c"] == "deleteContract") {
            $this->deleteOne("contracts", "contractid=" . $_GET["d"]);
        }
        $this->contracts = $this->selectArray("contracts", "contractid", "1=1");
        $this->clients = $this->selectArray("clients", "clientid", "1=1");
        $this->items = $this->selectArray("items", "itemid", "1=1");
        if (isset($_GET["c"]) && $_GET["c"] == "printPDF") {
            $this->printPDF($_GET["d"]);
        }
    }

    private function addClient($clientid = false)
    {
        $keys = ['firstname', 'lastname', 'personnummer', 'email', 'telephone'];
        $missing = array_diff_key(array_flip($keys), $_POST);
        if (!empty($missing)) {
            trigger_error("Missing keys");
            return;
        }
        $data = array_intersect_key($_POST, array_flip($keys));
                if($clientid){
            $this->update("clients", $data, "clientid=$clientid");
        } else{
            $this->insert("clients", $data);
        }
    }

    private function addKontrakt($contractid = false)
    {
        $keys = ['clientid', 'date_start', 'date_end', 'items', 'discount','information'];
        $missing = array_diff_key(array_flip($keys), $_POST);
        if (!empty($missing)) {
            trigger_error("Missing keys " . array_key_first($missing));
            return;
        }
        $data = array_intersect_key($_POST, array_flip($keys));
        $data["items"] = json_encode($data["items"]);
        if($contractid){
            $this->update("contracts", $data, "contractid=$contractid");
        } else{
            $this->insert("contracts", $data);
        }

    }

    public function items()
    {
        return $this->selectArray("items", "itemid", "1=1");
    }

    public function option_clients($pre = false)
    {
        $clients = $this->selectArray("clients", "clientid", "1=1");
        $return = "";
        foreach ($clients as $clientid => $client) {
            $sel = $pre == $clientid ? "selected=\"selected\"" : "";
            $return .= "<option value='{$clientid}' $sel>{$client["firstname"]} {$client["lastname"]} {$client["email"]}</option>";
        }
        return $return;
    }

    public function printPDF($contractid)
    {
//        require_once('vendor2/fpdf184/fpdf.php');
 //       require_once('vendor2/FPDI-2.3.3/src/autoload.php');
        require_once('pdf_hyrenmaskin.php');
        $filename = "hyrenmaskin.pdf";
        $pdf = new PDF();
        $pdf->AliasNbPages();

        $contract = $this->contracts[$contractid];
        $client = $this->clients[$contract["clientid"]];
        $rented = array();
        foreach(json_decode($contract["items"],1) as $itemid){
            $rented[] = $this->items[$itemid];
        }

        print_r($rented);

        $pdf->print($contract, $client,$rented);
        $pdf->Output("./storage/invoice_pdf/{$filename}", "F");
    }
}
