<?php
use Altahr\Elements;
global $list,$Hyreslista;
?>
<input type="hidden" id="edit" name="edit" value="0">
<?php if (!empty($_POST["edit"])): ?>
    <div class="card shadow mb-4 ml-3 mt-2 mr-3">
        <div class="card-body p-0">
            <div class="row m-0 p-2">
                <?php
                ksort($list[$_POST["edit"]]);
                foreach ($list[$_POST["edit"]] as $k => $v): ?>
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 p-1">
                        <div class="row p-0 m-0">
                            <div class="col-4 p-0" style="white-space:nowrap; overflow: hidden" title="<?= $k; ?>">
                                <?= $k; ?>
                            </div>
                            <div class="col-8 p-0">
                                <?php
                                if($k == "active"){echo Elements::select($Hyreslista->active,"newval[$k]",$v);}
                                else if($k == "area"){echo Elements::select($Hyreslista->area,"newval[$k]",$v);}
                                else{
                                    echo "<input type=\"text\" class=\"form-control\" style=\"width:100%; float:right\" name=\"newval[{$k}]\" value=\"{$v}\">";
                                }

                                ?>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card-footer">
            <div class="row m-0 p-0">
                <div class="col-3">
                    <input type="file" class="form-control mb-2" name="file">
                </div>
                <div class="col-3">
                    <?php if (!empty($Hyreslista->uploads[$_POST["edit"]])): ?>
                        <div class="">
                            <?php foreach ($Hyreslista->uploads[$_POST["edit"]] as $file): ?>
                                <input type="submit" class="btn btn-danger" value="Ta bort"
                                       name="delete_file[<?= $file; ?>]">
                                <a href="/storage/uploads/<?= $file; ?>" target="_blank"
                                   class="btn btn-link"><?= $file; ?></a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-6">
                    <div class="d-flex gap-2 justify-content-end" style="text-align: right;">
                        <input type="submit" class="btn btn-primary" name="addasnew" value="Lägg till som ny">
                        <input type="hidden" name="clientid" value="<?= $_POST["edit"]; ?>">
                        <input type="submit" class="btn btn-primary" name="update" value="Uppdatera hyresgäst">
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
