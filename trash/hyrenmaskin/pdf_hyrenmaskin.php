<?php
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

class PDF extends Fpdi
{
    private $fontsize = 11;
    private $font = "Arial";
    private $contract = array();
    private $client = array();
    private $items = array();
    private $days = 0;
    private $totalPrice = 0;
    private $pricePerDay = 0;
    private $itemIds = array();

    // Layout variables
    private $rowHeight = 6;
    private $col1 = 95;
    private $col2 = 95;
    private $logoWidth = 60;

    public function print($contract, $client, $items)
    {
        $this->contract = $contract;
        $this->client = $client;
        $this->items = $items;
        $this->AddPage();
        $this->SetFont($this->font, '', $this->fontsize);
        $this->printHeader();
        $this->calculatePrice();
        $this->printContent();
        $this->printFooter();
        // $this->Output('I', 'hyreskontrakt.pdf'); // Visar PDF i webbläsaren
    }

    private function printHeader()
    {
        // Add logo
        $this->Image('https://hyrenmaskin.se/img/hyrenmaskin_white.png', 140, 10, $this->logoWidth); // Justera sökväg och storlek efter behov
        $this->SetFont($this->font, 'B', 14);
        $this->Ln(10);
        $this->Cell($this->col1, $this->rowHeight, $this->latin('Hyreskontrakt anläggningsmaskin'), 0, 1);
        $this->Ln(10);
    }

    private function calculatePrice(){
        $from = new DateTime($this->contract["date_start"]);
       $to = new DateTime($this->contract["date_end"]);
       $interval = $from->diff($to);
        $this->days = $interval->days + 1;
    //print_r($this->items);
        $this->pricePerDay = 0;
        foreach($this->items as $item){
            $this->pricePerDay += $item["price"];
            $this->itemIds[$item["itemid"]] = $item["itemid"];
        }
        $this->totalPrice = $this->pricePerDay  * $this->days - $this->contract["discount"];
        //$this->Cell($this->col1, $this->rowHeight, $this->latin($this->totalPrice), 0, 1);
    }
    private function printBox()
    {
        $notes = array();

        $notes[0] = array(
            "text" => "OBS! Hela hyresbeloppet {$this->totalPrice} kr ska betalas med SWISH till telefonnummer 123 215 09 69 innan hyresperiodens början om inget annat är avtalat",
            "bottom" => "14",
        );
        if(in_array("2",$this->itemIds)){
            $notes[1] = array(
                "text" => "- Maskinen och ev. reservdunk skall återlämnas fulltankade med diesel. Om maskinen ej är tankad så debiteras hyrestagaren 25 kr litern + 200 Kr",
                "bottom" => "14",
            );
        }
        $notes[2] = array(
            "text" => "- Maskinen skall återlämnas avspolad och rengjord",
            "bottom" => "7",
        );
        $boxHeight = 8;
        foreach ($notes as $id => $note) {
            if (in_array($id, array(0, 1, 2))) {
                $boxHeight += $note["bottom"];
            } else {
                unset($notes[$id]);
            }
        }
        // print box
        $this->SetFillColor(230, 230, 250); // Light lavender fill color
        $this->SetDrawColor(0, 0, 0); // Black border color
        $this->SetTextColor(0, 0, 0); // Black text color
        $boxWidth = $this->GetPageWidth() - 20; // Adjust the width according to your needs
        $this->Rect(10, $this->GetY(), $boxWidth, $boxHeight, 'DF');
        // print notes
        $padding = 7; // Adjust padding as needed
        $this->SetX(10 + $padding); // Add padding to the left
        $currentY = $this->GetY() + 4;
        foreach ($notes as $id => $note) {
            $this->SetTextColor(0, 0, 0);
            if(empty($first)){
                $this->SetTextColor(255, 0, 0);
                $first = true;
            }
            $this->SetXY(10 + $padding, $currentY);
            $this->MultiCell($boxWidth - 2 * $padding, $this->rowHeight, $this->latin($note["text"]), 0, 'L');
            $currentY += $note["bottom"]; // Adjust line spacing as needed
        }
        $this->Ln(10);
    }

    private function printContacts()
    {
        $this->SetFont($this->font, 'B', $this->fontsize);
        // Print lessor and lessee details in two columns
        $this->Cell($this->col1, $this->rowHeight, $this->latin('Uthyrare'), 0, 0);
        $this->Cell($this->col2, $this->rowHeight, $this->latin('Hyrestagare'), 0, 1);
        $this->SetFont($this->font, '', $this->fontsize);
        $this->Cell($this->col1, $this->rowHeight, $this->latin('Stockamöllan Service AB'), 0, 0);
        $this->Cell($this->col2, $this->rowHeight, $this->latin('Namn: ' . $this->client['firstname'] . ' ' . $this->client['lastname']), 0, 1);
        $this->Cell($this->col1, $this->rowHeight, $this->latin('Telefon: 0708 59 67 88 / 0722 77 77 47'), 0, 0);
        $this->Cell($this->col2, $this->rowHeight, $this->latin('Telefon: ' . $this->client['telephone']), 0, 1);
        $this->Cell($this->col1, $this->rowHeight, $this->latin('E-post: info@hyrenmaskin.se'), 0, 0);
        $this->Cell($this->col2, $this->rowHeight, $this->latin('E-post: ' . $this->client['email']), 0, 1);
        $this->Cell($this->col1, $this->rowHeight, $this->latin('Org.nr: 556854-1592'), 0, 0);
        $this->Cell($this->col2, $this->rowHeight, $this->latin('Personnummer: ' . $this->client['personnummer']), 0, 1);
        $this->Ln(10);
    }

    private function printContent()
    {

        $this->printContacts();
        $this->printBox();
        // Print contract details
        $this->SetFont($this->font, 'B', $this->fontsize);
        $this->Cell(0, $this->rowHeight, $this->latin('Hyresobjekt'), 0, 1);
        $this->SetFont($this->font, '', $this->fontsize);
        $price = 0;
        foreach ($this->items as $itemid => $item) {
            $this->Cell(0, $this->rowHeight, $this->latin(" - " . $item["title"] . " (" . $item["price"] . " kr/dag)"), 0, 1);
            $price += $item["price"];
        }
        $this->Ln(5);
        // Print terms
        $termsText = [
            'Hyresperiod' => "Hyresperioden är från och med {$this->contract['date_start']} till och med {$this->contract['date_end']} kl 18.00 ",
            'Hyresavgift' => "Hyresavgiften är {$this->pricePerDay} kr x {$this->days} ",
            'Betalningsvillkor' => "Betalning av hyresavgiften ska göras senast {$this->contract['date_start']}. Betalningen ska ske till uthyraren med SWISH till telefonnummer 123 215 09 69.",

        ];
        $termsText["Hyresperiod"] .= $this->days > 1 ? "({$this->days} dagar) " : "({$this->days} dag)";
        $termsText["Hyresavgift"] .= $this->days > 1 ? "dagar. " : "dag. ";
        $termsText["Hyresavgift"] .= $this->contract["discount"] > 0 ? "Rabatt har lämnats med {$this->contract["discount"]} kr. " : "";
        $termsText["Hyresavgift"] .= "Hyrestagaren ska därmed betala {$this->totalPrice} kr inklusive moms för hela hyresperioden.";

        foreach ($termsText as $title => $text) {
            $totalLines = ceil(strlen($text) / 100) + 1; // +1 för header
            $this->checkPageBreak($totalLines);

            $this->SetFont($this->font, 'B', $this->fontsize);
            $this->Cell(0, $this->rowHeight, $this->latin($title), 0, 1);
            $this->SetFont($this->font, '', $this->fontsize);
            $this->MultiCell(0, $this->rowHeight, $this->latin($text));
            $this->Ln(5);
        }
        $terms = include("terms.php");
        foreach ($terms as $key => $texts) {
            $totalLines = 0;
            foreach ($texts as $text) {
                $totalLines += ceil(strlen($text) / 100) + 1; // +1 för varje nytt textblock
            }
            $this->checkPageBreak($totalLines);

            $this->SetFont($this->font, 'B', $this->fontsize);
            $this->Cell(0, $this->rowHeight, $this->latin($key), 0, 1);
            foreach ($texts as $no => $text) {
                $this->SetFont($this->font, '', $this->fontsize);
                $this->MultiCell(0, $this->rowHeight, $this->latin($text));
                $this->Ln(5);
            }
        }
    }

    function CheckPageBreak($numLines)
    {
        // Calculate the height needed for the text
        $neededHeight = $numLines * $this->rowHeight;
        // If the height needed would cause an overflow, add a new page immediately
        if ($this->GetY() + $neededHeight > $this->PageBreakTrigger) {
            $this->AddPage($this->CurOrientation);
        }
    }

    private function printFooter()
    {
        // Go to 1.5 cm from bottom
        //$this->SetY(-15);
        // Select Arial italic 8
        //$this->SetFont('Arial', 'I', 8);
        // Print centered page number
        //$this->Cell(0, 10, 'Sida ' . $this->PageNo() . ' av {nb}', 0, 0, 'C');
    }

    private function latin($utf8)
    {
        return mb_convert_encoding($utf8, "ISO-8859-1", "UTF-8");
    }
}

?>
