<?php


class User
{
    private $con;

    function __construct()
    {

        require_once dirname(__FILE__) . '/DbConnect.php';
        $db = new DbConnect();
        $this->con = $db->connect();
    }
    public function add_user($inputdata)
    {

        try {
            $type = $inputdata['type'];
            $firstname = $inputdata['firstname'];
            $lastname = $inputdata['lastname'];
            $phone = $inputdata['phone'];
            $email = $inputdata['email'];
            $address_line1 = $inputdata['address_line1'];

            if (array_key_exists('address_line2', $inputdata)) {
                $address_line2 = $inputdata['address_line2'];
            }else{
                $address_line2 = "";
            }

            if (array_key_exists('city', $inputdata)) {
                $city = $inputdata['city'];
            }else{
                $city = "";
            }
            if (array_key_exists('state', $inputdata)) {
                $state = $inputdata['state'];
            }else{
                $state = "";
            }
            if (array_key_exists('country', $inputdata)) {
                $country = $inputdata['country'];
            }else{
                $country = "";
            }
            if (array_key_exists('dob', $inputdata)) {
                $dob = $inputdata['dob'];
            }else{
                $dob = "";
            }
            if (array_key_exists('pincode', $inputdata)) {
                $pincode = $inputdata['pincode'];
            }else{
                $pincode = "";
            }


            $sql = "CALL add_user(".$type.",'".$firstname."','".$lastname."','"
                .$phone."','".$email."','"
                .$address_line1."','".$address_line2."','"
                .$city."','".$state."','"
                .$country."',".$pincode.",'".$dob."');";
//            print_r($sql);die;
            $stmt = $this->con->query($sql);
            if($stmt === true){
                $response = true;
            }
            else{
                $response = false;
            }
            $this->con->close();

        } catch (Exception $ex){
            print_r($ex);
        }
        return $response;
    }

    public function login($inputdata){
        try {
            $email = $inputdata['email'];
            $password = $inputdata['password'];

            $sql = "CALL login('" . $email . "','" . $password . "',@success,@session_id);";
            $stmt = $this->con->query($sql);
            $select = $this->con->query('SELECT @success,@session_id');
            $result = $select->fetch_assoc();
            $success = $result['@success'];
            $session_id = $result['@session_id'];

            $res['success'] = $success;
            $res['session_id'] = $session_id;
            return $res;
        }catch (Exception $exception){
            print_r($exception);
            return null;
        }
    }
    public function delete_user($inputdata){
        $user_id = $inputdata['user_id'];

        $sql = "CALL delete_user(".$user_id.",@success,@message);";
        $stmt = $this->con->query($sql);
        $select = $this->con->query('SELECT @success,@message');
        $result = $select->fetch_assoc();
        $success = $result['@success'];
        $message = $result['@message'];
        $res['success'] = $success;
        $res['message'] = $message;

        return $res;

    }
    public function get_user($inputdata){
        try {
            $user_id = $inputdata['user_id'];
            $sql = "SELECT * from user where id = " . $user_id . ";";
            $stmt = $this->con->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            return $result;
        }catch (Exception $exception){
            return null;
        }
    }

    public  function get_all_users($inputdata){
        try{
            if(array_key_exists('like', $inputdata)){
                $like = "%".$inputdata['like']."%";
            }else{
                $like = "%";
            }
            $sql = "SELECT  t1.id, t1.`firstname`, t1.`lastname`, t2.type , t1.`phone`, t1.`email`, t1.`created_on` 
                    FROM `user` as t1
                    INNER JOIN `user_type` as t2
                    ON t1.user_type = t2.id
                    WHERE (t1.`firstname` LIKE '".$like."'  ) 
                    OR (t1.`lastname` LIKE '".$like."' ) OR (t1.`email` LIKE '".$like."' ) OR (t1.`phone` LIKE '".$like."' );";

            $stmt = $this->con->prepare($sql);
            $stmt->execute();
            $details = $stmt->get_result();

            $data = array();
             foreach($details as $row){
                 $sql = "SELECT t1.title , (CASE WHEN t3.actual_dor is null then 1 else 0 end) as book_possesed
                            FROM book AS t1
                            INNER JOIN catalog AS t2
                            ON t1.isbn = t2.isbn
                            INNER JOIN borrow AS t3
                            ON t3.book_id = t2.book_id and t3.user_id =".$row['id'].";";
                 $stmt = $this->con->prepare($sql);
                 $stmt->execute();
                 $book_details = $stmt->get_result();
                 $book_data = array();
                 foreach ($book_details as $book_row){
                     array_push($book_data,$book_row);
                 }
                 $row['books'] = $book_data;

                array_push($data,$row);
             }

             return $data;
        }catch (Exception $exception){
            return null;
        }
    }
}
?>

