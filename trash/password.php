<?php
require_once ("code/controller/c_password.php");
$password = new Password();
?>
<div class="card shadow mb-4 ml-3 mt-2 mr-3" id="customerLog">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <form action="/password/" method="post">
            <input type="text" name="search" id="searchList" placeholder="Söktext">
            <!--input type="submit" name="search"-->
            </form>
        </h6>
    </div>
</div>
<?php
if(isset($_GET["b"]) && $_GET["b"] == "edit"){
?>
<div class="card shadow mb-4 ml-3 mt-2 mr-3" id="customerLog">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Lösenord</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <div id="dataTable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-bordered dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
                            <thead>
                            <tr role="row">
                                <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending">Field1</th>
                                <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending">Field2</th>
                                <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending">Field3</th>
                                <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending">Field4</th>
                                <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending">Field5</th>
                            </thead>
                            <form action="/password/" method="post">
                            <tbody id="display_log">
                            <tr>
                                <td><input type="text" name="field1" value="<?php echo $password->decrypt($password->pre["field1"]); ?>"></td>
                                <td><input type="text" name="field2" value="<?php echo $password->decrypt($password->pre["field2"]); ?>"></td>
                                <td><input type="text" name="field3" value="<?php echo $password->decrypt($password->pre["field3"]); ?>"></td>
                                <td><input type="text" name="field4" value="<?php echo $password->decrypt($password->pre["field4"]); ?>"></td>
                                <td><input type="text" name="field5" value="<?php echo $password->decrypt($password->pre["field5"]); ?>"></td>
                            </tr>
                            <tr>
                                <td colspan=5>
                                    <input type="hidden" name="id" value="<?php echo $_GET["c"]; ?>">
                                    <input type="submit" name="update" value="Uppdatera">
                                    <input type="submit" name="delete" value="Ta bort">
                                </td>
                            </tr>
                            </tbody>
                            </form>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
}
?>
<div class="card shadow mb-4 ml-3 mt-2 mr-3" id="customerLog">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Lösenord <a href="/password/add/">Lägg till</a> </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <div id="dataTable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                <div class="row"><div class="col-sm-12">
                        <table class="table table-sm table-bordered dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
                            <thead>
                            <tr role="row">
                                <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Name: activate to sort column descending">Id</th>
                                <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending">Field1</th>
                                <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending">Field2</th>
                                <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending">Field3</th>
                                <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending">Field4</th>
                                <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending">Field5</th>
                            </thead>
                            <tbody id="hyresLista" >
                            <?php
                            foreach($password->passwords as $key => $row){
                                echo "<tr class=\"clientForm\">";
                                echo "<td><a href='/password/edit/{$row["id"]}/'>{$row["id"]}</a></td>";
                                echo "<td>{$password->decrypt($row["field1"])}</td>";
                                echo "<td>{$password->decrypt($row["field2"])}</td>";
                                echo "<td>{$password->decrypt($row["field3"])}</td>";
                                echo "<td>{$password->decrypt($row["field4"])}</td>";
                                echo "<td>{$password->decrypt($row["field5"])}</td>";
                                echo "</tr>";
                            }
                            ?>

                            </tbody>
                        </table>
                 </div>
            </div>
        </div>
    </div>
</div>
