<?php
global $Elmeters;
$check_showall = empty($_SESSION["settings"]["showall"]) ? "" : "checked=checked";
$check_shorten = empty($_SESSION["settings"]["shorten"]) ? "" : "checked=checked";
echo "<form action=\"/elmeters/{$Elmeters->clientid}/\" method='post' style='background-color: #d7f1f5'>";
echo "<a href='/hyreslista/' class='btn btn-secondaty'>Hyreslista</a>";
echo "<button class=\"btn btn-primary dropdown-toggle m-2\" type=\"button\" id=\"dropdownMenuButton\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">";
echo "{$Elmeters->client['name']}";
echo "</button>";

echo "<ul class=\"dropdown-menu\" aria-labelledby=\"dropdownMenuButton\">";
foreach($Elmeters->clients as $clientid=>$client){
    switch($client["active"]){
        case 0 : $color = "black";break;
        case 4 : $color = "red";break;
        default : $color = "green";break;
    }
    $bg = $client["autoreading"] == true ? ";background-color:#66FF66" : "";
    echo "<li><a class=\"dropdown-item\" style='color:{$color}{$bg}' href=\"/elmeters/{$clientid}/\">{$client["name"]} [{$client["elavtal"]}]</a></li>";
}
echo "</ul>";

echo "<div style='display: inline-block;padding:3px 20px;'>";
echo "Mätningar t.o.m<input type='text' id='invoiceDate' class='form-control' name='showTo' value='" . $Elmeters->showTo . "' style='width:130px;display: inline-block'>";
echo "<input type='submit' id='settings' name='settings' value='Refresh' class='btn btn-primary'>";
echo "</div>";

echo "<div style='display: inline-block;padding:3px 20px;' class=\"dropdown\">";
echo "<button class=\"btn btn-primary dropdown-toggle m-2\" type=\"button\" id=\"dropdownMenuButton\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">";
$spots = $Elmeters->spot();
$spot = reset($spots);
$nextMonth =  mktime(0,0,0,round($spot["month"] + 1),15,$spot["year"]);
$year = date("Y",$nextMonth);
$month = date("m",$nextMonth);
$se4 = $spot["se4"];
echo date("M Y", strtotime("{$spot["year"]}-{$spot["month"]}-15"))." - {$spot["se4"]} öre/kWh";
echo "</button>";
echo "<ul class=\"dropdown-menu\" aria-labelledby=\"dropdownMenuButton\">";
foreach($spots as $spot) {
    echo "<li><a class=\"dropdown-item\" href=\"#\">".date("M Y", strtotime("{$spot["year"]}-{$spot["month"]}-15"))." - {$spot["se4"]} öre/kWh</a></li>";

}
echo "</ul>";
echo "<input type='text' name='year' class='form-control' style='display: inline-block;width:60px' value='$year'>";
echo "<input type='text' name='month'  class='form-control' style='display: inline-block;width:50px' value='$month'>";
echo "<input type='text' name='se4'  class='form-control' style='display: inline-block;width:80px' value='$se4'>";
echo "<input type='submit' class='btn btn-primary' name='addSPOT' value='Add SPOT'>";
echo "</div>";

echo "</form>";

