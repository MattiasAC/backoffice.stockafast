<a href="https://admin.altahr.se/hyreslista/">Hyreslista</a>
<?php
require_once("classFortnox.php");
$F = new Fortnox();
if(!isset($_GET["c"]) || !isset($_GET["d"]) ) {
    exit;
}
$json = $F->curl("https://api.fortnox.se/3/invoices?customernumber={$_GET["c"]}&fromdate={$_GET["d"]}", false, "GET");
$exclude = array();

$update = array();
$update["elinvoice_last"] = "1971-01-01";
$update["elinvoice_kwh"] = 0;
$update["elinvoice_kr"] = 0;
$update["elinvoice_updated"] = date("Y-m-d H:i:s");
echo "<table>";
if ($json !== false) {
    foreach ($json["Invoices"] as $invoice) {
        $invoice = $F->curl($invoice["@url"], false, "GET");
        if ($invoice !== false) {

            if (!empty($invoice["Invoice"]["InvoiceRows"])) {

                foreach ($invoice["Invoice"]["InvoiceRows"] as $row) {
                    $newElement = ["InvoiceDate" => $invoice["Invoice"]["InvoiceDate"],"CustomerNumber" => $invoice["Invoice"]["CustomerNumber"],"CustomerName" => $invoice["Invoice"]["CustomerName"]];
                    $row = $newElement + $row;
                    unset($row["AccountNumber"]);
                    //unset($row["ArticleNumber"]);
                    unset($row["ContributionValue"]);
                    unset($row["ContributionPercent"]);
                    unset($row["Cost"]);
                    unset($row["CostCenter"]);
                    unset($row["Discount"]);
                    unset($row["DiscountType"]);
                    unset($row["HouseWork"]);
                    unset($row["HouseWorkHoursToReport"]);
                    unset($row["HouseWorkType"]);
                    unset($row["Project"]);
                    unset($row["RowId"]);
                    unset($row["StockPointCode"]);
                    if (!isset($first)) {
                        $first = true;
                        echo "<tr><th>";
                        echo implode("</th><th>", array_keys($row));
                        echo "</th></tr>";
                    }
                   if(strpos(strtolower($row["Description"]), "el") !== false || strpos(strtolower($row["Description"]), "mätare") !== false){
                       echo "<tr><td style=''>";
                       echo implode("</td><td style=''>", $row);
                       echo "</td></tr>";

                       $update["elinvoice_last"] = $row["InvoiceDate"] > $update["elinvoice_last"]  ?$row["InvoiceDate"] : $update["elinvoice_last"];
                       $update["elinvoice_kwh"] += $row["DeliveredQuantity"];
                       $update["elinvoice_kr"] += $row["TotalExcludingVAT"];
                   }   else{
                       $exclude[$row["ArticleNumber"]][$row["Description"]] = implode(";",$row);
                   }

                }
            }
        }
    }
}
echo "</table>";
echo "<form action='/hyreslista/' method='post' style='margin:10px;padding:10px;background-color: #89e2c2'>";
echo "<div class='row'>";
foreach($update as $key => $value){
    echo "<div class='col-1'>$key </div>";
    echo "<div class='col-11'><input type='text' name='updatedata[$key]' value='$value'> </div>";
}
    echo "<div class='col-1'>Hyresgäst</div><div class='col-11'><input type='text' name='fortnox' value='{$_GET["c"]}'> </div>";
    echo "<div class='col-1'> </div><div class='col-11'><input type='submit' name='update_elinvoice' value='Update'> </div>";
echo "</div>";
echo "</form>";
echo "<pre>";
print_r($exclude);
echo "<pre>";

?>


