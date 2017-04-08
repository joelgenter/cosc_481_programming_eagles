<?php
session_start();

//Include Google client library
include_once 'src/Google_Client.php';
include_once 'src/contrib/Google_Oauth2Service.php';

/*
 * Configuration and setup Google API
 */
$clientId = '276192707871-cov16r6j7dj1nspc68f6ip4llpvsoubl.apps.googleusercontent.com'; //Google client ID
$clientSecret = 'e-GIUfE8QgGBSZWYcj9_QFmJ'; //Google client secret
$redirectURL = 'http://localhost/index.php'; //Callback URL

//Call Google API
$gClient = new Google_Client();
$gClient->setApplicationName('Login to EMU Protien Simulator');
$gClient->setClientId($clientId);
$gClient->setClientSecret($clientSecret);
$gClient->setRedirectUri($redirectURL);

$google_oauthV2 = new Google_Oauth2Service($gClient);
?>
