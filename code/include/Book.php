<?php

class Book{
    private $con;

 
    //Class constructor
    function __construct()
    {
        //Getting the DbConnect.php file
        require_once dirname(__FILE__) . '/DbConnect.php';
		
        //Creating a DbConnect object to connect to the database
        $db = new DbConnect();		
		
        //Initializing our connection link of this class
        //by calling the method connect of DbConnect class
        $this->con = $db->connect();		
    }   

    public function get_all_books(){
        $sql = "CALL show_all_books('%%');";
        $stmt = $this->con->query($sql);
        // if (!$this->con->query("CALL show_all_books()")) {
        //     echo "CALL failed: (" . $this->con->errno . ") " . $this->con->error;
        // }
    //    $stmt->execute();
       $details = $stmt->fetch_all();
        print_r($details);
    }
}
?>