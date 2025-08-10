<!-- The Modal -->
<div class="modal fade" id="modalClient" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formModalLabel">Ny kund</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="row" action="/hyrenmaskin/kontrakt/" method="POST">
                    <div class="row m-1">
                        <div class="col-3">Förnamn</div>
                        <div class="col-9"><input type="text" class="form-control" name="firstname"></div>
                    </div>
                    <div class="row m-1">
                        <div class="col-3">Efternamn</div>
                        <div class="col-9"><input type="text" class="form-control" name="lastname"></div>
                    </div>
                    <div class="row m-1">
                        <div class="col-3">E-post</div>
                        <div class="col-9"><input type="text" class="form-control" name="email"></div>
                    </div>
                    <div class="row m-1">
                        <div class="col-3">Telefon</div>
                        <div class="col-9"><input type="text" class="form-control" name="telephone"></div>
                    </div>
                    <div class="row m-1">
                        <div class="col-3">Personnummer</div>
                        <div class="col-9"><input type="text" class="form-control" name="personnummer"></div>
                    </div>
                    <div class="row m-1">
                        <div class="col-3"></div>
                        <div class="col-9"><input type="submit" class="btn btn-primary" name="add_client"
                                                  value="Lägg till"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>