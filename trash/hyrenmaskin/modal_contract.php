<!-- The Modal -->
<div class="modal fade" id="modalContract" tabindex="-1" role="dialog" aria-labelledby="formModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> <!-- Use modal-lg for a larger modal -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formModalLabel">Skapa/ändra kontrakt</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <!--form action="/hyrenmaskin/kontrakt/" method="POST">
                    <div class="form-group row">
                        <label for="clientid" class="col-sm-2 col-form-label">Kund</label>
                        <div class="col-sm-10">
                            <select name="clientid" id="clientid"
                                    class="form-control"><?= $k->option_clients() ?></select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Objekt</label>
                        <div class="col-sm-10">
                            <?php
                            foreach ($k->items() as $itemid => $item) {
                                echo '<div class="form-check">';
                                echo "<input type='checkbox' class='form-check-input' name='items[$itemid]' id='items_$itemid' value='$itemid'>";
                                echo "<label class='form-check-label' for='items_$itemid'>{$item['itemname']}</label>";
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="date_start" class="col-sm-2 col-form-label">Startdatum</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control" id="date_start" name="date_start">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="date_end" class="col-sm-2 col-form-label">Slutdatum</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control" id="date_end" name="date_end">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="discount" class="col-sm-2 col-form-label">Discount</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="discount" name="discount">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-10 offset-sm-2">
                            <input type="submit" class="btn btn-primary" name="add_kontrakt" value="Lägg till">
                        </div>
                    </div>
                </form-->
            </div>
        </div>
    </div>
</div>