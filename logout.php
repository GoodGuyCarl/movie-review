<?php

session_start();
require 'vendor/autoload.php';
$fb = new Facebook\Facebook([
     'app_id' => '6621295174664204',
     'app_secret' => 'f5801e183902397cb28ce980fae25af8',
     'default_graph_version' => 'v18.0',
]);

$helper = $fb->getRedirectLoginHelper();
if (isset($_SESSION['fb_access_token'])) {
     $logoutUrl = $helper->getLogoutUrl($_SESSION['fb_access_token'], 'https://localhost/movie-review/login');
}
session_destroy();
header('Location: ' . $logoutUrl . '');
