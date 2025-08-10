<style>
    td {
        vertical-align: top;
    }
</style>
<div class="card shadow mb-4 ml-3 mt-2 mr-3" id="hyresMenu">
    <div class="card-header">
        <a href="/syncFortnox/email/" class="btn">E-post & Telefon</a>
        <a href="/syncFortnox/avtal/" class="btn">Avtalsfaktura</a>
    </div>
    <?php
    require_once("classSync.php");
    $sync = new Sync();
    $sync->crud();
    $hyreslista = $sync->hyreslista();
    $_SESSION['UsingFortnoxUrl'] = $_SERVER['REQUEST_URI'];
    $show = "";
    $res = array();
    if (isset($_GET["b"]) && $_GET["b"] == "email") {
        $array = [];
        $show = "email";
        list($res, $array) = $sync->getCustomerData();
    } elseif (isset($_GET["b"]) && $_GET["b"] == "avtal") {
        $array = [];
        $show = "avtal";
        list($res, $array) = $sync->getLastInvoices();
    }
    if ($res == false) {
        echo "<div class=\"card-header\">";
        echo "<a href='{$sync->loginUrl}' class='btn btn-secondary'>Logga in Fortnox</a>";
        echo "</div>";
    }
    function get($row, $col, $compare)
    {
        global $array;
        if (!isset($array[$row["fortnox"]][$col])) {
            return "<td class='text-bg-danger'>Not found</td>";
        } else if (trim($array[$row["fortnox"]][$col]) != trim($compare)) {
            return "<td class='text-bg-warning'>
            <form action='' method='POST'>
            <input type='hidden' name='clientid' value='{$row["clientid"]}'>
            <input value='{$array[$row["fortnox"]][$col]}' name='update_{$col}' type='submit'>
            </form></td>";
        } else {
            return "<td class=''>{$array[$row["fortnox"]][$col]}</td>";
        }
    }

    function printRows($fortnox, $compare)
    {
        global $array;
        if (!isset($array[$fortnox])) {
            return "Not found";
        } else {
            $return = "<table class='table table-sm'>";
            $hyra = 0;
            $vat = -1;
            foreach ($array[$fortnox] as $row) {
                if (!isset($first)) {
                    $first = true;
                    $return .= "<tr>";
                    foreach ($row as $key => $val) {
                        $short = substr($key,0,10);
                        $return .= "<th title='$key'>{$short}</th>";
                    }
                    $return .= "</tr>";
                }
                $return .= "<tr>";
                foreach ($row as $key => $val) {
                    if (in_array($row["AccountNumber"], array("3001", "3004"))) {
                        if (round($row["TotalExcludingVAT"]) !== round($compare["monthly_exvat"] * $compare["invoicefrequency"])) {
                            $class = "text-bg-warning";
                        } else {
                            $class = "text-bg-success";
                        }
                        if (round($row["VAT"]) !== round($compare["vat"])) {
                            $classVat = "text-bg-warning";
                        } else {
                            $classVat = "text-bg-success";
                        }                        
                    } else {
                        $class = "";
                    }
                    $class = $key == "TotalExcludingVAT" ? $class : "";
                    $classVat = $key == "VAT" ? $classVat : "";

                    $short = substr($val,0,10);
                    $return .= "<td class='{$class}{$classVat}' title='{$val}'>{$short}</td>";
                }
                $return .= "</tr>";
            }
            $return .= "</table>";
            return $return;
        }
    }

    echo "<div class=\"card-body\">";
    if ($show == "email") {
        echo "<table class='table table-sm table-striped'>";
        foreach ($hyreslista as $row) {

            echo "<tr>";
            echo "<td>{$row["fortnox"]}</td>";
            echo "<td>{$row["name"]}</td>";
            echo get($row, "Name", $row["name"]);
            echo "<td>" . implode("<br>", explode(",", $row["email"])) . "</td>";
            echo get($row, "Email", $row["email"]);
            echo "<td>" . implode("<br>", explode(",", $row["telephone"])) . "</td>";
            echo get($row, "Phone", $row["telephone"]);
            echo "</tr>";;
        }
        echo "</table>";
    } else if ($show == "avtal") {
        echo "<table class='table table-sm table-striped w-auto'>";
        foreach ($hyreslista as $row) {

            echo "<tr>";
            echo "<td>{$row["fortnox"]}</td>";
            echo "<td>{$row["name"]}</td>";
            echo "<td>{$row["monthly_exvat"]}</td>";
            echo "<td>{$row["yearly_fee_exvat"]}</td>";
            echo "<td>{$row["vat"]}</td>";
            echo "<td><form action='' method='POST'>
            <input type='hidden' name='clientid' value='{$row["clientid"]}'>
            <input type='hidden' name='fortnox' value='{$row["fortnox"]}'>
            <input value='Update' name='updateLast' type='submit'>
            </form></td>";
            echo "<td>" . printRows($row["fortnox"], $row) . "</td>";
            echo "</tr>";;
        }
        echo "</table>";
    }
    echo "<pre>";
    print_r($res);
    echo "</div>";
    ?>
</div>
