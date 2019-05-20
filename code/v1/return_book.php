<?php
require_once dirname(__FILE__) . "/../include/Common.php";
require_once dirname(__FILE__) . "/../include/Book.php";
try {
    verifyRequiredParams(array('book_id', 'user_id'), $inputdata);

    $book = new Book();
    $result = $book->return_book($inputdata);
    if($result == 1){
        $response->success = true;
        $response->message = "Book returned successfully";
        $response->responseCode = 200;
    }else{
        $response->success = false;
        $response->message = "Failed to return book";
        $response->responseCode = 200;
    }


} catch (Exception $exception){

    print_r($exception);
    $response->success = false;
    $response->message = "Failed to return book";
    $response->responseCode = 200;
}
OutputResponse($response);
?>
