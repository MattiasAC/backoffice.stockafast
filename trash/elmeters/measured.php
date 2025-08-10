<div class="card m-2">
<div class="card-body text-bg-success">
<?php
use Altahr\Elements;
global $Elmeters,$Meter;
$last = false;

if($Elmeters->clientid == 118){

    $list = $Elmeters->Metersdata[$Meter->MeterId]->List;

    $measure_point = new DateTime($_POST["showTo"]);

    $periodstart = clone $measure_point;
    $periodstart->modify('first day of last month');

    $periodend = clone $measure_point;
    $periodend->modify('last day of last month');

    echo $periodstart->format('Y-m-d') . " " . $periodend->format('Y-m-d');


    $spot = reset($Elmeters->spot);
    $prev_month = (int)$measure_point->modify('last month')->format('n');
    $prev_year = (int)$measure_point->format('Y');
    foreach ($Elmeters->spot as $row) {
        if ($row['year'] == $prev_year && $row['month'] == $prev_month) {
            $spot = $row;
        }
    }

    $last = findClosest($list,$periodstart->format('Y-m-d'));
    $now = findClosest($list,$periodend->format('Y-m-d'));
    $strdate = $spot["year"]."-".$spot["month"]."-15";

    $dateLast = date("Y-m-d",strtotime($last["datetime"]));
    $valueLast = round($last["value"]);
    $dateNow = date("Y-m-d",strtotime($now["datetime"]));
    $valueNow = round($now["value"]);
    $valuePay = $valueNow - $valueLast;

    $date = date('F',strtotime($strdate));
    $value = number_format($spot["se4"]/100,2,".","");
    $yourValue = number_format($value + 1.5,2,".","");

echo "<div class='card'><div class='card-body'>";
echo "<textarea class='form-control' style='width:100%' onclick='copy(this)'>
Spotpris {$date} {$value} kr/kWh, ert pris {$yourValue} kr/kWh
Mätarställning {$dateLast}: {$valueLast} kWh, {$dateNow}: {$valueNow} kWh
</textarea>";
echo "<div style='display:flex'>";
echo "Antal kWh <input type='text' class='form-control' style='width:100px' value='{$valuePay}'onclick='copy(this)'>&nbsp;&nbsp;";
echo "á pris <input type='text' class='form-control' style='width:100px' value='{$yourValue}'onclick='copy(this)'>";
echo "</div>";
echo "</div></div>";
}
echo "<select id=\"id_{$Meter->MeterId}\" class='form-control'>";
foreach($Meter->All as $measureid => $measure){
    echo "<option value='$measureid'>".date("Y-m-d", strtotime($measure["datetime"]))." [".number_format($measure["value"],2,".","")." kWh]</option>";
}
echo "</select>";

$pairs =[];
foreach ($Meter as $key => $value) {
    if (!is_array($value)) {
        $pairs[] = "<th>{$key}</th><td title='{$value}'>{$value}</td>";
    }
}
echo "<table class='table-fixed w-100 table-sm'><tr>";
$count = 1;
foreach($pairs as $pair){
   echo $pair;
   if($count % 2 == 0){
       echo "</tr><tr>";
   }
   $count ++;
}
echo "</tr><tr>";
echo "<td colspan='2'><button type='button' class='btn btn-success open-modal' style='border: 2px solid #FFF;' mode='insert' clientid='{$Elmeters->clientid}' meterid='{$Meter->MeterId}'>New</button></td>";
echo "<td colspan='2'><button type='button' class='btn btn-success open-modal' mode='edit' clientid='{$Elmeters->clientid}' meterid='{$Meter->MeterId}'>Edit</button></td>";
echo "</tr>";
echo "</table>";
?>


</div>
</div>

