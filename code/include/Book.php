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
            if (array_key_exists('fine_payed', $inputdata)) {
                $fine_payed = $inputdata['fine_payed'];
            }else{
                $fine_payed = 0;
            }
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
    public  function  check_book_is_available($inputdata){
        try{
            $book_id = $inputdata['book_id'];
            $reservation_date = $inputdata['reservation_date'];

            $sql = "SELECT check_book_is_available(".$book_id.",'".$reservation_date."') AS isAvailable;";

            $stmt = $this->con->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            return $result['isAvailable'];

        }catch (Exception $exception){
            return null;
        }
    }
    public  function  reserve_book($inputdata){
        try{
            $book_id = $inputdata['book_id'];
            $user_id = $inputdata['user_id'];
            $reservation_date = $inputdata['reservation_date'];

            $sql = "CALL reserve_book(".$book_id.",".$user_id.",'".$reservation_date."',@success,@email,@title);";
//            print_r($sql);die;
            $stmt = $this->con->query($sql);
            $select = $this->con->query('SELECT @success,@email,@title');
            $result = $select->fetch_assoc();
            $success = $result['@success'];
            $email = $result['@email'];
            $title = $result['@title'];
            $subject = "Book ".$title." reserved successfully";
            if($success == 1){
                $html = '<!DOCTYPE html>
                    <html lang="en">
                    <head>
                      <title>Reservation Successful!</title>
                      <meta charset="utf-8">
                      <meta name="viewport" content="width=device-width, initial-scale=1">
                      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
                      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
                      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
                      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
                      <link rel="stylesheet" href="bootstrap/css/font-awesome.min.css">
                      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
                      <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
                      <!-- <link rel="stylesheet" href="library.css"> -->
                      <style>
                      .jumbotron.jumbotron-fluid {
                                        background-color: #F57C00;
                        padding: 15px;
                        margin: 0px;
                        width:100%;
                        z-index: 999;
                        text-align: center;
                       
                    }
                    i.fa.fa-book {
                                        color: white;
                                        font-size: 30px;
                        display: inline;
                    }
                    .white-custom{
                                        color: white;
                                        padding: 20px;
                        margin: 0px;
                        display: inline;
                    }
                    
                    
                    /* footer */
                    footer{
                                        background-color: black;
                        padding: 2px;
                    }
                    .p-footer {
                                        color: white;
                                        text-align: center;
                        
                    }
                    
                    table.center {
                                        margin-left:auto; 
                        margin-right:auto;
                        text-align: center;
                    } 
                      </style>
                      
                    </head>
                    <body>
                        <!-- Header -->
                        <div class="jumbotron jumbotron-fluid jumbotron-fluid-bgcolor">
                            <div class="container">
                                <i class="fa fa-book"></i>
                                <h2 class="white-custom">Campus Library</h2>
                            </div>
                        </div>
                        
                                     <br/><br/>
                            
                    
                                        <h2 align="center" >Reservation Successfull!</h2>
                    
                                        <br/>
                                        <div >
                                                <table class="center" border="3">
                                                        <tr>
                                                            <th colspan="2" ><h4>Book Reserved!!</h4></th>
                                                        </tr>
                                                        <tr>
                                                                <th> Book Name </th> 
                                                                <th>Date</th>
                                                        </tr>
                                                        <tr>
                                                                <td>'.$title.'</td>
                                                                <td>'.$reservation_date.'</td>
                                                        </tr>
                                                        
                                                    
                                                </table>
                                        </div>
                                      
                                    </div>
                            </div>
                       
                            
                       
                            <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                    
                         <!-- Footer -->
                        
                         <footer> 
                            <p class="p-footer mt-3">@2019 All rights resevered by Library system</p>
                        </footer>
                    </body>
                    </html>';



                $result = SendMail($email, $subject, $html, $html);
           }
            return $success;

        }catch (Exception $exception){
            print_r($exception);
        }
    }

    public function get_book($inputdata){

        try {
            $book_id = $inputdata['book_id'];

            $sql = "SELECT title from book where isbn = (SELECT  isbn from catalog where book_id = " . $book_id . ");";

            $stmt = $this->con->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            return $result['title'];
        }catch (Exception $ex){
            return null;
        }


    }

    public  function delete_by_bookid($inputdata){
        try{
            $book_id = $inputdata['book_id'];

            $sql = "call delete_by_book_id(".$book_id.",@success,@message)";
            // print_r($sql);die;
            $stmt = $this->con->query($sql);
            $select = $this->con->query('SELECT @success , @message');
            $result = $select->fetch_assoc();
           
            $success = $result['@success'];
            $message = $result['@message'];
            $res['success'] = $success;
            $res['message'] = $message;
     
            return $res;

        }catch (Exception $exception){
            return null;
        }
    }
}
?>