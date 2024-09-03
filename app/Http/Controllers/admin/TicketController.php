<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\user\User;
use App\Models\admin\AdminUser;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->helperController = new HelperController();
        $this->current_timestamp = now();
    }


    

    public function create_ticket(Request $request)
    {
        $response = [];
        $authorization = $this->helperController->get_user_token($request->bearerToken(), null);
        if($this->helperController->check_authorization($authorization, false))
        {
            // $validator = Validator::make($request->all(), [
            //     'check_in_time' => 'required'                    
            // ]);
            // if($validator->fails()) {
            //     return response()->json([
            //         'status' => 404,
            //         'error' => true,
            //         'messages' => $validator->errors(),
            //     ]);
            // }
            $this->check_update_user_daily_leave_status($request);
            $attendance_data=Attendance::where('attendance_date',now()->format('Y-m-d'))->first();
            if($attendance_data)  // update attendance data
            {
                if(Attendance::where(['id'=>$check_in_data['id'],'attendance_date'=> $check_in_data['attendance_date']])->update(['check_in_time'=>$check_in_data['check_in_time'],'attendance_date'=> $check_in_data['attendance_date'], 'status'=>'Present']))
                {
                    $response = [
                        'status' => 201,
                        'error' => true,
                        'messages' => 'user checked in updated successfully.',
                        'check_in_details' => null,
                    ];
                }
                else
                {
                    $response = [
                        'status' => 404,
                        'error' => true,
                        'messages' => 'user not checked in!',
                        'check_in_details' => null,
                    ];
                }
            }
            else
            {
                if(Attendance::create($check_in_data))
                {
                    $response = [
                        'status' => 201,
                        'error' => true,
                        'messages' => 'user checked in successfully.',
                        'check_in_details' => null,
                    ];
                }
                else
                {
                    $response = [
                        'status' => 404,
                        'error' => true,
                        'messages' => 'user not checked in!',
                        'check_in_details' => null,
                    ];
                }
            }            
        }
        else
        {
            $response = [
                'status' => 401,
                'error' => true,
                'messages' => 'Invalid credentials!',
                'check_in_details' => null,
            ];
        }
        return response()->json($response);
    }
    
}
