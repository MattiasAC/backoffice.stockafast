<?php
use Altahr\Database;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['csv']) && $_FILES['csv']['error'] == 0) {
        $uploadedFile = $_FILES['csv'];
        $uploadDir = 'html/elmeters/kraftringen/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = basename($uploadedFile['name']);
        $targetFilePath = $uploadDir . "kraftringen.csv";
           echo $uploadedFile['type'];
        $allowedTypes = ['text/csv'];
        if (in_array($uploadedFile['type'], $allowedTypes)) {
            if (move_uploaded_file($uploadedFile['tmp_name'], $targetFilePath)) {
                echo "Filen har laddats upp: $targetFilePath";
            } else {
                echo "Ett fel uppstod när filen skulle laddas upp.";
            }
        } else {
            echo "Endast CSV är tillåtna.";
        }
    } else {
        echo "Ingen fil valdes eller ett fel uppstod.";
    }
}
?>

<div class="card">
<div class="card-body">
    Logga in på kraftringen, välj förbrukning dag, välj period > 1 år. Exportera CSV och ladda upp här.
</div>
<div class="card-header">

    <form action='/elmeters/kraftringen/' method="post" enctype="multipart/form-data">
        <input type="file" name="csv">
        <input type="submit" vakue="Upload">
    </form>
</div>
<div class="card-body">
    <?php
    $db = new Database();
    $rows = file("html/elmeters/kraftringen/kraftringen.csv");
    $first = time();
    $last = strtotime("2001-01-01");
    foreach($rows as $row){
        $parts = explode(";",$row);
        if(count($parts) == 2) {
            $datum = strtotime(str_replace("\"","",$parts[0]));


            if($datum < time() && $datum > strtotime("2010-01-01")){
               $value = number_format(strtr($parts[1], array("\"" => "", "," => ".")), 0, "", "");
                $sql = "INSERT INTO el_kraftringen (date, `value`) VALUES ('" . date("Y-m-d", $datum) . "', '{$value}')  ON DUPLICATE KEY UPDATE `value` = '{$value}'";
                if($db->query($sql)){
                    $first = $datum < $first ? $datum : $first;
                    $last = $datum > $last ? $datum : $last;
                }

            }
        }

    } echo "Uppdaterat ".date("Y-m-d",$first)." till ".date("Y-m-d",$last);
    ?>
</div>
</div>