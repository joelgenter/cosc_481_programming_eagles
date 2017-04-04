<?php

class GoogleAuth
{
  protected $db;
  protected $client;

  public function __construct(DB $db = null, Google_Client $googleClient = null)
  {
    $this->db = $db;
    $this->client = $googleClient;

    if($this->client)
    {
      $this->client->setClientId('276192707871-cov16r6j7dj1nspc68f6ip4llpvsoubl.apps.googleusercontent.com');
      $this->client->setClientSecret('e-GIUfE8QgGBSZWYcj9_QFmJ');
      $this->client->setRedirectUri('http://localhost/index.php');
      $this->client->setScopes('email');
    }
  }

  public function isLoggedIn()
  {
    return isset($_SESSION['access_token']);
  }

  public function getAuthUrl()
  {
    return $this->client->createAuthUrl();
  }

  public function checkRedirectCode()
  {
    if(isset($_GET['code']))
    {
      $this->client->authenticate($_GET['code']);

      $this->setToken($this->client->getAccessToken());

      $payload = $this->getPayload();
      echo '<pre>', print_r($payload), '</pre>';

      //$this->storeUser($this->getPayload());

      return true;
    }

    return false;
  }

  public function setToken($token)
  {
    $_SESSION['access_token'] = $token;

    $this->client->setAccessToken($token);
  }

  public function logout()
  {
    unset($_SESSION['access_token']);
  }

  public function getPayload()
  {
    $payload = $this->client->verifyIdToken();

    return $payload;
  }

  protected function storeUser($payload)
  {
    $sql = "
      INSERT INTO protiensim (google_id, email)
      VALUES ({$payload['id']}, '{$payload['email']}')
      ON DUPLICATE KEY UPDATE id = id
    ";

    $this->db->query($sql);
  }

}
