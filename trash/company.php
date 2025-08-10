<?php
$company = array();
$company["ac"]["company"] = "Altahr Consulting AB";
$company["ac"]["cvr"] = "556811-9381";
$company["ac"]["vat"] = "SE556811938101";
$company["ac"]["street"] = "Lilla Hammars väg 13A";
$company["ac"]["zip"] = "236 37";
$company["ac"]["city"] = "Höllviken";
$company["ac"]["bg"] = "648 - 8571";
$company["ac"]["bankaccount"] = "SEB 5641 101 6100";
$company["ac"]["swift"] = "SE0450000000056411016100";
$company["ac"]["bic"] = "ESSESESS";
$company["ac"]["eori"] = "SE5568119381";

$company["sv"]["company"] = "Serio Verify ApS";
$company["sv"]["cvr"] = "33057970";
$company["sv"]["vat"] = "DK33057970";
$company["sv"]["street"] = "Store Kongensgade 66";
$company["sv"]["zip"] = "1264";
$company["sv"]["city"] = "Copenhagen";
$company["sv"]["bg"] = "";
$company["sv"]["bankaccount"] = "";
$company["sv"]["swift"] = "";
$company["sv"]["bic"] = "";
$company["sv"]["eori"] = "";

$company["sf"]["company"] = "Stockamöllan Fastigheter AB";
$company["sf"]["cvr"] = "559077-2504";
$company["sf"]["vat"] = "SE559077250401";
$company["sf"]["street"] = "Lilla Hammars väg 13A";
$company["sf"]["zip"] = "236 37";
$company["sf"]["city"] = "Höllviken";
$company["sf"]["bg"] = "5156-2171";
$company["sf"]["bankaccount"] = "SEB 5641 10 367 21";
$company["sf"]["swift"] = "SE17 5000 0000 0564 1103 6721";
$company["sf"]["bic"] = "ESSESESS";
$company["sf"]["eori"] = "";

$company["ss"]["company"] = "Stockamöllan Service AB";
$company["ss"]["cvr"] = "556854-1592";
$company["ss"]["vat"] = "SE556854159201";
$company["ss"]["street"] = "Lilla Hammarsväg 13A";
$company["ss"]["zip"] = "236 37";
$company["ss"]["city"] = "Höllviken";
$company["ss"]["bg"] = "765-3363";
$company["ss"]["bankaccount"] = "5641 101 8057";
$company["ss"]["swift"] = "SE3050000000056411018057";
$company["ss"]["bic"] = "ESSESESS";
$company["ss"]["eori"] = "";
?>

<div class="row mr-2 ml-3">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Företagsuppgifter</h6>
        </div>
        <div class="card-body">
                <table>
                    <tr><td><b>Företagsnamn</b></td><td><?php echo $company[$_GET["b"]]["company"];?></td></tr>
                    <tr><td><b>Organisationsnummer</b></td><td><?php echo $company[$_GET["b"]]["cvr"];?></td></tr>
                    <tr><td><b>VAT no</b></td><td><?php echo $company[$_GET["b"]]["vat"];?></td></tr>
                    <tr><td><b>Adress</b></td><td>
                            <?php echo $company[$_GET["b"]]["street"];?><br>
                            <?php echo $company[$_GET["b"]]["zip"];?> <?php echo $company[$_GET["b"]]["city"];?>
                        </td></tr>
                    <tr><td><b>Bankgiro</b></td><td><?php echo $company[$_GET["b"]]["bg"];?></td></tr>
                    <tr><td><b>Kontonummer</b></td><td><?php echo $company[$_GET["b"]]["bankaccount"];?></td></tr>
                    <tr><td><b>SWIFT</b></td><td><?php echo $company[$_GET["b"]]["swift"];?></td></tr>
                    <tr><td><b>BIC</b></td><td><?php echo $company[$_GET["b"]]["bic"];?></td></tr>
                    <tr><td><b>EORI</b></td><td><?php echo $company[$_GET["b"]]["eori"];?></td></tr>
                </table>
            <?php $textarea = "Mattias Altahr-Cederberg \n{$company[$_GET["b"]]["company"]} \nOrganisationsnummer: {$company[$_GET["b"]]["cvr"]} \n\nAdress:\n{$company[$_GET["b"]]["street"]} \n{$company[$_GET["b"]]["zip"]} {$company[$_GET["b"]]["city"]} \n\nBankkonto: {$company[$_GET["b"]]["bankaccount"]} \nBankgiro: {$company[$_GET["b"]]["bg"]}";?>
            <textarea style="width:100%;height:300px;"><?php echo $textarea;?></textarea>

        </div>
    </div>
</div>