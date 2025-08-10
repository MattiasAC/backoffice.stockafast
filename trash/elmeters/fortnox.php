<div class="card m-2" style="background-color: #4CAF50; color: white;">
<div class="card-body">
<?php
$_SESSION['UsingFortnoxUrl'] = $_SERVER['REQUEST_URI'];

global $Elmeters;
$FortnoxEl = new FortnoxEL($Elmeters->clientid,$Elmeters->pdfFileName,$Elmeters);


if (isset($_POST["fortnoxUpdate"])) {
    $Elmeters->insertInvoice();
    $FortnoxEl->FortnoxUpdateInvoice();
}
elseif(isset($_POST["fortnoxInsert"])){
    $Elmeters->insertInvoice();
    $FortnoxEl->FortnoxInsertInvoice();
}

$invoice = $FortnoxEl->getLastInvoice($Elmeters->client["fortnox"]);

echo "Fortnox ID: {$Elmeters->client["fortnox"]}, Avtal: {$Elmeters->client["elavtal"]}<br>";
if($invoice === false){
    echo "<a style='color:white' href='{$FortnoxEl->Fortnox->loginUrl}'>Logga in Fortnox</a><br>";
}
else {
    list($lastInvoice, $invoiceDate, $price) = $Elmeters->setFortnoxForm($invoice);
    echo "
    <form action=\"/elmeters/{$Elmeters->clientid}/\" method='post'>
        <table class='table' style='z-index: 0'>
            <tr>
                <td>Faktura</td>
                <td style='min-width: 150px'><input type='text' name='lastInvoice' class='form-control' value='{$lastInvoice}'></td>
                <td>VAT</td>
                <td style='min-width: 150px'><input type='text' name='vat' class='form-control' value='{$Elmeters->client["vat"]}'></td>
            </tr>
            <tr>
                <td>Datum</td>
                <td><input type='text' name='invoiceDate' class='datepicker form-control' value='{$invoiceDate}'></td>
                <td>Pris</td>
                <td><input type='text' name='price' class='form-control' value='{$price}'></td>
            </tr>
            <tr>
                <td>kWh</td>
                <td><input type='text' name='kwh' class='form-control' value='{$Elmeters->ClientData->ToInvoice}'></td>
                <td>Customer</td>
                <td><input type='text' name='customer' class='form-control' value='{$Elmeters->client["fortnox"]}'></td>
            </tr>
            <tr>
                <td colspan='2' style='text-align: center'>
                    <input type='submit' name='fortnoxInsert' class='btn btn-success' value='Ny Faktura'>
                </td>
                <td colspan='2' style='text-align: center'>
                    <input type='hidden' name='clientid' value='{$Elmeters->clientid}'>
                    <input type='submit' name='fortnoxUpdate' class='btn btn-success' value='Uppdatera Faktura'>
                </td>
            </tr>
        </table>
    </form>";
}
?>
</div>
</div>

 <!--
<input type='submit' name='fortnoxAddrow' class='btn-primary' value='Rad Fortnox'>
<input type='submit' name='fortnoxInvoice' class='btn-primary' value='Ny Fortnox'>

   -->