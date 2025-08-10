<?php
if (!defined("stammisen")) {
    die("unauthorized");
}

class Contract extends db
{
    public $pre;
    public $edit;
    public $contracts;

    public function __construct()
    {
        parent::__construct();
        $this->edit = !empty($_GET["b"]) ? $_GET["b"] : 0;
        if (isset($_POST["insert"])) {
            $this->insertContract();
        } elseif (isset($_POST["update"])) {
            $this->updateContract();
        } elseif (isset($_POST["duplicate"])) {
            $this->duplicateContract();
        } elseif (isset($_POST["delete"])) {
            $this->deleteContract();
        }
        if (!empty($_POST["id"])) {
            $this->setPre();
        }
    }

    public function newmap()
    {
        $return = array();
        $data = file_get_contents('html/contract/templates/'.$this->pre["template"]);
        $paragraphs = explode("*",$data);

        foreach($paragraphs as $paragraph){
            $rows = explode("\n",$paragraph);

            $temp["header"] = "";
            $temp["default"] = "";
            foreach($rows as $row){
                if(!empty(str_replace(array("\r", "\n"), '', $row))){
                    if(empty($temp["header"])){
                        $temp["header"] = $row;
                    }else{
                        $temp["default"] .= $row;
                    }
                }
            }
            
            $return[]=$temp;
        }
        
        return $return; 
    }
    

    public function selectExisting()
    {
        $contracts = $this->selectArray("contracts", "id", "1=1");
        $r = "<select name='id' class='form-control' onchange='submit();'>";
        $r .= "<option value='0'>Välj befintlig</option>";
        foreach ($contracts as $id => $data) {
            $sel = isset($this->pre["saveas"]) && $this->pre["saveas"] == $data["saveas"] ? "selected" : "";
            $r .= "<option $sel value='{$data["id"]}'>[{$id}] {$data["saveas"]}</option>";
        }
        $r .= "</select>";
        return $r;
    }

    private function insertContract()
    {
        $insert = array();
        $insert["name"] = $_POST["newname"];
        $insert["saveas"] = $_POST["newname"];
        $this->insert("contracts", $insert);
        $_POST["id"] = $this->insertid();
    }

    private function duplicateContract()
    {
        $sql = "INSERT INTO contracts(template,saveas,name,address,zipcity,org,pnr,email,image,contact,spacing,tel1,tel2) 
        SELECT concat(saveas,'_new') AS template,saveas,name,address,zipcity,org,pnr,email,image,contact,spacing,tel1,tel2 FROM contracts WHERE id = {$_POST["id"]}";
        $this->query($sql);
        $last = $this->insertid();
        echo $last;
        $sql = "INSERT INTO contracts_paragraphs(id,contracts_paragraphs.key,text)
        SELECT '$last' AS id,contracts_paragraphs.key,text FROM contracts_paragraphs WHERE id = {$_POST["id"]}";
        $this->query($sql);
    }

    private function deleteContract()
    {
        $this->delete("contracts_paragraphs", "id = {$_POST["id"]}");
        $this->delete("contracts", "id = {$_POST["id"]}");
        echo "Deleted";
        unset($_POST["id"]);
    }

    private function updateContract()
    {
        foreach ($_POST["paragraph"] as $id => $value) {
           // if($value != "-"){
                $sql = "INSERT INTO contracts_paragraphs SET contracts_paragraphs.id = {$_POST["id"]}, contracts_paragraphs.key ='{$id}', contracts_paragraphs.text= '{$value}' ON DUPLICATE KEY UPDATE contracts_paragraphs.text= '{$value}';";
                $this->query($sql);
           // }

        }
        $id = $_POST["id"];
        unset($_POST["id"]);
        unset($_POST["update"]);
        unset($_POST["paragraph"]);
        if (!empty($_FILES['image']['name'])) {
            $parts = explode(".", $_FILES['image']['name']);
            $filename = "client_{$id}." . $parts[1];
            $path = "storage/contracts/" . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $path)) {
                echo "The file {$filename} has been uploaded";
                $_POST["image"] = $filename;
            }
        }
        $this->update("contracts", $_POST, "id = {$id}");
        $_POST["id"] = $id;
    }

    private function setPre()
    {
        $this->pre = $this->selectOne("contracts", "id=" . $_POST["id"]);
        $this->pre["paragraph"] = $this->selectArray("contracts_paragraphs", "key", "id=" . $_POST["id"]);
        $this->printPDF($_POST["id"]);
    }

    public function getFileName()
    {
        $replace = array();
        $replace["å"] = "a";
        $replace["ä"] = "a";
        $replace["ö"] = "o";
        $replace[" "] = "_";
        if (!empty($this->pre["name"])) {
            $name = strtolower($this->pre["name"]);
        } else {
            $name = strtolower($this->pre["contact"]);
        }
        $name = strtr($name, $replace);
        return $name . ".pdf";
    }

    public function printPDF($id)
    {
        //require_once('vendor2/fpdf184/fpdf.php');
        //require_once('vendor2/FPDI-2.3.3/src/autoload.php');
        require_once('pdf_contract.php');
        //$filename = "contract_{$id}.pdf";
        $filename = $this->getFileName();


        $pdf = new PDF();
        $pdf->AliasNbPages();
        $pdf->print($this->pre, $this->newmap());
        $pdf->Output("./storage/invoice_pdf/{$filename}", "F");
    }
}
