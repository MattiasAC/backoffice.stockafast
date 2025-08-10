<?php
use Altahr\Database;
ini_set('session.gc_maxlifetime', 72 * 3600);
session_start();
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
    $data = date("M-d H:i:s") . ": " . $errfile . "(" . $errline . ") " . $errstr;
    echo "<div style='color:red'>$data</div>";
}
$lifetime = ini_get("session.gc_maxlifetime");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_error_handler("myErrorHandler");
define("WWW", "https://admin.altahr.com");
require("../vendor/autoload.php");



//require_once "code/db.php";
$db = new Database();
$page = isset($_GET["page"]) ? $_GET["page"] : "tenants";
if ($page == "logout") {
    $db->logout();
}
if (!$db->checklogin() || $page == "logout") {
    include "login.php";
    die();
}
define("stammisen", true);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" href="https://admin.altahr.se/favicon.ico">
    <title>Stockamöllan Fastigheter AB</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"  integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="/css/sb-admin-2.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/stocka.css" rel="stylesheet">
    <?php
    if (file_exists("css/{$page}.css")) {
        echo "<link href=\"/css/{$page}.css\" rel=\"stylesheet\">";
    }
    ?>
</head>
<body style="margin: 0px;padding: 0px;">
<?php include("html/menu.php"); ?>
<div style="margin-left:200px; display: flow-root;" id="mcontent">
    <?php
    if (isset($_GET["b"]) && file_exists("html/$page/{$_GET["b"]}.php")) {
        $subPage = $_GET["b"] . ".php";
    } else {
        $subPage = "index.php";
    }
    if (file_exists("html/$page")) {
        include("html/$page/$subPage");
    } elseif (file_exists("html/{$page}.php")) {
        include("html/{$page}.php");
    } else {
        include("html/notfound.php");
    }
    ?>
</div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script>
    $('.datepicker').datepicker({ format: 'yyyy-mm-dd' });
</script>
</html>
