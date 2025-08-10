<?php
require_once("../../code/db.php");
$db = new db();
if (!empty($_GET["id"])) {
    $pre = $db->selectOne("el_measured", "id=" . $_GET["id"]);
    $pre["submit"] = "<input type='submit' name='deleteMeasure' class='btn-secondary' value='Delete'>";
    $pre["submit"] .= "<input type='submit' name='updateMeasure' class='btn-secondary' value='Uppdatera mätpunkt'>";
} else {
    $pre = array();

    $pre["datetime"] = date("Y-m-d");
    $pre["value"] = 0;
    $pre["submit"] = "<input type='submit' name='addMeasure' class='btn-primary' value='Lägg till mätpunkt'>";
}

echo "
  <form action='/elmeters/{$_GET["clientid"]}/' method='post' enctype='multipart/form-data' style=''>
  <table class='table table-striped'>
  <tr><td>ClientID</td><td>{$_GET["clientid"]}<input type='hidden'  name='clientid' value='{$_GET["id"]}'></td></tr>
  <tr><td>MeterId</td><td>{$_GET["meterid"]}<input type='hidden' name='meterid' value='{$_GET["meterid"]}'></td></tr>
  <tr><td>Id</td><td>{$_GET["id"]}<input type='hidden'  name='id' value='{$_GET["id"]}'></td></tr>
  <tr><td>Datum</td><td><input type='text' name='datetime' class='form-control' value='{$pre["datetime"]}'></td></tr>
  <tr><td>kWh</td><td><input type='text' name='value' class='form-control'  value='{$pre["value"]}'></td></tr>
  <tr><td>Image</td><td><input type='file' name='image' class='form-control'></td></tr>
  <tr><td colspan='2' style='text-align: center'>{$pre["submit"]}</td></tr>
</table>
  </form>";
?>