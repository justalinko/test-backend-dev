<?php
   
namespace App\Http\Controllers;
   
use Illuminate\Http\Request;
use App\Http\Controllers\ApiBaseController as ApiBaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Rules\PasswordValidation;
use DB;

class ApiAuthController extends ApiBaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     * 
     * @method register
     * 
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => ['required',new PasswordValidation],
            'repeat_password' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $data = $request->all();
        $input['email'] = $data['email'];
        $input['name'] = $data['name'];
        $input['password'] = bcrypt($data['password']);

        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;
   
        return $this->sendResponse($success, 'User register successfully.');
    }
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')->accessToken; 
            $success['name'] =  $user->name;

            /** insert to logs */
            DB::table('auth_logs')->insert([
                ['user_id' => $user->id, 
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ,
                'description' => 'User logged in ']
            ]);
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorized.', ['error'=>'Unauthorized']);
        } 
    }
}