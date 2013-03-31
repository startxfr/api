<?php

if ($_GET['code'] != '') {
    $data = array(
        "code" => $_GET['code'],
        "client_id" => "703694493039.apps.googleusercontent.com",
        "client_secret" => "ghpmYHB6pOTB5m1EBpaap2Ju",
        "redirect_uri" => "http://127.0.0.1/github/sxapi/oauth-google2.php",
        "grant_type" => "authorization_code"
    );
    $connexion = curl_init();
    curl_setopt($connexion, CURLOPT_RETURNTRANSFER, true);
    $url = "https://accounts.google.com/o/oauth2/token";
    curl_setopt($connexion, CURLOPT_URL, $url);
    curl_setopt($connexion, CURLOPT_POST, true);
    curl_setopt($connexion, CURLOPT_POSTFIELDS, $data);
    $result = curl_exec($connexion);
    if ($result === false)
        echo "Server error : " . htmlentities(curl_error($this->connexion));
    else {
        $result = json_decode($result);
        var_dump($result);
        $connexion = curl_init();
        curl_setopt($connexion, CURLOPT_RETURNTRANSFER, true);
        $url = "https://www.googleapis.com/oauth2/v1/userinfo?access_token=" . $result->access_token;
        curl_setopt($connexion, CURLOPT_URL, $url);
        $result = curl_exec($connexion);
        $result = json_decode($result);
        var_dump($result);
    }
} else {
    echo "auth error : " . $_GET['error'];
}
?>