<?php

if (isset($_GET['code'])) {
    require_once 'api-lib/lib/plugins/google-api-php-client/src/Google_Client.php';
    session_start();
    $client = new Google_Client();
//    $client->setApplicationName("Google Calendar PHP Starter Application");
    $client->setClientId('703694493039.apps.googleusercontent.com');
    $client->setClientSecret('ghpmYHB6pOTB5m1EBpaap2Ju');
    $client->setRedirectUri('http://127.0.0.1/github/sxapi/oauth-google2.php');
    $client->authenticate($_GET['code']);
    $_SESSION['user']['access_token'] = $client->getAccessToken();
    header('Location: http://127.0.0.1/github/sxapi/oauth-google.php');
    exit;
} else {
    switch ($_GET['error']) {
        case "access_denied":
            echo "No access because : " . $_GET['error'];
            break;
        default:
            echo "No access because : " . $_GET['error'];
            break;
    }
}



//
//
//
//
//require_once 'api-lib/lib/plugins/google-api-php-client/src/Google_Client.php';
//require_once 'api-lib/lib/plugins/google-api-php-client/src/contrib/Google_CalendarService.php';
//session_start();
//
//$client = new Google_Client();
//$client->setApplicationName("Google Calendar PHP Starter Application");
//$client->setClientId('703694493039.apps.googleusercontent.com');
//$client->setClientSecret('ghpmYHB6pOTB5m1EBpaap2Ju');
//$client->setRedirectUri('http://127.0.0.1/github/sxapi/oauth-google2.php');
//$client->setDeveloperKey('AI39si52MyOlUTuIrQJkLYlbTR5TPK46Ny5Kk6p-Ps5s2dh4jD3N4k161rSD4cvljNczLg8cVEkfj92DhIUk25DUt3K7tG9iWg');
//$cal = new Google_CalendarService($client);
//if (isset($_GET['logout'])) {
//    unset($_SESSION['user']['access_token']);
//}
//
//if (isset($_GET['code'])) {
//    $client->authenticate($_GET['code']);
//    $_SESSION['user']['access_token'] = $client->getAccessToken();
////    header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
//}
//
//if (isset($_SESSION['user']['access_token'])) {
//echo "fffff";
//    $client->setAccessToken($_SESSION['user']['access_token']);
//}
//
//if ($client->getAccessToken()) {
//echo "qqqqqqq";
//    $calList = $cal->calendarList->listCalendarList();
//    print "<h1>Calendar List</h1><pre>" . print_r($calList, true) . "</pre>";
//
//
//    $_SESSION['user']['access_token'] = $client->getAccessToken();
//} else {
//echo "jjjjjjj";
//    $authUrl = $client->createAuthUrl();
//    print "<a class='login' href='$authUrl'>Connect Me!</a>";
//}
?>
