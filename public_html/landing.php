<?php
$clientID = '337674646256-oudh360cvbennb198b99dgi7f6mpoo3a.apps.googleusercontent.com';
$clientSecret = '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';
$redirectURI = 'https://admin.altahr.se/landing.php';

$scope = "https://www.googleapis.com/auth/photoslibrary openid https://www.googleapis.com/auth/userinfo.email";
$scope = "https://www.googleapis.com/auth/photoslibrary.edit.appcreateddata";

$authURL = 'https://accounts.google.com/o/oauth2/v2/auth';
$authURL .= '?response_type=code';
$authURL .= '&client_id=' . $clientID;
$authURL .= '&redirect_uri=' . urlencode($redirectURI);
//$authURL .= '&scope=https://www.googleapis.com/auth/gmail.readonly'; // Gmail scope
$authURL .= '&scope='.urlencode($scope);
$authURL .= '&approval_prompt=auto';
echo "<a href='/landing.php'>Start</a> ";
echo "<a href='$authURL'>Log in with Google</a> ";

if (isset($_GET["accessToken"])) {
    $accessToken = $_GET["accessToken"];
    echo "<a href='/landing.php?accessToken=$accessToken'>refresh</a>";
    echo "<hr>";
    include("google_photo.php");
}
elseif (isset($_GET["code"])) {
    $authorizationCode = $_GET["code"];

    $tokenEndpoint = 'https://oauth2.googleapis.com/token';
    $requestBody = http_build_query([
        'code' => $authorizationCode,
        'client_id' => $clientID,
        'client_secret' => $clientSecret,
        'redirect_uri' => 'https://admin.altahr.se/landing.php',
        'grant_type' => 'authorization_code'
    ]);
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $tokenEndpoint,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $requestBody,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/x-www-form-urlencoded'
        ]
    ]);
    $response = curl_exec($curl);// Close cURL session
    curl_close($curl);
    $accessToken = json_decode($response, 1)["access_token"];
    echo "<a href='/landing.php?accessToken=$accessToken'>refresh</a>";
    echo "<hr>";
    include("google_photo.php");
}
?>