<div class="card m-2">
    <div class="card-body text-bg-secondary">
        <?php

        use Altahr\Elements;

        global $Elmeters;
        $invoiced = 0;
        $array = [];
        if (!empty($Elmeters->ClientData->InvoiceList)) {
            foreach ($Elmeters->ClientData->InvoiceList as $invoice) {
                $array[] = "<b>{$invoice["date"]}</b> Faktura:{$invoice["invoiceid"]} <b>{$invoice["kwh"]} kWh</b><a href=\"/elmeters/{$Elmeters->clientid}/delete_i/{$invoice["id"]}\" onclick=\"return confirm('Är du säker på att du vill ta bort fakturan?');\"> Delete</a>";
                $invoiced += $invoice["kwh"];
            }
            $first = reset($Elmeters->ClientData->InvoiceList);
            $selected = "{$first["date"]}&nbsp;&nbsp;&nbsp;&nbsp;<b>{$first["kwh"]} kWh</b>";
            echo Elements::dropDown($array, $selected, "width:100%;", "width:90%;");
            echo "<table>";
            echo "<tr><th>Invoiced</th><td>{$invoiced} kWh</td></tr>";
            echo "<tr><th>Fortnox</th><td>{$Elmeters->client["fortnox"]}</td></tr>";
            echo "<tr><th>VAT</th><td>{$Elmeters->client["vat"]} %</td></tr>";
            foreach ($Elmeters->ClientData as $property => $value) {
                if (!is_array($value))
                    echo "<tr><th>{$property}</th><td>{$value}</td></tr>";
            }
            echo "</table>";
        }else{
            echo "Inga automatiska fakturor skickade";
        }
        ?>
    </div>
</div>
