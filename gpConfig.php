<?php
session_start();

//Include Google client library
include_once 'src/Google_Client.php';
include_once 'src/contrib/Google_Oauth2Service.php';

/*
 * Configuration and setup Google API
 */
$clientId = '786699123825-24163j8ee5b0ma1htsbc173ptmbid348.apps.googleusercontent.com'; //Google client ID
$clientSecret = 'ltY8wWEUUrgLtnEEFjxQy_ln'; //Google client secret
$redirectURL = 'http://jeremyginnard.com/ProteinSimulations/index.php'; //Callback URL

//Call Google API
$gClient = new Google_Client();
$gClient->setApplicationName('Login to EMU Protien Simulator');
$gClient->setClientId($clientId);
$gClient->setClientSecret($clientSecret);
$gClient->setRedirectUri($redirectURL);

$google_oauthV2 = new Google_Oauth2Service($gClient);
