<?php

namespace Altahr;
class Elements extends Database
{
    public static function dropDown($array, $selected, $style= "",$style_ul= "",$class="btn btn-primary dropdown-toggle",$id = false){
         $id = $id ?: "dropdownMenuButton_" . uniqid();
        $return = "";
        if($selected){
            $return.= "<button class=\"{$class}\" style=\"{$style}\" type=\"button\" id=\"{$id}\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">";
            $return.= $selected;
            $return.= "</button>";
        }

        $return.= "<ul class=\"dropdown-menu\" style=\"{$style_ul}\"  aria-labelledby=\"{$id}\">";
        foreach($array as $key=>$val){
            $return.= "<li class=\"dropdown-item\">{$val}</li>";
        }
        $return.= "</ul>";
        return $return;
    }
    public static function select($array,$name, $selected, $style= "",$style_ul= "",$class="btn btn-primary dropdown-toggle",$id = false){
        $return = "";
        $return.= "<select name='{$name}' style='{$style}' onchange='suaaaaaaaaaaaaaaabmit()' class='form-control'>";
        foreach($array as $key=>$val){
            $select = $selected == $key ? "selected=\"selected\"" : "";
            $return.= "<option value='{$key}' $select>{$val} </option>";
        }
        $return.= "</select>";
        return $return;
    }
}

?>