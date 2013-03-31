<?php


$url = "https://accounts.google.com/o/oauth2/auth?";
$url.= "scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile&";
//$url.= "state=profile&";
$url.= "redirect_uri=http%3A%2F%2F127.0.0.1%2Fgithub%2Fsxapi%2Foauth-google2.php&";
$url.= "response_type=code&";
$url.= "access_type=offline&";
$url.= "client_id=703694493039.apps.googleusercontent.com&";
$url.= "approval_prompt=force";


header("location: $url");
exit;
?>