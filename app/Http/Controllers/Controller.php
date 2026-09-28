<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;




class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

     #group multiple object
     function response_success($data, $message = null){

        $response = [];
        $response['status'] = "ok";
        $response['message'] = $message;
        $response['data'] = $data;

        return response()->json($response,Response::HTTP_OK);
    }

    function response_failed($message,$errors=null,$redirectTo=null){

        $response = [];
        $response['status'] = "error";
        $response['redirectTo'] = $redirectTo;

        if($errors){
            $response['errors'] = $errors;
        }
        
        $response['message'] = $message;

        return response()->json($response, Response::HTTP_BAD_REQUEST);
    }

    #return one object / pagination
    function response_ok($response = [],$message = null){

        $response['status'] = "ok";
        $response['message'] = $message;

        return response()->json($response, Response::HTTP_OK);
        
    }

}
