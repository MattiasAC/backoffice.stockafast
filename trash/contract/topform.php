<?php $templates = ["stocka.txt", "4A.txt"]; ?>
<div class="row">
    <div class="col-4">
        <select class="form-control" name="template">
            <?php
            foreach ($templates as $t) {
                $sel = $c->pre["template"] == $t ? "selected" : "";
                echo "<option $sel>$t</option>";
            }
            ?>
        </select>
        <input type="text" class="form-control" name="saveas" placeholder="Save as"
               value="<?php echo $c->pre["saveas"]; ?>">
        <input type="text" class="form-control" name="spacing" placeholder="Spacing"
               value="<?php echo $c->pre["spacing"]; ?>">
        <input type="text" class="form-control" name="name" placeholder="Namn" value="<?php echo $c->pre["name"]; ?>">
        <input type="text" class="form-control" name="contact" placeholder="Kontakt"
               value="<?php echo $c->pre["contact"]; ?>">
    </div>
    <div class="col-4">
        <input type="text" class="form-control" name="address" placeholder="Adress"
               value="<?php echo $c->pre["address"]; ?>">
        <input type="text" class="form-control" id="zipcity" name="zipcity" placeholder="Zip City"
               value="<?php echo $c->pre["zipcity"]; ?>">
        <input type="text" class="form-control" name="org" placeholder="Org.nr" value="<?php echo $c->pre["org"]; ?>">
        <input type="text" class="form-control" name="pnr" placeholder="P.nr" value="<?php echo $c->pre["pnr"]; ?>">
        <input type="text" class="form-control" name="breaks" placeholder="Breaks" value="<?php echo $c->pre["breaks"]; ?>">
    </div>
    <div class="col-4">
        <input type="text" class="form-control" name="email" placeholder="E-post"
               value="<?php echo $c->pre["email"]; ?>">
        <input type="text" class="form-control" name="tel1" placeholder="Tel 1" value="<?php echo $c->pre["tel1"]; ?>">
        <input type="text" class="form-control" name="tel2" placeholder="Tel 2" value="<?php echo $c->pre["tel2"]; ?>">
        <input type="file" class="form-control" name="image">
    </div>
</div>
<div class="row d-flex justify-content-center">
    <input type="hidden" name="id" value="<?php echo $c->pre["id"]; ?>">
    <input type="submit" class="btn btn-secondary m-2" name="duplicate" value="Duplicate">
    <input type="submit" class="btn btn-secondary m-2" name="delete" value="Delete">
    <input type='submit' class="btn btn-primary m-2" name='update' value='Uppdatera'>
</div>
