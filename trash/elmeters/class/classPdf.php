<?php
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

class PDF extends Fpdi
{

    public $used;
    public $last_measured;
    public $paid;

    function Latin($text){
        return mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
    }
    function printSummery()
    {
        $this->SetFont('Arial', '', 8);
        $this->Cell(90, 5, $this->Latin("Total elförbrukning till och med " . substr($this->last_measured, 0, 10) . ": " . number_format($this->used, 0, ".", " ") . " kWh", 0, 1, 'l', 0));
        $this->ln();
    }

    function printSpot($array, $extra)
    {
        $array = array_slice($array, 0, 3);
        $a = 35;
        $b = 35;
        $c = 35;
        $d = 35;
        $e = 35;
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(200, 200, 200);
        $this->Cell($a, 5, "SPOTPRIS SE4", 0, 1, 'l', false);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell($a, 5, mb_convert_encoding("Månad", 'ISO-8859-1', 'UTF-8'), 0, 0, 'l', true);
        $this->Cell($b, 5, "Spotpris", 0, 0, 'l', true);
        $this->Cell($c, 5, mb_convert_encoding("Påslag", 'ISO-8859-1', 'UTF-8'), 0, 0, 'l', true);
        $this->Cell($d, 5, "Ert pris", 0, 0, 'l', true);
        $this->ln();
        //$this->SetFont('Arial','b',8);
        foreach ($array as $key => $row) {

            $this->SetFont('Arial', '', 8);
            $date = mktime(0, 0, 0, $row["month"], 15, $row["year"]);
            $yours = number_format($extra + $row["se4"], 2, ".", "");
            $this->Cell($a, 5, date("F Y", $date), 0, 0, 'l', true);
            $this->Cell($b, 5, mb_convert_encoding($row["se4"] . " öre/kWh", 'ISO-8859-1', 'UTF-8'), 0, 0, 'l', true);
            $this->Cell($b, 5, mb_convert_encoding($extra . " öre/kWh", 'ISO-8859-1', 'UTF-8'), 0, 0, 'l', true);
            $this->Cell($b, 5, mb_convert_encoding($yours . " öre/kWh", 'ISO-8859-1', 'UTF-8'), 0, 0, 'l', true);
            $this->ln();
            $this->SetFont('Arial', '', 8);
        }    
    }

    function printInvoiced($array)
    {
        $a = 35;
        $b = 35;
        $c = 35;
        $this->SetFont('Arial', 'B', 8);
        $this->Cell($a, 5, "FAKTUROR", 0, 1, 'l', false);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell($a, 5, "Datum", 0, 0, 'l', false);
        $this->Cell($b, 5, "Fakturanr", 0, 0, 'l', false);
        $this->Cell($c, 5, "Fakturerad el", 0, 0, 'l', false);
        $this->ln();
        $this->SetFont('Arial', '', 8);
        $sum = 0;
        $count = 0;
        $countrows = sizeof($array);
        $showrows = 12;
        $shorten = $countrows > 15;
        foreach ($array as $key => $row) {
            $count++;
            if (!$shorten || $count >= ($countrows - $showrows) || $count == 1) {
                if ($row["date"] == date("Y-m-d")) {
                    $this->SetFont('Arial', 'B', 8);
                    $this->SetTextColor(55, 112, 64);
                }
                $this->Cell($a, 5, $row["date"], 0, 0, 'l', false);
                $this->Cell($a, 5, mb_convert_encoding($row["invoiceid"], 'ISO-8859-1', 'UTF-8'), 0, 0, 'l', false);
                $this->Cell($a, 5, $row["kwh"] . " kWh", 0, 0, 'l', false);
                $this->ln();
                if ($count == 1 && $shorten && $countrows > $showrows) {
                    $this->Cell($a, 5, "---", 0, 0, 'l', false);
                    $this->Cell($a, 5, "---", 0, 0, 'l', false);
                    $this->Cell($a, 5, "---", 0, 0, 'l', false);
                    $this->ln();
                }
            }
            $sum += $row["kwh"];
            $this->SetFont('Arial', '', 8);
            $this->SetTextColor(0, 0, 0);
        }
        $this->SetFont('Arial', '', 8);
        $this->ln();
        $this->Cell(90, 5, $this->Latin("Totalt fakturerat: " . number_format($sum, 0, ".", " ") . " kWh (Diff: " . number_format($sum - $this->used, 0, ".", " ") . "kWh) ", 0, 1, 'l', 0));
        $this->ln();
        /*
     $this->SetFont('Arial','B',8);
     $this->Cell($a,5,"",0,0,'l',false);
     $this->Cell($a,5,"Totalt     ",0,0,'R',false);
     $this->Cell($a,5,-$sum." kWh",0,0,'l',false);
     $this->paid = $sum;
     $this->ln(); */
    }

    function printEl($array, $header, $pos)
    {
        $header = $this->latin($header);
        list($a, $b, $c, $d) = $pos;
        $this->SetFont('Arial', 'B', 8);
        $this->Cell($a, 5, $header, 0, 1, 'l', false);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell($a, 5, "Datum", 0, 0, 'l', false);
        $this->Cell($b, 5, mb_convert_encoding("Mätarställn.", 'ISO-8859-1', 'UTF-8'), 0, 0, 'l', false);
        $this->Cell($c, 5, mb_convert_encoding("Förbrukning", 'ISO-8859-1', 'UTF-8'), 0, 0, 'l', false);
        $this->ln();
        $this->SetFont('Arial', '', 8);

        $count = 0;
        $countrows = sizeof($array);
        $showrows = 12;
        $shorten = $countrows > 15;
        $used =0;
        foreach ($array as $key => $row) {
            $count++;
            $value = number_format($row["value"], 0, ".", "");
            $init = isset($init) ? $init : $value;
            $used = number_format($row["value"] - $init, 0, ".", "");
            if (!$shorten || $count >= ($countrows - $showrows) || $count == 1) {
                $this->Cell($a, 5, substr($row["datetime"], 0, 10), 0, 0, 'l', false);
                $this->Cell($b, 5, $value . " kWh", 0, 0, 'l', false);
                $this->Cell($c, 5, $used . " kWh", 0, 0, 'l', false);
                if ($row["type"] == 3) {
                    $this->SetFont('Arial', '', 8);
                    $this->SetTextColor(138, 19, 15);
                    $this->Cell($d, 5, "*Uppskattat värde", 0, 0, 'l', false);
                    $this->SetTextColor(0, 0, 0);
                }
                $this->ln();
                if ($count == 1 && $shorten && $countrows > $showrows) {
                    $this->Cell($a, 5, "---", 0, 0, 'l', false);
                    $this->Cell($b, 5, "---", 0, 0, 'l', false);
                    $this->Cell($c, 5, "---", 0, 0, 'l', false);
                    $this->ln();
                }
            }
            $lastvalue = $row["value"];
            $this->last_measured = $row["datetime"] > $this->last_measured ? $row["datetime"] : $this->last_measured;
            $first = false;
        }
        $this->used += $used;
    }

    function pasteInvoice($filename)
    {
        $this->setSourceFile('storage/invoice_pdf/' . $filename);
        $tplIdx = $this->importPage(1);
        $this->useTemplate($tplIdx, 0, 0, 210);
        $this->setFillColor(255, 255, 255);
        $this->setXY(0, 4);
        $this->cell(160);
        $this->cell(40, 10, "Sida {$this->PageNo()}(2)", 0, 0, "R", 1);
    }

    function Header()
    {
        $this->SetFont('Arial', '', 8);
        $this->setFillColor(255, 255, 255);
        $this->setXY(0, 4);
        $this->cell(160);
        //$this->cell(40,10,"Sida {$this->PageNo()}(2)",0,0,"R",1);
        $this->setXY(10, 10);
    }

    function Footer()
    {
        // Go to 1.5 cm from bottom
        $this->SetY(-15);
        // Select Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Print centered page number
        $this->Cell(0, 10, 'Sida ' . $this->PageNo() . " av {nb}", 0, 0, 'C');
    }
}

?>
