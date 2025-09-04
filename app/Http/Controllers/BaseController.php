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
            'status' => true,
            'message' => $message
        ];
        $merge = array_merge($default, $options);
        return response()->json($merge, 200);
    }
   
    function error($message, $options = [])
    {
        $default = [
            'status' => false,
            'message' => $message
        ];
        $merge = array_merge($default, $options);
        return response()->json($merge, 200);
    }
}