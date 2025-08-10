<script>
    function copy(element) {
        element.select();
        document.execCommand("copy");
    }
</script>
<?php
foreach (glob(__DIR__ . "/class/*.php") as $filename) {
    include_once $filename;
}
function findClosest(array $data, string $inputDate): array
{
    $targetDate = strtotime($inputDate);
    $closestRow = null;
    $closestDiff = PHP_INT_MAX;

    foreach ($data as $row) {
        $currentDate = strtotime($row['datetime']);
        $diff = abs($currentDate - $targetDate);

        if ($diff < $closestDiff) {
            $closestDiff = $diff;
            $closestRow = $row;
        }
    }
    return $closestRow;
}

$Elmeters = new Elmeters();
include('topMenu.php');
include("modal_measured.php");
echo "<div class='row'>";
echo "<div class='col-4'>";
if (empty($Elmeters->Metersdata)) {
    echo "<div class=\"card m-2 text-bg-warning\"><div class=\"card-body\">Inga elmätare kunde hittas</div>";
} else {
    include('fortnox.php');
    foreach ($Elmeters->Metersdata as $meterid => $Meter) {
        include('measured.php');
    }
    include('invoiced.php');
    echo "</div>";
    echo "<div class='col-8'>";
    echo "<form class=\"figure\" action=\"/elmeters/{$Elmeters->clientid}/\" method='post'>";
    echo "<div class='d-flex flex-nowrap' style='overflow-x: auto; white-space: nowrap;'>";
    $images = $Elmeters->setImages();
    foreach ($images as $image) {
        echo $image;
    }
    echo "</div>";
    echo "</form>";
    echo "<iframe style=\"width:100%;height:2000px\" name=\"iframe\" src=\"https://admin.altahr.se/html/elmeters/pdf/el_{$Elmeters->client["name"]}.pdf\"></iframe>";
}
echo "</div>";
echo "</div>";
