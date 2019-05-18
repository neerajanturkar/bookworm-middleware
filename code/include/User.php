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

            $sql = "CALL add_user(".$type.",'".$firstname."','".$lastname."','"
                .$phone."','".$email."','"
                .$address_line1."','".$address_line2."','"
                .$city."','".$state."','"
                .$country."','".$dob."');";
//            print_r($sql);die;
            $stmt = $this->con->query($sql);
            if($stmt === true){
                $response = true;
            }
            else{
                $response = false;
            }
            $this->con->close();
            print_r($stmt);
        } catch (Exception $ex){
            print_r($ex);
        }
        return $response;
    }
}
?>