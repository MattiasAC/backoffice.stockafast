<form method="post" class="p-2" style="background-color: #d7f1f5;">
    <?php
    global $L;
    use Altahr\Elements;

    echo Elements::select(["lokaler"=>"Lokaler","el_meters"=>"Elmätare"],"table",$Edit->table,"display:inline;width:200px;");
    echo "<input class=\"btn btn-secondary m-1\" type='submit' value='Refresh'>";

    ?>
</form>