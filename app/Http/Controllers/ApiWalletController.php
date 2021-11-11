<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiBaseController as ApiBaseController;;

use App\Models\ewallet;
use App\Models\ewallet_topup;
use App\Models\ewallet_history;
use App\Models\ewallet_withdraw;
use App\Models\ewallet_transfer;
use App\Models\User;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ApiWalletController extends ApiBaseController
{

    public function getBalance(Request $request)
    {
        $userdata = $request->user('api');
        $wallet = ewallet::find($userdata->id);

        return  $this->sendResponse(['balance' => $wallet->balance, 'name' => $userdata->name], 'Successfully get data');
    }

    /** TopUp */
    public function topup(Request $request)
    {
        $userdata = $request->user('api');
        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer|min:2',
            'payment_method' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', ['kode' => 1064, 'message' => 'Not null exception', 'validation_errors' => $validator->errors()]);
        }

        $input = $request->all();
        $input['user_id'] = $userdata->id;
        $input['invoice'] = date('Ymd') . '' . $userdata->id . '' . rand(1, 9);
        $input['status'] = 'unpaid';
        $input['description'] = 'TopUp ' . $input['amount'] . ' IDR via ' . $input['payment_method'];

        /** save to topup */
        ewallet_topup::create($input);

        $data['invoice'] = $input['invoice'];
        $data['status'] = $input['status'];
        $data['description'] = $input['description'];
        $data['status_url'] = url('/api/wallet/topup-status/' . $data['invoice']);
        return  $this->sendResponse($data, 'Successfully');
    }
    public function topup_status($id)
    {
        $data = ewallet_topup::where('user_id', Auth::user()->id)->find($id);

        return $this->sendResponse($data, 'Successfully');
    }
    public function unpaid_bill()
    {
        $countisNull = ewallet_topup::where('billing_id', null)
            ->where('user_id', Auth::user()->id)
            ->where('status', 'unpaid')->count();

        if ($countisNull > 0) {
            // give billing_id is null
            $generateBill = substr(Auth::user()->id . time() . date('Dm'), 0, 10);
            ewallet_topup::where('billing_id', null)->update(['billing_id' => $generateBill]);
        }
        $data1 = ewallet_topup::where('status', 'unpaid')->where('user_id', Auth::user()->id);
        $bill = $data1->get()[0]['billing_id'];
        $data['item'] = $data1->get();
        $data['billing_total'] = $data1->sum('amount');
        $data['invoice_pdf'] = url('/api/document-pdf/' . $bill);
        $data['invoice_pdf_download'] = url('/api/document-pdf/' . $bill . '?download=true');
        $data['payment_url'] = url('/api/wallet/billing-payment/' . $bill);
        return $this->sendResponse($data, 'Successfully');
    }

    public function bill_payment($bill_id)
    {
        $countisUnpaid = ewallet_topup::where('status', 'unpaid')->where('billing_id', $bill_id)->count();
        if ($countisUnpaid > 0) {
            /** example for paid */
            ewallet_topup::where('billing_id', $bill_id)->update(['status' => 'paid']);
        }

        $data1 = ewallet_topup::where('billing_id', $bill_id)->where('user_id', Auth::user()->id);
        $data['item'] = $data1->get();
        $data['billing_id'] = $bill_id;
        $data['status_all'] = 'paid';
        $data['invoice_pdf'] = url('/api/document-pdf/' . $bill_id);
        $data['invoice_pdf_download'] = url('/api/document-pdf/' . $bill_id . '?download=true');
        $data['billing_total'] = $data1->sum('amount');

        /** save history */
        ewallet_history::saveHistory($data['billing_total'], 'IN');

        return $this->sendResponse($data, 'Successfully');
    }

    /* end topup */


    /** withdrawal */

    public function withdraw(Request $request)
    {
        $wallet = ewallet::where('user_id', Auth::user()->id)->first();
        $userdata = $request->user('api');

        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer|min:2',
            'destination' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', ['kode' => 1064, 'message' => 'Not null exception', 'validation_errors' => $validator->errors()]);
        }

        /** check balance  */
        if ($request->amount > $wallet['balance']) {
            return $this->sendError('the withdrawal amount exceeds the available balance', ['error' => 'the withdrawal amount exceeds the available balance'], 403);
        }


        $destination = $request->destination;

        $dest = json_decode($wallet['withdrawal'], true);

        $input['user_id'] = $userdata->id;
        $input['amount'] = $request->amount;
        $input['destination'] = json_encode($dest[$destination]);
        $input['status'] = 'pending';
        $input['description'] = 'Withdrawal processed. ';
        $save = ewallet_withdraw::create($input);

        $data['amount'] = $input['amount'];
        $data['destination'] = $input['destination'];
        $data['status'] = 'pending';
        $data['status_url'] = url('/api/wallet/withdraw-status/' . $save->id);

        return $this->sendResponse($data, 'Successfully');
    }
    public function withdraw_status($id, Request $request)
    {
        $data = ewallet_withdraw::where('user_id', Auth::user()->id)->find($id);

        return $this->sendResponse($data, 'Successfully');
    }

    /** end withdraw */

    /** transfer */
    public function transfer(Request $request)
    {
        $wallet = ewallet::where('user_id', Auth::user()->id)->first();
        $userdata = $request->user('api');

        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer|min:2',
            'emailto' => 'required',
            'description' => 'nullable'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', ['kode' => 1064, 'message' => 'Not null exception', 'validation_errors' => $validator->errors()]);
        }

        if ($request->amount > $wallet['balance']) {
            return $this->sendError('the transfer amount exceeds the available balance', ['error' => 'the transfer amount exceeds the available balance'], 403);
        }

        $userto = User::where('email', $request->emailto);
        if ($userto->count() > 0) {
            $to = $userto->first();


            $from_id = $userdata->id;
            $to_id = $to['id'];
            $amount = $request->amount;
            $status = 'complete';
            $desc = $request->description;

            /** insert to transfer */
            ewallet_transfer::create([
                'from_user_id' => $from_id,
                'to_user_id' => $to_id,
                'amount' => $amount,
                'status' => $status,
                'description' => $desc
            ]);
            /** insert to history  for from */
            ewallet_history::saveHistory($amount, 'out');
            /** insert to history for to */
            ewallet_history::saveHistoryById($to_id, $amount, 'in');

            /** update balance target */
            // - balance 
            $currentBalance = $wallet['balance'];
            $finalBalance = ($wallet['balance'] - $amount);
            ewallet::where('user_id', $userdata->id)->update(['balance' => $finalBalance]);
            // + balance 
            $wallet2 = ewallet::where('user_id', $to_id)->first();
            $currentBalance2 = $wallet2['balance'];
            $finalBalance2 = ($wallet2['balance'] + $amount);
            ewallet::where('user_id', $to_id)->update(['balance' => $finalBalance2]);


            $data['to']['email'] = $request->emailto;
            $data['to']['name'] = $to['name'];
            $data['from']['email'] = $userdata->email;
            $data['from']['name'] = $userdata->name;
            $data['amount'] = $amount;
            $data['description'] = $desc;
            $data['status'] = $status;
            return $this->sendResponse($data, 'Successfully');
        } else {

            return $this->sendError('Email wrong or not found', ['kode' => 3000, 'message' => 'email not found']);
        }
    }

    /** end transfer */

    /** mutation */

    public function mutation(Request $request)
    {
        $data['all_histories'] = ewallet_history::where('user_id', $request->user()->id)->get();
        $data['topup_logs'] = ewallet_topup::where('user_id', $request->user()->id)->get();
        $data['transfer_logs'] = ewallet_transfer::where('from_user_id', $request->user()->id)->get();
        $data['withdraw_logs'] = ewallet_withdraw::where('user_id', $request->user()->id)->get();

        return $this->sendResponse($data, 'Successfully');
    }

    /** end mutation */
}
