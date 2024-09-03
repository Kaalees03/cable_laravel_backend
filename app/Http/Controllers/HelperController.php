<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Mail;

use App\Mail\SendOtpMail;
use App\Models\OtpVerficationModel;
use App\Models\user\User;

class HelperController extends Controller
{
    public function uniqueusernumber($user_type, $latest_id): string
    {
        // $user_type 0=end-user, 1=admin-user
        $return_unique = null;
        $idlength = strlen($latest_id);
        $increment_length = $idlength < 4 ? 4 : $idlength + 1;
        $suffix_content = date('y') . str_pad($latest_id, $increment_length, '0', STR_PAD_LEFT);
        $return_unique = $user_type == 1 ? 'CB-ADMIN-' . $suffix_content : 'CB-USER-' . $suffix_content;
        return $return_unique;
    }

    public function send_otp_mailsms($send_otp_type,$email,$mobile_number): array
    {
        $returnMessage['error_data'] = false;
        $returnMessage['error_message'] = 'Failed to resend otp!';
        $otp_code = mt_rand(1000, 9999);
        if($mobile_number) 
        {
            $header_data['authkey'] = env('MSG91_AUTHKEY'); // MSG91 authkey_here
            $header_data['accept'] = 'application/json';
            $header_data['content-type'] = 'application/JSON';
            $recipients_data['mobiles'] = '91' . $mobile_number;
            $recipients_data['var1'] = "$otp_code";
            $request_data['template_id'] = env('MSGTEMPLATE1'); // SMS OTP Template id
            $request_data['short_url'] = '1'; // 1=ON, 0=OFF
            $request_data['recipients'] = [$recipients_data];
            $response = Http::withHeaders($header_data)->post('https://control.msg91.com/api/v5/flow', $request_data);
            $return_reply = $response->json();
            $sendOtpMessage = false;
            if($return_reply)
            {
               $sendOtpMessage = true;
            }
        }
        if($email)
        {
            $mail_details['user_name'] = 'Customer';
            $mail_details['email'] = $this->maskEmail($email);
            $mail_details['otp_code'] = $otp_code;
            $returnReply = Mail::to($email)->send(new SendOtpMail($mail_details));           
            $sendOtpMessage = $returnReply == 0 ? true : false;
        }
        if($sendOtpMessage)
        {
            $update_data['is_verified'] = 2;
            $update_data['updated_at'] = now();
            $otpCode_builder = OtpVerficationModel::query();
            if($mobile_number)
            {
                $otpCode_builder->where('mobile_number', $mobile_number);
                $otp_data['mobile_number'] = $mobile_number;
            }
            if($email)
            {
                $otpCode_builder->where('email', $email);
                $otp_data['email'] = $email;
            }
            $otpCode_builder->where('is_verified', 0)->update($update_data);
            $otp_data['otp_code'] = $otp_code;
            $otp_data['otp_type'] = $send_otp_type;
            $otp_data['created_at'] = now();
            OtpVerficationModel::insert($otp_data);
            $returnMessage['error_data'] = true;
            $returnMessage['error_message'] = $send_otp_type == 2 ? 'OTP resent successfully.'
            : 'OTP sent successfully.';
        } 
        else
        {
            $returnMessage['error_data'] = false;
            $returnMessage['error_message'] = $send_otp_type == 2 ? 'Failed to resend otp!'
            : 'Failed to send otp!';
        }
        return $returnMessage;
    }

      public function maskEmail($email)
    {
        $masked = '';
        $emailParts = explode('@', $email);
        $username = $emailParts[0];
        $domain = $emailParts[1];

        // Masking logic
        $maskedUsername = substr($username, 0, 4) . '******'; // Keep the first 4 characters visible
        $masked = $maskedUsername . '@' . $domain;

        return $masked;
    }


    public function set_user_token($user_details)
    {     
        $return_data = null;
        if ($user_details) {     
            $now = time();      
            $pay_load['iat'] = time();
            $pay_load['exp'] = time() + 60;
            $pay_load['enC_request'] = $user_details;
            $return_data = JWT::encode($pay_load, env('JWT_KEY'), env('JWT_ALGORITHM'));
        }
        return $return_data;
    }

    public function get_user_token_org($token, $user_device): int
    {
       
        $user_id = 0;
        $user_details = null;
        if (!empty($token)) {
            if (isset($token)) {
                $decoded_data = JWT::decode($token, new Key(env('JWT_KEY'), env('JWT_ALGORITHM')));
                $datetime = Carbon::createFromTimestamp($decoded_data->iat);
                if(!$user_device)
                {
                    $user_id = $decoded_data->enC_request->id;
                } 
                elseif(now()->diffInDays($datetime) < 60)
                {
                    $user_id = $decoded_data->enC_request->id;
                }
                if(!$user_details) 
                {
                    $user_details = User::where('unique_id', $decoded_data->enC_request->unique_id)->first();
                }
                if(!$user_details) 
                {
                    $user_details = AdminUser::where('unique_id', $decoded_data->enC_request->unique_id)->first();
                }
                if(!$user_details) 
                {
                    $user_id = 0;
                }
            }
        }
        return $user_id;
    }
    
    public function get_user_token($token, $user_device): int
    {
        $user_id = 0;

        if (!empty($token)) {
            try {
                // Decode the token
                $decoded_data = JWT::decode($token, new Key(env('JWT_KEY'), env('JWT_ALGORITHM')));
                $iat = Carbon::createFromTimestamp($decoded_data->iat);
                $exp = Carbon::createFromTimestamp($decoded_data->exp);
                
                // Check if the token is expired
                if (now()->greaterThan($exp)) {
                    // Token has expired
                    return 0;
                }

                // Check if the token is valid based on user_device
                if (!$user_device || now()->diffInDays($iat) < 60) {
                    // Retrieve user details based on unique_id
                    $user_details = User::where('unique_id', $decoded_data->enC_request->unique_id)->first();
                    if (!$user_details) {
                        $user_details = AdminUser::where('unique_id', $decoded_data->enC_request->unique_id)->first();
                    }
                    
                    if ($user_details) {
                        $user_id = $user_details->id;
                    }
                }
            } catch (ExpiredException $e) {
                // Handle expired token
                return 0; // Or any other logic for expired token
            } catch (\Exception $e) {
                // Handle other exceptions
                return 0; // Or any other logic for invalid token
            }
        }

        return $user_id;
    }


    public function check_authorization($authorization, $adminMember)
    {
        $return_data = null;
        if ($adminMember) {
            $return_data = AdminUser::where('id', $authorization)->where('status', 1)->first();
        } else {
            $return_data = User::where('id', $authorization)->where('status', 1)->first();
        }
        return $return_data;
    }
    
}
