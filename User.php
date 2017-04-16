<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
class User {
    private $userTbl    = 'Users';
    private $db;

	function __construct(){
        require 'db_connection.php';
        $this->db = $conn;
    }

	function checkUser($userData = array()){
        if(!empty($userData)){
            //Check whether user data already exists in database
            $prevQuery = "SELECT * FROM ".$this->userTbl." WHERE oauth_provider = '".$userData['oauth_provider']."' AND oauth_uid = '".$userData['oauth_uid']."'";
            $prevResult = $this->db->query($prevQuery);
            if($prevResult->num_rows > 0){
                //Update user data if already exists
                $query = "UPDATE ".$this->userTbl." SET username = '".$userData['username']."', firstName = '".$userData['first_name']."', lastName = '".$userData['last_name']."', email = '".$userData['email']."' WHERE oauth_provider = '".$userData['oauth_provider']."' AND oauth_uid = '".$userData['oauth_uid']."'";
                $update = $this->db->query($query);
            }else{
                //Insert user data
                $query = "INSERT INTO ".$this->userTbl." SET oauth_provider = '".$userData['oauth_provider']."', oauth_uid = '".$userData['oauth_uid']."', username = '".$userData['username']."', firstName = '".$userData['first_name']."', lastName = '".$userData['last_name']."', email = '".$userData['email']."'";
                $insert = $this->db->query($query);
            }

            //Get user data from the database
            $result = $this->db->query($prevQuery);
            $userData = $result->fetch_assoc();
        }

        //Return user data
        return $userData;
    }

    function getStatus($username)
    {
      $sql = "SELECT type FROM " .$this->userTbl." WHERE username = '" .$username."'";
      $result = $this->db->query($sql);

      $userData = $result->fetch_assoc();
      return $userData;
    }
}
