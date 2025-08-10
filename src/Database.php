<?php
namespace Altahr;
class Database extends DbGeneric{
    public function __construct()
    {
        parent::__construct();
        
    }
    public function checklogin(){
        if(isset($_POST["login"])) {
            if(false && empty($_POST["g-recaptcha-response"])){
                echo "Fyll i captcha";

            }else{
                if ($_POST["username"] == "viktor" && $_POST["password"] == "stenbocksliden"){
                    $_SESSION["user"] = "Viktor";
                    return true;
                }
            }
            return false;
        }elseif(isset($_SESSION["user"])){
            $_SESSION["user"] = $_SESSION["user"];
            return true;
        }
        return false;
    }
 
    public function log(){
        $save = array();
        $save["REDIRECT_SCRIPT_URL"] = "";
        $save["REDIRECT_SCRIPT_URI"] = "";
        $save["SCRIPT_URI"] = "";
        $save["HTTP_SEC_CH_UA"] = "";
        $save["HTTP_SEC_CH_UA_MOBILE"] = "";
        $save["HTTP_SEC_CH_UA_PLATFORM"] = "";
        $save["HTTP_USER_AGENT"] = "";
        $save["HTTP_REFERER"] = "";
        $save["REMOTE_ADDR"] = "";
        $save["REDIRECT_URL"] = "";
        $save["REDIRECT_QUERY_STRING"] = "";
        $save["QUERY_STRING"] = "";
        $save["REQUEST_URI"] = "";
        $update = array();
        $update["log"] = json_encode(array_intersect_key($_SERVER,$save));
        $this->insert("log",$update);
    }
    public function num($string){
        return number_format($string,2,".","");
    }
    public function printArray($array,$tosum = array()){
        $headSet = false;
        foreach($tosum as $col){
            $sum[$col] = 0;
        }
        $html = "";
        $html .= "<table class='table'>";
        foreach ($array as $row => $cols){
            foreach($sum as $col => $v){
                $num = $this->num($cols[$col]);
                if(is_numeric($num)){
                    $sum[$col] += $this->num($cols[$col]);
                }

            }
            if(!$headSet){
                $html .= "<thead class=\"thead-dark\"><tr>";
                $html .= "<th>KEY</th>";
                foreach ($cols as $c => $col){
                    $html .= "<th>".utf8_encode($c)."</th>";
                }
                $html .= "</tr></thead>";
                $html .= "<tbody>";
                $headSet = true;
            }
            $html .= "<tr>";
            $html .= "<td>".utf8_encode($row)."</td>";
            foreach ($cols as $c => $col){
                $html .= "<td>".utf8_encode($col)."</td>";
            }
            $html .= "</tr>";
        }
        $html .= "</tbody></table>";
        return print_r($sum,1).$html;
    }
    public function logout(){
        session_destroy();
        session_unset();
    }
}
?>