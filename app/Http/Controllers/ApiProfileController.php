<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiBaseController as ApiBaseController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Rules\PasswordValidation;
use Illuminate\Support\Facades\Auth;
use DB;

class ApiProfileController extends ApiBaseController
{
    public function reset_password(Request $request, User $user)
    {

        $validator = Validator::make($request->all(), [
            'oldpassword' => 'required',
            'newpassword' => ['required', new PasswordValidation],
            'confirm_password' => 'required|same:newpassword',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $userdata = $request->user('api');
        $pdata = $request->all();
        $old_password = $pdata['oldpassword'];
        $new_password = $pdata['newpassword'];

        if (!Hash::check($old_password, $userdata->password) && $new_password != $pdata['confirm_password']) {

            return $this->sendError('Error : Old password is wrong or confirm password is doesn\'t match !', $request->all(), 500);
        } else {
            /** update password */
            $user = User::find($userdata->id);
            $user->password = bcrypt($new_password);
            $user->save();


            /** save previos password */
            DB::table('password_resets')->insert([
                [
                    'email' => $userdata->email,
                    'previous_password' => Hash::make($old_password),
                    'token' => ''
                ]
            ]);

            return $this->sendResponse('Password updated successfully', 200);
        }
    }

    public function logs(Request $request){
        
        $data = DB::table('auth_logs')->where(['user_id' => $request->user('api')->id ])->get();

        return $this->sendResponse($data,200);
    }
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return $this->sendResponse('Logged out',200);
    }
}
