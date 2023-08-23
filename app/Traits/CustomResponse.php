<?php


namespace App\Traits;

trait CustomResponse {
    public function customResponse($data , $message = null , $code = 200){
        return response([
            "status" => "request was successful" ,
            "message" => $message,
            "data" => $data
        ] , $code);
    }
}
