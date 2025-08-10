<?php

class Objekt extends db
{
    public $items;

    public function __construct()
    {
        parent::__construct();
        $this->setDB("hyrenmaskin");
        if (isset($_POST["add_price"])) {
            $this->addPrice();
        } elseif (!empty($_POST["delete_priceid"])) {
            $this->delete("items_price", "priceid={$_POST["delete_priceid"]}");
        }
        $this->items = $this->selectArray("items", "itemid", "1=1");
    }

    public function getPriceList($itemid)
    {
        $prices = array_values($this->price($_POST["itemid"]));
        $limits = array();
        $count = 0;
        foreach ($prices as $price) {
            if ($count !== 0) {
                $limits[$count]["from"] = $prices[$count-1]["days"];
                $limits[$count]["to"] = $price["days"] - 1;
                $limits[$count]["price"] = $prices[$count - 1]["price"];
            }
            $count++;
        }
        $temp = $price["days"];
        $limits[$count]["from"] = "{$temp}";
        $limits[$count]["to"] = " ";
        $limits[$count]["price"] = $prices[$count - 1]["price"];
        return $limits;
    }

    private function addPrice()
    {
        $keys = ['days', 'price', 'itemid'];
        $missing = array_diff_key(array_flip($keys), $_POST);
        if (!empty($missing)) {
            trigger_error("Missing keys");
            return;
        }
        $data = array_intersect_key($_POST, array_flip($keys));
        print_r($data);
        $this->insert("items_price", $data);
    }

    public function price($itemid)
    {
        $price = $this->selectArray("items_price", "priceid", "itemid=$itemid ORDER BY days");
        return $price;
    }
}

$o = new Objekt();
?>
    <div class="row m-1">
        <div class="col-6">
            <div class="card shadow m-3">
                <div class="card-header">
                    Uthyrningsobjekt
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tr>
                            <th>Id</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>&nbsp;</th>
                        </tr>
                        <?php
                        foreach ($o->items as $item) {
                            echo "<tr>";
                            echo "<td>{$item["itemid"]}</td>";
                            echo "<td>{$item["title"]}</td>";
                            echo "<td>{$item["text"]}</td>";
                            echo "<td> <a href='#' onclick=\"document.getElementById('itemid').value='{$item["itemid"]}';document.getElementById('postForm').submit(); return false;\">Edit</a></td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <form id="postForm" action="/hyrenmaskin_objekt/" method="post" style="display:none;">
        <input type="text" id="itemid" name="itemid" value="">
        <input type="text" id="delete_priceid" name="delete_priceid" value="">
    </form>
<?php
if (isset($_POST["itemid"])) {
    echo "<h1 class='ml-4'>{$o->items[$_POST["itemid"]]["title"]}</h1>";
    include("price.php");
}
?>