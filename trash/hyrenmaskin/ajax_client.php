<?php
include("../../code/db.php");
include("c_kontrakt.php");
$k = new Kontrakt();
if(empty($_POST["id"])){
    $clientid = 0;
    $client=[
      "firstname" => "",
      "lastname" => "",
      "email" => "",
      "personnummer" => "",
      "telephone" => "",
      "email" => "",
    ];
}   else{    $clientid = $_POST["id"];
    $client = $k->clients[$clientid];
}
?>
<form class="row" action="/hyrenmaskin/kontrakt/" method="POST">
    <div class="row m-1">
        <div class="col-3">Förnamn</div>
        <div class="col-9"><input type="text" class="form-control" name="firstname" value="<?=$client["firstname"]?>"></div>
    </div>
    <div class="row m-1">
        <div class="col-3">Efternamn</div>
        <div class="col-9"><input type="text" class="form-control" name="lastname" value="<?=$client["lastname"]?>"></div>
    </div>
    <div class="row m-1">
        <div class="col-3">E-post</div>
        <div class="col-9"><input type="text" class="form-control" name="email" value="<?=$client["email"]?>"></div>
    </div>
    <div class="row m-1">
        <div class="col-3">Telefon</div>
        <div class="col-9"><input type="text" class="form-control" name="telephone" value="<?=$client["telephone"]?>"></div>
    </div>
    <div class="row m-1">
        <div class="col-3">Personnummer</div>
        <div class="col-9"><input type="text" class="form-control" name="personnummer" value="<?=$client["personnummer"]?>"></div>
    </div>
    <div class="row m-1">
        <div class="col-sm-10 offset-sm-2">
            <?php
            if(empty($clientid)){
               echo "<input type='submit' class='btn btn-primary' name='add_client' value='Lägg till'>";
            }else{
               echo "<input type='hidden' name='clientid' value='{$clientid}'>";
               echo "<a class='mr-5' href='/hyrenmaskin/kontrakt/deleteClient/{$clientid}/'><i class='fas fa-trash'></i></a>";
               echo "<input type='submit' class='btn btn-primary' name='update_client' value='Uppdatera'>";
            }
               ?>
        </div>
    </div>
</form>