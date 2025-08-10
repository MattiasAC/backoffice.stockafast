<?php
require_once("classEdit.php");
global $width, $textwidth,$imagewidth,$columns;
$Edit = new Edit();
require_once("topmenu.php");
echo "<div class=\"card shadow mb-4\">";
echo "<div class=\"card-body\">";
echo "<table class='table table-sm table-striped'>";
$counter = 0;
foreach ($Edit->list as $key => $row) {
    $counter ++;
    if (!isset($headerSet)) {
        $headerSet = true;
        echo "<tr>";
        echo "<th style='width:30px'>ID</th>";
        foreach($row as $k => $v){
            $width = 35 + $Edit->lengths[$k]*8;
            echo "<th style='width:{$width}px;'>$k</th>";
        }
        echo "<th style='width:70px !important;'>&nbsp</th>";
        echo "<th style='width:50px !important;'>&nbsp</th>";
        echo "</tr>";
    }
    echo "<form method='post' action=''>";
    echo "<input type='hidden' name='id' value='{$key}'>";
    echo "<input type='hidden' name='table' value='{$Edit->table}'>";
    echo "<input type='hidden' name='primary' value='{$Edit->primary}'>";
    echo "<tr>";
    echo "<td style='width:30px'>{$key}</td>";
    foreach ($row as $k => $v) {
        $width = 35 +$Edit->lengths[$k]*8;
        echo "<td style='width:{$width}px'><input type='text' class='form-control' name='{$k}' value='{$v}'></td>";
    }
    echo "<td><input type='submit' class='btn btn-primary' name='update' value='Update'></td>";
    echo "<td><input type='submit' class='btn btn-warning' name='delete' value='X' onclick=\"return confirm('Är du säker på att du vill ta bort detta?');\"></td>";
    echo "</tr>";
    echo "</form>";
    if ($counter == count($Edit->list)) {
        echo "<form method='post' action=''>";
        echo "<tr>";
        echo "<th style='width:30px'> 
            <input type='text' class='form-control' name='id' value=''>
            <input type='hidden' name='table' value='{$Edit->table}'>
            <input type='hidden' name='primary' value='{$Edit->primary}'>
        </th>";
        foreach($row as $k => $v){
            $width = 35 +$Edit->lengths[$k]*8;
            echo "<td style='width:{$width}px'><input type='text' class='form-control' name='{$k}' value=''></td>";
        }
         echo "<td><input type='submit' class='btn btn-secondary' name='insert' value='Insert'></td>";
        echo "</tr>";
        echo "</form>";
    }

}
echo "</table>";
echo "</div>";
echo "</div>";
?>
