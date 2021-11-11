<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ewallet_topup;
use PDF;

class Documents extends Controller
{
    
    public function print_invoice($bill_id , Request $request)
    {
        $wallet = ewallet_topup::where(['billing_id' => $bill_id])->get();
 
    	$pdf = PDF::loadview('invoice_pdf',['data'=>$wallet , 'bill_id' => $bill_id]);

        if($request->download == false){
    	return $pdf->stream('invoice-'.$bill_id.'.pdf');
        }else{
        return $pdf->download('invoice-'.$bill_id.'.pdf');
        }
    }
}
