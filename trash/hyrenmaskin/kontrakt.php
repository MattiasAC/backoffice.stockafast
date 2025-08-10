<?php
include("c_kontrakt.php");
$k = new Kontrakt();
include("modal_client.php");
include("modal_contract.php");
?>
<div class="row m-1">
    <div class="col-6">
        <div class="card shadow m-3">
            <div class="card-header">
                <button id="test" type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalContract" contractid="0">Nytt
                    kontrakt
                </button>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <tr>
                        <th>Id</th>
                        <th>Kund</th>
                        <th>Objekt</th>
                        <th>Start</th>
                        <th>Slut</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    <?php
                    foreach ($k->contracts as $contractid => $contract) {
                        $client = $k->clients[$contract["clientid"]];
                        $renting = json_decode($contract["items"], 1);
                        $items = $k->items;
                        echo "<tr>";
                        echo "<td>{$contract["contractid"]}</td>";
                        echo "<td>{$client["firstname"]} {$client["lastname"]}</td>";
                        echo "<td>";
                        foreach ($renting as $rent) {
                            echo $items[$rent]["title"] . "<br>";
                        }
                        echo "</td>";
                        echo "<td>{$contract["date_start"]}</td>";
                        echo "<td>{$contract["date_end"]}</td>";
                        echo "<td><a href='/hyrenmaskin/kontrakt/printPDF/{$contractid}/'><i class='fas fa-file-pdf'></i></a></td>";
       echo "<td><a href='#'><i class='fas fa-edit' style='cursor: pointer' data-toggle='modal' data-target='#modalContract' contractid='{$contractid}'></i></a></td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="card shadow m-3">
            <div class="card-header">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalClient" clientid="0">Ny kund
                </button>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <tr>
                        <th>Förnamn</th>
                        <th>Efternamn</th>
                        <th>E-post</th>
                        <th>Personnummer</th>
                        <th>Telefon</th>
                        <th></th>
                    </tr>
                    <?php
                    foreach ($k->clients as $clientid => $client) {
                        echo "<tr>";
                        echo "<td>{$client["firstname"]}</td>";
                        echo "<td>{$client["lastname"]}</td>";
                        echo "<td>{$client["email"]}</td>";
                        echo "<td>{$client["personnummer"]}</td>";
                        echo "<td>{$client["telephone"]}</td>";
                         echo "<td><a href='#'><i class='fas fa-edit' style='cursor: pointer' data-toggle='modal' data-target='#modalClient' clientid='{$clientid}'></i></a></td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
if (isset($_GET["c"]) && $_GET["c"] == "printPDF") {
    ?>
    <iframe src="https://admin.altahr.se/storage/invoice_pdf/hyrenmaskin.pdf" style="height: 2000px"></iframe>
    <?php
}
?>
