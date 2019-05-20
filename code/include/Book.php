<?php

class Book{
    private $con;

    function __construct()
    {

        require_once dirname(__FILE__) . '/DbConnect.php';
        $db = new DbConnect();
        $this->con = $db->connect();		
    }   

    public function get_all_books($inputdata){

         if(array_key_exists('like', $inputdata)){
             $like = "%".$inputdata['like']."%";
         }else{
             $like = "%";
         }
         $sql = "CALL show_books('".$like."');";
         $stmt = $this->con->query($sql);
         $details = $stmt->fetch_all();


         $this->con->close();
         unset($obj);
         unset($sql);
         unset($query);
         $data = array();
         foreach($details as $row){
                $r['title'] = $row[0];
                $r['subtitle'] = $row[1];
                $r['publication'] = $row[2];
                $r['isbn'] = $row[3];
                $r['description'] = $row[4];
                $r['published_date'] = $row[5];
                $r['page_count'] = $row[6];
                $r['language'] = $row[7];
                $r['thumbnail'] = $row[8];
                $r['authors'] = $row[9];
                $r['type'] = $row[11];



             $db = new DbConnect();
             $this->con = $db->connect();

             $sql = "SELECT BIN_TO_UUID(book_id) as book_id, is_locked , is_borrowed , location FROM catalog WHERE isbn = '".$r['isbn']."';";

                $stmt = $this->con->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();
                $data1 = array();
                while ($row = $result->fetch_assoc()) {

                     array_push($data1, $row);
                }
                $r['copies'] = $data1;
                array_push($data,$r);
            }


         $stmt->close();
         return $data;
    }
    public function add_book($inputdata){

        try{
            $type = $inputdata['type'];
            $isbn = $inputdata['isbn'];
            $title = $inputdata['title'];
            $cnt = $inputdata['cnt'];
            $author = $inputdata['author'];

            if (array_key_exists('subtitle', $inputdata)) {
                $subtitle = $inputdata['subtitle'];
            }else{
                $subtitle = "";
            }
            if (array_key_exists('publication', $inputdata)) {
                $publication = $inputdata['publication'];
            }else{
                $publication = "";
            }
            if (array_key_exists('description', $inputdata)) {
                $description = $inputdata['description'];
            }else{
                $description = "";
            }
            if (array_key_exists('published_date', $inputdata)) {
                $published_date = $inputdata['publishedDate'];
            }else{
                $published_date = "";
            }
            if (array_key_exists('page_count', $inputdata)) {
                $page_count = $inputdata['page_count'];
            }else{
                $page_count = "";
            }
            if (array_key_exists('language', $inputdata)) {
                $language = $inputdata['language'];
            }else{
                $language = "";
            }
            if (array_key_exists('thumbnail', $inputdata)) {
                $thumbnail = $inputdata['thumbnail'];
            }else{
                $thumbnail = "";
            }

            $sql = "CALL add_book(".$type.",'".$title."','".$subtitle."','"
                .$publication."','".$description."','"
                .$published_date."','".$page_count."','"
                .$language."','".$thumbnail."','"
                .$isbn."','".$cnt."','"
                .$author."');";

            print_r($sql);
            $stmt = $this->con->query($sql);
            print_r($stmt);
            if($stmt === true){
                $response = true;
            }
            else{
                $response = false;
            }
            $this->con->close();
        }catch (Exception $ex){
            print_r($ex);
        }

        return $response;
    }

    public function borrow_book($inputdata){
        try{
            $book_id = $inputdata['book_id'];
            $user_id = $inputdata['user_id'];
            $doi = date("Y-m-d");
            $sql = "SELECT get_date_of_return('".$doi."',".$user_id.") as dor;";
            $stmt = $this->con->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            $dor = $result['dor'];

            $sql1 = "CALL borrow_book(".$book_id.",".$user_id.",'".$doi."','".$dor."',@success);";
            $stmt1 = $this->con->query($sql1);


            $select = $this->con->query('SELECT @success');
            $result = $select->fetch_assoc();
            $success = $result['@success'];

            return $success;
        }catch(Exception $ex ){
            echo $ex;
            return 0;
        }


    }
}
?>