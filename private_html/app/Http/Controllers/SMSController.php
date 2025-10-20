<?php

namespace App\Http\Controllers;

use App\Models\SmsHistory;
// use phpseclib\Net\SSH2;
use phpseclib3\Net\SSH2;
use Illuminate\Http\Request;

class SMSController extends Controller
{
   
    const SENDER = "ONTKEM";
    const DLT_CT_ID = "1007867281807748703";
    const DLT_PE_ID = "1001637741904157041";

    const route_id = "DLT_SERVICE_IMPLICT";
    
    const MSG91_AUTH = "332630Ankpdo7C5ee8613cP1";
    const DLTID = "1007433066913268886";
    const ROUTE = "4";
    const ROUTE_DEFAULT = "default";
    const MSG91 = "MSG91";

    // Sirtel 
    const DLT_TM_ID = "1001096933494158";
    
    public static function readySMS(Request $request, $filters = null) {

        $sms_array = array(
            'sms_type' => "OTP",
            'user_id' => $filters->get('userId'),
            'mobile' => $filters->get('mobile'),
            'message' => $filters->get('message'),
            'message_status' => "Sending",
            'route' => self::ROUTE,
            'sms_api' => self::MSG91
        );

        $smsHistory = new SmsHistory();
        $smsHistory->fill($sms_array);
        $smsHistory->save();

        // self::sendSMSViaMSG91($request, $filters);
        // self::sendSMSViaAIRTEL($request, $filters);
        self::sshAirtelSMSCommand($request, $filters);
    }

    public static function sendSMSViaMSG91(Request $request, $filters = null) {

        //Multiple mobiles numbers separated by comma
        $mobileNumber = $filters->get('mobile');

        $message = $filters->get('message');

        //Prepare you post parameters
        $postData = array(
            'authkey' => self::MSG91_AUTH,
            'mobiles' => $mobileNumber,
            'message' => $message,
            'sender' => self::SENDER,
            'DLT_TE_ID' => self::DLTID,
            'route' => self::ROUTE_DEFAULT
        );

        //API URL
        $url="http://api.msg91.com/api/sendhttp.php";

        // init the resource
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData
            //,CURLOPT_FOLLOWLOCATION => true
        ));

        //Ignore SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        //get response
        $output = curl_exec($ch);

        //Print error if any
        if(curl_errno($ch)) {
            echo 'error:' . curl_error($ch);
        }

        curl_close($ch);
    }

    public static function sendSMSViaAIRTEL(Request $request) {

        echo "Pratik ";
        $curl = curl_init();

        //Prepare you post parameters
        // $postData = array(
        //     'authkey' => self::MSG91_AUTH,
        //     'mobiles' => $mobileNumber,
        //     'message' => '$message',
        //     'sender' => self::SENDER,
        //     'DLT_TE_ID' => self::DLTID,
        //     'route' => self::ROUTE_DEFAULT
        // );

        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://digimate.airtel.in:15181/BULK_API/SendMessage?loginID=onetake_htuser&password=onetake@123&mobile=70124785541&text=Your
                Playflix Verification Code is 925511
                g4e5mxAU/0z&senderid=PLYFLX&DLT_TM_ID=1001096933494158&DLT_CT_ID=1007598329773682219&DLT_PE_ID=1001637741904157041&route_id=DLT_SERVICE_IMPLICT&Unicode=0&camp_name=onetake_user",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        //   CURLOPT_POSTFIELDS => "{\n            \"loginID\":\"onetake_htuser","password\":\"onetake@123","mobile\":\"8866442661","text\":\"Playflix Verification Code is 290910  g4e5mxAU","senderid\":\"PLYFLX","DLT_TM_ID\":\"1001096933494158","DLT_CT_ID\":\"1007598329773682219","DLT_PE_ID\":\"1001637741904157041","route_id\":\"DLT_SERVICE_IMPLICT","Unicode\":\"0","camp_name\":\"onetake_user\"\n}",
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {

            echo "cURL Error #:" . $err;
        }
        else {
          echo $response;
        }

        echo " Patel ";
    }

    public function sendSMSViaAIRTELOLD(Request $request) {

        //Multiple mobiles numbers separated by comma
        // $mobileNumber = $filters->get('mobile');

        // $message = $filters->get('message');

        //Prepare you post parameters
        $postData = array(
            'mobile' => '8866442661',
            'text' => 'Your Playflix Verification Code is 589796
            g4e5mxAU/0z',
            'senderid' => self::SENDER,
            'DLT_TM_ID' => self::DLT_TM_ID,
            'DLT_CT_ID' => self::DLT_CT_ID,
            'DLT_PE_ID' => self::DLT_PE_ID,
            'route_id' => self::route_id,
            'Unicode' => 0,
            'camp_name' => 'onetake_user'
        );

        //API URL
        $url="http://digimate.airtel.in:15181/BULK_API/SendMessage?loginID=onetake_htuser&password=onetake@123&";

        // init the resource
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData
            //,CURLOPT_FOLLOWLOCATION => true
        ));

        //Ignore SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        //get response
        $output = curl_exec($ch);

        //Print error if any
        if(curl_errno($ch)) {

            $info = curl_getinfo($ch); 
            echo 'Took ', $info['total_time'], ' seconds to send a request to ', $info['url'], "\n";
        }

        curl_close($ch);
    }

    public static function sshCommand() {

        $host = '139.59.47.210';
        $port = 22;
        $username = 'master_mswfkeexgc';
        $password = '2tE3wMzV';

        // $connection = ssh2_connect($host, $port);
        // ssh2_auth_password($connection, $username, $password);

        // $stream = ssh2_exec($connection, 'df -h');
        // stream_set_blocking($stream, true);
        // $output = stream_get_contents($stream);

        // print_r($output);

        // $host = config('ssh.host');
        // $username = config('ssh.username');
        // $password = config('ssh.password');

        $command = "wget 'http://digimate.airtel.in:15181/BULK_API/SendMessage?loginID=onetake_htuser&password=onetake@123&mobile=8866442661&text=Your Playflix Verification Code is 925511 g4e5mxAU/0z&senderid=PLYFLX&DLT_TM_ID=1001096933494158&DLT_CT_ID=1007598329773682219&DLT_PE_ID=1001637741904157041&route_id=DLT_SERVICE_IMPLICT&Unicode=0&camp_name=onetake_user'";

        $ssh = new SSH2($host);

        if (!$ssh->login($username, $password)) {

            $output ='Login Failed';
        }
        else {
            $output = $ssh->exec($command);
        }

        print_r($output);
    }

    public static function sshAirtelSMSCommand(Request $request, $filters = null) {

        //Multiple mobiles numbers separated by comma
        $mobileNumber = $filters->get('mobile');
        $message = $filters->get('message');

        $host = '139.59.47.210';
        $port = 22;
        $username = 'master_mswfkeexgc';
        $password = '2tE3wMzV';

        // $host = config('ssh.host');
        // $username = config('ssh.username');
        // $password = config('ssh.password'); 

        $command = "wget 'https://digimate.airtel.in:15443/BULK_API/SendMessage?loginID=onetake_htuser&password=onetake@123&mobile=$mobileNumber&text=$message&senderid=ONTKEM&DLT_TM_ID=1001096933494158&DLT_CT_ID=1007867281807748703&DLT_PE_ID=1001637741904157041&route_id=DLT_SERVICE_IMPLICT&Unicode=0&camp_name=onetake_user'";

        $ssh = new SSH2($host);

        if (!$ssh->login($username, $password)) {

            $output ='Login Failed';
        }
        else {
            $output = $ssh->exec($command);
        }
    }
}
