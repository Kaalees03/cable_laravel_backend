<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\user\User;
use Illuminate\Support\Facades\Hash;


class UserAuthController extends Controller
{
    public function __construct()
    {
        $this->helperController = new HelperController();
        $this->current_timestamp = now();
    }

    public function user_register(Request $request)  //:JsonResponse
    {       
        $response=[];
        $token_key=null;
        $first_name=$request->first_name;
        $last_name=$request->last_name;
        $email_address=$request->email_address;
        $password=$request->password;
        $confirm_password=$request->confirm_password;
        $validator = Validator::make($request->all(),[
            'first_name'=> 'required',
            'last_name'=> 'required',
            'mobile_number'=> 'required|unique:users,mobile_number|min:10|max:10',
            'email_address'=> 'required|email|unique:users,email_address',
            'password'=> 'required|string|min:5',
            'confirm_password'=> 'required|string|min:5|same:password',                                   
        ]);
        if($validator->fails())
        {
            return response()->json([
                    'status'=>404,
                    'error'=>true,
                    'messages' => $validator->errors(),
                    'token_key' => $token_key,
                    'user_details' => null,
            ]);
        }
        else
        {
            $user_inserted_id = User::insertGetId([              
                'full_name' => $request->first_name,
                'sur_name' =>   $request->last_name,
                'unique_id' =>   $request->last_name,
                'email_address' => $request->email_address,
                'password' => Hash::make($password),
            ]);
            $update_user_unique_data['unique_id']= $this->helperController->uniqueusernumber(2, $user_inserted_id);
            if(User::where('id', $user_inserted_id)->update($update_user_unique_data))    
            {
                return response()->json([
                    'status'    => 201,
                    'error'     => true,
                    'messages'  => 'User data created successfully.',
                    'token_key' => $token_key,
                    'user_details' => null,             
                ]);  
            }
            else
            {
                return response()->json([
                    'status'    => 404,
                    'error'     => true,
                    'messages'  => 'Failed to create user record!',
                    'token_key' => $token_key,
                    'user_details' => null,             
                ]);  
            }
        }          

    }

    public function user_login(Request $request):JsonResponse
    {                
        $response=[];
        $email_address=$request->email_address;
        $password=$request->password; 
        // return $hashedPassword = Hash::make($password);
        $fcm_token = $request->fcm_token;
        $token_key = null;                                                                                                                                                                                                                                                                                                                                      
        // validation
        $validator=Validator::make($request->all(),[
            'email_address' => 'required',
            'password' => 'required|min:5',
        ]);
        if($validator->fails())
        {
            return response()->json([
                'status' => 404,
                'error' => true,
                'messages' => $validator->errors(),
                'token_key' => $token_key,
                'user_details' => null,
            ]);
        }       
        if(Auth::attempt(['email_address'=> $email_address, 'password'=> $password , 'status'=>1]))
        {  
            $authenticatedUser = Auth::user();           
            $user_details['id']=$authenticatedUser->id;
            $user_details['unique_id']=$authenticatedUser->unique_id;           
            $token_key = $this->helperController->set_user_token($user_details);
            return response()->json([
                 'status' => 201,   
                 'error' => true,
                 'messages' => 'user verified successfully',
                 'token_key' => $token_key,
                 'user_details' => null,              
            ]);
        }
        else
        {
           
            return response()->json([
                 'status' => 404,   
                 'messages' => 'User details doesn`t exits!',
                 'token_key' => $token_key,
                 'user_details' => null,                     
            ]);
        }
        return response()->json($response);        
    }
}
