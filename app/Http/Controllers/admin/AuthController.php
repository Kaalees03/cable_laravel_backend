<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\user\User;
use App\Models\admin\AdminUser;
use App\Models\OtpVerficationModel;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->helperController = new HelperController();
        $this->current_timestamp = now();
    }

    // Admin login
  
    public function admin_login(Request $request):JsonResponse
    {
        $response=[];
        $email=$request->email;
        $password=$request->password; 
        // return $hashedPassword = Hash::make($password);
        $fcm_token = $request->fcm_token;
        $token_key = null; 

        // validation
        $validator=Validator::make($request->all(),[
            'email' => 'required',
            'password' => 'required|min:5',
        ]);
        if($validator->fails())
        {
            return response()->json([
                'status' => 404,
                'error' => true,
                'messages' => $validator->errors(),
                'token_key' => $token_key,
                'admin_details' => null,
            ]);
        }
        if(Auth::guard('admin_user')->attempt(['email'=> $email, 'password'=> $password , 'status'=>1]))
        {   
            $authenticatedUser = Auth::guard('admin_user')->user();           
            $admin_user_details['id']=$authenticatedUser->id;
            $admin_user_details['unique_id']=$authenticatedUser->unique_id;           
            $token_key = $this->helperController->set_user_token($admin_user_details);
            return response()->json([
                 'status' => 201,   
                 'error' => true,
                 'messages' => 'Admin verified successfully',
                 'token_key' => $token_key,
                 'admin_details' => null,              
            ]);
        }
        else
        {
            return response()->json([
                 'status' => 404,   
                 'messages' => 'Admin details doesn`t exits!',
                 'token_key' => $token_key,
                 'admin_details' => null,                     
            ]);
        }
        return response()->json($response);
        
    }

    // Admin register

    public function admin_register(Request $request):JsonResponse
    {
        $response=[];
        $name=$request->name;
        $email=$request->email;
        $password=$request->password;      
        $mobile_number=$request->mobile_number;      
        $fcm_token = $request->fcm_token;
        $token_key = null;                                                       
        // validation
        $validator=Validator::make($request->all(),[
            'name' => 'required',
            'mobile_number' => 'required',
            'email' => 'required',
            'password' => 'required|min:5',
        ]);
        if($validator->fails())
        {   
            return response()->json([
                'status' => 404,
                'error' => true,
                'messages' => $validator->errors(),
                'token_key' => $token_key,
                'admin_details' => null,
            ]);
        }
       else
        {
            $admin_user_data['name']= $name;        
            $admin_user_data['email']= $email;
            $admin_user_data['password']= Hash::make($password);
            $admin_user_data['mobile_number']= $mobile_number;
            $admin_user_data['verified_at'] = $this->current_timestamp;
            $admin_user_data['email_verified_at'] = $this->current_timestamp;
            $admin_user_data['created_at'] = $this->current_timestamp;
            $admin_user_insertid = AdminUser::insertGetId($admin_user_data);
            $update_admin_unique_data['unique_id']= $this->helperController->uniqueusernumber(1, $admin_user_insertid);
            if (AdminUser::where('id', $admin_user_insertid)->update($update_admin_unique_data))
            {
                return response()->json([
                    'status' => 201,   
                    'error' => true,
                    'messages' => 'Admin data created successfully',    
                    // 'token_key' => $token_key,
                    // 'admin_details' => null,                                   
               ]);
            }        
            else
            {
                return response()->json([
                     'status' => 404,   
                     'error' => true,
                     'messages' => 'Failed to create Admin record!',   
                    //  'token_key' => $token_key,
                    //  'admin_details' => null,                               
                ]);
            }
        }
      
        return response()->json($response);
        
    }

    // Forgot password section start

    public function forgot_password(Request $request):JsonResponse
    {
        $response=[];
        $email=$request->email;
        $mobile_number=$request->mobile_number;
        $validator=Validator::make($request->all(),[
             'email' => 'required|email'
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status'=> '404',
                'error'=> 'true',
                'messages'=> $validator->errors(),
                'admin_details' => null, 
            ]);
        }
        $return_data=$this->helperController->send_otp_mailsms(1,$email,$mobile_number);
        $response = [
            'status' => $return_data['error_data'] ? 201 : 404,
            'error' => !$return_data['error_data'],
            'messages' => $return_data['error_message'],
        ];
        return response()->json($response);

    }
   
    public function otpcode_verification_org(Request $request):JsonResponse
    {      
        $response = [];
        $token_key = null;
        $mobile_number = $request->mobile_number;
        $email = $request->email;
        $otp_code = $request->otp_code;
        $otpCode_builder = OtpVerficationModel::query();
        if (!empty($mobile_number)) {
            $otpCode_builder->where('mobile_number', $mobile_number);
        }
        if (!empty($email)) {
            $otpCode_builder->where('email', $email);
        }
        $otp_details = $otpCode_builder->where('otp_code', $otp_code)
            ->where('is_verified', 0)->latest()->first();

        if (empty($otp_details)) {          
            return response()->json($response = [
                'status' => 404,
                'error' => true,
                'messages' => 'User doesn`t exist',
                'token_key' => $token_key,
                'admin_details' => null,
            ]);
        }

        $diffInMinutes = now()->diffInMinutes($otp_details->created_at);
        if ($diffInMinutes < 20) {
            $user_builder = User::query();
            if (!empty($mobile_number)) {
                $user_builder->where('mobile_number', $mobile_number);
                $register_data['mobile_number'] = $mobile_number;
                $register_data['verified_at'] = $this->current_timestamp;
            }
            if (!empty($email)) {
                $user_builder->where('email', $email);
                $register_data['email'] = $email;
                $register_data['email_verified_at'] = $this->current_timestamp;
            }
            $user_details = $user_builder->where('status', 1)->first();
            $register_data['updated_at'] = $this->current_timestamp;

            $otp_data['is_verified'] = 1;
            $otp_data['verified_at'] = $this->current_timestamp;
            $otp_data['updated_at'] = $this->current_timestamp;

            if ($user_details) {
                $user_builder->update($register_data);
                $otpCode_builder->where('otp_code', $otp_code)->update($otp_data);
                $token_key = $this->helperController->set_user_token($user_details);
                // $employee_builder = User::select('admin_users.*', 'manage_role.*', 'privilege_limit.*')
                //     ->leftJoin('privilege_limit', 'admin_users.id', '=', 'privilege_limit.employee_id')
                //     ->leftJoin('manage_role', 'admin_users.role_id', '=', 'manage_role.id')
                //     ->where('admin_users.id', $user_details->id)->where('admin_users.status', 1)->first();
                $response = [
                    'status' => 201,
                    'error' => false,
                    'messages' => 'Otp verified successfully.',
                    'token_key' => $token_key,
                    'admin_details' => null,
                ];
            } else {
                $response = [
                    'status' => 401,
                    'error' => true,
                    'messages' => 'User doesn`t exist!',
                    'token_key' => $token_key,
                    'admin_details' => null,
                ];
            }
        } else {
            $response = [
                'status' => 404,
                'error' => true,
                'messages' => 'Otp has been expired!',
                'token_key' => $token_key,
                'admin_details' => null,
            ];
        }

        return response()->json($response);
    }

    public function otpcode_verification(Request $request):JsonResponse
    {      
        $response = [];
        $token_key = null;
        $mobile_number = $request->mobile_number;
        $email_address = $request->email_address;
        $otp_code = $request->otp_code;
        $otpCode_builder = OtpVerficationModel::query();
        if (!empty($mobile_number)) {
            $otpCode_builder->where('mobile_number', $mobile_number);
        }
        if (!empty($email_address)) {
            $otpCode_builder->where('email', $email_address);
        }
         $otp_details = $otpCode_builder->where('otp_code', $otp_code)
            ->where('is_verified', 0)->latest()->first();

        if (empty($otp_details)) {          
            return response()->json($response = [
                'status' => 404,
                'error' => true,
                'messages' => 'User doesn`t exist',
                'token_key' => $token_key,
                'employee_details' => null,
            ]);
        }

        $diffInMinutes = now()->diffInMinutes($otp_details->created_at);
        if ($diffInMinutes < 20) {
            $user_builder = User::query();
            if (!empty($mobile_number)) {
                $user_builder->where('mobile_number', $mobile_number);
                $register_data['mobile_number'] = $mobile_number;
                $register_data['verified_at'] = $this->current_timestamp;
            }
            if (!empty($email_address)) {
                $user_builder->where('email', $email_address);
                $register_data['email'] = $email_address;
                $register_data['email_verified_at'] = $this->current_timestamp;
            }
            $user_details = $user_builder->where('status', 1)->first();
            $register_data['updated_at'] = $this->current_timestamp;

            $otp_data['is_verified'] = 1;
            $otp_data['verified_at'] = $this->current_timestamp;
            $otp_data['updated_at'] = $this->current_timestamp;

            if ($user_details) {
                $user_builder->update($register_data);
                $otpCode_builder->where('otp_code', $otp_code)->update($otp_data);
                $token_key = $this->helperController->set_user_token($user_details);
                // $employee_builder = User::select('admin_users.*', 'manage_role.*', 'privilege_limit.*')
                //     ->leftJoin('privilege_limit', 'admin_users.id', '=', 'privilege_limit.employee_id')
                //     ->leftJoin('manage_role', 'admin_users.role_id', '=', 'manage_role.id')
                //     ->where('admin_users.id', $user_details->id)->where('admin_users.status', 1)->first();
                $response = [
                    'status' => 201,
                    'error' => false,
                    'messages' => 'Otp verified successfully.',
                    'token_key' => $token_key,
                    'employee_details' => null,
                ];
            } else {
                $response = [
                    'status' => 401,
                    'error' => true,
                    'messages' => 'User doesn`t exist!',
                    'token_key' => $token_key,
                    'employee_details' => null,
                ];
            }
        } else {
            $response = [
                'status' => 404,
                'error' => true,
                'messages' => 'Otp has been expired!',
                'token_key' => $token_key,
                'employee_details' => null,
            ];
        }

        return response()->json($response);
    }

    public function resend_otpcode(Request $request): JsonResponse
    {
        $response = [];
        $mobile_number = $request->mobile_number;
        $email = $request->email;
        $user_builder = User::query();
        if(!empty($mobile_number))
        {
            $user_builder->where('mobile_number', $mobile_number);
        }
        if(!empty($email))
        {
            $user_builder->where('email', $email);
        }
        $user_details = $user_builder->first();

        if ($user_details) {
            $return_data = $this->helperController->send_otp_mailsms(2, $email, $mobile_number);
            $response = [
                'status' => $return_data['error_data'] ? 201 : 404,
                'error' => !$return_data['error_data'],
                'messages' => $return_data['error_message'],
            ];
        } else {
            $response = [
                'status' => 401,
                'error' => true,
                'messages' => 'Invalid credentials!',
            ];
        }

        return response()->json($response);
    }

    public function change_password(Request $request): JsonResponse
    {
        $response = [];
        $authorization = $this->helperController->get_user_token($request->bearerToken(), null);

        $old_password = $request->old_password;
        $new_password = $request->new_password;
        $confirm_password = $request->confirm_password;

        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|string|min:8|same:new_password',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 404,
                'error' => true,
                'messages' => $validator->errors(),
            ]);
        }

        if ($this->helperController->check_authorization($authorization, true)) {

            $user_data = AdminUser::select('password')->where('id', $authorization)
                ->first()->makeVisible('password');
            if (Hash::check($old_password, $user_data->password)) {
                $user_datas['password'] = Hash::make($new_password);
                $user_datas['updated_at'] = $this->current_timestamp;
                if (AdminUser::where('id', $authorization)->update($user_datas)) {
                    $response = [
                        'status' => 201,
                        'error' => false,
                        'messages' => 'Password changed successfully.',
                    ];
                } else {
                    $response = [
                        'status' => 404,
                        'error' => true,
                        'messages' => 'Failed to change password!',
                    ];
                }
            } else {
                $response = [
                    'status' => 404,
                    'error' => true,
                    'messages' => 'Incorrect old password!',
                ];
            }
        } else {
            $response = [
                'status' => 401,
                'error' => true,
                'messages' => 'Invalid credentials!',
            ];
        }

        return response()->json($response);
    }
    
    public function update_password(Request $request): JsonResponse
    {
        $response = [];
        $authorization = $this->helperController->get_user_token($request->bearerToken(), null);
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|string|min:5',
            'confirm_password' => 'required|string|min:5|same:new_password',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 404,
                'error' => true,
                'messages' => $validator->errors(),
            ]);
        }
        if ($this->helperController->check_authorization($authorization, false)) {
            $user_datas['password'] = Hash::make($request->new_password);
            $user_datas['updated_at'] = $this->current_timestamp;
            if (User::where('id', $authorization)->update($user_datas)) {
                $response = [
                    'status' => 201,
                    'error' => false,
                    'messages' => 'Password update successfully.',
                ];
            } else {
                $response = [
                    'status' => 404,
                    'error' => true,
                    'messages' => 'Failed to update password!',
                ];
            }
        } else {
            $response = [
                'status' => 401,
                'error' => true,
                'messages' => 'Invalid credentials!',
            ];
        }

        return response()->json($response);
    }

    // Forgot password section end


}
