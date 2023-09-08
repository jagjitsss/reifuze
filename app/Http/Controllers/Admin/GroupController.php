<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Contact;
use App\Model\CustomField;
use App\Model\Section;
use App\Model\LeadCategory;
use App\Model\Number;
use App\Model\Group;
use App\Model\Market;
use App\Model\Tag;
use App\Model\Account;
use App\Model\Campaign;
use App\Model\CampaignList;
use App\Model\Template;
use App\Model\Script;
use App\Model\Reply;
use App\Model\Scheduler;
use App\Mail\TestEmail;
use App\Mail\Mailcontact;  //  05092023 sachin
use App\Model\Contractupload; //  06092023 sachin
use Smalot\PdfParser\Parser; // 06092023 sachin
use Illuminate\Support\Facades\Mail;
use App\Model\Sms;
use App\Model\FailedSms;
use Twilio\Rest\Client;
use Illuminate\Http\Request;
use Carbon\Carbon;
use RealRashid\SweetAlert\Facades\Alert;
use DB;


class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {



        // $groups = Group::with('contacts')->get()->sortByDesc("created_at");

        // $groupCounts = $groups->map(function ($group) {
        //     return [
        //         'group_name' => $group->name, // Replace 'name' with the actual group name attribute
        //         'contact_count' => $group->contacts->count(),
        //     ];
        // });

        $groups = Group::with('contacts')->get()->sortByDesc("created_at");

        $groupCounts = $groups->map(function ($group) {
            $totalContacts = $group->contacts->count();
            $percentage = ($totalContacts / 100)*100;

            return [
                'group_name' => $group->name, // Replace 'name' with the actual group name attribute
                'contact_count' => $totalContacts,
                'percentage' => $percentage,
            ];
        });

        $sr = 1;;
        $markets=Market::all();
        $tags=Tag::all();
        $campaigns = Campaign::getAllCampaigns();

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $groups,
                'success' => true,
                'status' => 200,
                'message' => 'OK'
            ]);
        } else {
            return view('back.pages.group.index', compact('groups','groupCounts', 'sr','campaigns','markets','tags'));
        }
    }

    public function contactInfo(Request $request , $id = ''){
        //return 'wertyui';
        $leads = LeadCategory::all();
        $scripts = Script::all();
        $tags = Tag::all();
        $sections=Section::all();
        $leadinfo = DB::table('lead_info')->where('contact_id' , $id)->first();

        if($leadinfo == null){
            DB::table('lead_info')->insert(['contact_id' => $id]);
            $leadinfo = DB::table('lead_info')->where('contact_id' , $id)->first();
        }
        $property_infos = DB::table('property_infos')->where('contact_id' , $id)->first();
        if($property_infos == null){
            DB::table('property_infos')->insert(['contact_id' => $id]);
            $property_infos = DB::table('property_infos')->where('contact_id' , $id)->first();
        }
        $values_conditions = DB::table('values_conditions')->where('contact_id' , $id)->first();
        if($values_conditions == null){
            DB::table('values_conditions')->insert(['contact_id' => $id]);
            $values_conditions = DB::table('values_conditions')->where('contact_id' , $id)->first();
        }
        $property_finance_infos = DB::table('property_finance_infos')->where('contact_id' , $id)->first();
        if($property_finance_infos == null){
            DB::table('property_finance_infos')->insert(['contact_id' => $id]);
            $property_finance_infos = DB::table('property_finance_infos')->where('contact_id' , $id)->first();
        }
        $selling_motivations = DB::table('selling_motivations')->where('contact_id' , $id)->first();
        if($selling_motivations == null){
            DB::table('selling_motivations')->insert(['contact_id' => $id]);
            $selling_motivations = DB::table('selling_motivations')->where('contact_id' , $id)->first();
        }
        $negotiations = DB::table('negotiations')->where('contact_id' , $id)->first();
        if($negotiations == null){
            DB::table('negotiations')->insert(['contact_id' => $id]);
            $negotiations = DB::table('negotiations')->where('contact_id' , $id)->first();
        }
        $title_company = DB::table('title_company')->where('contact_id' , $id)->first();
        if($title_company == null){
            DB::table('title_company')->insert(['contact_id' => $id]);
            $title_company = DB::table('title_company')->where('contact_id' , $id)->first();
        }
        //dd($property_infos);
        $getAllAppointments = Scheduler::where('user_id', 1)->get();
        // print_r($getAllAppointments);
        // exit;
        
        return view('back.pages.group.contactDetail', compact('id','title_company','leadinfo','scripts','sections','property_infos','values_conditions','property_finance_infos','selling_motivations','negotiations','leads', 'tags','getAllAppointments'));
    }

    public function updateinfo(Request $request){
        $table = $request->table;
        $id = $request->id;
        $feild_id = $request->feild_id;
        $section_id = $request->section_id;
        $fieldVal = $request->fieldVal;
        $fieldName = $request->fieldName;
        if($table == 'custom_field_values'){
            $contact = DB::table($table)->where('contact_id' , $id)->where('feild_id' , $feild_id)->first();
            if($contact){
                DB::table($table)->where('contact_id' , $id)->where('feild_id' , $feild_id)->update([$fieldName => $fieldVal]);
            }else{
                DB::table($table)->insert(['contact_id' => $id ,'feild_id' => $feild_id ,'section_id' => $section_id , $fieldName => $fieldVal]);
            }
        }else{
            $contact = DB::table($table)->where('contact_id' , $id)->first();
            if($contact){
                DB::table($table)->where('contact_id' , $id)->update([$fieldName => $fieldVal]);
            }else{
                DB::table($table)->insert(['contact_id' => $id ,$fieldName => $fieldVal]);
            }

        }

        return 'added';
        //return view('back.pages.group.contactDetail', compact('id','groups', 'campaigns','markets'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $existing_group_id='';
        $existing_group_id=$request->existing_group_id;
        $group_id = '';
        $campaign_id='';
        //return $request->campaign_id;
        if($request->campaign_id != 0){
            $campaign_id = $request->campaign_id;
            //die($campaign_id );
            $compain = Campaign::where('id',$campaign_id)->first();
            $group_id = $compain->group_id;
        }


        //return $group_id;

        if($existing_group_id!=0)
        {

         $group_id=$existing_group_id;
         $group = Group::where('id', $group_id)->first();
         $group->market_id = $request->market_id;
         $group->tag_id = $request->tag_id;
         $group->save();
        }
        else
        {

        $group = new Group();
        $group->market_id = $request->market_id;
        $group->tag_id = $request->tag_id;
        $group->name = $request->name;
        $group->save();
        }



        $file = $request->file('file');

        // File Details
        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $tempPath = $file->getRealPath();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();

        // Valid File Extensions
        $valid_extension = array("csv");

        // 2MB in Bytes
        $maxFileSize = 2097152;

        // Check file extension
        if (in_array(strtolower($extension), $valid_extension)) {

            // Check file size
            if ($fileSize <= $maxFileSize) {

                // File upload location
                $location = 'uploads';

                // Upload file
                $file->move($location, $filename);

                // Import CSV to Database
                $filepath = public_path($location . "/" . $filename);

                // Reading file
                $file = fopen($filepath, "r");

                $importData_arr = array();
                $i = 0;

                while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                    $num = count($filedata);

                    // Skip first row (Remove below comment if you want to skip the first row)
                    if ($i == 0) {
                        $i++;
                        continue;
                    }
                    for ($c = 0; $c < $num; $c++) {
                        $importData_arr[$i][] = $filedata [$c];
                    }
                    $i++;
                }
                fclose($file);


                // Insert to MySQL database
                foreach ($importData_arr as $importData) {
                    if($group_id != ''){
                        $checkContact = Contact::where('number' , '+1' . preg_replace('/[^0-9]/', '', $importData[5]))->where('group_id' , $group_id)->first();
                        if($checkContact == null){
                            $insertData1 = array(
                                "group_id" => $group_id,
                                "name" => $importData[0],
                                "last_name" => $importData[1],
                                "street" => $importData[2],
                                "city" => $importData[3],
                                "state" => $importData[4],
                                "zip" => $importData[5],
                                "number" => '+1' . preg_replace('/[^0-9]/', '', $importData[6]),
                                "number2" => '+1' . preg_replace('/[^0-9]/', '', $importData[7]),
                                "number3" => '+1' . preg_replace('/[^0-9]/', '', $importData[8]),
                                "email1" => $importData[9],
                                "email2" => $importData[10]
                            );
                            Contact::create($insertData1);
                            $groupsID = Group::where('id',$group_id)->first();
                            $sender_numbers = Number::where('market_id' , $groupsID->market_id)->inRandomOrder()->first();
                    if($sender_numbers){
                        $account = Account::where('id' , $sender_numbers->account_id)->first();
                        if($account){
                            $sid = $account->account_id;
                            $token = $account->account_token;
                        }else{
                            $sid = '';
                            $token = '';
                        }
                    }

                            $campaignsList = CampaignList::where('campaign_id' , $campaign_id)->orderby('schedule', 'ASC')->first();
                            // print_r($campaignsList);die;
                            if($campaignsList){
                                $row = $campaignsList;
                                $template = Template::where('id',$row->template_id)->first();
                                if($row->type == 'email'){
                                    //dd($contacts);
                                    //return $cont->name;
                                    if($importData[9] != ''){
                                        $email = $importData[9];
                                    }elseif($importData[10]){
                                        $email = $importData[10];
                                    }
                                    if($email != ''){
                                        $subject = $template->subject;
                                        $subject = str_replace("{name}", $importData[0], $subject);
                                        $subject = str_replace("{street}", $importData[2], $subject);
                                        $subject = str_replace("{city}", $importData[3], $subject);
                                        $subject = str_replace("{state}", $importData[4], $subject);
                                        $subject = str_replace("{zip}", $importData[5], $subject);
                                        $message = $template!=null ? $template->body : '';
                                        $message = str_replace("{name}", $importData[0], $message);
                                        $message = str_replace("{street}", $importData[2], $message);
                                        $message = str_replace("{city}", $importData[3], $message);
                                        $message = str_replace("{state}", $importData[4], $message);
                                        $message = str_replace("{zip}", $importData[5], $message);
                                        $unsub_link = url('admin/email/unsub/'.$email);
                                        $data = ['message' => $message,'subject' => $subject, 'name' =>$importData[0], 'unsub_link' =>$unsub_link];
                                      Mail::to($email)->send(new TestEmail($data));
                                     ;
                                    }
                                }elseif($row->type == 'sms'){
                                    $client = new Client($sid, $token);
                                    if($importData[6] != ''){
                                        $number = '+1' . preg_replace('/[^0-9]/', '', $importData[6]);
                                    }elseif($importData[7] != ''){
                                        $number = '+1' . preg_replace('/[^0-9]/', '', $importData[7]);
                                    }elseif($importData[8] != ''){
                                        $number = '+1' . preg_replace('/[^0-9]/', '', $importData[8]);
                                    }
                                    $receiver_number = $number;
                                    $sender_number = $sender_numbers->number;
                                    $message = $template!=null && $template->body ? $template->body : '';
                                    $message = str_replace("{name}", $importData[0], $message);
                                    $message = str_replace("{street}", $importData[2], $message);
                                    $message = str_replace("{city}", $importData[3], $message);
                                    $message = str_replace("{state}", $importData[4], $message);
                                    $message = str_replace("{zip}", $importData[5], $message);
                                    if($receiver_number != ''){
                                        try {
                                            $sms_sent = $client->messages->create(
                                                $receiver_number,
                                                [
                                                    'from' => $sender_number,
                                                    'body' => $message,
                                                ]
                                            );
                                            if ($sms_sent) {
                                                $old_sms = Sms::where('client_number', $receiver_number)->first();
                                                if ($old_sms == null) {
                                                    $sms = new Sms();
                                                    $sms->client_number = $receiver_number;
                                                    $sms->twilio_number = $sender_number;
                                                    $sms->message = $message;
                                                    $sms->media = '';
                                                    $sms->status = 1;
                                                    $sms->save();
                                                  $this->incrementSmsCount($sender_number);
                                                } else {
                                                    $reply_message = new Reply();
                                                    $reply_message->sms_id = $old_sms->id;
                                                    $reply_message->to = $sender_number;
                                                    $reply_message->from = $receiver_number;
                                                    $reply_message->reply = $message;
                                                    $reply_message->system_reply = 1;
                                                    $reply_message->save();
                                                   $this->incrementSmsCount($sender_number);
                                                }
                                            }
                                        } catch (\Exception $ex) {
                                            $failed_sms = new FailedSms();
                                            $failed_sms->client_number = $receiver_number;
                                            $failed_sms->twilio_number = $sender_number;
                                            $failed_sms->message = $message;
                                            $failed_sms->media = '';
                                            $failed_sms->error = $ex->getMessage();
                                            $failed_sms->save();
                                        }
                                    }
                                }elseif($row->type == 'mms'){
                                    $client = new Client($sid, $token);
                                    if($importData[6] != ''){
                                        $number = '+1' . preg_replace('/[^0-9]/', '', $importData[6]);
                                    }elseif($importData[7] != ''){
                                        $number = '+1' . preg_replace('/[^0-9]/', '', $importData[7]);
                                    }elseif($importData[8] != ''){
                                        $number = '+1' . preg_replace('/[^0-9]/', '', $importData[8]);
                                    }
                                    $receiver_number = $number;
                                    $sender_number = $sender_numbers->number;
                                    $message = $template!=null ? $template->body : '';
                                    $message = str_replace("{name}", $importData[0], $message);
                                    $message = str_replace("{street}", $importData[2], $message);
                                    $message = str_replace("{city}", $importData[3], $message);
                                    $message = str_replace("{state}", $importData[4], $message);
                                    $message = str_replace("{zip}", $importData[5], $message);
                                    if($receiver_number != ''){
                                        try {
                                            $sms_sent = $client->messages->create(
                                                $receiver_number,
                                                [
                                                    'from' => $sender_number,
                                                    'body' => $message,
                                                    'mediaUrl' => [$template->mediaUrl],
                                                ]
                                            );

                                            if ($sms_sent) {
                                                $old_sms = Sms::where('client_number', $receiver_number)->first();
                                                if ($old_sms == null) {
                                                    $sms = new Sms();
                                                    $sms->client_number = $receiver_number;
                                                    $sms->twilio_number = $sender_number;
                                                    $sms->message = $message;
                                                    $sms->media = $template->mediaUrl;
                                                    $sms->status = 1;
                                                    $sms->save();
                                                   $this->incrementSmsCount($sender_number);
                                                } else {
                                                    $reply_message = new Reply();
                                                    $reply_message->sms_id = $old_sms->id;
                                                    $reply_message->to = $sender_number;
                                                    $reply_message->from = $receiver_number;
                                                    $reply_message->reply = $message;
                                                    $reply_message->system_reply = 1;
                                                    $reply_message->save();
                                                   $this->incrementSmsCount($sender_number);
                                                }

                                            }
                                        } catch (\Exception $ex) {
                                            $failed_sms = new FailedSms();
                                            $failed_sms->client_number = $receiver_number;
                                            $failed_sms->twilio_number = $sender_number;
                                            $failed_sms->message = $message;
                                            $failed_sms->media = $template->mediaUrl;
                                            $failed_sms->error = $ex->getMessage();
                                            $failed_sms->save();
                                        }
                                    }
                                }elseif($row->type == 'rvm'){
                                        $contactsArr = [];
                                        if($importData[6] != ''){
                                            $number = '+1' . preg_replace('/[^0-9]/', '', $importData[6]);
                                        }elseif($importData[7] != ''){
                                            $number = '+1' . preg_replace('/[^0-9]/', '', $importData[7]);
                                        }elseif($importData[8] != ''){
                                            $number = '+1' . preg_replace('/[^0-9]/', '', $importData[8]);
                                        }
                                        if($number){
                                            $c_phones = $number;
                                            $vrm = \Slybroadcast::sendVoiceMail([
                                                                'c_phone' => ".$c_phones.",
                                                                'c_url' =>$template->body,
                                                                'c_record_audio' => '',
                                                                'c_date' => 'now',
                                                                'c_audio' => 'Mp3',
                                                                //'c_callerID' => "4234606442",
                                                                'c_callerID' => $sender_numbers->number,
                                                                //'mobile_only' => 1,
                                                                'c_dispo_url' => 'https://brian-bagnall.com/bulk/bulksms/public/admin/voicepostback'
                                                               ])->getResponse();
                                        }

                                    }
                            }

                        }

                    }
                    if($existing_group_id==0)
                    {
                    $insertData = array(
                        "group_id" => $group->id,
                        "name" => $importData[0],
                        "last_name" => $importData[1],
                        "street" => $importData[2],
                        "city" => $importData[3],
                        "state" => $importData[4],
                        "zip" => $importData[5],
                        "number" => '+1' . preg_replace('/[^0-9]/', '', $importData[6]),
                        "number2" => '+1' . preg_replace('/[^0-9]/', '', $importData[7]),
                        "number3" => '+1' . preg_replace('/[^0-9]/', '', $importData[8]),
                        "email1" => $importData[9],
                        "email2" => $importData[10]
                    );
                    Contact::create($insertData);
                }
                }
                Alert::success('Success!', 'Group Created!');
            } else {
                Alert::error('Oops!', 'File too large. File must be less than 2MB');
            }

        } else {
            $group->delete();
            Alert::error('Oops!', 'Invalid File Extension.');
        }
        return redirect()->back();
    }

    public function incrementSmsCount(string $number)
    {
        $number = Number::where('number', $number)->first();
        $number->sms_count++;
        $number->save();
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Model\Group $group
     * @return \Illuminate\Http\Response
     */
    public function getAllContacts(Request $request)
    {
        $sr = 1;
        $contacts = Contact::where("is_dnc", 0)->get();
        return view('back.pages.group.view_all', compact('contacts', 'sr'));

    }

    public function show(Group $group, Request $request)
    {
        $contractres = Contractupload::all()->sortByDesc("created_at");
        $id =$group->id;
        $sr = 1;
        if ($request->wantsJson()) {
            $contacts = Contact::where("group_id", $group->id)->where("is_dnc", 0)->get();
            return response()->json([
                'data' => $contacts,
                'success' => true,
                'status' => 200,
                'message' => 'OK'
            ]);
        } else {
            return view('back.pages.group.details', compact('group', 'sr','contractres', 'id'));
          //  return view('back.pages.group.details', compact('group', 'sr', 'id'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Model\Group $group
     * @return \Illuminate\Http\Response
     */
    public function edit(Group $group)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Model\Group $group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Group $group)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Model\Group $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        Group::find($request->id)->delete();
        Alert::success('Success!', 'Group Removed!');
        return redirect()->back();
    }


    public function getScript($id = ''){
        $scrip = Script::where('id',$id)->first();
        return view('back.pages.group.ajaxScript', compact('scrip'));
    }
    public function skipTrace(Request $request)
    {
        // Retrieve group_id and skip_trace_option from the request

        $skipTraceOption = $request->input('skip_trace_option');

    //  05092023 sachin
   
        // Perform skip tracing logic here using the Datazapp API
        // You can make API requests to initiate skip tracing based on the selected option

        // Return a response indicating success or failure
       // return response()->json(['message' => 'Skip tracing initiated successfully']);
    }

    public function mailcontactlist(Request $request)
    {
        if(count($request->checked_id)>0){
            foreach($request->checked_id as $contactId){
                
                $contractRes = Contractupload::where("id",$request->contracttype)->first();
                $mailcontact = Contact::where("id",$contactId)->first();
                $subject = "Testing 05092023";
                // $message = "Message Testing 05092023".'<br>';
                $message = $contractRes->content;
                $url = url('myHtml/'.$contractRes->id.'/'.$mailcontact->id);
                $data = ['message' => $message,'subject' => $subject, 'name' =>$mailcontact->name,"contract_id"=>$contractRes->id,"contactid"=>$mailcontact->id,"Url"=>$url];
                Mail::to($mailcontact->email1)->send(new Mailcontact($data));
                Contact::where("id",$contactId)->update(['mail_sent'=>1]);
            }
                Alert::success('Success!', 'Mail sent successfully!');
                return redirect()->back();
        }
    } 

   public function uploadcontract(Request $request){


    $file = $request->file;

    $fileName = $file->getClientOriginalName();
    $extension = $file->getClientOriginalExtension();
    $tempPath = $file->getRealPath();
    $fileSize = $file->getSize();
    $mimeType = $file->getMimeType();

    // Valid File Extensions
    $valid_extension = array("pdf");
    // Check file extension
    if (in_array(strtolower($extension), $valid_extension)) {
    $pdfParser = new Parser();
    $pdf = $pdfParser->parseFile($file->path());
    $content = $pdf->getText();
        $filenameNew =  time().'.'.$fileName;
    $Contractupload = new Contractupload;
       $Contractupload->content = $content;
       $Contractupload->type_contract =$request->optiontype;        
       $Contractupload->file =$filenameNew;     
       $Contractupload->save();
       $file->move('../public/contractpdf/', $filenameNew);
       Alert::success('Success!', 'Contract Uploaded successfully!');
       return redirect()->back();
    } else {
        Alert::error('Oops!', "Please enter only pdf file");
        return redirect()->back();
    }
   }



   
   public function uploadcontractedit(Request $request){
    // dd($request->all());

    $file = $request->file;

    $fileName = $file->getClientOriginalName();
    $extension = $file->getClientOriginalExtension();
    $tempPath = $file->getRealPath();
    $fileSize = $file->getSize();
    $mimeType = $file->getMimeType();

    // Valid File Extensions
    $valid_extension = array("pdf");
    // Check file extension
    if (in_array(strtolower($extension), $valid_extension)) {
    $pdfParser = new Parser();
    $pdf = $pdfParser->parseFile($file->path());
    $content = $pdf->getText();
        $filenameNew =  time().'.'.$fileName;
       $Contractupload = Contractupload::find(1);
       $destinationPath = public_path('/contractpdf/'.$Contractupload->file);
   
    if(file_exists($destinationPath)){
        unlink($destinationPath);
    }
       $Contractupload->content = $content;
       $Contractupload->type_contract =$request->optiontype;        
       $Contractupload->file =$filenameNew;     
       $Contractupload->save();
       $file->move('../public/contractpdf/', $filenameNew);
       Alert::success('Success!', 'Contract Updated successfully!');
       return redirect()->back();
    } else {
        Alert::error('Oops!', "Please enter only pdf file");
        return redirect()->back();
    }
   }

   public function contractview(Request $request)
   {
       $contractres = Contractupload::all()->sortByDesc("created_at");
       return view('back.pages.group.contractview', compact('contractres'));
   }

   public function myHtml($id,$contactid)
   {
    $contractRes = Contractupload::where("id",$id)->first();
    $contactrRes = Contact::where("id",$contactid)->first();
    $name =$contactrRes->name;
    $contractres = str_replace('#name#', $name , $contractRes->content);
       return view('back.pages.group.myFile', compact('contractres'));
   }
    

}
