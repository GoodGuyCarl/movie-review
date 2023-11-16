<?php
session_start();
require 'vendor/autoload.php';

$fb = new Facebook\Facebook([
    'app_id' => '6621295174664204',
    'app_secret' => 'f5801e183902397cb28ce980fae25af8',
    'default_graph_version' => 'v2.10',
]);

$helper = $fb->getRedirectLoginHelper();

try {
    $accessToken = $helper->getAccessToken();
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}

if (!isset($accessToken)) {
    if ($helper->getError()) {
        header('HTTP/1.0 401 Unauthorized');
        echo "Error: " . $helper->getError() . "\n";
        echo "Error Code: " . $helper->getErrorCode() . "\n";
        echo "Error Reason: " . $helper->getErrorReason() . "\n";
        echo "Error Description: " . $helper->getErrorDescription() . "\n";
    } else {
        header('HTTP/1.0 400 Bad Request');
        echo 'Bad request';
    }
    exit;
}
if (isset($accessToken)) {
    $oAuth2Client = $fb->getOAuth2Client();
    $tokenMetadata = $oAuth2Client->debugToken($accessToken);
    $_SESSION['fb_user_id'] = (string) $tokenMetadata->getUserId();
    $_SESSION['fb_name'] = (string) $tokenMetadata->getField('name');
    $_SESSION['role'] = 'user';
    $_SESSION['expire'] = time() + 30 * 60;
    $_SESSION['fb_access_token'] = (string) $accessToken;
    header('Location: index.php');
    exit;
}
// // Logged in
// echo '<h3>Access Token</h3>';
// var_dump($accessToken->getValue());

// // The OAuth 2.0 client handler helps us manage access tokens
// $oAuth2Client = $fb->getOAuth2Client();

// // Get the access token metadata from /debug_token
// $tokenMetadata = $oAuth2Client->debugToken($accessToken);
// echo '<h3>Metadata</h3>';
// var_dump($tokenMetadata);

// // Validation (these will throw FacebookSDKException's when they fail)
// $tokenMetadata->validateAppId($config['app_id']);
// // If you know the user ID this access token belongs to, you can validate it here
// //$tokenMetadata->validateUserId('123');
// $tokenMetadata->validateExpiration();

// if (!$accessToken->isLongLived()) {
//     // Exchanges a short-lived access token for a long-lived one
//     try {
//         $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
//     } catch (Facebook\Exceptions\FacebookSDKException $e) {
//         echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
//         exit;
//     }

//     echo '<h3>Long-lived</h3>';
//     var_dump($accessToken->getValue());
// }

// if (isset($accessToken)) {
//     $_SESSION['facebook_access_token'] = (string) $accessToken;
//     header('Location: index.php');
//     exit;
// } elseif ($helper->getError()) {
//     exit;
// }