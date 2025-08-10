<?php

namespace Altahr;
class Fortnox extends Database
{
    public $clientSecret, $authCode, $clientId, $credentials, $redirect_uri, $loginUrl;

    public function __construct()
    {
        parent::__construct();
        $this->init();
    }

    private function init()
    {
        $scopes = ["companyinformation", "customer", "invoice", "payment", "inbox", "archive", "article", "connectfile", "bookkeeping", "settings", "noxfinansinvoice"];

        //$this->redirect_uri = "https://admin.altahr.se/landingpages/fortnox.php";
        $this->redirect_uri = "https://backoffice.stockafast.se/fortnox_landing.php";
        $this->clientId = "KtmB69mt9dMe";
        $this->clientSecret = "pw0Z0VUfMU";
        $this->credentials = base64_encode($this->clientId . ":" . $this->clientSecret);

        $login = array();
        $login["client_id"] = $this->clientId;
        $login["response_type"] = "code";
        $login["state"] = "somestate123";
        $login["scope"] = implode(" ", $scopes);
        $login["redirect_uri"] = $this->redirect_uri;
        $this->loginUrl = "https://apps.fortnox.se/oauth-v1/auth?".http_build_query($login);
    }

    public function setToken($code)
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
            CURLOPT_POSTFIELDS => 'grant_type=authorization_code&code=' . $code . '&redirect_uri=' . $this->redirect_uri,
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
        $json = json_decode($response, 1);
        $_SESSION["fortnoxToken"] = $json["access_token"];
        $_SESSION["fortnoxRefresh"] = $json["refresh_token"];
        $_SESSION["Expires"] = time() + $json["expires_in"];
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
        $json = json_decode($response, 1);
        $_SESSION["fortnoxToken"] = $json["access_token"];
        $_SESSION["fortnoxRefresh"] = $json["refresh_token"];
        $_SESSION["Expires"] = time() + $json["expires_in"];
    }

    public function curl($endpoint, $http = "GET", $data = false)
    {
        if (!isset($_SESSION["fortnoxToken"]) || !isset($_SESSION["fortnoxRefresh"]) || !isset($_SESSION["Expires"])) {
            return false;
        }
        $timeToExpires = $_SESSION["Expires"] - time();
        if ($timeToExpires < 300) {
            echo "RefreshToken";
            $this->refreshToken();
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
        if (!empty($json["ErrorInformation"]) || !empty($json["message"])) {
            echo "<pre>";
            print_r($json);
            echo "</pre>";
            return false;
        }
        return $json;
    }
}

?>