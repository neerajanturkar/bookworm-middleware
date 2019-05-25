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

             $sql = "SELECT book_id as book_id, is_locked , is_borrowed , location FROM catalog WHERE isbn = '".$r['isbn']."';";

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
            $e_author = $inputdata['e_author'];
            $n_author = $inputdata['n_author'];

            if(strlen($e_author) == 0){
                unset($e_author);
            }
            if(strlen($n_author) == 0){
                unset($n_author);
            }
            if($type == 0){
                $type = 1;
            }


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
                .$published_date."',".$page_count.",'"
                .$language."','".$thumbnail."','"
                .$isbn."','".$cnt."','"
                .$e_author."','".$n_author."');";

//            print_r($sql);die;
            $stmt = $this->con->query($sql);

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
    public function return_book($inputdata){
        try{
            $book_id = $inputdata['book_id'];
            $user_id = $inputdata['user_id'];
            $dor = date("Y-m-d");

            $sql = "CALL return_book(".$book_id.",".$user_id.",'".$dor."',@success,@fine);";
//            print_r($sql);die;
            $stmt = $this->con->query($sql);


            $select = $this->con->query('SELECT @success , @fine');
            $result = $select->fetch_assoc();
            $success = $result['@success'];
            $fine = $result['@fine'];
            $res['success'] = $success;
            $res['fine'] = $fine;
            return $res;
        }catch(Exception $ex ){
            echo $ex;
            return 0;
        }


    }
    public function renew_book($inputdata){
        try{
            $book_id = $inputdata['book_id'];
            $user_id = $inputdata['user_id'];


            $sql = "CALL renew_book(".$book_id.",".$user_id.",@success);";
            $stmt = $this->con->query($sql);


            $select = $this->con->query('SELECT @success');
            $result = $select->fetch_assoc();
            $success = $result['@success'];

            return $success;
        }catch(Exception $ex ){
            echo $ex;
            return 0;
        }


    }
    public function  check_authors_exist($inputdata){
        try{
            $authors = $inputdata['authors'];
            $author_array = explode(",",$authors);
            $exist_authors = array();
            $new_authors = array();
            foreach ($author_array as $author){
                $sql = "CALL check_author_exists('".$author."',@exist);";
                $stmt = $this->con->query($sql);
                $select = $this->con->query('SELECT @exist');
                $result = $select->fetch_assoc();
                $exist = $result['@exist'];
                if($exist == 1){
                    array_push($exist_authors,$author);
                }else{
                    array_push($new_authors,$author);
                }
            }
            $res['new_authors'] = $new_authors;
            $res['exist_authors'] = $exist_authors;
            return $res;

        }catch (Exception $ex){
            return null;
        }
    }
    public function get_fine($inputdata){
        try{
            $book_id = $inputdata['book_id'];
            $user_id = $inputdata['user_id'];
            $ador = date("Y-m-d");
            $sql = "CALL get_fine(".$book_id.",".$user_id.",'".$ador."',@fine);";
            $stmt = $this->con->query($sql);
            $select = $this->con->query('SELECT @fine');
            $result = $select->fetch_assoc();
            $fine = $result['@fine'];
            return $fine;

        }catch (Exception $ex){
            return null;
        }
    }

}
?>