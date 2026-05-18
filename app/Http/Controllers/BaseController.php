<?php
  
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use App\Models\User;
  
class BaseController extends Controller
{
    
    function success($message, $options = [])
    {
        $default = [
            'status'  => true,
            'success' => true,
            'message' => $message
        ];
        $merge = array_merge($default, $options);
        return response()->json($merge, 200);
    }
   
    function error($message, $options = [], $statusCode = 422)
    {
        $default = [
            'status'  => false,
            'success' => false,
            'message' => $message
        ];
        $merge = array_merge($default, $options);
        return response()->json($merge, $statusCode);
    }
}