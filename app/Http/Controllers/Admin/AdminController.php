<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Reply;
use App\Model\Settings;
use App\Model\Sms;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Model\GoalsReached;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

use Helper;

class AdminController extends Controller
{
    public function index()
    {
        $settings=Settings::first();
        $messages_sent_today=(Sms::whereDate('created_at', Carbon::today())->where('is_received',0)->withTrashed()->count())+(Reply::where('system_reply',1)->whereDate('created_at', Carbon::today())->withTrashed()->count());
        $messages_received_today=(Sms::whereDate('created_at', Carbon::today())->where('is_received',1)->withTrashed()->count())+(Reply::where('system_reply',0)->whereDate('created_at', Carbon::today())->withTrashed()->count());
        $total_sent_lifetime=(Sms::distinct('client_number')->where('is_received',0)->withTrashed()->count())+(Reply::distinct('to')->where('system_reply',1)->withTrashed()->count());
        $total_received_lifetime=(Sms::where('is_received',1)->withTrashed()->count())+(Reply::where('system_reply',0)->withTrashed()->count());
        $messages_sent_today_goals=getReportingDataOfSMS(0);
        $messages_sent_seven_days_goals=getReportingDataOfSMS(7);
        $messages_sent_month_days_goals=getReportingDataOfSMS(30);
        $messages_sent_ninety_days_goals=getReportingDataOfSMS(90);
        $user=Auth::id();
        $goal=GoalsReached::where('user_id',$user)->first();
        $goalValue=$goal->goals??0;


//        $messages_sent_week=(Sms::whereDate('created_at', Carbon::today())->where('is_received',0)->withTrashed()->count())+(Reply::where('system_reply',1)->whereDate('created_at', Carbon::today())->withTrashed()->count());
//        $messages_received_week=(Sms::whereDate('created_at', Carbon::today())->where('is_received',1)->withTrashed()->count())+(Reply::where('system_reply',0)->whereDate('created_at', Carbon::today())->withTrashed()->count());




        return view('back.index',compact('goalValue','total_sent_lifetime','total_received_lifetime','messages_sent_today','messages_received_today',"settings",'messages_sent_today_goals','messages_sent_seven_days_goals','messages_sent_month_days_goals','messages_sent_ninety_days_goals'));
    }

    public function setGoals(Request $request)
    {
        $user=Auth::id();
        $goal=GoalsReached::where('user_id',$user)->first();
        $goalValue=$goal->goals??0;
        return view('back.pages.goal-settings.index',compact('goalValue'));
    }
    public function saveGoals(Request $request)
    {
        $user=Auth::id();
        $goal=GoalsReached::where('user_id',$user)->first();
        if($goal)
        {
            $goal->update([
                "goals"=>$request->get('contact_people')
            ]);
        }
        else
        {
            GoalsReached::create([
                "user_id"=>$user,
                "goals"=>$request->get('contact_people')                
            ]);
        }
        Alert::success('Success','Goal Saved!');
        return redirect()->back();
    }
}
