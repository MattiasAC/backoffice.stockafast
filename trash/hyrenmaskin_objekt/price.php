<div class="row m-1">
    <div class="col-3">
        <div class="card shadow m-3">
            <div class="card-header">
                Pris
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <tr>
                        <th>Days</th>
                        <th>Price</th>
                        <th>&nbsp;</th>
                    </tr>
                    <?php
                    $prices = $o->price($_POST["itemid"]);

                    foreach ($prices as $price) {

                        echo "<tr>";
                        echo "<td>{$price["days"]}</td>";
                        echo "<td>{$price["price"]}</td>";
                         echo "<td> <a href='#' onclick=\"document.getElementById('itemid').value='{$o->items[$_POST["itemid"]]["itemid"]}';document.getElementById('delete_priceid').value='{$price["priceid"]}';document.getElementById('postForm').submit(); return false;\">X</a></td>";

                        echo "</tr>";
                    }


                    ?>
                    <form action="/hyrenmaskin_objekt/" method="post">
                        <tr>
                        <th><input type="text" name="days" class="form-control"></th>
                        <th><input type="text" name="price" class="form-control"></th>
                        <th>
                            <input type="hidden" name="itemid" value="<?=$_POST["itemid"];?>">
                            <input type="submit" name="add_price" class="form-control" value="add"></th>
                        <th>&nbsp;</th>
                    </tr>
                    </form>
                </table>
                <?php
                echo "<pre>";
                foreach($o->getPriceList($_POST["itemid"]) as $row){
                    echo "<b>{$row["from"]}-{$row["to"]} dagar</b> {$row["price"]} kr/dag inkl. moms<br>";
                }
                ?>
            </div>
        </div>
    </div>
</div>