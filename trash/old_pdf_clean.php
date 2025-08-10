<style>
    th,td{
        border:1px solid #CCC;
        text-align:left;
        padding:2px;
    }
</style>
<?php
require_once "../../code/db.php";
class clean extends db
{
    public function __construct()
    {
        parent::__construct();


        $data = $this->selectArray("sf_hyreslista","clientid","active in (".$_GET["ids"].") ORDER BY monthly_exvat DESC");

        echo "<table style='border-collapse: collapse'>";
            echo "<tr>";
            echo "<th>Namn</th>";
            echo "<th>Status</th>";
            echo "<th>Area</th>";
            echo "<th>Activity</th>";
            echo "<th>Activity</th>";
            echo "<th>Size</th>";

            echo "<th>Hyra/Avgift</th>";
            echo "<th>Elmätare</th>";
            echo "<th>Elavtal</th>";
            echo "<th>Elgren</th>";
            echo "</tr>";
        foreach ($data as $row){
            echo "<tr>";
            echo "<td>{$row["name"]}</td>";
            echo "<td>{$row["active"]}</td>";
            echo "<td>{$row["area"]}</td>";
            echo "<td>{$row["activity"]}</td>";
            echo "<td>{$row["object"]}</td>";
            echo "<td>{$row["size"]} kvm</td>";

            echo "<td>".number_format($row["monthly_exvat"] + ($row["yearly_fee_exvat"]/12),0,"."," ")."</td>";
            echo "<td>"; echo $row["elgage"] == 1 ? "JA":"";echo "</td>";
            echo "<td>{$row["elavtal"]}</td>";
            echo "<td>{$row["elgren"]}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}
$clean = new clean();
?>