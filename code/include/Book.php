<?php

class Book{
    private $con;

    function __construct()
    {

        require_once dirname(__FILE__) . '/DbConnect.php';
        $db = new DbConnect();
        $this->con = $db->connect();		
    }   

    public function get_all_books(){
         $sql = "CALL show_books('%');";
         $stmt = $this->con->query($sql);
         $details = $stmt->fetch_all();
         $data = array();
         foreach($details as $row){
                $d['title'] = $row[0];
                $d['isbn'] = $row[1];
                $d['author_firstname'] = $row[2];
                $d['author_lastname'] = $row[3];
                array_push($data,$d);
            }

         $result = array();
  
         foreach ($data as $row){
             $key = array_search($row['isbn'], array_column($result, 'isbn'));

             if($key === false) {

                 $r['title'] = $row['title'];
                 $r['isbn'] = $row['isbn'];
                 $r['author'] = $row['author_firstname']." ".$row['author_lastname'];
                 array_push($result,$r);

             }else{
                $result[$key]['author'] = $result[$key]['author'].",".$row['author_firstname']." ".$row['author_lastname'];
             }
         }
        
         return $result;
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


            $stmt = $this->con->query($sql);
            if($stmt === true){
                $response = true;
            }
            else{
                $response = false;
            }
        }catch (Exception $ex){
            print_r($ex);
        }

        return $response;
    }
}
?>