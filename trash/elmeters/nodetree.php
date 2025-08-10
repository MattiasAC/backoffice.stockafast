<?php
    include("class/classConsumption.php");
    $c = new Consumption();
?>
<div class="card">
    <div class="card-body">
        <?php
        echo '<table>';
        $meter[0]=[
                "parent" => false,
                "meterid" => 0,
                "name_internal" => "Stockamöllan",
                "comment" => "Fakturerat Kraftringen",
                "rooms" => "SF",
                ];
        $meter[100]=[
                "parent" => false,
                "meterid" => 100,
                "name_internal" => "Altahr",
                 "comment" => "Fakturerat E.ON",
                "rooms" => "AC",
                ];
        echo $c->renderTree($meter,1);
        echo '</table>';


        ?>
    </div>
</div>
<div class="card">
    <div class="card-body">
        Missing:<br>
        <?php
        print_r(array_diff_key($c->usedMeters,$c->allMeters));
        print_r(array_diff_key($c->allMeters,$c->usedMeters));
        ?>
    </div>
    </div>