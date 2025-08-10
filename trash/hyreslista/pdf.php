<?php
class PDF extends FPDF
{
    private $Hyreslista;

    public function __construct($Hyreslista)
    {
        parent::__construct();
        $this->Hyreslista = $Hyreslista;
        $this->AddPage("L");
        $this->table();
        $this->Output("F", "./html/hyreslista/hyreslista/hyreslista.pdf");
    }

    private function font($type = "")
    {
        switch ($type) {
            case "head":
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255);
                $this->SetFont('Arial', 'b', 9);
                break;
            case "sum":
                $this->SetFillColor(255, 255, 255);
                $this->SetTextColor(0);
                $this->SetFont('Arial', 'b', 9);
                break;
            default:
                $this->SetFillColor(255, 255, 255);
                $this->SetTextColor(0);
                $this->SetFont('Arial', '', 9);
                break;
        }
    }

    private function latin($utf8)
    {
        return mb_convert_encoding($utf8 ?? '', "ISO-8859-1", "UTF-8");
    }

    function table()
    {
        require_once('classColumns.php');
        global $columns;
        $totalWidth = 280; // Total bredd för sidan (justera om du använder andra marginaler)
        $displayedColumns = 0;
        $this->font("head");
        foreach ($columns as $key => $column) {
            if ($this->Hyreslista->displayColumn($key)) {
                $displayedColumns++;
            }
        }
        $columnWidth = $displayedColumns > 0 ? $totalWidth / $displayedColumns : 0;
        foreach ($columns as $key => $column) {
            if ($this->Hyreslista->displayColumn($key)) {
                $this->Cell($columnWidth, 7, $this->latin($column->head), 0, 0, 'L', true);
            }
        }
        $this->Ln();
        $this->font();
$fill = false;

foreach ($this->Hyreslista->list as $clientid => $row) {
    if ($this->Hyreslista->displayRow($row["active"], $row["area"])) {
        $this->SetFillColor($fill ? 230 : 255);

        foreach ($columns as $key => $cell) {
            if ($this->Hyreslista->displayColumn($key)) {
                $cell->set($row, $key);
                $this->Cell($columnWidth, 7, $this->latin($cell->display), 0, 0, 'L', true);
            }
        }

        $this->Ln();
        $fill = !$fill;
    }
}
        $grid = [];
        $colIndex = 1;
        foreach ($columns as $key => $column) {
            if ($this->Hyreslista->displayColumn($key)) {
                $rowIndex = 1;

                if ($column->displaySum) {
                    foreach ($column->sums as $key => $sum) {
                        $grid[$rowIndex][$colIndex] = $this->Hyreslista->num($sum, 0) . $column->sumSuffix;
                        $rowIndex++;
                    }
                    $grid[$rowIndex][$colIndex] = $this->Hyreslista->num(array_sum($column->sums), 0) . $column->sumSuffix;
                    $rowIndex++;
                    if ($column->sumPerYear) {
                        $grid[$rowIndex][$colIndex] = $this->Hyreslista->num(12 * array_sum($column->sums), 0) . $column->sumSuffix . "/år";
                        //echo "<div style='background:#FFFF0025'>(" . $Hyreslista->num(12 * array_sum($column->sums), 0) . $column->sumSuffix . "/år)</div>";
                    }
                }
                $colIndex++;
            }
        }
        $this->font("sum");
        for ($rowIndex = 2; $rowIndex < 10; $rowIndex++) {
            for ($colIndex = 1; $colIndex < 20; $colIndex++) {
                $text = isset($grid[$rowIndex][$colIndex]) ? $grid[$rowIndex][$colIndex] : "";
                $this->Cell($columnWidth, 7, $this->latin($text), 0, 0, 'L', true);
            }
            $this->Ln();
        }

    }




}
?>