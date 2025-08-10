<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require("../vendor/autoload.php");
use Altahr\Fortnox;

$Fortnox = new Fortnox();
if (isset($_GET["code"])) {
    $Fortnox->setToken($_GET["code"]);
}else{
    echo "Ingen kod";
    print_r($_GET);
}
if(isset($_SESSION['UsingFortnoxUrl'])) {
    echo "<script>window.location.href = \"{$_SESSION['UsingFortnoxUrl']}\";</script>";
    echo "<a href=\"{$_SESSION['UsingFortnoxUrl']}\">{$_SESSION['UsingFortnoxUrl']}</a>";
}else{
    echo "Ingen  UsingFortnoxUrl";
}
?>