<?php
require_once ("c_contract.php");
$c = new Contract();
?>
<div class="card shadow mb-4 ml-3 mt-2 mr-3">
<div class="card-body py-3">
    <form class="row" action="/contract/contract/" method="POST">
        <div class="col-2"> <?php echo $c->selectExisting();?></div>
        <div class="col-2"> <input type="text" class="form-control" style="display: flex" name="newname" value="Namn ny hyresgäst"></div>
        <div class="col-2"> <input type="submit" class="btn btn-primary" name="insert" value="Lägg till"></div>



    </form>
</div>
</div>
<?php
if(!empty($_POST["id"])) {
    ?>
    <div class="card shadow mb-4 ml-3 mt-2 mr-3">
        <div class="card-body py-3">
            <form action="/contract/contract/" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-6">
                        <?php include('topform.php');?>
                        <div class="row">
                            <?php
                            $count = 1;
                            foreach ($c->newmap() as $letter=>$data){
                                $text = isset($c->pre["paragraph"][$letter]) ? $c->pre["paragraph"][$letter]["text"]: "";


                                echo "<div class='col-2 text-dark'><input type='submit' class='btn btn-primary' name='update' value='Uppdatera'></div>";

                                echo "<textarea name='paragraph[$letter]' id='p_{$letter}' class='col-5'>{$text}</textarea>";
                                if(!empty($text)){
                                    $displaycount = $count.". ";
                                    $count++;
                                }else{
                                    $displaycount = "";
                                }
                                echo "<div class='col-5'><div class='col-12 text-dark'><b>{$displaycount}{$data["header"]}</b></div>";
                                echo "<div class='default' destination='p_{$letter}'>{$data["default"]}</div></div>";
                            }
                            ?>
                        </div>


                    </div>
                    <div class="col-6"><?php  echo "{$c->getFileName()}<br><iframe style=\"width:100%;height:10000px\" name=\"iframe\" src=\"https://admin.altahr.se/storage/invoice_pdf/{$c->getFileName()}\"></iframe>";?></div>
                </div>
            </form>
        </div>
    </div>
    <?php
}
?>
