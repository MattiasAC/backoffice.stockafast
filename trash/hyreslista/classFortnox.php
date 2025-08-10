<?php
class Fortnox extends db
{
    public $clientSecret, $authCode, $clientId, $credentials, $redirect_uri,$httpstring;
    public function __construct()
    {
        parent::__construct();
        $this->init();

        if (strpos($_SERVER["REQUEST_URI"], "code") !== false) {
            $this->getToken($_SERVER["REQUEST_URI"]);
        } elseif (strpos($_SERVER["REQUEST_URI"], "refresh") !== false) {
            $this->refreshToken();
        }
    }
    private function init(){

        $scopes = ["companyinformation", "customer", "invoice", "payment", "inbox", "archive", "article", "connectfile", "bookkeeping", "settings", "noxfinansinvoice"];

        $login = array();
        $login["client_id"] = "KtmB69mt9dMe";
        $login["response_type"] = "code";
        $login["state"] = "somestate123";
        $login["scope"] = implode(" ", $scopes);
        $login["redirect_uri"] = "https://admin.altahr.se/hyreslista/update_el/";

        $this->redirect_uri  = $login["redirect_uri"];
        $this->httpstring = http_build_query($login);

        $user = "admin@1469231";
        $this->clientId = "KtmB69mt9dMe";
        $this->clientSecret = "pw0Z0VUfMU";
        $this->credentials = base64_encode($this->clientId . ":" . $this->clientSecret);
    }
    public function getToken($uri)
    {
        $start = substr($uri, strpos($uri, "code") + 5);
        $authCode = trim(substr($start, 0, strpos($start, "&")));
        echo "<br>Authcode: " . $this->authCode;
        echo "<br>";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://apps.fortnox.se/oauth-v1/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'grant_type=authorization_code&code=' . $authCode . '&redirect_uri='.$this->redirect_uri,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Basic ' . $this->credentials . '',
                'ClientId: ' . $this->clientId . '',
                'ClientSecret: ' . $this->clientSecret . '',
                'Credentials: ' . $this->credentials . ''
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        $_SESSION["fortnoxToken"] = json_decode($response, 1)["access_token"];
        $_SESSION["fortnoxRefresh"] = json_decode($response, 1)["refresh_token"];
    }
    public function refreshToken()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://apps.fortnox.se/oauth-v1/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'grant_type=refresh_token&refresh_token=' . $_SESSION["fortnoxRefresh"] . '',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Basic ' . $this->credentials . '',
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $_SESSION["fortnoxToken"] = json_decode($response, 1)["access_token"];
        $_SESSION["fortnoxRefresh"] = json_decode($response, 1)["refresh_token"];
    }
    private function showForm(){
        echo "<div>";
        echo "<a class=\"btn btn-primary\" href=\"https://apps.fortnox.se/oauth-v1/auth?{$this->httpstring}\">Logga in</a>";
        echo "<a class=\"btn btn-primary\" href=\"https://admin.altahr.se/hyreslista/update_el/?refresh=1323\">Refresh</a>";
        echo "</div>";
    }
    public function curl($endpoint, $data = false, $http = false)
    {
        if(!isset($_SESSION["fortnoxToken"])){
            $this->showForm();
            return false;
        }
        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $_SESSION["fortnoxToken"];
        $headers[] = 'Accept: application/json';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $endpoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if ($http == "UPLOAD") {
            $headers[] = 'Content-Type: multipart/form-data';
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        } elseif ($http == "POST") {
            $headers[] = 'Content-Type: application/json';
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($http == "PUT") {
            $headers[] = 'Content-Type: application/json';
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $res = curl_exec($curl);
        $json = json_decode($res, 1);
        if(!empty($json["ErrorInformation"]) || !empty($json["message"])){
            $this->showForm();
            echo "<pre>";
            print_r($json);
            echo "</pre>";
            return false;

        }
        return $json;
    }
}
?>