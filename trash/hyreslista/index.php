<?php
/** @var $columns */

$files = scandir("storage/uploads/");
$contracts = [];
foreach ($files as $file) {
    if (strpos($file, "_") !== false) {
        $parts = explode("_", $file);
        $contracts[$parts[0]][] = $file;
    }
}
include("classColumns.php");
include("classHyreslista.php");
$Hyreslista = new Hyreslista();
$Hyreslista->uploads = $contracts;


$list = $Hyreslista->list;

echo "<form id=\"form\"  action=\"/hyreslista/\" method=\"post\" enctype=\"multipart/form-data\">";
include("sub_form.php");
include("sub_edit.php");
echo "</form>";
if(isset($_POST["pdf"])) {
    echo "<iframe src=\"https://admin.altahr.se/html/hyreslista/hyreslista/hyreslista.pdf\" class=\"w-100\" style='height:1000px'></iframe>";
    
}
echo "<div class=\"card shadow mb-4 ml-3 mt-2 mr-3\">";
echo "<div class=\"card-body\">";
echo "<table class='table-bordered table-hover table-responsive' id='myTable'>";
echo "<tr>";
foreach ($columns as $key => $column) {
    if ($Hyreslista->displayColumn($key)) {
        echo "<th class='text-bg-header' style='$column->headStyle;cursor:pointer' onclick='sortColumns(\"{$key}\");'>";
        echo $column->head;
        echo "</th>";
    }
}
echo "</tr>";
$emails = array();
$count = 1;
foreach ($list as $clientid => $row) {
    if ($Hyreslista->displayRow($row["active"], $row["area"])) {
        if(!empty($row["email"]))
        $emails[] = trim($row["email"]);
        echo "<tr>";
        foreach ($columns as $key => $cell) {
            if ($Hyreslista->displayColumn($key)) {
                $cell->set($row,$key);
                echo "<td class=\"{$cell->class}\" style=\"{$cell->style}\">";
                echo $cell->display;
                echo "</td>";
            }
        }
        echo "</tr>";
        $count++;
    }
}
echo "<tr>";
foreach ($columns as $key => $column) {
    if ($Hyreslista->displayColumn($key)) {
        echo "<td style='vertical-align: top;text-align:right'>";
        if ($column->displaySum) {
            foreach ($column->sums as $key => $sum) {
                echo "<div>" . number_format($sum, 0,""," ") . $column->sumSuffix . "</div>";
            }
            echo "<div style='border-top:1px solid #000;font-weight: bold'>" . number_format(array_sum($column->sums), 0,""," ") . $column->sumSuffix . "</div>";
            if ($column->sumPerYear) {
                echo "<div style='background:#FFFF0025'>(" . number_format(12 * array_sum($column->sums), 0,""," ") . $column->sumSuffix . "/år)</div>";
            }
        }
        echo "</td>";
    }
}
echo "</tr>";
echo "</table>";
echo implode(",",$emails);
echo "</div>";
echo "</div>";
?>
