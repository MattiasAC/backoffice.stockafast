<?php
global $Hyreslista, $columns;
if (sizeof($_POST) == 0) {
    $_POST["active"]["1"] = 1;
    $_POST["column"]["name"] = true;
    $_POST["column"]["elavtal"] = true;
    $_POST["column"]["files"] = true;
    $_POST["column"]["index"] = true;
    $_POST["column"]["next_index"] = true;
    $_POST["column"]["contract_from"] = true;
    $_POST["column"]["inc"] = true;
    $_POST["column"]["monthly_exvat"] = true;
    $_POST["column"]["clientid"] = true;
    $_POST["column"]["invoicefrequency"] = true;
    $_POST["column"]["size"] = true;
}
$_SESSION["pdf"] = $_POST["pdf"] ?? null;
if (!isset($_POST["pdf"])) unset($_SESSION["pdf"]);
$_SESSION["order"] = $_POST["order"] ?? "ASC";
$_SESSION["orderby"] = $_POST["orderby"] ?? "name";
?>
<button type="button" class="btn btn-primary ml-2 mt-0" onclick="toggleVisibility()">Via menyn</button>
<div class="card shadow mb-4 ml-3 mt-2 mr-3" id="hyresMenu">
    <div class="card-body p-0" id="hyresMenu">
        <div class="row m-0 p-0">
            <div class="col-3 p-2">
                <div class='column-container'
                     style='display: flex; flex-wrap: wrap; max-height: 300px; overflow: auto;'>
                    <?php echo $Hyreslista->selectArea(); ?>
                </div>
            </div>
            <div class="col-4 p-2" style="background-color: #EEE">
                <div class='column-container'
                     style='display: flex; flex-wrap: wrap; max-height: 220px; overflow: auto;'>
                    <?php echo $Hyreslista->selectColumns($columns); ?>
                </div>
            </div>
            <div class="col-3 p-2">
                <div class='column-container'
                     style='display: flex; flex-wrap: wrap; max-height: 300px; overflow: auto;'>
                    <?php echo $Hyreslista->selectActivity(); ?>
                </div>
            </div>
            <div class="col-2 p-2" style="background-color: #EEE">
                <input type="hidden" id="orderby" value="<?= $_SESSION["orderby"]; ?>" name="orderby">
                <input type="hidden" id="order" value="<?= $_SESSION["order"]; ?>" name="order">
                <input type="submit" value="Ny hyresgäst" class="btn" name="insert"><br><br>
                <input type="checkbox" name="pdf" value="1"
                       onclick="submit()" <?= isset($_SESSION["pdf"]) ? "checked" : "" ?>> Visa PDF
                <input type="hidden" value="true" name="formsubmit">
            </div>
        </div>
    </div>
</div>
