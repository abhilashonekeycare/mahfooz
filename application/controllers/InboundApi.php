<?php
// error_reporting(0);
// date_default_timezone_set('Asia/Kolkata');

// date_default_timezone_set('Asia/Kolkata');
//$response[""] = "";

//var_dump($_POST);
$_POST = json_decode(file_get_contents('php://input'), true);


// ------------------------------------Log starts here-----------------------------------

//$_POST = json_decode(file_get_contents('php://input'), true);
// $log_time = date('Y-m-d h:i:sa');
// $log_msg = print_r($_POST,1);;

// wh_log("************** Start Log For Day : '" . $log_time . "'**********");
// wh_log($log_msg);
// wh_log("************** END Log For Day : '" . $log_time . "'**********");

// function wh_log($log_msg)
// {
// $log_filename = "log";
// if (!file_exists($log_filename)) 
// {
// // create directory/folder uploads.
// mkdir($log_filename, 0777, true);
// }
// $log_file_data = $log_filename.'/IVR_log_' . date('d-M-Y') . '.log';
// file_put_contents($log_file_data, $log_msg . "\n", FILE_APPEND);
// }





// ---------------------------log end here ----------------------------------------------
defined('BASEPATH') OR exit('No direct script access allowed');

class InboundApi extends CI_Controller {

public function __construct()
{
parent::__construct();
$this->load->model('api_model');
$this->load->model('inboundApi_model');
$this->load->helper('url');
$this->load->helper('text');
}


public function index(){ 

	header("Access-Control-Allow-Origin: *");
header("Access-Control-Request-Headers: GET,POST,OPTIONS,DELETE,PUT");
header('Access-Control-Allow-Headers: Accept,Accept-Language,Content-Language,Content-Type');

$data = json_decode(file_get_contents('php://input'), true);
 json_encode($data);

$mobile = $data['mobile_no'];

if ($data['confirm']) {
	$confirm = $data['confirm'];
}else{
	$confirm = null;
}

if ($data['child_id']) {
	$children_no = $data['child_id'];
}else{
	$children_no = null;
}



if ($data['medical_history']) {
	$medical_history = $data['medical_history'];
}else{
	$medical_history = null;
}


if ($data['vaccination_history']) {
	$vaccination_history = $data['vaccination_history'];
}else{
	$vaccination_history = null;
}

	$categories = $this->inboundApi_model->get_categories();

$mothers = $this->inboundApi_model->get_mothers_mobile($mobile);

$child_dtls = $this->inboundApi_model->get_child_mobile($mobile);

//$children = json_encode($child_dtls);

//$children = json_decode($children);

//echo $children;

$child_id = array();
$childname = array();

$i = '1';
foreach ($child_dtls as $children) {

	//$children->child_id;
	 $child_id[] = $children->child_id;
	 $childname[] = $children->child_name;

	 $i++;
}

// $childname = json_encode($childname,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);


$chldrndtls = array(
	//"slno" => $i;
			"child_id" => $child_id,
			"childname" => $childname);

// for ($i=0; $i < ; $i++) { 
// 	# code...
// }

//print_r($children);
if ($mothers) {
	// print_r($mothers);

	if($mothers && $confirm == '1'){



// GET CHILD DETAILS AS PER THEIR DATA OF BIRTH YOUNGER ONE WILL BE IN PRIORITY LIST









$mother_no = $mothers->mthrs_mbl_no;
$children_dtls = $this->inboundApi_model->get_child__by_dob_mobile($mother_no);

//print_r($children_dtls->child_id);

if ($medical_history == '1') {


$itr = '1';

$add_message_child = array();

$child_dtls_dob = array();

$child_dtls_id = array();

if ($children_dtls) {
	# code...

foreach ($children_dtls as $children_dtls) {

	//echo $children_dtls->child_id.',';

$child_dtls_dob[] = $children_dtls->child_name;

$child_dtls_id[] = $children_dtls->child_id;

	$add_message_child[] = 'Press '.$itr.' For '.$children_dtls->child_name;
	$itr++;
}

}



 //print_r($child_dtls_dob[$children_no-1]);

  $addmessages_tags = implode(', ', $add_message_child);


  //$childidnos = $child_dtls_id[$children_no-1];


if($children_no =='0' || $children_no =='1'|| $children_no =='2'|| $children_no =='3' ){
	echo "simple message";

if ($child_dtls_dob) {
$childnos = $child_dtls_dob[$children_no-1];

$childidnos = $child_dtls_id[$children_no-1];

$msgchild = 'Welcome Mehfooz mei apka swagat h ,'.$mothers->mthrs_name.' Ji,apke bcche '.$childnos.', k baarein mein jankari';
}else{
	$childnos = "no data";
	$childidnos = "no-data";
$msgchild = 'Welcome '.$mothers->mthrs_name.','.'Mehfooz mei apka swagat h , apke bcche ki koi jankari uplabdh nhi hai';

}


	$mother_message = array(
		//"sl_no" => "1",
		"child_nameeeeee" => $childnos,
		//"child_details" => $chldrndtls,
		"mobileno" => $mothers->mthrs_mbl_no,
		"message" => $msgchild,
		"child_ki_id" =>  $childidnos
		//"child_name" => $child_dtls->child_name
	);
}elseif($children_no =='4' || $children_no =='5'|| $children_no =='6'|| $children_no =='7'){


	$mother_message = array(
		//"sl_no" => "1",
		"mother_name" => $mothers->mthrs_name,
		//"child_details" => $chldrndtls,
		"mobileno" => $mothers->mthrs_mbl_no,
		"message" => $mothers->mthrs_name.' ji, '.' apke bcche ki koi jankari uplabdh nhi hai'
		// "message" => 'Welcome '.$mothers->mthrs_name.','.'Mehfooz mei apka swagat h ,'.$addmessages_tags
		//"child_name" => $child_dtls->child_name
	);

	}else{


	$mother_message = array(
		//"sl_no" => "1",
		"mother_name" => $mothers->mthrs_name,
		//"child_details" => $chldrndtls,
		"mobileno" => $mothers->mthrs_mbl_no,
		"message" => 'Welcome '.$mothers->mthrs_name.','.'Mehfooz mei apka swagat h ,'.$addmessages_tags
		//"child_name" => $child_dtls->child_name
	);

	}

}elseif($vaccination_history == '1'){

	$mother_message = array(
		"mother_name" => $mothers->mthrs_name,
		//"child_details" => $chldrndtls,
		"mobileno" => $mothers->mthrs_mbl_no,
		"message" => 'Welcome '.$mothers->mthrs_name.','.'Mehfooz mei apka swagat h , for INFORMATION about vaccination history');



// =============++Start vaccination  info logic++ ====================================================

// 	if ($children_no == null) {
// 		$mother_message = array(
// 		"mother_name" => $mothers->mthrs_name,
// 		//"child_details" => $chldrndtls,
// 		"mobileno" => $mothers->mthrs_mbl_no,
// 		"message" => 'Welcome '.$mothers->mthrs_name.','.'Mehfooz mei apka swagat h , for INFORMATION about vaccination history');
// 	}elseif($children_no =='0' || $children_no =='1'|| $children_no =='2'|| $children_no =='3' ){
// 	echo "simple vaccination_history message";

// $itr = '1';

// $add_message_child = array();

// $child_dtls_dob = array();

// if ($children_dtls) {
// 	# code...

// foreach ($children_dtls as $children_dtls) {

// 	//echo $children_dtls->child_id.',';

// $child_dtls_dob[] = $children_dtls->child_name;

// 	$add_message_child[] = 'Press '.$itr.' For '.$children_dtls->child_name;
// 	$itr++;
// }

// }



//  //print_r($child_dtls_dob[$children_no-1]);

//   $addmessages_tags = implode(', ', $add_message_child);


// if ($child_dtls_dob) {
// $childnos = $child_dtls_dob[$children_no-1];

// $msgchild = 'Welcome Mehfooz mei apka swagat h ,'.$mothers->mthrs_name.' Ji,apke bcche '.$childnos.', k baarein mein jankari';
// }else{
// 	$childnos = "no data";
// $msgchild = 'Welcome '.$mothers->mthrs_name.','.'Mehfooz mei apka swagat h , apke bcche ki koi jankari uplabdh nhi hai';

// }


// 	$mother_message = array(
// 		//"sl_no" => "1",
// 		"child_nameeeeee" => $childnos,
// 		//"child_details" => $chldrndtls,
// 		"mobileno" => $mothers->mthrs_mbl_no,
// 		"message" => $msgchild
// 		//"child_name" => $child_dtls->child_name
// 	);

	

// }

// End Vaccination Info logic







}else{

	$mother_message = array(
		"mother_name" => $mothers->mthrs_name,
		//"child_details" => $chldrndtls,
		"mobileno" => $mothers->mthrs_mbl_no,
		"message" => 'Welcome '.$mothers->mthrs_name.','.'Mehfooz mei apka swagat h ,Press 1 For Medical History OR Press 2 for vaccination history');
}

// END OF CHILD DETAILS AND  AS PER THEIR DATE OF BIRTH 



}elseif($mothers && $confirm == '2' || $mothers && $confirm == '3' || $mothers && $confirm == '4' || $mothers && $confirm == '5'  || $mothers && $confirm == '6'  || $mothers && $confirm == '7'  || $mothers && $confirm == '8'  || $mothers && $confirm == '9'){

	$mother_message = array( "message" => "Seems You are wrong person, Please contact you ASHA OR ANM");
}elseif($mothers){

	$mother_message = array(
		// "sl_no" => "1",
		"mother_id" => $mothers->mthrs_db_id,
		"mother_name" => $mothers->mthrs_name,
		// "child_details" => $chldrndtls,
		"mobileno" => $mothers->mthrs_mbl_no,
		"message" => 'is This '.$mothers->mthrs_name.' Please Confirm ! if Yes Press 1 or Press 2'
		//"child_name" => $child_dtls->child_name
	);
}


	
// end of getting data
}

	//echo "okkkkkkkkz";
// }else{
// 	echo "NOOOOPS";
// }

if (empty($mother_message)) {
	$mother_message = array("message"=> "No Data");
}


$this->output
->set_status_header(200)
->set_content_type('application/json')
->set_output(json_encode($mother_message,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)); 









// End of index function 
 }




// end of 	inboundApi
}
