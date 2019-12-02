<?php
//error_reporting(0);
date_default_timezone_set('Asia/Kolkata');
defined('BASEPATH') OR exit('No direct script access allowed');

class Cronjbs extends CI_Controller {

public function __construct()
{
parent::__construct();
$this->load->model('api_model');
$this->load->helper('url');
$this->load->helper('text');
}

public function index()
{

	echo "Hellooooooooooooo";
	echo $samay = date('H');

	if ($samay== '15') {
		


$followup_data = json_decode(
file_get_contents('http://35.154.186.145/okc_ivr_apis/api/getFollowData/2019-10-01/')
);

//print_r($followup_data);

foreach($followup_data as $followup_data){

 

// $ocdData = array(
// 'contact_no' => $followup_data->mobile_no,
// 'ocd_child_id' => $followup_data->child_id,
// 'call_start_time' => $followup_data->Message,
// 'call_duration' => "call_duration",
// 'call_status' => "call_status",
// 'call_for' => $followup_data->message_for,
// 'call_end_time' => "end_time",
// 'created_at' => date('Y-m-d H:i:s'),
// 'follow_up_data' => $followup_data->follow_up,
// 'created_date' => date('Y-m-d')
// );
// //print_r($ocdData);

// $id = $this->api_model->insertOCDdata($ocdData);



// ==================================ADD NEW LOGIC ===========================



// $postdata = http_build_query(
// array (
//   'question' => 'this is questions',
//   'questionMedia' => '',
//   'choices' => 
//   array (
//     0 => 
//     array (
//       'text' => '123123123',
//     ),
//     1 => 
//     array (
//       'text' => 'qweqweqwe',
//     ),
//     2 => 
//     array (
//       'text' => 'asdasdasd',
//     ),
//     3 => 
//     array (
//       'text' => 'zxczxczxcxc',
//     ),
//   ),
//   'allowMultiChoice' => false,
//   'endDate' => NULL,
//   'enableCaptcha' => false,
//   'hideResults' => false,
//   'restriction' => 'ip',
// )
// );

$ocdData = array(
	// "mobile_no"=>"9810789821",
	// "child_id"=> "46",
	// "start_time"=> "2019-10-11 10:22:30",
	// "end_time"=> "2019-10-11 10:24:47",
	// "message_for"=> "BUCKET114",
 //    "follow_up"=> "no"

		"mobile_no"=>$followup_data->mobile_no,
	"child_id"=> $followup_data->child_id,
	"start_time"=> date('Y-m-d H:i:s'),
	"end_time"=> date('Y-m-d H:i:s'),
	"message_for"=> "BUCKET114",
    "follow_up"=> "no"
// 'contact_no' => $followup_data->mobile_no,
// 'ocd_child_id' => $followup_data->child_id,
// 'call_start_time' => $followup_data->Message,
// 'call_duration' => "call_duration",
// 'call_status' => "call_status",
// 'call_for' => $followup_data->message_for,
// 'call_end_time' => "end_time",
// 'created_at' => date('Y-m-d H:i:s'),
// 'follow_up_data' => $followup_data->follow_up,
// 'created_date' => date('Y-m-d')
);



// json_encode($ocdData);

// $_url = "http://35.154.186.145/okc_ivr_apis/api/outBoundCallData";

// $ch = curl_init();
//         curl_setopt($ch, CURLOPT_URL,$_url);
//         curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//         curl_setopt($ch, CURLOPT_HEADER, false); 
//         curl_setopt($ch, CURLOPT_POST, count($ocdData));
//         curl_setopt($ch, CURLOPT_POSTFIELDS, $ocdData);    

//         $output=curl_exec($ch);

//         curl_close($ch);

//         return $output;



$data = array(
            "mobile_no"=>"9810789821",
	"child_id"=> "46",
	"start_time"=> "2019-10-11 10:22:30",
	"end_time"=> "2019-10-11 10:24:47",
	"message_for"=> "BUCKET114",
    "follow_up"=> "no"
    );

    $data_string = json_encode($data);

    $curl = curl_init('http://35.154.186.145/okc_ivr_apis/api/outBoundCallData');

    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_string))
    );

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  // Make it so the data coming back is put into a string
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);  // Insert the data

    // Send the request
    $result = curl_exec($curl);

    // Free up the resources $curl is using
    curl_close($curl);

    //echo $result;















// $opts = array('http' =>
//     array(
//         'method'  => 'POST',
//         'content' => $ocdData,
//         'header'  => 'User-Agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36\r\n'
//     )   
// );

// $context  = stream_context_create($opts);
// $result = file_get_contents('http://35.154.186.145/okc_ivr_apis/api/outBoundCallData', false, $context);
// echo $result;



// ================================= END OF NEW LOGIC =============================











$response = array(
'status' => 'success'
);
//}

$this->output
->set_status_header(200)
->set_content_type('application/json')
->set_output(json_encode($response)); 




}


	}

}


} // End Of Controller 


// End Of File 
?> 