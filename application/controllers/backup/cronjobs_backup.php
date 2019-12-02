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

	if ($samay== '13') {
		


$followup_data = json_decode(
file_get_contents('http://35.154.186.145/okc_ivr_apis/api/getFollowData/2019-10-01/')
);

//print_r($followup_data);

foreach($followup_data as $followup_data){

 

// $ocdData = http_build_query(
// 	array(
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
// )

// );




	$ocdData = http_build_query(
	array(
'mobile_no' => $followup_data->mobile_no,
'child_id' => $followup_data->child_id,
'start_time' => "2019-10-18 11:50:23",
'end_time' =>"2019-10-18 12:15:23",
'message_for' => $followup_data->message_for,
'follow_up' => "end_time",
)

);
//print_r($ocdData);

//$id = $this->api_model->insertOCDdata($ocdData);



// $postdata = http_build_query(
//     array(
//         'var1' => 'some content',
//         'var2' => 'doh'
//     )
// );

$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-Type: application/x-www-form-urlencoded',
        'content' => $ocdData
    )
);

$context  = stream_context_create($opts);

$result = file_put_contents('http://35.154.186.145/okc_ivr_apis/api/outBoundCallData', $context);


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