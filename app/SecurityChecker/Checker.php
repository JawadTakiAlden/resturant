<?php

namespace App\SecurityChecker;


class Checker
{
    public static function isParamsFoundInRequest(){
        if (request()->query()){
            return true;
        }
        return false ;
    }

    public static function CheckerResponse($message = 'Query Params Not allowed For This Api' , $code = 401){
        return response([
           'message' => $message
        ] , $code);
    }
}
