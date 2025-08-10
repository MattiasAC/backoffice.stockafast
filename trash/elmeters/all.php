<?php
include("class/classConsumption.php");
$c = new Consumption();

use Altahr\Fortnox;
use Altahr\Database;
$db = new Database();

if (isset($_GET["c"]) && $_GET["c"] == "fortnox") {
    $first = $db->selectOne("elFortnox","1=1 ORDER BY invoicedate DESC");

    $from = $first["InvoiceDate"];
    echo "Före: ".$db->count("elFortnox","1=1");
    echo("<br>Letar från ". $from);
    $_SESSION['UsingFortnoxUrl'] = $_SERVER['REQUEST_URI'];
    $fortnox = new Fortnox();
    $res = $fortnox->curl("https://api.fortnox.se/3/invoices?accountnumberfrom=3514&accountnumberto=3515&fromdate={$from}");

    $array = array();
    if ($res == false) {
        echo "<a href='{$fortnox->loginUrl}'>Logga in Fortnox</a><br>";
    } else {
        if(!isset($res["Invoices"])){
             die("no invoices");
        }
        $invoices = 0;
        $rows = 0;
        foreach ($res["Invoices"] as $invoice) {
            $invoices++;

            $i = $fortnox->curl($invoice["@url"]);
            foreach ($i["Invoice"]["InvoiceRows"] as $row) {

                if (in_array($row["AccountNumber"], ["3514", "3515"]) || strpos(strtolower($row["Description"]), "el") !== false) {
                    $temp = array();
                    $temp["CustomerNumber"] = $i["Invoice"]["CustomerNumber"];
                    $temp["DocumentNumber"] = $i["Invoice"]["DocumentNumber"];
                    $temp["InvoiceDate"] = $i["Invoice"]["InvoiceDate"];
                    $temp["AccountNumber"] = $row["AccountNumber"];
                    $temp["Description"] = $row["Description"];
                    $temp["DeliveredQuantity"] = $row["DeliveredQuantity"];
                    $temp["Unit"] = $row["Unit"];
                    $temp["Price"] = $row["Price"];
                    $temp["Total"] = $row["Total"];
                    $temp["TotalExcludingVAT"] = $row["TotalExcludingVAT"];
                    $q = $db->insertIgnore("elFortnox",$temp);
                    $array[] = $temp;
                    $rows++;
                }
            }
        }
    }
    echo "<br>Invoices: $invoices Rader: $rows";
    echo "<br>Efter: ".$db->count("elFortnox","1=1");
    echo "<hr>";
}
echo "<a href='/elmeters/all/fortnox/'>Uppdatera Fortnox</a><br>";
?>
<script>/*location.href = 'https://admin.altahr.se/elmeters/all/fortnox/';*/</script>
<div class="card">
    <div class="card-body">
        <?php
        echo '<table class="table table-sm table-striped" style="table-layout: auto;width: auto;">';
        echo '<tr>
               <th class="text-bg-header">El-Mätare</th>
               <th class="text-bg-header">Årsförbrukning</th>
               <th class="text-bg-header">Lokaler</th>
               <th class="text-bg-header">Hyresgäst</th>
               <th class="text-bg-header">Senast avläsning</th>
               <th class="text-bg-header">Senaste Faktura</th>
               <th class="text-bg-header">Senaste Fortnox</th>
             </tr>';
        $meter[0] = [
            "parent" => false,
            "meterid" => 0,
            "name_internal" => "Stockamöllan",
            "comment" => "Fakturerat Kraftringen",
            "rooms" => "SF",
        ];
        $meter[100] = [
            "parent" => false,
            "meterid" => 100,
            "name_internal" => "Altahr",
            "comment" => "Fakturerat E.ON",
            "rooms" => "AC",
        ];
        echo $c->AllMeters($meter, 1);
        echo '</table>';
        ?>
    </div>
</div>
<div class="card">
    <div class="card-body">
        Used not in all:"<?php print_r(array_diff_key($c->usedMeters, $c->allMeters)) ?><br>
        All not in usd:"<?php print_r(array_diff_key($c->allMeters, $c->usedMeters)) ?><br>
    </div>
</div>