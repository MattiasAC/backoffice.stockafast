<?php
session_start();
function myErrorHandler($errno, $errstr, $errfile, $errline) {
    $data = date("M-d H:i:s").": ".$errfile. "(".$errline.") ".$errstr;
    echo "<div style='color:red'>$data</div>";
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_error_handler("myErrorHandler");

require_once "../../code/db.php";
require_once('../../vendor2/fpdf184/fpdf.php');

class PDF extends FPDF
{
        private function latin($utf8)
    {
        return mb_convert_encoding($utf8, "ISO-8859-1", "UTF-8");
    }
    function table($data,$fields){
        $human["active"]["1"] = "";
        $human["active"]["3"] = "Intresserade, ej underskrivet";
        $human["active"]["4"] = "Extra";
        $human["active"]["5"] = "Gratis";

        // Colors, line width and bold font
        $this->SetFillColor(100,100,100);
        $this->SetTextColor(255);
        $this->SetDrawColor(0,0,0);
        $this->SetLineWidth(.1);
        $this->SetFont('Arial','',9);

        // Header
        $w = array(5,55,40,20,20,25,20,20,20,60,30,30,30,30,30,30,30,30);

        $cell = 0;
        foreach($fields as $field){
            $parts = explode(":",$field);
            $this->Cell($w[$cell],7,$this->latin($parts[0]),0,0,'L',true);
            $cell ++;
        }
        $this->Ln();

        // Color and font restoration
        $this->SetFillColor(224,224,224);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = false;
        $yearly = 0;
        $rowNumber = 0;
        foreach($data as $row)
        {
       
            $rowNumber ++;
            $cell = 0;
            $year = $row["monthly_exvat"] * 12 + $row["yearly_fee_exvat"];
            $yearly += $year;
            foreach($fields as $field){
                $parts = explode(":",$field);
                $display = $parts[1];
                if(isset($row[$parts[1]]) && isset($human[$parts[1]][$row[$parts[1]]])){
                    $display = $human[$parts[1]][$row[$parts[1]]];
                }
                else if(isset($row[$parts[1]])){
                    $display = $row[$parts[1]];
                }else{
                    $display = $rowNumber;
                }

                if($parts[0] == "Yta (kr/kvm/år)"){

                    $kvmpris = round($year/$row[$parts[1]]);
                    $display = $row[$parts[1]]. "kvm (".$kvmpris." kr/kvm/år)";
                }
                elseif($parts[0] == "Yta"){
                                       if(empty($row[$parts[1]])){
                        print_r($row);
                    }
                    $kvmpris = empty($row[$parts[1]]) ? 0: round($year/$row[$parts[1]]);
                    $display = $row[$parts[1]]. " kvm";
                }
                elseif($parts[0] == "Total momsyta"){
                    $totytamoms = isset($totytamoms) ? $totytamoms : 0;
                    if($row["vat"] == 25){
                        $totytamoms += empty($row["size"]) ? 0 : $row["size"];
                    }else{
                        $this->SetTextColor(150,150,150);
                    }
                    $display = "{$totytamoms} kvm";
                }
                elseif($parts[0] == "Total omomsad yta"){
                    $totytaomoms = isset($totytaomoms) ? $totytaomoms : 0;
                    if($row["vat"] == 0){
                        $totytaomoms += empty($row["size"]) ? 0 : $row["size"];
                    }else{
                        $this->SetTextColor(150,150,150);
                    }
                    $display = "{$totytaomoms} kvm";
                }
                elseif($parts[0] == "Tot per år moms"){
                    $totmoms = isset($totmoms) ? $totmoms : 0;
                    if($row["vat"] == 25){
                        $totmoms += $row["monthly_exvat"] * 12 + $row["yearly_fee_exvat"];
                    }else{
                        $this->SetTextColor(150,150,150);
                    }
                    $display = "{$totmoms} kr";
                }
                elseif($parts[0] == "Tot per år ej moms"){
                    $totomoms = isset($totomoms) ? $totomoms : 0;
                    if($row["vat"] == 0){
                        $totomoms += $row["monthly_exvat"] * 12+ $row["yearly_fee_exvat"];;
                    }else{
                        $this->SetTextColor(150,150,150);
                    }
                    $display = "{$totomoms} kr";
                }
                elseif($parts[0] == "Uppsägning efter kontraktsslut"){
                    if(strpos($row[$parts[1]],"/") !== false){
                        $p = explode("/",$row[$parts[1]]);
                        $display = $p[0]. " m. före kontrakt, annars ".$p[1]." m. förl.";
                    }elseif(is_numeric($row[$parts[1]])){
                        $display = $row[$parts[1]]. " månader";
                    }else{
                        $display = "";
                    }

                }elseif($parts[0] == "Kontrakt till"  || $parts[0] == "Kontrakt från" ){
                    if($row[$parts[1]] == "0000-00-00"){
                        $display = "";
                    }

                }
                $this->Cell($w[$cell],6,$this->latin($display),0,0,'L',$fill);
                $this->SetTextColor(0,0,0);
                $cell ++;
            }

            $this->Ln();
            $fill = !$fill;
        }
        // Closing line
        $this->Cell(array_sum($w),0,'','T');
        $this->SetFont('Arial','B',13);
        $this->Ln();
        $this->Cell(array_sum(array_slice($w,0,sizeof($fields),true)),6,$this->latin("Årlig omsättning ".number_format($yearly,0,"."," ")." kr exklusive moms"),0,0,'C',$fill);

        $this->SetFont('Arial','I',13);
        $summera_moms = false;
        if($summera_moms){
            $fill = !$fill;
            $this->Ln();
            $this->Cell(array_sum($w),6,utf8_decode("Momsfördelning yta ".number_format((100* $totytamoms) / ($totytamoms+$totytaomoms),2,"."," ")."% moms"),0,0,'R',$fill);
        }
        if($summera_moms){
            $fill = !$fill;
            $this->Ln();
            $this->Cell(array_sum($w),6,utf8_decode("Momsfördelning omsättning ".number_format((100* $totmoms) / ($totmoms+$totomoms),2,"."," ")."% moms"),0,0,'R',$fill);
        }

    }

}
class mypdf extends db
{
    public function __construct()
    {
        parent::__construct();


        $data = $this->selectArray("sf_hyreslista","clientid","active in (".$_GET["ids"].") ORDER BY monthly_exvat DESC");
        $fields = array(":nr","Namn:name",'Verksamhet:activity',"Yta:size","Hyra ex.m:monthly_exvat","Årsavgift ex.m:yearly_fee_exvat","Momssats:vat","Total momsyta:vat","Total omomsad yta:vat","Tot per år moms:vat","Tot per år ej moms:vat");

        // Bank
        $fields = array(":nr","Namn:name",'Verksamhet:activity',"Yta:size","Hyra ex.m:monthly_exvat","Årsavgift ex.m:yearly_fee_exvat","Momssats:vat","Kontrakt från:contract_from","Kontrakt till:contract_to","Uppsägning efter kontraktsslut:cancellation");
        $pdf = new PDF();
        $pdf->AddPage("L");
        $pdf->table($data,$fields);
        $pdf->Output();
    }
}
$pdf = new mypdf();
?>