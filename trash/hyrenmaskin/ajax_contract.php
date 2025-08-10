<?php
include("../../code/db.php");
include("c_kontrakt.php");
$k = new Kontrakt();
if(empty($_POST["id"])){
    $contract = [];
    $client = [
        "clientid" => 0
    ];
    $objects = [];
}   else{
    $contract = $k->contracts[$_POST["id"]];
    $client = $k->clients[$contract["clientid"]];
    $objects = json_decode($contract["items"],1);
    //print_r($objects);
}
?>
<form action="/hyrenmaskin/kontrakt/" method="POST">
    <div class="form-group row">
        <label for="clientid" class="col-sm-2 col-form-label">Kund </label>
        <div class="col-sm-10">
            <select name="clientid" id="clientid"
                    class="form-control"><?= $k->option_clients($client["clientid"]) ?></select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Objekt</label>
        <div class="col-sm-10">
            <?php
            foreach ($k->items() as $itemid => $item) {
                $ch = in_array($itemid,$objects) ? "checked=\"checked\"" : "";
                echo '<div class="form-check">';
                echo "<input type='checkbox' class='form-check-input' $ch name='items[$itemid]' id='items_$itemid' value='$itemid'>";
                echo "<label class='form-check-label' for='items_$itemid'>{$item['title']}</label>";
                echo '</div>';
            }
            ?>
        </div>
    </div>
    <div class="form-group row">
        <label for="date_start" class="col-sm-2 col-form-label">Startdatum</label>
        <div class="col-sm-10">
            <input type="date" class="form-control"  name="date_start" value="<?=$contract["date_start"]?>">
        </div>
    </div>
    <div class="form-group row">
        <label for="date_end" class="col-sm-2 col-form-label">Slutdatum</label>
        <div class="col-sm-10">
            <input type="date" class="form-control"  name="date_end" value="<?=$contract["date_end"]?>">
        </div>
    </div>
    <div class="form-group row">
        <label for="discount" class="col-sm-2 col-form-label">Discount</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="discount" name="discount" value="<?=$contract["discount"]?>">
        </div>
    </div>
        <div class="form-group row">
        <label for="discount" class="col-sm-2 col-form-label">Information</label>
        <div class="col-sm-10">
            <textarea class="form-control" name="information"><?=$contract["information"]?></textarea>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-10 offset-sm-2">
            <?php
            if(empty($_POST["id"])){
               echo "<input type='submit' class='btn btn-primary' name='add_kontrakt' value='Lägg till'>";
            }else{
               echo "<input type='hidden' name='contractid' value='{$_POST["id"]}'>";
               echo "<a class='mr-5' href='/hyrenmaskin/kontrakt/deleteContract/{$_POST["id"]}/'><i class='fas fa-trash'></i></a>";
               echo "<input type='submit' class='btn btn-primary' name='update_kontrakt' value='Uppdatera'>";
            }
               ?>
        </div>
    </div>
</form>