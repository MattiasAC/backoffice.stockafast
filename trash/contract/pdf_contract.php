<?php

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

class PDF extends Fpdi
{

    public $map, $data, $template;
    public $fontsize = 12;
    private $font = "Arial";

    public function setTemplate()
    {
        $this->template = array();
        $this->template["stocka.txt"]["logo"] = "storage/contracts/logo.jpg";
        $this->template["stocka.txt"]["company"] = "Stockamöllan Fastigheter AB";
        $this->template["stocka.txt"]["companyaddress1"] = "Lilla Hammars väg 13A";
        $this->template["stocka.txt"]["companyaddress2"] = "23637 Höllviken";
        $this->template["stocka.txt"]["orgnr"] = "559077-2504";
        $this->template["stocka.txt"]["person"] = "Viktor Rempling";
        $this->template["4A.txt"]["logo"] = "storage/contracts/altahr.png";
        $this->template["4A.txt"]["company"] = "Altahr Consulting AB";
        $this->template["4A.txt"]["companyaddress1"] = "Lilla Hammars väg 13A";
        $this->template["4A.txt"]["companyaddress2"] = "23637 Höllviken";
        $this->template["4A.txt"]["orgnr"] = "556811-9381";
        $this->template["4A.txt"]["person"] = "Mattias Altahr-Cederberg";
    }

    function print($data, $map)
    {
        $this->setTemplate();
        $this->AddPage();
        $this->data = $data;
        $this->map = $map;
        $this->fontsize = 12;
        $this->printHeader();
        $this->printParagraphs();
        $this->printFooter();
    }

    private function latin($utf8)
    {
        return mb_convert_encoding($utf8, "ISO-8859-1", "UTF-8");
    }

    function printHeader()
    {
        $rowHeight = ceil($this->fontsize / 2);
        $colWidth = 90;
        $this->SetFont($this->font, '', round($this->fontsize + 16));
        $this->Cell(90, 20, "Hyresavtal", 0, 1, 'l', 0);
        $this->SetFont($this->font, '', round($this->fontsize));
        $this->Cell(90, 0, "Kontraktsnummer " . $this->data["id"], 0, 1, 'l', 0);
        $this->image($this->template[$this->data["template"]]["logo"], 130, 0, 60);
        $this->ln(15);
        $this->SetFont($this->font, 'B', $this->fontsize);
        $this->Cell($colWidth, $rowHeight, $this->latin("Hyresvärd"), 0, 0, 'l', 0);
        $this->Cell($colWidth, $rowHeight, $this->latin("Hyresgäst"), 0, 1, 'l', 0);
        $this->SetFont($this->font, '', $this->fontsize);
        $this->Cell($colWidth, $rowHeight, $this->latin($this->template[$this->data["template"]]["company"]), 0, 0, 'l', 0);
        if (empty($this->data["name"])) {
            $this->Cell($colWidth, $rowHeight, mb_convert_encoding($this->data["contact"], "ISO-8859-1", "UTF-8"), 0, 1, 'l', 0);
        } else {
            $this->Cell($colWidth, $rowHeight, mb_convert_encoding($this->data["name"], "ISO-8859-1", "UTF-8"), 0, 1, 'l', 0);
        }
        $this->Cell($colWidth, $rowHeight, $this->latin($this->template[$this->data["template"]]["companyaddress1"]), 0, 0, 'l', 0);
        $this->Cell($colWidth, $rowHeight, mb_convert_encoding($this->data["address"], "ISO-8859-1", "UTF-8"), 0, 1, 'l', 0);
        $this->Cell($colWidth, $rowHeight, $this->latin($this->template[$this->data["template"]]["companyaddress2"]), 0, 0, 'l', 0);
        $this->Cell($colWidth, $rowHeight, mb_convert_encoding($this->data["zipcity"], "ISO-8859-1", "UTF-8"), 0, 1, 'l', 0);
        $this->Cell($colWidth, $rowHeight, mb_convert_encoding("Org.nr. 559077-2504", "ISO-8859-1", "UTF-8"), 0, 0, 'l', 0);
        if (!empty($this->data["org"])) {
            $this->Cell($colWidth, $rowHeight, $this->latin("Orgnr:" . $this->data["org"]), 0, 1, 'l', 0);
        } else {
            $this->Cell($colWidth, $rowHeight, mb_convert_encoding("Personnr. " . $this->data["pnr"], "ISO-8859-1", "UTF-8"), 0, 1, 'l', 0);
        }
        $this->SetFont($this->font, 'BI', $this->fontsize);
        $this->Cell($colWidth, $rowHeight, mb_convert_encoding("(nedan kallad hyresvärden)", "ISO-8859-1", "UTF-8"), 0, 0, 'l', 0);

        $this->SetFont($this->font, '', $this->fontsize);

        $this->Cell($colWidth, $rowHeight, mb_convert_encoding("E-post: " . $this->data["email"], "ISO-8859-1", "UTF-8"), 0, 1, 'l', 0);
        if (!empty($this->data["tel1"])) {
            $this->Cell($colWidth, $rowHeight, mb_convert_encoding("", "ISO-8859-1", "UTF-8"), 0, 0, 'l', 0);
            $this->Cell($colWidth, $rowHeight, mb_convert_encoding("Tel: " . $this->data["tel1"], "ISO-8859-1", "UTF-8"), 0, 1, 'l', 0);
        }
        if (!empty($this->data["tel2"])) {
            $this->Cell($colWidth, $rowHeight, mb_convert_encoding("", "ISO-8859-1", "UTF-8"), 0, 0, 'l', 0);
            $this->Cell($colWidth, $rowHeight, mb_convert_encoding("Tel2: " . $this->data["tel2"], "ISO-8859-1", "UTF-8"), 0, 1, 'l', 0);
        }

        if($this->data["email"] == "martins.morozovs@gmail.com"){

            $this->SetFont($this->font, '', $this->fontsize);
            $this->Cell($colWidth, $rowHeight, mb_convert_encoding("", "ISO-8859-1", "UTF-8"), 0, 0, 'l', 0);
            $this->Cell($colWidth, $rowHeight, mb_convert_encoding("", "ISO-8859-1", "UTF-8"), 0, 1, 'l', 0);
            $this->Cell($colWidth, $rowHeight, mb_convert_encoding("", "ISO-8859-1", "UTF-8"), 0, 0, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin("Andrius Kalinauskas"), 0, 1, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin(""), 0, 0, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin("Tågarpsvågen 37A,"), 0, 1, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin(""), 0, 0, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin("26176 Asmundtorp"), 0, 1, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin(""), 0, 0, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin("Personnr. 870424-0772"), 0, 1, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin(""), 0, 0, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin("E-post: Kalinauskasandrius@gmail.com"), 0, 1, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin(""), 0, 0, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin("Tel: 0707476939"), 0, 1, 'l', 0);
        }


        $this->SetFont($this->font, 'BI', $this->fontsize);
        $this->Cell($colWidth, $rowHeight, mb_convert_encoding("", "ISO-8859-1", "UTF-8"), 0, 0, 'l', 0);
        $this->Cell($colWidth, $rowHeight, mb_convert_encoding("(nedan kallad hyresgästen)", "ISO-8859-1", "UTF-8"), 0, 1, 'l', 0);
        $this->ln();
        if (!empty($this->data["image"]) && file_exists("storage/contracts/" . $this->data["image"])) {
            $this->image("storage/contracts/" . $this->data["image"], 45, null, 90);
        }
        if (1 == 2) {
            $this->SetFont($this->font, '', $this->fontsize);
            $this->Cell($colWidth, $rowHeight, $this->latin("Tel. 0704 68 63 89"), 0, 1, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin(""), 0, 0, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin("E-post. r.westin@telia.com"), 0, 1, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin(""), 0, 0, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin(""), 0, 1, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin(""), 0, 0, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin("Sari Tuire Elisabet Helminen Westin"), 0, 1, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin(""), 0, 0, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin("Söderslättsgatan 47 lgh 1002"), 0, 1, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin(""), 0, 0, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin("23153 Trelleborg"), 0, 1, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin(""), 0, 0, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin("Personnr. 19660608-3985"), 0, 1, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin(""), 0, 0, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin("Tel. 0703 81 81 60"), 0, 1, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin(""), 0, 0, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin("E-post. sari.helminen@opo.se"), 0, 1, 'l', 0);
            $this->SetFont($this->font, 'BI', $this->fontsize);
            $this->Cell($colWidth, $rowHeight, mb_convert_encoding("", "ISO-8859-1", "UTF-8"), 0, 0, 'l', 0);
            $this->Cell($colWidth, $rowHeight, mb_convert_encoding("(nedan kallad hyresgästen)", "ISO-8859-1", "UTF-8"), 0, 1, 'l', 0);
        }
    }

    function printParagraphs()
    {
        $count = 1;
        $rowHeight = ceil($this->fontsize / 2);
        foreach ($this->map as $letter => $data) {
            if (!empty($this->data["paragraph"][$letter]["text"]) && strlen($this->data["paragraph"][$letter]["text"]) > 2) {

                $sectionHeight = $this->calculateSectionHeight($data["header"], $this->data["paragraph"][$letter]["text"], $rowHeight);
                //echo $this->GetY()." ".$sectionHeight." ".$this->PageBreakTrigger." ".$this->latin($data["header"])."<br>";
                if ($this->GetY() + $sectionHeight + 20 > $this->PageBreakTrigger) {
                    $this->AddPage();
                }
                $this->ln($this->data["spacing"]);
                $this->SetFont('Arial', 'B', $this->fontsize);
                $this->multicell(180, $rowHeight, "{$count}. " . $this->latin($data["header"]), 0);
                $this->SetFont('Arial', '', $this->fontsize);
                $this->multicell(180, $rowHeight, $this->latin(htmlspecialchars($this->data["paragraph"][$letter]["text"])), 0);
                $count++;
            }
        }
    }

    function calculateSectionHeight($header, $text, $rowHeight)
    {
        $headerLines = ceil($this->GetStringWidth($header) / 180);
        $textLines = ceil($this->GetStringWidth($text) / 180);
        return ($headerLines + $textLines) * $rowHeight;
    }

    function printFooter()
    {
        $rowHeight = ceil($this->fontsize / 2);
        $colWidth = 68;
        $this->SetFont($this->font, '', $this->fontsize);
        $this->ln();
        if(1==1){
            $this->SetFont($this->font, 'B', $this->fontsize);
            $this->Cell($colWidth, $rowHeight, mb_convert_encoding("", "ISO-8859-1", "UTF-8"), 0, 1, 'l', 0);
            $this->Cell($colWidth, $rowHeight, mb_convert_encoding("", "ISO-8859-1", "UTF-8"), 0, 1, 'l', 0);
            $this->Cell($colWidth, $rowHeight, mb_convert_encoding("", "ISO-8859-1", "UTF-8"), 0, 1, 'l', 0);
            $this->Cell($colWidth, $rowHeight, mb_convert_encoding("Detta avtal har upprättats digitalt och signeras elektroniskt med BankID av samtliga parter", "ISO-8859-1", "UTF-8"), 0, 1, 'l', 0);
            $this->Cell($colWidth, $rowHeight, mb_convert_encoding("Avtalet träder i kraft när alla parter har signerat.", "ISO-8859-1", "UTF-8"), 0, 1, 'l', 0);
        }else{
            $this->Cell($colWidth, 25, mb_convert_encoding("Detta hyresavtal har upprättats i två exemplar, ett för vardera parten.", "ISO-8859-1", "UTF-8"), 0, 1, 'l', 0);
            $this->Cell($colWidth, 15, mb_convert_encoding("Hyresvärd", "ISO-8859-1", "UTF-8"), 0, 0, 'l', 0);
            $this->Cell($colWidth, 15, mb_convert_encoding("Hyresgäst", "ISO-8859-1", "UTF-8"), 0, 1, 'l', 0);
            // $this->Cell($colWidth, 15, mb_convert_encoding("Hyresgäst", "ISO-8859-1", "UTF-8"), 0, 1, 'l', 0);
            $this->Cell($colWidth, $rowHeight, mb_convert_encoding("Ort:", "ISO-8859-1", "UTF-8"), 0, 0, 'l', 0);
            $this->Cell($colWidth, $rowHeight, mb_convert_encoding("Ort:", "ISO-8859-1", "UTF-8"), 0, 1, 'l', 0);
            //$this->Cell($colWidth, $rowHeight, mb_convert_encoding("Ort:", "ISO-8859-1", "UTF-8"), 0, 1, 'l', 0);
            $this->Cell($colWidth, 15, mb_convert_encoding("Datum:", "ISO-8859-1", "UTF-8"), 0, 0, 'l', 0);
            $this->Cell($colWidth, 15, mb_convert_encoding("Datum:", "ISO-8859-1", "UTF-8"), 0, 1, 'l', 0);
            //$this->Cell($colWidth, 15, mb_convert_encoding("Datum:", "ISO-8859-1", "UTF-8"), 0, 1, 'l', 0);
            $this->Cell($colWidth, $rowHeight, "..............................................", 0, 0, 'l', 0);
            $this->Cell($colWidth, $rowHeight, "..............................................", 0, 1, 'l', 0);
            //$this->Cell($colWidth, $rowHeight, "..............................................", 0, 1, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin($this->template[$this->data["template"]]["person"]), 0, 0, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin($this->data["contact"]), 0, 1, 'l', 0);
            //$this->Cell($colWidth, $rowHeight, $this->latin("Sari Westin"), 0, 1, 'l', 0);
            $this->Cell($colWidth, $rowHeight, $this->latin($this->template[$this->data["template"]]["company"]), 0, 0, 'l', 0);
            if ($this->data["template"] == "4A.txt") {
                $this->Cell($colWidth, $rowHeight, mb_convert_encoding("", "ISO-8859-1", "UTF-8"), 0, 1, 'l', 0);
            } else {
                $this->Cell($colWidth, $rowHeight, mb_convert_encoding($this->data["name"], "ISO-8859-1", "UTF-8"), 0, 1, 'l', 0);
            }

        }

    }

    function Footer()
    {
        // Go to 1.5 cm from bottom
        $this->SetY(-15);
        // Select Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Print centered page number
        //$this->Cell(0, 10, 'Sida ' . $this->PageNo() . ' av {nb}', 0, 0, 'C');
    }
}

?>
