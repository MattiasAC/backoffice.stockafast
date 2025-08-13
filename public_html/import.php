<?php
//use Altahr\Database;
//require("../vendor/autoload.php");
//$db = new Database();
//
//$content = file_get_contents('text.txt');
//$chunks = array_filter(array_map('trim', explode("*", $content)), fn($chunk) => !empty($chunk) && !preg_match("/^\n+$/", $chunk));
//$sortorder = 1;
//
//foreach ($chunks as $chunk) {
//    $lines = explode("\n", trim($chunk), 2);
//    $name = $db->real_escape_string(trim($lines[0]));
//    $text = $db->real_escape_string(trim($lines[1] ?? ''));
//    if (!empty($name)) {
//        $db->query("INSERT INTO kontrakt_std (name, text, sortorder) VALUES ('$name', '$text', '$sortorder')");
//        $sortorder++;
//    }
//}
//
//echo "Import completed.";
//$db->close();
//?>