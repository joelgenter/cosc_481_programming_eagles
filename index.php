<?php
//var_dump(openssl_get_cert_locations());

// echo "openssl.cafile: ", ini_get('openssl.cafile'), "\n";
// echo "curl.cainfo: ", ini_get('curl.cainfo'), "\n";
?>

<?php

require_once 'app/init.php';

//$http= new GuzzleHttp\Client(['verify' => 'C:\Users\Griffin\Desktop\wamp\wamp\bin\php\php7.0.10\extras\ssl\cacert.pem']);

$googleClient = new Google_Client;

//$googleClient->setHttpClient($http);

//turn off cURL 60 error
$guzzleClient = new \GuzzleHttp\Client(array( 'curl' => array( CURLOPT_SSL_VERIFYPEER => false, ), ));
$googleClient->setHttpClient($guzzleClient);



$auth = new GoogleAuth($googleClient);

if($auth->checkRedirectCode())
{
  //die($_GET['code']);
  header('Location: index.php');
}

?>

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Website</title>
  <head>
  <body>
    <?php if(!$auth->isLoggedIn()): ?>
      <a href="<?php echo $auth->getAuthUrl(); ?>">Sign in with Google</a>
    <?php else: ?>
      You are signed in. <a href="logout.php">Sign out</a>
    <?php endif; ?>
  </body>
</html>
