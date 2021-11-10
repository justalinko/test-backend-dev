<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KontainerController extends Controller
{
    
    public function form(){
        return view('form_kontainer');
    }

    public function parse($num)
    {
        /**
         *  detect all  
         * @see App\helpers.php
        */
       $detect = detectAll($num);
       $isPrima = isPrime($num);
        $data['number'] = $num;
        $data['isPrima'] = $isPrima;
        $data['result'] = $detect;
        return response()->json($data,200);
    }
}
