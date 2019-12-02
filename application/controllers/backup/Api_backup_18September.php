<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

public function __construct()
{
parent::__construct();
$this->load->model('api_model');
$this->load->helper('url');
$this->load->helper('text');
}

public function blogs()
{
header("Access-Control-Allow-Origin: *");

$blogs = $this->api_model->get_blogs($featured=false, $recentpost=false);

$posts = array();
if(!empty($blogs)){
foreach($blogs as $blog){

$short_desc = strip_tags(character_limiter($blog->description, 70));
$author = $blog->first_name.' '.$blog->last_name;

$posts[] = array(
'id' => $blog->id,
'title' => $blog->title,
'short_desc' => html_entity_decode($short_desc),
'author' => $author,
'image' => base_url('media/images/'.$blog->image),
'created_at' => $blog->created_at
);
}
}

$this->output
->set_content_type('application/json')
->set_output(json_encode($posts));
}

public function featured_blogs()
{
header("Access-Control-Allow-Origin: *");

$blogs = $this->api_model->get_blogs($featured=true, $recentpost=false);

$posts = array();
if(!empty($blogs)){
foreach($blogs as $blog){

$short_desc = strip_tags(character_limiter($blog->description, 70));
$author = $blog->first_name.' '.$blog->last_name;

$posts[] = array(
'id' => $blog->id,
'title' => $blog->title,
'short_desc' => html_entity_decode($short_desc),
'author' => $author,
'image' => base_url('media/images/'.$blog->image),
'created_at' => $blog->created_at
);
}
}

$this->output
->set_content_type('application/json')
->set_output(json_encode($posts));
}

public function blog($id)
{
header("Access-Control-Allow-Origin: *");

$blog = $this->api_model->get_blog($id);

$author = $blog->first_name.' '.$blog->last_name;

$post = array(
'id' => $blog->id,
'title' => $blog->title,
'description' => $blog->description,
'author' => $author,
'image' => base_url('media/images/'.$blog->image),
'created_at' => $blog->created_at
);

$this->output
->set_content_type('application/json')
->set_output(json_encode($post));
}

public function recent_blogs()
{
header("Access-Control-Allow-Origin: *");

$blogs = $this->api_model->get_blogs($featured=false, $recentpost=5);

$posts = array();
if(!empty($blogs)){
foreach($blogs as $blog){

$short_desc = strip_tags(character_limiter($blog->description, 70));
$author = $blog->first_name.' '.$blog->last_name;

$posts[] = array(
'id' => $blog->id,
'title' => $blog->title,
'short_desc' => html_entity_decode($short_desc),
'author' => $author,
'image' => base_url('media/images/'.$blog->image),
'created_at' => $blog->created_at
);
}
}

$this->output
->set_content_type('application/json')
->set_output(json_encode($posts));
}

public function categories()
{
header("Access-Control-Allow-Origin: *");

$categories = $this->api_model->get_categories();

$category = array();
if(!empty($categories)){
foreach($categories as $cate){
$category[] = array(
'id' => $cate->id,
'name' => $cate->category_name
);
}
}

$this->output
->set_content_type('application/json')
->set_output(json_encode($category));
}

public function page($slug)
{
header("Access-Control-Allow-Origin: *");

$page = $this->api_model->get_page($slug);

$pagedata = array(
'id' => $page->id,
'title' => $page->title,
'description' => $page->description
);

$this->output
->set_content_type('application/json')
->set_output(json_encode($pagedata));
}

public function contact()
{
header("Access-Control-Allow-Origin: *");
header("Access-Control-Request-Headers: GET,POST,OPTIONS,DELETE,PUT");
header('Access-Control-Allow-Headers: Accept,Accept-Language,Content-Language,Content-Type');

$formdata = json_decode(file_get_contents('php://input'), true);

if( ! empty($formdata)) {

$name = $formdata['name'];
$email = $formdata['email'];
$phone = $formdata['phone'];
$message = $formdata['message'];

$contactData = array(
'name' => $name,
'email' => $email,
'phone' => $phone,
'message' => $message,
'created_at' => date('Y-m-d H:i:s', time())
);

$id = $this->api_model->insert_contact($contactData);

$this->sendemail($contactData);

$response = array('id' => $id);
}
else {
$response = array('id' => '');
}

$this->output
->set_content_type('application/json')
->set_output(json_encode($response));
}

public function sendemail($contactData)
{
$message = '<p>Hi, <br />Some one has submitted contact form.</p>';
$message .= '<p><strong>Name: </strong>'.$contactData['name'].'</p>';
$message .= '<p><strong>Email: </strong>'.$contactData['email'].'</p>';
$message .= '<p><strong>Phone: </strong>'.$contactData['phone'].'</p>';
$message .= '<p><strong>Name: </strong>'.$contactData['message'].'</p>';
$message .= '<br />Thanks';

$this->load->library('email');

$config['protocol'] = 'sendmail';
$config['mailpath'] = '/usr/sbin/sendmail';
$config['charset'] = 'iso-8859-1';
$config['wordwrap'] = TRUE;
$config['mailtype'] = 'html';

$this->email->initialize($config);

$this->email->from('demo@rsgitech.com', 'RSGiTECH');
$this->email->to('demo2@rsgitech.com');
$this->email->cc('another@rsgitech.com');
$this->email->bcc('them@rsgitech.com');

$this->email->subject('Contact Form');
$this->email->message($message);

$this->email->send();
}

public function login() 
{
header("Access-Control-Allow-Origin: *");
header("Access-Control-Request-Headers: GET,POST,OPTIONS,DELETE,PUT");
header('Access-Control-Allow-Headers: Accept,Accept-Language,Content-Language,Content-Type');

$formdata = json_decode(file_get_contents('php://input'), true);

$username = $formdata['username'];
$password = $formdata['password'];

$user = $this->api_model->login($username, $password);

if($user) {
$response = array(
'user_id' => $user->id,
'first_name' => $user->first_name,
'last_name' => $user->last_name,
'token' => $user->token
);
}
else {
$response = array();
}

$this->output
->set_content_type('application/json')
->set_output(json_encode($response));
}

public function adminBlogs()
{
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: authorization, Content-Type");

$token = $this->input->get_request_header('Authorization');

$isValidToken = $this->api_model->checkToken($token);

$posts = array();
if($isValidToken) {
$blogs = $this->api_model->get_admin_blogs();
foreach($blogs as $blog) {
$posts[] = array(
'id' => $blog->id,
'title' => $blog->title,
'image' => base_url('media/images/'.$blog->image),
'created_at' => $blog->created_at
);
}

$this->output
->set_status_header(200)
->set_content_type('application/json')
->set_output(json_encode($posts)); 
}
}

public function adminBlog($id)
{
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: authorization, Content-Type");

$token = $this->input->get_request_header('Authorization');

$isValidToken = $this->api_model->checkToken($token);

if($isValidToken) {

$blog = $this->api_model->get_admin_blog($id);

$post = array(
'id' => $blog->id,
'title' => $blog->title,
'description' => $blog->description,
'image' => base_url('media/images/'.$blog->image),
'is_featured' => $blog->is_featured,
'is_active' => $blog->is_active
);


$this->output
->set_status_header(200)
->set_content_type('application/json')
->set_output(json_encode($post)); 
}
}

public function createBlog()
{
header("Access-Control-Allow-Origin: *");
header("Access-Control-Request-Headers: GET,POST,OPTIONS,DELETE,PUT");
header("Access-Control-Allow-Headers: authorization, Content-Type");

$token = $this->input->get_request_header('Authorization');

$isValidToken = $this->api_model->checkToken($token);

if($isValidToken) {

$title = $this->input->post('title');
$description = $this->input->post('description');
$is_featured = $this->input->post('is_featured');
$is_active = $this->input->post('is_active');

$filename = NULL;

$isUploadError = FALSE;

if ($_FILES && $_FILES['image']['name']) {

$config['upload_path']          = './media/images/';
$config['allowed_types']        = 'gif|jpg|png|jpeg';
$config['max_size']             = 500;

$this->load->library('upload', $config);
if ( ! $this->upload->do_upload('image')) {

$isUploadError = TRUE;

$response = array(
'status' => 'error',
'message' => $this->upload->display_errors()
);
}
else {
$uploadData = $this->upload->data();
$filename = $uploadData['file_name'];
}
}

if( ! $isUploadError) {
$blogData = array(
'title' => $title,
'user_id' => 1,
'description' => $description,
'image' => $filename,
'is_featured' => $is_featured,
'is_active' => $is_active,
'created_at' => date('Y-m-d H:i:s', time())
);

$id = $this->api_model->insertBlog($blogData);

$response = array(
'status' => 'success'
);
}

$this->output
->set_status_header(200)
->set_content_type('application/json')
->set_output(json_encode($response)); 
}
}

public function updateBlog($id)
{
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: authorization, Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

$token = $this->input->get_request_header('Authorization');

$isValidToken = $this->api_model->checkToken($token);

if($isValidToken) {

$blog = $this->api_model->get_admin_blog($id);
$filename = $blog->image;

$title = $this->input->post('title');
$description = $this->input->post('description');
$is_featured = $this->input->post('is_featured');
$is_active = $this->input->post('is_active');

$isUploadError = FALSE;

if ($_FILES && $_FILES['image']['name']) {

$config['upload_path']          = './media/images/';
$config['allowed_types']        = 'gif|jpg|png|jpeg';
$config['max_size']             = 500;

$this->load->library('upload', $config);
if ( ! $this->upload->do_upload('image')) {

$isUploadError = TRUE;

$response = array(
'status' => 'error',
'message' => $this->upload->display_errors()
);
}
else {

if($blog->image && file_exists(FCPATH.'media/images/'.$blog->image))
{
unlink(FCPATH.'media/images/'.$blog->image);
}

$uploadData = $this->upload->data();
$filename = $uploadData['file_name'];
}
}

if( ! $isUploadError) {
$blogData = array(
'title' => $title,
'user_id' => 1,
'description' => $description,
'image' => $filename,
'is_featured' => $is_featured,
'is_active' => $is_active
);

$this->api_model->updateBlog($id, $blogData);

$response = array(
'status' => 'success'
);
}

$this->output
->set_status_header(200)
->set_content_type('application/json')
->set_output(json_encode($response)); 
}
}

public function deleteBlog($id)
{
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: authorization, Content-Type");

$token = $this->input->get_request_header('Authorization');

$isValidToken = $this->api_model->checkToken($token);

if($isValidToken) {

$blog = $this->api_model->get_admin_blog($id);

if($blog->image && file_exists(FCPATH.'media/images/'.$blog->image))
{
unlink(FCPATH.'media/images/'.$blog->image);
}

$this->api_model->deleteBlog($id);

$response = array(
'status' => 'success'
);

$this->output
->set_status_header(200)
->set_content_type('application/json')
->set_output(json_encode($response)); 
}
}



// Start OutBound Call Data Details

// Mothers listing Codes Starts from here 



public function outBoundCallData()
{
header("Access-Control-Allow-Origin: *");
header("Access-Control-Request-Headers: GET,POST,OPTIONS,DELETE,PUT");
header("Access-Control-Allow-Headers: authorization, Content-Type");

$token = $this->input->get_request_header('Authorization');

$isValidToken = $this->api_model->checkToken($token);

$data = json_decode(file_get_contents('php://input'), true);
 json_encode($data);


// $randm_number = mt_rand(10000, 99999);

// $rnd_no = "BMOKC-".$randm_number;

//if($isValidToken) {

//$child_id = $this->input->post('child_id');
$contact_no = $data['mobile_no'];
$child_id = $data['child_id'];
$start_time = $data['start_time'];
$end_time = $data['end_time'];
$call_duration = $data['call_duration'];
$call_for = $data['message_for'];
$followup = $data['follow_up'];

if($call_duration < '30'){
$call_status = "incomplete";

}elseif($call_duration >= '30'){

$call_status = "complete";

}


$ocdData = array(
'contact_no' => $contact_no,
'ocd_child_id' => $child_id,
'call_start_time' => $start_time,
'call_duration' => $call_duration,
'call_status' => $call_status,
'call_for' => $call_for,
'call_end_time' => $end_time,
'created_at' => date('Y-m-d H:i:s'),
'follow_up_data' => $followup
);
//print_r($ocdData);

$id = $this->api_model->insertOCDdata($ocdData);

$response = array(
'status' => 'success'
);
//}

$this->output
->set_status_header(200)
->set_content_type('application/json')
->set_output(json_encode($response)); 
//} Valid Token Ends Here 
}
// End Of OutBound Call Details 




































// Mothers listing Codes Starts from here 



public function createMother()
{
header("Access-Control-Allow-Origin: *");
header("Access-Control-Request-Headers: GET,POST,OPTIONS,DELETE,PUT");
header("Access-Control-Allow-Headers: authorization, Content-Type");

$token = $this->input->get_request_header('Authorization');

$isValidToken = $this->api_model->checkToken($token);
$randm_number = mt_rand(10000, 99999);

$rnd_no = "BMOKC-".$randm_number;

if($isValidToken) {

$mothername = $this->input->post('mothername');
// $motheruqno = $this->input->post('motheruqno');
$motheruqno = $rnd_no;


$mothermblno = $this->input->post('mothermblno');
$mthrpassword = $this->input->post('mthrpassword');
$motherlastname = $this->input->post('motherlastname');
$motheroptmblno = $this->input->post('motheroptmblno');
$motherage = $this->input->post('motherage');
$mother_area = $this->input->post('mother_area');
$mother_areacode = $this->input->post('mother_areacode');
$anm_name = $this->input->post('anm_name');
$anm_no = $this->input->post('anm_no');
$asha_name = $this->input->post('asha_name');
$asha_no = $this->input->post('asha_no');

// $filename = NULL;

// $isUploadError = FALSE;

// if ($_FILES && $_FILES['image']['name']) {

// 	$config['upload_path']          = './media/images/';
//           $config['allowed_types']        = 'gif|jpg|png|jpeg';
//           $config['max_size']             = 500;

//           $this->load->library('upload', $config);
//           if ( ! $this->upload->do_upload('image')) {

//           	$isUploadError = TRUE;

// 		$response = array(
// 			'status' => 'error',
// 			'message' => $this->upload->display_errors()
// 		);
//           }
//           else {
//           	$uploadData = $this->upload->data();
//          		$filename = $uploadData['file_name'];
//           }
// }

// if( ! $isUploadError) {
$motherData = array(
'mthrs_name' => $mothername,
'mthrs_unq_no' => $motheruqno,
'mthrs_mbl_no' => $mothermblno,
'mthrs_passwrd' => $mthrpassword,
'mthrs_last_name' => $motherlastname,
'mthrs_optn_mbl_no' => $motheroptmblno,
'age' => $motherage,
'area' => $mother_area,
'area_code' => $mother_areacode,
'anm_name' => $anm_name,
'anm_contact' => $anm_no,
'asha_name' => $asha_name,
'asha_contact' => $asha_no,
'mthr_status' => 1
// 'description' => $description,
// 'image' => $filename,
// 'is_featured' => $is_featured,
// 'is_active' => $is_active,
// 'created_at' => date('Y-m-d H:i:s', time())
);

$id = $this->api_model->insertMother($motherData);

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



// get Mothers Details

public function getMother($id)
{
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: authorization, Content-Type");

//echo $id;

$token = $this->input->get_request_header('Authorization');

//echo $token;

$isValidToken = $this->api_model->checkToken($token);

// echo $isValidToken;

// if($isValidToken) {

$mother = $this->api_model->get_mothers_id($id);

//print_r($mother);

$post = array(
'mthrs_db_id' => $mother->mthrs_db_id,
'mthrs_name' => $mother->mthrs_name,
'mthrs_unq_no' => $mother->mthrs_unq_no,
'mthrs_mbl_no' => $mother->mthrs_mbl_no,
'mthrs_passwrd'=> $mother->mthrs_passwrd,
'mthrs_last_name' => $mother->mthrs_last_name,
'mthrs_optn_mbl_no' => $mother->mthrs_optn_mbl_no,
'age' => $mother->age,
'area' => $mother->area,
'area_code' => $mother->area_code,
'anm_name' => $mother->anm_name,
'anm_contact' => $mother->anm_contact,
'asha_name' => $mother->asha_name,
'asha_contact' => $mother->asha_contact,
'mthr_status' => $mother->mthr_status
);


$this->output
->set_status_header(200)
->set_content_type('application/json')
->set_output(json_encode($post)); 
//}
}




public function getMotherByMobile($mbl)
{
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: authorization, Content-Type");

//echo $id;

$token = $this->input->get_request_header('Authorization');

//echo $token;

$isValidToken = $this->api_model->checkToken($token);

// echo $isValidToken;

// if($isValidToken) {

$mother = $this->api_model->get_mothers_mobile($mbl);
$mother_by_unq = $this->api_model->get_mothers_unq_no($mbl);

$message = "koi data nhi h";

//print_r($mother);
if($mother){
$post = array(
'mthrs_db_id' => $mother->mthrs_db_id,
'mthrs_name' => $mother->mthrs_name,
'message' => $mother->mthrs_name,
'success'=>'yes',
'mthrs_unq_no' => $mother->mthrs_unq_no,
'mthrs_mbl_no' => $mother->mthrs_mbl_no
// 'mthrs_passwrd'=> $mother->mthrs_passwrd,
// 'mthrs_last_name' => $mother->mthrs_last_name,
// 'mthrs_optn_mbl_no' => $mother->mthrs_optn_mbl_no,
// 'age' => $mother->age,
// 'area' => $mother->area,
// 'area_code' => $mother->area_code,
// 'anm_name' => $mother->anm_name,
// 'anm_contact' => $mother->anm_contact,
// 'asha_name' => $mother->asha_name,
// 'asha_contact' => $mother->asha_contact,
// 'mthr_status' => $mother->mthr_status
);
}else if($mother_by_unq){
$post = array(
'mthrs_db_id' => $mother_by_unq->mthrs_db_id,
'mthrs_name' => $mother_by_unq->mthrs_name,
'message' => $mother_by_unq->mthrs_name,
'success'=>'yes',
'mthrs_unq_no' => $mother_by_unq->mthrs_unq_no,
'mthrs_mbl_no' => $mother_by_unq->mthrs_mbl_no

);
}else{

$post = array(
'message' => $message,
'success'=>'no'

);

}	

$this->output
->set_status_header(200)
->set_content_type('application/json')
->set_output(json_encode($post)); 
//}
}




public function motherByMobile($mbl)
{
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: authorization, Content-Type");
header('content-type:application/json;charset=utf-8');

//echo $id;

$token = $this->input->get_request_header('Authorization');

//echo $token;

$isValidToken = $this->api_model->checkToken($token);

// echo $isValidToken;

// if($isValidToken) {

$mother = $this->api_model->get_mothers_mobile($mbl);
// if($mother){
$mthr_id = $mother->mthrs_db_id; 
// }


$children = $this->api_model->get_child_id($mthr_id);

//print_r($children);

$message = "koi data nhi h";
//$child1 = array();

// foreach ($children as $children) {
// 						$child1['1']= $children['child_name'];
// 					}

$result1 = json_encode($children);
$result2 = json_decode($result1);

//print_r($mother);
if($mother){
$post = array(
'mthrs_db_id' => $mother->mthrs_db_id,
'mthrs_name' => $mother->mthrs_name,
'message' => $mother->mthrs_name,
'success'=>'yes',
'mthrs_unq_no' => $mother->mthrs_unq_no,
'mthrs_mbl_no' => $mother->mthrs_mbl_no,
'name_child' => $result2['0']->child_name

// 'mthrs_passwrd'=> $mother->mthrs_passwrd,
// 'mthrs_last_name' => $mother->mthrs_last_name,
// 'mthrs_optn_mbl_no' => $mother->mthrs_optn_mbl_no,
// 'age' => $mother->age,
// 'area' => $mother->area,
// 'area_code' => $mother->area_code,
// 'anm_name' => $mother->anm_name,
// 'anm_contact' => $mother->anm_contact,
// 'asha_name' => $mother->asha_name,
// 'asha_contact' => $mother->asha_contact,
// 'mthr_status' => $mother->mthr_status
);
}else{

$post = array(
'message' => $message,
'success'=>'no'

);

}	

$this->output
->set_status_header(200)
->set_content_type('application/json')
->set_output(json_encode($post)); 
//}
}














// Update Child IVR------------Starts from Here 


public function updateChildOne($id)
{
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: authorization, Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

$token = $this->input->get_request_header('Authorization');

$isValidToken = $this->api_model->checkToken($token);

if($isValidToken) {

$blog = $this->api_model->get_mothers_id($id);
// $filename = $blog->image;

$mothername = $this->input->post('mothername');
$motheruqno = $this->input->post('motheruqno');
$mothermblno = $this->input->post('mothermblno');
$mthrpassword = $this->input->post('mthrpassword');
$motherlastname = $this->input->post('motherlastname');
$motheroptmblno = $this->input->post('motheroptmblno');
$motherage = $this->input->post('motherage');
$mother_area = $this->input->post('mother_area');
$mother_areacode = $this->input->post('mother_areacode');
$anm_name = $this->input->post('anm_name');
$anm_no = $this->input->post('anm_no');
$asha_name = $this->input->post('asha_name');
$asha_no = $this->input->post('asha_no');
$mthr_status = '1';



$isUploadError = FALSE;

$motherData = array(
//'mthrs_db_id' => $mother->mthrs_db_id,
'mthrs_name' => $mothername,
'mthrs_unq_no' => $motheruqno,
'mthrs_mbl_no' => $mothermblno,
'mthrs_passwrd'=> $mthrpassword,
'mthrs_last_name' => $motherlastname,
'mthrs_optn_mbl_no' => $motheroptmblno,
'age' => $motherage,
'area' => $mother_area,
'area_code' => $mother_areacode,
'anm_name' => $anm_name,
'anm_contact' => $anm_no,
'asha_name' => $asha_name,
'asha_contact' => $asha_no,
'mthr_status' => $mthr_status
);

$this->api_model->updateMother_s($id, $motherData);

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





// End of update Child IVR ---------------------


















public function deleteMother($id)
{
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: authorization, Content-Type");

$token = $this->input->get_request_header('Authorization');

// $isValidToken = $this->api_model->checkToken($token);

// if($isValidToken) {

$blog = $this->api_model->get_mothers_id($id);

// if($blog->image && file_exists(FCPATH.'media/images/'.$blog->image))
// {
// 	unlink(FCPATH.'media/images/'.$blog->image);
// }

$this->api_model->deleteMother($id);

$response = array(
'status' => 'success'
);

$this->output
->set_status_header(200)
->set_content_type('application/json')
->set_output(json_encode($response)); 
//}
}




// get child for ivr API


public function getChildLists()
{

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: authorization, Content-Type");

$token = $this->input->get_request_header('Authorization');

$isValidToken = $this->api_model->checkToken($token);

$posts = array();
if($isValidToken) {
$children = $this->api_model->get_childs();
foreach($children as $child) {
$posts[] = array(
'child_id' => $child->child_id,
'mthr_id' => $child->mthr_id,
'child_unq_id' => $child->child_unq_id,
'child_name' => $child->child_name,
'child_dob' => $child->child_dob,
'child_status'=>$child->child_status,
'is_vacinated_before' => $child->is_vacinated_before,
'bcg' => $child->bcg,
'bcg_done_date' => $child->bcg_done_date,
'hepb_birth' => $child->hepb_birth,
'hepb_birth_done_date' => $child->hepb_birth_done_date,
'vaccine_step1_name' => $child->vaccine_step1_name,
'vaccine_step1_start' => $child->vaccine_step1_start,
'vaccine_step1_end' => $child->vaccine_step1_end,
'vaccine_step1_status' => $child->vaccine_step1_status,
'vaccine_step1_done_date' => $child->vaccine_step1_done_date,

'vaccine_step2_name' => $child->vaccine_step2_name,
'vaccine_step2_start' => $child->vaccine_step2_start,
'vaccine_step2_end' => $child->vaccine_step1_end,
'vaccine_step2_status' => $child->vaccine_step2_status,
'vaccine_step2_done_date' => $child->vaccine_step2_done_date,


'vaccine_step3_name' => $child->vaccine_step3_name,
'vaccine_step3_start' => $child->vaccine_step3_start,
'vaccine_step3_end' => $child->vaccine_step3_end,
'vaccine_step3_status' => $child->vaccine_step3_status,
'vaccine_step3_done_date' => $child->vaccine_step3_done_date,


'vaccine_step4_name' => $child->vaccine_step4_name,
'vaccine_step4_start' => $child->vaccine_step4_start,
'vaccine_step4_end' => $child->vaccine_step4_end,
'vaccine_step4_status' => $child->vaccine_step4_status,
'vaccine_step4_done_date' => $child->vaccine_step4_done_date,


'vaccine_step5_name' => $child->vaccine_step5_name,
'vaccine_step5_start' => $child->vaccine_step5_start,
'vaccine_step5_end' => $child->vaccine_step5_end,
'vaccine_step5_status' => $child->vaccine_step5_status,
'vaccine_step5_done_date' => $child->vaccine_step5_done_date,


'vaccine_step6_name' => $child->vaccine_step6_name,
'vaccine_step6_start' => $child->vaccine_step6_start,
'vaccine_step6_end' => $child->vaccine_step6_end,
'vaccine_step6_status' => $child->vaccine_step6_status,
'vaccine_step6_done_date' => $child->vaccine_step6_done_date,


'vaccine_step7_name' => $child->vaccine_step7_name,
'vaccine_step7_start' => $child->vaccine_step7_start,
'vaccine_step7_end' => $child->vaccine_step7_end,
'vaccine_step7_status' => $child->vaccine_step7_status,
'vaccine_step7_done_date' => $child->vaccine_step7_done_date,


'vaccine_step8_name' => $child->vaccine_step8_name,
'vaccine_step8_start' => $child->vaccine_step8_start,
'vaccine_step8_end' => $child->vaccine_step8_end,
'vaccine_step8_status' => $child->vaccine_step8_status,
'vaccine_step8_done_date' => $child->vaccine_step8_done_date,
// 'BCG'=> $child->BCG,
// 'BCG_date'=> $child->BCG_date,
// 'BCG_done_date' => $child->BCG_done_date,

// 'OPV_O'=> $child->OPV_O,
// 'OPV_O_date'=> $child->OPV_O_date,
// 'OPV_O_done_date' => $child->OPV_O_done_date,
// 'Hep_B'=> $child->Hep_B,
// 'Hep_B_date'=> $child->Hep_B_date,
// 'Hep_B_done_date' => $child->Hep_B_done_date,
// 'OPV1'=> $child->OPV1,
// 'OPV1_date'=> $child->OPV1_date,
// 'OPV1_done_date' => $child->OPV1_done_date,

// 'OPV2'=> $child->OPV2,
// 'OPV2_date'=> $child->OPV2_date,
// 'OPV2_done_date' => $child->OPV2_done_date,


// 'PENTA1'=> $child->PENTA1,
// 'PENTA1_date'=> $child->PENTA1_date,
// 'PENTA1_done_date' => $child->PENTA1_done_date,


// 'PENTA2'=> $child->PENTA2,
// 'PENTA2_date'=> $child->PENTA2_date,
// 'PENTA2_done_date' => $child->PENTA2_done_date,


// 'PENTA3'=> $child->PENTA3,
// 'PENTA3_date'=> $child->PENTA3_date,
// 'PENTA3_done_date' => $child->PENTA3_done_date,


// 'OPV3'=> $child->OPV3,
// 'OPV3_date'=> $child->OPV3_date,
// 'OPV3_done_date' => $child->OPV3_done_date,


// 'IPV'=> $child->IPV,
// 'IPV_date'=> $child->IPV_date,
// 'IPV_done_date' => $child->IPV_done_date,


// 'MMR'=> $child->MMR,
// 'MMR_date'=> $child->MMR_date,
// 'MMR_done_date' => $child->MMR_done_date,


// 'JE1'=> $child->JE1,
// 'JE1_date'=> $child->JE1_date,
// 'JE1_done_date' => $child->JE1_done_date,

// 'VIT_A_1'=> $child->VIT_A_1,
// 'VIT_A_1_date'=> $child->VIT_A_1_date,
// 'VIT_A_1_done_date' => $child->VIT_A_1_done_date,

// 'OPV_BOOSTER'=> $child->OPV_BOOSTER,
// 'OPV_BOOSTER_date'=> $child->OPV_BOOSTER_date,
// 'OPV_BOOSTER_done_date' => $child->OPV_BOOSTER_done_date,



// 'JE2'=> $child->JE2,
// 'JE2_date'=> $child->JE2_date,
// 'JE2_done_date' => $child->JE2_done_date,

// 'VIT_A_2'=> $child->VIT_A_2,
// 'VIT_A_2_date'=> $child->VIT_A_2_date,
// 'VIT_A_2_done_date' => $child->VIT_A_2_done_date,

// 'OPV_BOOSTER'=> $child->OPV_BOOSTER,
// 'OPV_BOOSTER_date'=> $child->OPV_BOOSTER_date,
// 'OPV_BOOSTER_done_date' => $child->OPV_BOOSTER_done_date,

// 'DPT_1_BOOSTER'=> $child->DPT_1_BOOSTER,
// 'DPT_1_BOOSTER_date'=> $child->DPT_1_BOOSTER_date,
// 'DPT_1_BOOSTER_done_date' => $child->DPT_1_BOOSTER_done_date,

// 'DPT_2_BOOSTER_BOOSTER'=> $child->DPT_2_BOOSTER,
// 'DPT_2_BOOSTER_date'=> $child->DPT_2_BOOSTER_date,
// 'DPT_2_BOOSTER_done_date' => $child->DPT_2_BOOSTER_done_date











// 'image' => base_url('media/images/'.$blog->image),
// 'created_at' => $blog->created_at
);
}

$this->output
->set_status_header(200)
->set_content_type('application/json')
->set_output(json_encode($posts,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)); 
}
}
























// list of Mothers

public function motherLists()
{
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: authorization, Content-Type");

$token = $this->input->get_request_header('Authorization');

$isValidToken = $this->api_model->checkToken($token);

$posts = array();
if($isValidToken) {
$mothers = $this->api_model->get_mothers();
foreach($mothers as $mother) {
$posts[] = array(
'mthrs_db_id' => $mother->mthrs_db_id,
'mthrs_name' => $mother->mthrs_name,
'mthrs_unq_no' => $mother->mthrs_unq_no,
'mthrs_mbl_no' => $mother->mthrs_mbl_no
// 'image' => base_url('media/images/'.$blog->image),
// 'created_at' => $blog->created_at
);
}

$this->output
->set_status_header(200)
->set_content_type('application/json')
->set_output(json_encode($posts)); 
}
}






public function updateMother($id)
{
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: authorization, Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

$token = $this->input->get_request_header('Authorization');

$isValidToken = $this->api_model->checkToken($token);

if($isValidToken) {

$blog = $this->api_model->get_mothers_id($id);
// $filename = $blog->image;

$mothername = $this->input->post('mothername');
$motheruqno = $this->input->post('motheruqno');
$mothermblno = $this->input->post('mothermblno');
$mthrpassword = $this->input->post('mthrpassword');
$motherlastname = $this->input->post('motherlastname');
$motheroptmblno = $this->input->post('motheroptmblno');
$motherage = $this->input->post('motherage');
$mother_area = $this->input->post('mother_area');
$mother_areacode = $this->input->post('mother_areacode');
$anm_name = $this->input->post('anm_name');
$anm_no = $this->input->post('anm_no');
$asha_name = $this->input->post('asha_name');
$asha_no = $this->input->post('asha_no');
$mthr_status = '1';

//$is_active = $this->input->post('is_active');

// 'mthrs_db_id' => $mother->mthrs_db_id,
// 		'mthrs_name' => $mother->mthrs_name,
// 		'mthrs_unq_no' => $mother->mthrs_unq_no,
// 		'mthrs_mbl_no' => $mother->mthrs_mbl_no

$isUploadError = FALSE;

// if ($_FILES && $_FILES['image']['name']) {

// 	$config['upload_path']          = './media/images/';
//           $config['allowed_types']        = 'gif|jpg|png|jpeg';
//           $config['max_size']             = 500;

//           $this->load->library('upload', $config);
//           if ( ! $this->upload->do_upload('image')) {

//           	$isUploadError = TRUE;

// 		$response = array(
// 			'status' => 'error',
// 			'message' => $this->upload->display_errors()
// 		);
//           }
//           else {

// 		if($blog->image && file_exists(FCPATH.'media/images/'.$blog->image))
// 		{
// 			unlink(FCPATH.'media/images/'.$blog->image);
// 		}

//           	$uploadData = $this->upload->data();
//          		$filename = $uploadData['file_name'];
//           }
// }

// if( ! $isUploadError) {

// 	$mothername = $this->input->post('mothername');
// $motheruqno = $this->input->post('motheruqno');
// $mothermblno = $this->input->post('mothermblno');
$motherData = array(
//'mthrs_db_id' => $mother->mthrs_db_id,
'mthrs_name' => $mothername,
'mthrs_unq_no' => $motheruqno,
'mthrs_mbl_no' => $mothermblno,
'mthrs_passwrd'=> $mthrpassword,
'mthrs_last_name' => $motherlastname,
'mthrs_optn_mbl_no' => $motheroptmblno,
'age' => $motherage,
'area' => $mother_area,
'area_code' => $mother_areacode,
'anm_name' => $anm_name,
'anm_contact' => $anm_no,
'asha_name' => $asha_name,
'asha_contact' => $asha_no,
'mthr_status' => $mthr_status
);

$this->api_model->updateMother_s($id, $motherData);

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




// Add Child Data==============================================

public function addChild()
{
header("Access-Control-Allow-Origin: *");
header("Access-Control-Request-Headers: GET,POST,OPTIONS,DELETE,PUT");
header("Access-Control-Allow-Headers: authorization, Content-Type");

$token = $this->input->get_request_header('Authorization');

$isValidToken = $this->api_model->checkToken($token);

if($isValidToken) {

if ($this->input->post('vacinated_ok')== 'true') {
$str = "yes";
}else{
$str = "no";
}


$childData['mthr_id'] = $this->input->post('mthrs_db_id');
$childData['child_unq_id'] = $this->input->post('childunqno');
$childData['mthr_name'] = $this->input->post('mothername');
$childData['child_contact'] = $this->input->post('mothermblno');
$childData['child_name'] = $this->input->post('childname');
$childData['child_dob'] = $this->input->post('childdob');
$childData['child_status'] = '1';
$childData['add_time'] = date('Y-m-d');
$childData['is_vacinated_before'] = $str;
$childData['BCG'] =  $this->input->post('bcg');
$childData['BCG_done_date'] =  $this->input->post('bcg_done_date');
$childData['hepb_birth'] =  $this->input->post('bcg');
$childData['hepb_birth_done_date'] =  $this->input->post('bcg_done_date');

$dob_date = $this->input->post('childdob');

// $childData['BCG_date'] =  date('Y-m-d', strtotime($dob_date. ' + 1 year'));
// $childData['OPV_O_date'] = date('Y-m-d', strtotime($dob_date. ' + 15 days'));
// $childData['Hep_B_date'] = date('Y-m-d', strtotime($dob_date. ' + 1 day'));
// $childData['OPV1_date'] = date('Y-m-d', strtotime($dob_date. ' + 6 weeks'));//
// $childData['OPV2_date'] = date('Y-m-d', strtotime($dob_date. ' + 10 weeks'));

// if(empty($this->input->post('opv_o'))){
// 	"false";
// }else{
// 	$this->input->post('opv_o');
// }





$childData['vaccine_step1_name'] = "OPV-0";
$childData['vaccine_step1_start'] = date('Y-m-d', strtotime($dob_date));
$childData['vaccine_step1_end'] = date('Y-m-d', strtotime($dob_date. ' + 15 days'));
$childData['vaccine_step1_status'] = $this->input->post('opv_o');
$childData['vaccine_step1_done_date'] = $this->input->post('opv_o_done_date');

$childData['vaccine_step2_name'] = "OPV 1+ Penta1 OR (DPT1 + HepatitisB-1)+ RV1 + IPV1";
$childData['vaccine_step2_start'] = date('Y-m-d', strtotime($dob_date. ' + 6 weeks'));
$childData['vaccine_step2_end'] = date('Y-m-d', strtotime($dob_date. ' + 1 year'));
$childData['vaccine_step2_status'] = $this->input->post('hep_b');
$childData['vaccine_step2_done_date'] = $this->input->post('hep_b_done_date');


$childData['vaccine_step3_name'] = "OPV 2+ Penta2 OR(DPT2 + HepatitisB-2)+ RV2";
$childData['vaccine_step3_start'] = date('Y-m-d', strtotime($dob_date. ' + 10 weeks'));
$childData['vaccine_step3_end'] = date('Y-m-d', strtotime($dob_date. ' + 1 year'));
$childData['vaccine_step3_status'] = $this->input->post('opv1');
$childData['vaccine_step3_done_date'] = $this->input->post('opv1_done_date');

$childData['vaccine_step4_name'] = "OPV 3+ Penta3 OR (DPT3 + HepatitisB-3)+ RV3 + IPV2";
$childData['vaccine_step4_start'] = date('Y-m-d', strtotime($dob_date. ' + 10 weeks'));
$childData['vaccine_step4_end'] = date('Y-m-d', strtotime($dob_date. ' + 1 year'));
$childData['vaccine_step4_status'] = $this->input->post('penta1');
$childData['vaccine_step4_done_date'] = $this->input->post('penta1_done_date');


$childData['vaccine_step5_name'] = "MR 1+ Vitamin A+ JE1";
$childData['vaccine_step5_start'] = date('Y-m-d', strtotime($dob_date. ' + 9 month'));
$childData['vaccine_step5_end'] = date('Y-m-d', strtotime($dob_date. ' + 5 year'));
$childData['vaccine_step5_status'] =  $this->input->post('opv2');
$childData['vaccine_step5_done_date'] =  $this->input->post('opv2_done_date');


$childData['vaccine_step6_name'] = "DPT Booster1 + MR2 + OPV Booster + JE2";
$childData['vaccine_step6_start'] = date('Y-m-d', strtotime($dob_date. ' + 16 month'));
$childData['vaccine_step6_end'] = date('Y-m-d', strtotime($dob_date. ' + 5 year'));
$childData['vaccine_step6_status'] = $this->input->post('penta2');
$childData['vaccine_step6_done_date'] = $this->input->post('penta2_done_date');



$childData['vaccine_step7_name'] = "Vitamin A 2";
$childData['vaccine_step7_start'] = date('Y-m-d', strtotime($dob_date. ' + 16 month'));
$childData['vaccine_step7_end'] = date('Y-m-d', strtotime($dob_date. ' + 5 year'));
$childData['vaccine_step7_status'] = $this->input->post('opv3');
$childData['vaccine_step7_done_date'] = $this->input->post('opv3_done_date');

$childData['vaccine_step8_name'] = "DPT Booster1";
$childData['vaccine_step8_start'] = date('Y-m-d', strtotime($dob_date. ' + 5 year'));
$childData['vaccine_step8_end'] = date('Y-m-d', strtotime($dob_date. ' + 6 year'));
$childData['vaccine_step8_status'] = $this->input->post('penta3');
$childData['vaccine_step8_done_date'] = $this->input->post('penta3_done_date');









// $childData['BCG_date'] =  date('Y-m-d', strtotime($dob_date. ' + 1 year'));
// $childData['OPV_O_date'] = date('Y-m-d', strtotime($dob_date. ' + 15 days'));
// $childData['Hep_B_date'] = date('Y-m-d', strtotime($dob_date. ' + 1 day'));
// $childData['OPV1_date'] = date('Y-m-d', strtotime($dob_date. ' + 6 weeks'));//
// $childData['OPV2_date'] = date('Y-m-d', strtotime($dob_date. ' + 10 weeks'));
// $childData['PENTA1_date'] =  date('Y-m-d', strtotime($dob_date. ' + 6 weeks'));
// $childData['PENTA2_date'] =  date('Y-m-d', strtotime($dob_date. ' + 10 weeks'));
// $childData['OPV3_date'] =  date('Y-m-d', strtotime($dob_date. ' + 14 weeks'));
// $childData['PENTA3_date'] = date('Y-m-d', strtotime($dob_date. ' + 14 weeks'));
// //$childData['MEASLES'] = date('Y-m-d', strtotime($dob_date. ' + 12 month'));
// $childData['IPV_date'] = date('Y-m-d', strtotime($dob_date. ' + 14 weeks'));
// $childData['MMR_date'] = date('Y-m-d', strtotime($dob_date. ' + 12 month'));
// $childData['JE1_date'] = date('Y-m-d', strtotime($dob_date. ' + 12 month'));
// $childData['VIT_A_1_date'] = date('Y-m-d', strtotime($dob_date. ' + 9 month'));
// $childData['OPV_BOOSTER_date'] = date('Y-m-d', strtotime($dob_date. ' + 24 month'));
// $childData['DPT_1_BOOSTER_date'] = date('Y-m-d', strtotime($dob_date. ' + 24 month'));
// $childData['JE2_date'] = date('Y-m-d', strtotime($dob_date. ' + 24 month'));
// $childData['VIT_A_2_date'] = date('Y-m-d', strtotime($dob_date. ' + 16 month'));
// $childData['DPT_2_BOOSTER_date'] = date('Y-m-d', strtotime($dob_date. ' + 6 year'));





$this->db->insert('child_details', $childData);
return $this->db->insert_id();

}

$response = array(
'status' => 'ok'
);

$this->output
->set_status_header(200)
->set_content_type('application/json')
->set_output(json_encode($response));











}






















public function createChild()
{
header("Access-Control-Allow-Origin: *");
header("Access-Control-Request-Headers: GET,POST,OPTIONS,DELETE,PUT");
header("Access-Control-Allow-Headers: authorization, Content-Type");

$token = $this->input->get_request_header('Authorization');

$isValidToken = $this->api_model->checkToken($token);

if($isValidToken) {

if ($this->input->post('vacinated_ok')== 'true') {
$str = "yes";
}else{
$str = "no";
}


$childData['mthr_id'] = $this->input->post('mthrs_db_id');
$childData['child_unq_id'] = $this->input->post('childunqno');
$childData['mother_name'] = $this->input->post('mothername');
$childData['child_name'] = $this->input->post('childname');
$childData['child_contact'] = $this->input->post('mothermblno');
$childData['child_dob'] = $this->input->post('childdob');
$childData['child_status'] = '1';
$childData['is_vacinated_before'] = $str;



// if vaciinated Before

//?$_POST['userName']:'Anonymous';
$childData['BCG'] =  $this->input->post('bcg');
$childData['OPV_O'] = $this->input->post('opv_o');
$childData['Hep_B'] = $this->input->post('hep_b');
$childData['OPV1'] = $this->input->post('opv1');
$childData['OPV2'] = $this->input->post('opv2');
$childData['PENTA1'] =  $this->input->post('penta1');
$childData['PENTA2'] =  $this->input->post('penta2');
$childData['OPV3'] =  $this->input->post('opv3');
$childData['PENTA3'] = $this->input->post('penta3');
//$childData['MEASLES'] = date('Y-m-d', strtotime($dob_date. ' + 12 month'));
$childData['IPV'] = $this->input->post('ipv');
$childData['MMR'] = $this->input->post('mmr');
$childData['JE1'] = $this->input->post('je1');
$childData['VIT_A_1'] = $this->input->post('vit_a_1');
$childData['OPV_BOOSTER'] = $this->input->post('opv_booster');
$childData['DPT_1_BOOSTER'] = $this->input->post('dpt_1');
$childData['JE2'] = $this->input->post('je_2');
$childData['VIT_A_2'] = $this->input->post('vit_a_2');
$childData['DPT_2_BOOSTER'] = $this->input->post('dpt_2');



// another=============================================


$childData['BCG_done_date'] =  $this->input->post('bcg_done_date');
$childData['OPV_O_done_date'] = $this->input->post('opv_o_done_date');
$childData['Hep_B_done_date'] = $this->input->post('hep_b_done_date');
$childData['OPV1_done_date'] = $this->input->post('opv1_done_date');
$childData['OPV2_done_date'] = $this->input->post('opv2_done_date');
$childData['PENTA1_done_date'] =  $this->input->post('penta1_done_date');
$childData['PENTA2_done_date'] =  $this->input->post('penta2_done_date');
$childData['OPV3_done_date'] =  $this->input->post('childdob');
$childData['PENTA3_done_date'] = $this->input->post('penta3_done_date');
//$childData['MEASLES'] = date('Y-m-d', strtotime($dob_date. ' + 12 month'));
$childData['IPV_done_date'] = $this->input->post('ipv_done_date');
$childData['MMR_done_date'] = $this->input->post('mmr_done_date');
$childData['JE1_done_date'] = $this->input->post('je1_done_date');
$childData['VIT_A_1_done_date'] = $this->input->post('vit_a_1_done_date');
$childData['OPV_BOOSTER_done_date'] = $this->input->post('opv_booster_done_date');
$childData['DPT_1_BOOSTER_done_date'] = $this->input->post('dpt_1_done_date');
$childData['JE2_done_date'] = $this->input->post('je_2_done_date');
$childData['VIT_A_2_done_date'] = $this->input->post('vit_a_2_done_date');
$childData['DPT_2_BOOSTER_done_date'] = $this->input->post('dpt_2_done_date');














// end of if vaccinated before 


















$dob_date = $this->input->post('childdob');


if(empty($dob_date)){

// $childData['BCG_done_date'] =  $this->input->post('bcg_done_date');
// $childData['OPV_O_done_date'] = $this->input->post('opv_o_done_date');
// $childData['Hep_B_done_date'] = $this->input->post('hep_b_done_date');
// $childData['OPV1_done_date'] = $this->input->post('opv1_done_date');
// $childData['OPV2_done_date'] = $this->input->post('opv2_done_date');
// $childData['PENTA1_done_date'] =  $this->input->post('penta1_done_date');
// $childData['PENTA2_done_date'] =  $this->input->post('penta2_done_date');
// $childData['OPV3_done_date'] =  $this->input->post('childdob');
// $childData['PENTA3_done_date'] = $this->input->post('penta3_done_date');
// //$childData['MEASLES'] = date('Y-m-d', strtotime($dob_date. ' + 12 month'));
// $childData['IPV_done_date'] = $this->input->post('ipv_done_date');
// $childData['MMR_done_date'] = $this->input->post('mmr_done_date');
// $childData['JE1_done_date'] = $this->input->post('je1_done_date');
// $childData['VIT_A_1_done_date'] = $this->input->post('vit_a_1_done_date');
// $childData['OPV_BOOSTER_done_date'] = $this->input->post('opv_booster_done_date');
// $childData['DPT_1_BOOSTER_done_date'] = $this->input->post('dpt_1_done_date');
// $childData['JE2_done_date'] = $this->input->post('je_2_done_date');
// $childData['VIT_A_2_done_date'] = $this->input->post('vit_a_2_done_date');
// $childData['DPT_2_BOOSTER_done_date'] = $this->input->post('dpt_2_done_date');
$bcg_done_date1 = $this->input->post('bcg_done_date');
$opv_o_done_date1 = $this->input->post('opv_o_done_date');
$hep_b_done_date1 = $this->input->post('hep_b_done_date');
$opv1_done_date1 = $this->input->post('opv1_done_date');
$opv2_done_date1 = $this->input->post('opv2_done_date');
$opv3_done_date1 = $this->input->post('opv3_done_date');
$penta1_done_date1 = $this->input->post('penta1_done_date');
$penta2_done_date1 = $this->input->post('penta2_done_date');
$penta3_done_date1 = $this->input->post('penta3_done_date');
$dpt_1_done_date1 = $this->input->post('dpt_1_done_date');
$dpt_2_done_date1 = $this->input->post('dpt_2_done_date');
$dpt_3_done_date1 = $this->input->post('dpt_3_done_date');
$je_1_done_date1 = $this->input->post('je_1_done_date');
$je_2_done_date1 = $this->input->post('je_2_done_date');
$opv_booster_done_date1 = $this->input->post('opv_booster_done_date');
$mmr_done_date1 = $this->input->post('mmr_done_date');
// $opv_o_done_date1 = $this->input->post('opv_o_done_date');
// $opv_o_done_date1 = $this->input->post('opv_o_done_date');
// $opv_o_done_date1 = $this->input->post('opv_o_done_date');
// $opv_o_done_date1 = $this->input->post('opv_o_done_date');
// $opv_o_done_date1 = $this->input->post('opv_o_done_date');
// $opv_o_done_date1 = $this->input->post('opv_o_done_date');
// $opv_o_done_date1 = $this->input->post('opv_o_done_date');
// $opv_o_done_date1 = $this->input->post('opv_o_done_date');
// $opv_o_done_date1 = $this->input->post('opv_o_done_date');
// $opv_o_done_date1 = $this->input->post('opv_o_done_date');
// $opv_o_done_date1 = $this->input->post('opv_o_done_date');
// $opv_o_done_date1 = $this->input->post('opv_o_done_date');
// $opv_o_done_date1 = $this->input->post('opv_o_done_date');
// $opv_o_done_date1 = $this->input->post('opv_o_done_date');
// $opv_o_done_date1 = $this->input->post('opv_o_done_date');
// $opv_o_done_date1 = $this->input->post('opv_o_done_date');




if(!empty($bcg_done_date1)){

 $dob_date = date('Y-m-d', strtotime($bcg_done_date1. ' - 1 day'));

 $childData['child_dob'] = $dob_date;

}elseif(!empty($opv_o_done_date1)){

 $dob_date = date('Y-m-d', strtotime($opv_o_done_date1. ' - 15 days'));
 $childData['child_dob'] = $dob_date;

}elseif(!empty($hep_b_done_date1)){

 $dob_date = date('Y-m-d', strtotime($hep_b_done_date1. ' - 1 day'));
 $childData['child_dob'] = $dob_date;

}elseif(!empty($opv1_done_date1)){

 $dob_date = date('Y-m-d', strtotime($opv1_done_date1. ' - 42 days'));
$childData['child_dob'] = $dob_date;
}elseif(!empty($opv2_done_date1)){

 $dob_date = date('Y-m-d', strtotime($opv2_done_date1. ' - 70 days'));
 $childData['child_dob'] = $dob_date;

}elseif(!empty($opv3_done_date1)){

 $dob_date = date('Y-m-d', strtotime($opv3_done_date1. ' - 98 days'));
 $childData['child_dob'] = $dob_date;

}elseif(!empty($penta1_done_date1)){

 $dob_date = date('Y-m-d', strtotime($penta1_done_date1. ' - 42 days'));
$childData['child_dob'] = $dob_date;
}elseif(!empty($penta2_done_date1)){

 $dob_date = date('Y-m-d', strtotime($penta2_done_date1. ' - 70 days'));
 $childData['child_dob'] = $dob_date;

}elseif(!empty($penta3_done_date1)){

 $dob_date = date('Y-m-d', strtotime($penta3_done_date1. ' - 98 days'));
 $childData['child_dob'] = $dob_date;

}elseif(!empty($dpt_1_done_date1)){

 $dob_date = date('Y-m-d', strtotime($dpt_1_done_date1. ' - 42 days'));
 $childData['child_dob'] = $dob_date;

}elseif(!empty($dpt_2_done_date1)){

 $dob_date = date('Y-m-d', strtotime($dpt_2_done_date1. ' - 70 days'));
 $childData['child_dob'] = $dob_date;

}elseif(!empty($dpt_3_done_date1)){

 $dob_date = date('Y-m-d', strtotime($dpt_3_done_date1. ' - 98 days'));
 $childData['child_dob'] = $dob_date;

}elseif(!empty($je_1_done_date1)){

 $dob_date = date('Y-m-d', strtotime($je_1_done_date1. ' - 270 days'));
 $childData['child_dob'] = $dob_date;

}elseif(!empty($je_2_done_date1)){

 $dob_date = date('Y-m-d', strtotime($je_2_done_date1. ' - 480 days'));
 $childData['child_dob'] = $dob_date;

}elseif(!empty($opv_booster_done_date1)){

 $dob_date = date('Y-m-d', strtotime($opv_booster_done_date1. ' - 480 days'));
 $childData['child_dob'] = $dob_date;

}elseif(!empty($mmr_done_date1)){

 $dob_date = date('Y-m-d', strtotime($mmr_done_date1. ' - 270 days'));
 $childData['child_dob'] = $dob_date;

}

// $childData['OPV_O_done_date'] = $this->input->post('opv_o_done_date');
// $childData['Hep_B_done_date'] = $this->input->post('hep_b_done_date');
// $childData['OPV1_done_date'] = $this->input->post('opv1_done_date');
// $childData['OPV2_done_date'] = $this->input->post('opv2_done_date');
// $childData['PENTA1_done_date'] =  $this->input->post('penta1_done_date');
// $childData['PENTA2_done_date'] =  $this->input->post('penta2_done_date');
// $childData['OPV3_done_date'] =  $this->input->post('childdob');
// $childData['PENTA3_done_date'] = $this->input->post('penta3_done_date');
// //$childData['MEASLES'] = date('Y-m-d', strtotime($dob_date. ' + 12 month'));
// $childData['IPV_done_date'] = $this->input->post('ipv_done_date');
// $childData['MMR_done_date'] = $this->input->post('mmr_done_date');
// $childData['JE1_done_date'] = $this->input->post('je1_done_date');
// $childData['VIT_A_1_done_date'] = $this->input->post('vit_a_1_done_date');
// $childData['OPV_BOOSTER_done_date'] = $this->input->post('opv_booster_done_date');
// $childData['DPT_1_BOOSTER_done_date'] = $this->input->post('dpt_1_done_date');
// $childData['JE2_done_date'] = $this->input->post('je_2_done_date');
// $childData['VIT_A_2_done_date'] = $this->input->post('vit_a_2_done_date');
// $childData['DPT_2_BOOSTER_done_date'] = $this->input->post('dpt_2_done_date');

















    //$dob_date = "2001-01-01";









}

$childData['Hep_B_date'] = date('Y-m-d', strtotime($dob_date));
$childData['Hep_B_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 1 day'));

$childData['BCG_date'] =  date('Y-m-d', strtotime($dob_date));
$childData['BCG_last_date'] =  date('Y-m-d', strtotime($dob_date. ' + 1 year'));

$childData['OPV_O_date'] = date('Y-m-d', strtotime($dob_date));
$childData['OPV_O_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 15 days'));

$childData['RVV1_date'] = date('Y-m-d', strtotime($dob_date. ' + 42 days'));
$childData['RVV1_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 365 days'));

$childData['IPV1_date'] = date('Y-m-d', strtotime($dob_date. ' + 42 days'));
$childData['IPV1_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 365 days'));

$childData['OPV1_date'] = date('Y-m-d', strtotime($dob_date. ' + 42 days'));//
$childData['OPV1_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 1825 days'));

$childData['DPT1_date'] = date('Y-m-d', strtotime($dob_date. ' + 42 days'));//
$childData['DPT1_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 730 days'));

$childData['HepB1_date'] = date('Y-m-d', strtotime($dob_date. ' + 42 days'));//
$childData['HepB1_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 365 days'));

$childData['PENTA1_date'] =  date('Y-m-d', strtotime($dob_date. ' + 42 days'));
$childData['PENTA1_last_date'] =  date('Y-m-d', strtotime($dob_date. ' + 365 days'));



// Bucket 4 =================================================================
$childData['RVV2_date'] = date('Y-m-d', strtotime($dob_date. ' + 70 days'));
$childData['RVV2_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 365 days'));


$childData['OPV2_date'] = date('Y-m-d', strtotime($dob_date. ' + 70 days'));//
$childData['OPV2_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 1825 days'));

$childData['DPT2_date'] = date('Y-m-d', strtotime($dob_date. ' + 70 days'));//
$childData['DPT2_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 730 days'));

$childData['HepB2_date'] = date('Y-m-d', strtotime($dob_date. ' + 70 days'));//
$childData['HepB2_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 365 days'));

$childData['PENTA2_date'] =  date('Y-m-d', strtotime($dob_date. ' + 70 days'));
$childData['PENTA2_last_date'] =  date('Y-m-d', strtotime($dob_date. ' + 365 days'));


// Bucket 5 =================================================================

$childData['RVV3_date'] = date('Y-m-d', strtotime($dob_date. ' + 98 days'));
$childData['RVV3_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 365 days'));

$childData['IPV2_date'] = date('Y-m-d', strtotime($dob_date. ' + 98 days'));
$childData['IPV2_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 365 days'));

$childData['OPV3_date'] = date('Y-m-d', strtotime($dob_date. ' + 98 days'));//
$childData['OPV3_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 1825 days'));

$childData['DPT3_date'] = date('Y-m-d', strtotime($dob_date. ' + 98 days'));//
$childData['DPT3_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 365 days'));

$childData['HepB3_date'] = date('Y-m-d', strtotime($dob_date. ' + 98 days'));//
$childData['HepB3_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 365 days'));

$childData['PENTA3_date'] =  date('Y-m-d', strtotime($dob_date. ' + 98 days'));
$childData['PENTA3_last_date'] =  date('Y-m-d', strtotime($dob_date. ' + 365 days'));


// Bucket 6 =================================================================
$childData['MMR_date'] = date('Y-m-d', strtotime($dob_date. ' + 270 days'));
$childData['MMR_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 1825 days'));


$childData['JE1_date'] = date('Y-m-d', strtotime($dob_date. ' + 270 days'));
$childData['JE1_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 5475 days'));


$childData['VIT_A_1_date'] = date('Y-m-d', strtotime($dob_date. ' + 270 days'));
$childData['VIT_A_1_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 1825 days'));





// Bucket 7 =================================================================

$childData['OPV_BOOSTER_date'] = date('Y-m-d', strtotime($dob_date. ' + 480 days'));
$childData['OPV_BOOSTER_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 1825 days'));


$childData['DPT_1_BOOSTER_date'] = date('Y-m-d', strtotime($dob_date. ' + 480 days'));
$childData['DPT_1_BOOSTER_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 2555 days'));

$childData['MMR2_date'] = date('Y-m-d', strtotime($dob_date. ' + 480 days'));
$childData['MMR2_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 1825 days'));


$childData['JE2_date'] = date('Y-m-d', strtotime($dob_date. ' + 480 days'));
$childData['JE2_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 5475 days'));

// VITAMIN BUCKET =========================================================

$childData['VIT_A_2_date'] = date('Y-m-d', strtotime($dob_date. ' + 540 days'));
$childData['VIT_A_2_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 1825 days'));

$childData['VIT_A_3_date'] = date('Y-m-d', strtotime($dob_date. ' + 720 days'));
$childData['VIT_A_3_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 1825 days'));


$childData['VIT_A_4_date'] = date('Y-m-d', strtotime($dob_date. ' + 900 days'));
$childData['VIT_A_4_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 1825 days'));


$childData['VIT_A_5_date'] = date('Y-m-d', strtotime($dob_date. ' + 1080 days'));
$childData['VIT_A_5_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 1825 days'));


$childData['VIT_A_6_date'] = date('Y-m-d', strtotime($dob_date. ' + 1260 days'));
$childData['VIT_A_6_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 1825 days'));

$childData['VIT_A_7_date'] = date('Y-m-d', strtotime($dob_date. ' + 1340 days'));
$childData['VIT_A_7_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 1825 days'));


$childData['VIT_A_8_date'] = date('Y-m-d', strtotime($dob_date. ' + 1520 days'));
$childData['VIT_A_8_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 1825 days'));


$childData['VIT_A_9_date'] = date('Y-m-d', strtotime($dob_date. ' + 1700 days'));
$childData['VIT_A_9_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 1825 days'));



// Bucket 8 starts ==========================================================


$childData['DPT_2_BOOSTER_date'] = date('Y-m-d', strtotime($dob_date. ' + 1825 days'));
$childData['DPT_2_BOOSTER_last_date'] = date('Y-m-d', strtotime($dob_date. ' + 2190 days'));

// Bucket 8 =================================================================

















$childData['OPV2_date'] = date('Y-m-d', strtotime($dob_date. ' + 10 weeks'));


$childData['PENTA1_date'] =  date('Y-m-d', strtotime($dob_date. ' + 6 weeks'));
$childData['PENTA2_date'] =  date('Y-m-d', strtotime($dob_date. ' + 10 weeks'));
$childData['OPV3_date'] =  date('Y-m-d', strtotime($dob_date. ' + 14 weeks'));
$childData['PENTA3_date'] = date('Y-m-d', strtotime($dob_date. ' + 14 weeks'));
//$childData['MEASLES'] = date('Y-m-d', strtotime($dob_date. ' + 12 month'));
$childData['IPV_date'] = date('Y-m-d', strtotime($dob_date. ' + 14 weeks'));


// if empty starts from here 


//$childData['BCG'] =  $this->input->post('bcg');

if ($childData['BCG'] == "") {
	$childData['BCG'] = "false";
}else{
	$childData['BCG'] = $childData['BCG'];
}


if ($childData['OPV_O'] == "") {
	$childData['OPV_O'] = "false";
}else{
	$childData['OPV_O'] = $childData['OPV_O'];
}



if ($childData['Hep_B'] == "") {
	$childData['Hep_B'] = "false";
}else{
	$childData['Hep_B'] = $childData['Hep_B'];
}


if ($childData['OPV1'] == "") {
	$childData['OPV1'] = "false";
}else{
	$childData['OPV1'] = $childData['OPV1'];
}

if ($childData['OPV2'] == "") {
	$childData['OPV2'] = "OPV2";
}else{
	$childData['OPV2'] = $childData['OPV2'];
}
if ($childData['OPV3'] == "") {
	$childData['OPV3'] = "false";
}else{
	$childData['OPV3'] = $childData['OPV3'];
}
if ($childData['DPT1'] == "") {
	$childData['DPT1'] = "false";
}else{
	$childData['DPT1'] = $childData['DPT1'];
}
if ($childData['DPT2'] == "") {
	$childData['DPT2'] = "false";
}else{
	$childData['DPT2'] = $childData['DPT2'];
}
if ($childData['DPT3'] == "") {
	$childData['DPT3'] = "false";
}else{
	$childData['DPT3'] = $childData['DPT3'];
}
if ($childData['HepB1'] == "") {
	$childData['HepB1'] = "false";
}else{
	$childData['HepB1'] = $childData['HepB1'];
}
if ($childData['HepB2'] == "") {
	$childData['HepB2'] = "false";
}else{
	$childData['HepB2'] = $childData['HepB2'];
}
if ($childData['HepB3'] == "") {
	$childData['HepB3'] = "false";
}else{
	$childData['HepB3'] = $childData['HepB3'];
}
if ($childData['RVV1'] == "") {
	$childData['RVV1'] = "false";
}else{
	$childData['RVV1'] = $childData['RVV1'];
}
if ($childData['RVV2'] == "") {
	$childData['RVV2'] = "false";
}else{
	$childData['RVV2'] = $childData['RVV2'];
}

if ($childData['RVV3'] == "") {
	$childData['RVV3'] = "false";
}else{
	$childData['RVV3'] = $childData['RVV3'];
}


if ($childData['IPV1'] == "") {
	$childData['IPV1'] = "false";
}else{
	$childData['IPV1'] = $childData['IPV1'];
}


if ($childData['IPV2'] == "") {
	$childData['IPV2'] = "false";
}else{
	$childData['IPV2'] = $childData['IPV2'];
}
if ($childData['PENTA1'] == "") {
	$childData['PENTA1'] = "false";
}else{
	$childData['PENTA1'] = $childData['PENTA1'];
}
if ($childData['PENTA2'] == "") {
	$childData['PENTA2'] = "false";
}else{
	$childData['PENTA2'] = $childData['PENTA2'];
}
if ($childData['PENTA3'] == "") {
	$childData['PENTA3'] = "false";
}else{
	$childData['PENTA3'] = $childData['PENTA3'];
}
if ($childData['IPV'] == "") {
	$childData['IPV'] = "false";
}else{
	$childData['IPV'] = $childData['IPV'];
}
if ($childData['MMR'] == "") {
	$childData['MMR'] = "false";
}else{
	$childData['MMR'] = $childData['MMR'];
}
if ($childData['MMR2'] == "") {
	$childData['MMR2'] = "false";
}else{
	$childData['MMR2'] = $childData['MMR2'];
}
if ($childData['JE1'] == "") {
	$childData['JE1'] = "false";
}else{
	$childData['JE1'] = $childData['JE1'];
}
if ($childData['VIT_A_1'] == "") {
	$childData['VIT_A_1'] = "false";
}else{
	$childData['VIT_A_1'] = $childData['VIT_A_1'];
}



if ($childData['VIT_A_2'] == "") {
	$childData['VIT_A_2'] = "false";
}else{
	$childData['VIT_A_2'] = $childData['VIT_A_2'];
}


if ($childData['VIT_A_3'] == "") {
	$childData['VIT_A_3'] = "false";
}else{
	$childData['VIT_A_3'] = $childData['VIT_A_3'];
}
if ($childData['VIT_A_4'] == "") {
	$childData['VIT_A_4'] = "false";
}else{
	$childData['VIT_A_4'] = $childData['VIT_A_4'];
}
if ($childData['VIT_A_5'] == "") {
	$childData['VIT_A_5'] = "false";
}else{
	$childData['VIT_A_5'] = $childData['VIT_A_5'];
}
if ($childData['VIT_A_6'] == "") {
	$childData['VIT_A_6'] = "false";
}else{
	$childData['VIT_A_6'] = $childData['VIT_A_6'];
}
if ($childData['VIT_A_7'] == "") {
	$childData['VIT_A_7'] = "false";
}else{
	$childData['VIT_A_7'] = $childData['VIT_A_7'];
}
if ($childData['VIT_A_8'] == "") {
	$childData['VIT_A_8'] = "false";
}else{
	$childData['VIT_A_8'] = $childData['VIT_A_8'];
}
if ($childData['VIT_A_9'] == "") {
	$childData['VIT_A_9'] = "false";
}else{
	$childData['VIT_A_9'] = $childData['VIT_A_9'];
}
if ($childData['OPV_BOOSTER'] == "") {
	$childData['OPV_BOOSTER'] = "false";
}else{
	$childData['OPV_BOOSTER'] = $childData['OPV_BOOSTER'];
}
if ($childData['DPT_1_BOOSTER'] == "") {
	$childData['DPT_1_BOOSTER'] = "false";
}else{
	$childData['DPT_1_BOOSTER'] = $childData['DPT_1_BOOSTER'];
}
if ($childData['JE2'] == "") {
	$childData['JE2'] = "false";
}else{
	$childData['JE2'] = $childData['JE2'];
}
if ($childData['DPT_2_BOOSTER'] == "") {
	$childData['DPT_2_BOOSTER'] = "false";
}else{
	$childData['DPT_2_BOOSTER'] = $childData['DPT_2_BOOSTER'];
}


$childData['added_time'] = date("Y-m-d H:i:s");

































// $childData['OPV_O'] = $this->input->post('opv_o');
// $childData['Hep_B'] = $this->input->post('hep_b');
// $childData['OPV1'] = $this->input->post('opv1');
// $childData['OPV2'] = $this->input->post('opv2');
// $childData['PENTA1'] =  $this->input->post('penta1');
// $childData['PENTA2'] =  $this->input->post('penta2');
// $childData['OPV3'] =  $this->input->post('opv3');
// $childData['PENTA3'] = $this->input->post('penta3');
// //$childData['MEASLES'] = date('Y-m-d', strtotime($dob_date. ' + 12 month'));
// $childData['IPV'] = $this->input->post('ipv');
// $childData['MMR'] = $this->input->post('mmr');
// $childData['JE1'] = $this->input->post('je1');
// $childData['VIT_A_1'] = $this->input->post('vit_a_1');
// $childData['OPV_BOOSTER'] = $this->input->post('opv_booster');
// $childData['DPT_1_BOOSTER'] = $this->input->post('dpt_1');
// $childData['JE2'] = $this->input->post('je_2');
// $childData['VIT_A_2'] = $this->input->post('vit_a_2');
// $childData['DPT_2_BOOSTER'] = $this->input->post('dpt_2');


// if not empty ends here 
























// $childData[''] = '1';
// $childData[''] = '1';

$this->db->insert('children_details', $childData);
return $this->db->insert_id();




}



$response = array(
'status' => 'ok'
);




$this->output
->set_status_header(200)
->set_content_type('application/json')
->set_output(json_encode($response));











}





// End Add Child data==========================================

// Start Of Get Child Data =========================================
public function getChild($id)
{
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: authorization, Content-Type");

//echo $id;

$token = $this->input->get_request_header('Authorization');

//echo $token;

$isValidToken = $this->api_model->checkToken($token);

// echo $isValidToken;

// if($isValidToken) {

$child = $this->api_model->get_child_id($id);

//print_r($mother);
$posts = array();

if($child){

foreach ($child as $child) {


$posts[] = array(
'child_id' => $child->child_id,
'mthr_id' => $child->mthr_id,
'child_unq_id' => $child->child_unq_id,
'child_name' => $child->child_name,
'child_dob'=> $child->child_dob,
'child_status' => $child->child_status,
'is_vacinated_before' => $child->is_vacinated_before,
'bcg' => $child->bcg,
'bcg_done_date' => $child->bcg_done_date,
'hepb_birth' => $child->hepb_birth,
'hepb_birth_done_date' => $child->hepb_birth_done_date,
'vaccine_step1_name' => $child->vaccine_step1_name,
'vaccine_step1_start' => $child->vaccine_step1_start,
'vaccine_step1_end' => $child->vaccine_step1_end,
'vaccine_step1_status' => $child->vaccine_step1_status,
'vaccine_step1_done_date' => $child->vaccine_step1_done_date,

'vaccine_step2_name' => $child->vaccine_step2_name,
'vaccine_step2_start' => $child->vaccine_step2_start,
'vaccine_step2_end' => $child->vaccine_step1_end,
'vaccine_step2_status' => $child->vaccine_step2_status,
'vaccine_step2_done_date' => $child->vaccine_step2_done_date,


'vaccine_step3_name' => $child->vaccine_step3_name,
'vaccine_step3_start' => $child->vaccine_step3_start,
'vaccine_step3_end' => $child->vaccine_step3_end,
'vaccine_step3_status' => $child->vaccine_step3_status,
'vaccine_step3_done_date' => $child->vaccine_step3_done_date,


'vaccine_step4_name' => $child->vaccine_step4_name,
'vaccine_step4_start' => $child->vaccine_step4_start,
'vaccine_step4_end' => $child->vaccine_step4_end,
'vaccine_step4_status' => $child->vaccine_step4_status,
'vaccine_step4_done_date' => $child->vaccine_step4_done_date,


'vaccine_step5_name' => $child->vaccine_step5_name,
'vaccine_step5_start' => $child->vaccine_step5_start,
'vaccine_step5_end' => $child->vaccine_step5_end,
'vaccine_step5_status' => $child->vaccine_step5_status,
'vaccine_step5_done_date' => $child->vaccine_step5_done_date,


'vaccine_step6_name' => $child->vaccine_step6_name,
'vaccine_step6_start' => $child->vaccine_step6_start,
'vaccine_step6_end' => $child->vaccine_step6_end,
'vaccine_step6_status' => $child->vaccine_step6_status,
'vaccine_step6_done_date' => $child->vaccine_step6_done_date,


'vaccine_step7_name' => $child->vaccine_step7_name,
'vaccine_step7_start' => $child->vaccine_step7_start,
'vaccine_step7_end' => $child->vaccine_step7_end,
'vaccine_step7_status' => $child->vaccine_step7_status,
'vaccine_step7_done_date' => $child->vaccine_step7_done_date,


'vaccine_step8_name' => $child->vaccine_step8_name,
'vaccine_step8_start' => $child->vaccine_step8_start,
'vaccine_step8_end' => $child->vaccine_step8_end,
'vaccine_step8_status' => $child->vaccine_step8_status,
'vaccine_step8_done_date' => $child->vaccine_step8_done_date,









// 'BCG' => $child->BCG,
// 'BCG_date' => $child->BCG_date,
// 'BCG_done_date' => $child->BCG_done_date,
// 'OPV_O' => $child->OPV_O,
// 'OPV_O_date' => $child->OPV_O_date,
// 'OPV_O_done_date' => $child->OPV_O_done_date
);
}

}else{

$posts[] = array(
'message'=>'No Data Found Of This ID'
);
}



$this->output
->set_status_header(200)
->set_content_type('application/json')
->set_output(json_encode($posts)); 
//}
}







// End Of get  Child data ================================= ============















// =====================================================End Of Next Wednesday Lists Data 

public function getOutBoundCallData()
{
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: authorization, Content-Type");

$token = $this->input->get_request_header('Authorization');

$isValidToken = $this->api_model->checkToken($token);



// echo $time = date("H");
//     /* Set the $timezone variable to become the current timezone */
//     // echo $timezone = date("");
//     /* If the time is less than 1200 hours, show good morning */
//     if ($time < "12") {
//         echo "Good morning";
//     } else
//     /* If the time is grater than or equal to 1200 hours, but less than 1700 hours, so good afternoon */
//     if ($time >= "12" && $time < "17") {
//         echo "Good afternoon";
//     } else
//     /* Should the time be between or equal to 1700 and 1900 hours, show good evening */
//     if ($time >= "17" && $time < "19") {
//         echo "Good evening";
//     } else
//     /* Finally, show good night if the time is greater than or equal to 1900 hours */
//     if ($time >= "19") {
//         echo "Good night";
//     }

 $dinnank = date('D');
$samay = date('H');


if(date('D') == 'Wed') { 
$wed = date('Y-m-d');
}else{
$wed = date('Y-m-d', strtotime('next Wednesday')); }


if(date('D') == 'Sat') { 
$sat = date('Y-m-d');
}else{
$sat = date('Y-m-d', strtotime('next Saturday')); }

$bcg_dates_posts = array();
//$opv_o_dates = array();

// if($isValidToken) {
$bcg_dates = $this->api_model->get_bcg_dates();
$opv_o_dates1 = $this->api_model->get_opv_o_dates();
$hep_b_dates = $this->api_model->get_hep_b_dates();
$opv1_dates = $this->api_model->get_opv1_dates();
$opv2_dates = $this->api_model->get_opv2_dates();
$opv3_dates = $this->api_model->get_opv3_dates();
$penta1_dates = $this->api_model->get_penta1_dates();
$penta2_dates = $this->api_model->get_penta2_dates();
$penta3_dates = $this->api_model->get_penta3_dates();
$dpt1_dates = $this->api_model->get_DPT1_dates();
$dpt2_dates = $this->api_model->get_DPT2_dates();
$dpt3_dates = $this->api_model->get_DPT3_dates();
$hepb1_dates = $this->api_model->get_HepB1_dates();

//print_r($hepb1_dates);
$child_details = $this->api_model->get_child_details();
$hepb2_dates = $this->api_model->get_HepB2_dates();
$hepb3_dates = $this->api_model->get_HepB3_dates();
$rvv1_dates = $this->api_model->get_RVV1_dates();
$rvv2_dates = $this->api_model->get_RVV2_dates();
$rvv3_dates = $this->api_model->get_RVV3_dates();
$ipv1_dates = $this->api_model->get_ipv1_dates();
$ipv2_dates =  $this->api_model->get_ipv2_dates();
$mmr2_dates = $this->api_model->get_mmr2_dates();
$ipv_dates = $this->api_model->get_ipv_dates();
$mmr_dates = $this->api_model->get_mmr_dates();
$je1_dates = $this->api_model->get_je1_dates();
$je2_dates = $this->api_model->get_je2_dates();
$vit_a_1_dates = $this->api_model->get_vit_a_1_dates();
$vit_a_2_dates = $this->api_model->get_vit_a_2_dates();
$vit_a_3_dates = $this->api_model->get_vit_a_3_dates();
$vit_a_4_dates = $this->api_model->get_vit_a_4_dates();
$vit_a_5_dates = $this->api_model->get_vit_a_5_dates();
$vit_a_6_dates = $this->api_model->get_vit_a_6_dates();
$vit_a_7_dates = $this->api_model->get_vit_a_7_dates();
$vit_a_8_dates = $this->api_model->get_vit_a_8_dates();
$vit_a_9_dates = $this->api_model->get_vit_a_9_dates();
$opv_booster_dates = $this->api_model->get_opv_booster_dates();

$dpt1_booster_dates = $this->api_model->get_dpt1_booster_dates();
$dpt2_booster_dates = $this->api_model->get_dpt2_booster_dates();

//print_r($bcg_dates);




// ==========================================start of different arrays ================================


// ==========================================End of different arrays ====================================

//$total_array = array();

// $total_array = array_merge($bcg_dates_posts, $opv_o_dates,$hep_b_dates,$opv1_dates,$opv2_dates,$opv3_dates,$penta1_dates,$penta2_dates,$penta3_dates,$ipv_dates,$mmr_dates,$je1_dates,$je2_dates,$vit_a_1_dates,$vit_a_2_dates,$opv_booster_dates,$dpt1_booster_dates,$dpt2_booster_dates);

// ==========================23 August ======================

// $total_array = array_merge($bcg_dates_posts,$opv_dates_posts,$opv1_dates_posts,$opv2_dates_posts,$opv3_dates_posts,$dpt1_dates_posts,$dpt2_dates_posts,$dpt3_dates_posts,$hepb1_dates_posts,$hepb2_dates_posts,$hepb3_dates_posts,$rvv1_dates_posts,$rvv2_dates_posts,$rvv3_dates_posts,$ipv1_dates_posts,$ipv2_dates_posts,$penta1_dates_posts,$penta2_dates_posts,$penta3_dates_posts,$mmr_dates_posts,$mmr2_dates_posts,$je1_dates_posts,$je2_dates_posts,$vit_a_1_dates_posts,$vit_a_2_dates_posts,$vit_a_3_dates_posts,$vit_a_4_dates_posts,$vit_a_5_dates_posts,$vit_a_6_dates_posts,$vit_a_7_dates_posts,$vit_a_8_dates_posts,$vit_a_9_dates_posts,$opv_booster_dates_posts,$je1_dates_posts,$je2_dates_posts,$dpt1_booster_dates_posts,$dpt2_booster_dates_posts);





  $bcg_dates_posts = array();$opv_dates_posts = array();$opv1_dates_posts = array();$opv2_dates_posts = array();
  $opv3_dates_posts = array();$dpt1_dates_posts = array();$dpt2_dates_posts = array();$dpt3_dates_posts = array();
  $hepb1_dates_posts = array();$hepb2_dates_posts = array();$hepb3_dates_posts = array();$rvv1_dates_posts = array();

$checkrvv1 = array();

$checkbucket3 = array();
$count = 0;

  $rvv2_dates_posts = array();$rvv3_dates_posts = array();$ipv1_dates_posts = array();$ipv2_dates_posts = array();
  $penta1_dates_posts = array();$penta2_dates_posts = array();$penta3_dates_posts = array();$mmr_dates_posts = array();
  $mmr2_dates_posts = array();$je1_dates_posts = array();$je2_dates_posts = array();$vit_a_1_dates_posts = array();
  $vit_a_2_dates_posts = array();$vit_a_2_dates_posts = array();$vit_a_3_dates_posts = array();
  $vit_a_4_dates_posts = array();$vit_a_5_dates_posts = array();$vit_a_6_dates_posts = array();
  $vit_a_7_dates_posts = array();$vit_a_8_dates_posts = array();$vit_a_9_dates_posts = array();
  $opv_booster_dates_posts = array();
  $dpt1_booster_dates_posts = array();$dpt2_booster_dates_posts = array();


  $opv1 = 0;$opv2 = 0;$opv3 = 0;$dpt1 = 0;$dpt2 = 0;$dpt3 = 0;$hepb1 = 0;$hepb2 = 0;$hepb3 = 0;$rvv1 =0;$rvv2 = 0;$rvv3 = 0;
  $ipv1 = 0;$ipv2 = 0;$penta1 = 0;$penta2 = 0;$penta3 = 0;$mmr = 0;$mmr2 = 0;$je1 = 0;$je2 = 0;$vit_a_1 = 0;$vit_a_2 = 0;
  $vit_a_3 = 0;$vit_a_4 = 0;$vit_a_5 = 0;$vit_a_6 = 0;$vit_a_7 = 0;$vit_a_8 = 0;$vit_a_9 = 0;$opv_booster = 0;$dpt1_booster = 0;
  $dpt2_booster = 0;

  // $bcg_dates_posts = array();$bcg_dates_posts = array();
  
// foreach ($child_details as $child) {
	
// }

foreach($bcg_dates as $bcg_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunaday OutBound Calls 
$bcg_dates_posts[] = array(
'vaccine' => 'BCG',
'child_id' => $bcg_date->child_unq_id,
'mobile' => $bcg_date->child_contact,
'message' => $bcg_date->child_name.' Sunday For का BCG टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls 
$bcg_dates_posts[] = array(
'vaccine' => 'BCG',
'child_id' => $bcg_date->child_unq_id,
'mobile' => $bcg_date->child_contact,
'message' => $bcg_date->child_name.' का Tuesday for  BCG टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls 
$bcg_dates_posts[] = array(
'vaccine' => 'BCG',
'child_id' => $bcg_date->child_unq_id,
'mobile' => $bcg_date->child_contact,
'message' => $bcg_date->child_name.' Wednesday Call for का BCG टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls(It Will Be Friday after Completion Of the Logic )
$bcg_dates_posts[] = array(
'vaccine' => 'BCG',
'child_id' => $bcg_date->child_unq_id,
'mobile' => $bcg_date->child_contact,
'message' => $bcg_date->child_name.' का Thursday Call for BCG टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}
// elseif ($dinnank == "Wed" && $samay >= "16" && $samay < "20") {	 //Wednesday Follow Up Calls From 16-20
// $bcg_dates_posts[] = array(
// 'vaccine' => 'BCG',
// 'child_id' => $bcg_date->child_unq_id,
// 'mobile' => $bcg_date->child_contact,
// 'message' => $bcg_date->child_name.' का Wednesday Follow Up Calls From 16-20 for BCG टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

// );

// }elseif ($dinnank == "Sat" && $samay >= "16" && $samay < "20") {	 //Saturday Follow Up Calls From 16-20
// $bcg_dates_posts[] = array(
// 'vaccine' => 'BCG',
// 'child_id' => $bcg_date->child_unq_id,
// 'mobile' => $bcg_date->child_contact,
// 'message' => $bcg_date->child_name.' का Saturday Follow Up Calls From 16-20 for BCG टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

// );

// }

} // Foreach Loop End 



foreach($opv_o_dates1 as $opv_o_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunaday OutBound Calls 
$opv_dates_posts[] = array(
'vaccine' => 'OPV-0',
'child_id' => $opv_o_date->child_unq_id,
'mobile' => $opv_o_date->child_contact,
'message' => $opv_o_date->child_name.' Sunday For का OPV-0 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls 
$opv_dates_posts[] = array(
'vaccine' => 'OPV-0',
'child_id' => $opv_o_date->child_unq_id,
'mobile' => $opv_o_date->child_contact,
'message' => $opv_o_date->child_name.' का Tuesday for  OPV-0 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls 
$opv_dates_posts[] = array(
'vaccine' => 'OPV-0',
'child_id' => $opv_o_date->child_unq_id,
'mobile' => $opv_o_date->child_contact,
'message' => $opv_o_date->child_name.' Wednesday Call for का OPV-0 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls(It Will Be Friday after Completion Of the Logic )
$opv_dates_posts[] = array(
'vaccine' => 'OPV-0',
'child_id' => $opv_o_date->child_unq_id,
'mobile' => $opv_o_date->child_contact,
'message' => $opv_o_date->child_name.' का Thursday Call for OPV-0 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


} // Foreach Loop End  2



foreach($rvv1_dates as $rvv1_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday

	$rvv1++;


$rvv1_dates_posts[] = array(
'vaccine' => 'RVV1',
'child_id' => $rvv1_date->child_unq_id,
'mobile' => $rvv1_date->child_contact,
'message' => $rvv1_date->child_name.' Sunday Call For का RVV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

$checkrvv1[] = array(
'RVV1' => true,
);

$checkbucket3[$count++] = "RVV1"; 

// $checkbucket3[] = array(
// 'RVV1' => true,
// );


}elseif ($dinnank == "Tue") { //Tuesday OutBound Calls For Wednesday

	$rvv1++;

	 
$rvv1_dates_posts[] = array(
'vaccine' => 'RVV1',
'child_id' => $rvv1_date->child_unq_id,
'mobile' => $rvv1_date->child_contact,
'message' => $rvv1_date->child_name.' Tuesday for  RVV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$rvv1++;

$rvv1_dates_posts[] = array(
'vaccine' => 'RVV1',
'child_id' => $rvv1_date->child_unq_id,
'mobile' => $rvv1_date->child_contact,
'message' => $rvv1_date->child_name.' Wednesday Call for का RVV1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	

$rvv1++;

 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$rvv1_dates_posts[] = array(
'vaccine' => 'RVV1',
'child_id' => $rvv1_date->child_unq_id,
'mobile' => $rvv1_date->child_contact,
'message' => $rvv1_date->child_name.' का Thursday Call for RVV1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


} // Foreach Loop End 3


foreach($ipv1_dates as $ipv1_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$ipv1++;
$ipv1_dates_posts[] = array(
'vaccine' => 'IPV1',
'child_id' => $ipv1_date->child_unq_id,
'mobile' => $ipv1_date->child_contact,
'message' => $ipv1_date->child_name.' Sunday Call For का IPV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);


$checkbucket3[$count++] = "IPV1";

// $checkbucket3[] = array(
// 'IPV1' => true,
// );

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$ipv1++;
$ipv1_dates_posts[] = array(
'vaccine' => 'IPV1',
'child_id' => $ipv1_date->child_unq_id,
'mobile' => $ipv1_date->child_contact,
'message' => $ipv1_date->child_name.'  Tuesday for  IPV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$ipv1++;
$ipv1_dates_posts[] = array(
'vaccine' => 'IPV1',
'child_id' => $ipv1_date->child_unq_id,
'mobile' => $ipv1_date->child_contact,
'message' => $ipv1_date->child_name.'  Wednesday Call for का IPV1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$ipv1++;
$ipv1_dates_posts[] = array(
'vaccine' => 'IPV1',
'child_id' => $ipv1_date->child_unq_id,
'mobile' => $ipv1_date->child_contact,
'message' => $ipv1_date->child_name.'  Thursday Call for IPV1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


} // Foreach Loop End 4


foreach($opv1_dates as $opv1_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$opv1++;
$opv1_dates_posts[] = array(
'vaccine' => 'OPV1',
'child_id' => $opv1_date->child_unq_id,
'mobile' => $opv1_date->child_contact,
'message' => $opv1_date->child_name.' Sunday Call For का OPV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$opv1++;
$opv1_dates_posts[] = array(
'vaccine' => 'OPV1',
'child_id' => $opv1_date->child_unq_id,
'mobile' => $opv1_date->child_contact,
'message' => $opv1_date->child_name.' Tuesday for  OPV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$opv1++;
$opv1_dates_posts[] = array(
'vaccine' => 'OPV1',
'child_id' => $opv1_date->child_unq_id,
'mobile' => $opv1_date->child_contact,
'message' => $opv1_date->child_name.' Wednesday Call for का OPV1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$opv1++;
$opv1_dates_posts[] = array(
'vaccine' => 'OPV1',
'child_id' => $opv1_date->child_unq_id,
'mobile' => $opv1_date->child_contact,
'message' => $opv1_date->child_name.' Thursday Call for OPV1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


} // Foreach Loop End 5



foreach($dpt1_dates as $dpt1_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$dpt1++;
$dpt1_dates_posts[] = array(
'vaccine' => 'DPT1',
'child_id' => $dpt1_date->child_unq_id,
'mobile' => $dpt1_date->child_contact,
'message' => $dpt1_date->child_name.' DPT1 टीका  का  Sunday Call For  टीकाटीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$dpt1++;
$dpt1_dates_posts[] = array(
'vaccine' => 'DPT1',
'child_id' => $dpt1_date->child_unq_id,
'mobile' => $dpt1_date->child_contact,
'message' => $dpt1_date->child_name.' DPT1 Tuesday for  IPV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$dpt1++;
$dpt1_dates_posts[] = array(
'vaccine' => 'DPT1',
'child_id' => $dpt1_date->child_unq_id,
'mobile' => $dpt1_date->child_contact,
'message' => $dpt1_date->child_name.' DPT1 Call for का IPV1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$dpt1++;
$dpt1_dates_posts[] = array(
'vaccine' => 'DPT1',
'child_id' => $dpt1_date->child_unq_id,
'mobile' => $dpt1_date->child_contact,
'message' => $dpt1_date->child_name.' DPT1 Call for  टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


} // Foreach Loop End 6



foreach($hepb1_dates as $hepb1_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$hepb1++;
$hepb1_dates_posts[] = array(
'vaccine' => 'HepB1',
'child_id' => $hepb1_date->child_unq_id,
'mobile' => $hepb1_date->child_contact,
'message' => $hepb1_date->child_name.' HEPB1 टीका का  Sunday Call For  टीकाटीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$hepb1++;
$hepb1_dates_posts[] = array(
'vaccine' => 'HepB1',
'child_id' => $hepb1_date->child_unq_id,
'mobile' => $hepb1_date->child_contact,
'message' => $hepb1_date->child_name.' HEPB1 Tuesday for  IPV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$hepb1++;
$hepb1_dates_posts[] = array(
'vaccine' => 'HepB1',
'child_id' => $hepb1_date->child_unq_id,
'mobile' => $hepb1_date->child_contact,
'message' => $hepb1_date->child_name.' HEPB1 Call for का IPV1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$hepb1++;
$hepb1_dates_posts[] = array(
'vaccine' => 'HepB1',
'child_id' => $hepb1_date->child_unq_id,
'mobile' => $hepb1_date->child_contact,
'message' => $hepb1_date->child_name.' HEPB1 Call for टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


} // Foreach Loop End 7


foreach($penta1_dates as $penta1_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$penta1++;
$penta1_dates_posts[] = array(
'vaccine' => 'Penta1',
'child_id' => $penta1_date->child_unq_id,
'mobile' => $penta1_date->child_contact,
'message' => $penta1_date->child_name.' Penta1 टीका का  Sunday Call For टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$penta1++;
$penta1_dates_posts[] = array(
'vaccine' => 'Penta1',
'child_id' => $penta1_date->child_unq_id,
'mobile' => $penta1_date->child_contact,
'message' => $penta1_date->child_name.' Penta1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$penta1++;
$penta1_dates_posts[] = array(
'vaccine' => 'Penta1',
'child_id' => $penta1_date->child_unq_id,
'mobile' => $penta1_date->child_contact,
'message' => $penta1_date->child_name.' Penta1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$penta1++;
$penta1_dates_posts[] = array(
'vaccine' => 'Penta1',
'child_id' => $penta1_date->child_unq_id,
'mobile' => $penta1_date->child_contact,
'message' => $penta1_date->child_name.' Penta1 Call for टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


} // Foreach Loop End 8



//Bucket 4 starts ========================================

foreach($rvv2_dates as $rvv2_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$rvv2_dates_posts[] = array(
'vaccine' => 'RVV2',
'child_id' => $rvv2_date->child_unq_id,
'mobile' => $rvv2_date->child_contact,
'message' => $rvv2_date->child_name.' RVV2 Sunday Call For  टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$rvv2_dates_posts[] = array(
'vaccine' => 'RVV2',
'child_id' => $rvv2_date->child_unq_id,
'mobile' => $rvv2_date->child_contact,
'message' => $rvv2_date->child_name.' RVV2 Tuesday for टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$rvv2_dates_posts[] = array(
'vaccine' => 'RVV2',
'child_id' => $rvv2_date->child_unq_id,
'mobile' => $rvv2_date->child_contact,
'message' => $rvv2_date->child_name.' RVV2 Wednesday Call for  टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$rvv2_dates_posts[] = array(
'vaccine' => 'RVV2',
'child_id' => $rvv2_date->child_unq_id,
'mobile' => $rvv2_date->child_contact,
'message' => $rvv2_date->child_name.' RVV2 Thursday Call for टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


} // Foreach Loop End 9





foreach($opv2_dates as $opv2_date) {  // Foreach Loop Starts 10

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$opv2_dates_posts[] = array(
'vaccine' => 'OPV2',
'child_id' => $opv2_date->child_unq_id,
'mobile' => $opv2_date->child_contact,
'message' => $opv2_date->child_name.' OPV2 टीका Sunday Call For टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$opv2_dates_posts[] = array(
'vaccine' => 'OPV2',
'child_id' => $opv2_date->child_unq_id,
'mobile' => $opv2_date->child_contact,
'message' => $opv2_date->child_name.' OPV2 टीका Tuesday for  टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$opv2_dates_posts[] = array(
'vaccine' => 'OPV2',
'child_id' => $opv2_date->child_unq_id,
'mobile' => $opv2_date->child_contact,
'message' => $opv2_date->child_name.' OPV2 टीका Wednesday Call for का  टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$opv2_dates_posts[] = array(
'vaccine' => 'OPV2',
'child_id' => $opv2_date->child_unq_id,
'mobile' => $opv2_date->child_contact,
'message' => $opv2_date->child_name.' OPV2 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


} // Foreach Loop End 10



foreach($dpt2_dates as $dpt2_date) {  // Foreach Loop Starts  11

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$dpt2_dates_posts[] = array(
'vaccine' => 'DPT2',
'child_id' => $dpt2_date->child_unq_id,
'mobile' => $dpt2_date->child_contact,
'message' => $dpt2_date->child_name.' DPT2 टीका  का  Sunday Call For  टीकाटीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$dpt2_dates_posts[] = array(
'vaccine' => 'DPT2',
'child_id' => $dpt2_date->child_unq_id,
'mobile' => $dpt2_date->child_contact,
'message' => $dpt2_date->child_name.' DPT2  Tuesday for  IPV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$dpt2_dates_posts[] = array(
'vaccine' => 'DPT2',
'child_id' => $dpt2_date->child_unq_id,
'mobile' => $dpt2_date->child_contact,
'message' => $dpt2_date->child_name.' DPT2  Call for टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$dpt2_dates_posts[] = array(
'vaccine' => 'DPT2',
'child_id' => $dpt2_date->child_unq_id,
'mobile' => $dpt2_date->child_contact,
'message' => $dpt2_date->child_name.' DPT2  टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


} // Foreach Loop End 11



foreach($hepb2_dates as $hepb2_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$hepb2_dates_posts[] = array(
'vaccine' => 'HepB2',
'child_id' => $hepb2_date->child_unq_id,
'mobile' => $hepb2_date->child_contact,
'message' => $hepb2_date->child_name.' HEPB2 टीका का  Sunday Call For  टीकाटीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$hepb2_dates_posts[] = array(
'vaccine' => 'HepB2',
'child_id' => $hepb2_date->child_unq_id,
'mobile' => $hepb2_date->child_contact,
'message' => $hepb2_date->child_name.' HEPB2 Tuesday for टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$hepb2_dates_posts[] = array(
'vaccine' => 'HepB2',
'child_id' => $hepb2_date->child_unq_id,
'mobile' => $hepb2_date->child_contact,
'message' => $hepb2_date->child_name.' HEPB2 Call for का  टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$hepb2_dates_posts[] = array(
'vaccine' => 'HepB2',
'child_id' => $hepb2_date->child_unq_id,
'mobile' => $hepb2_date->child_contact,
'message' => $hepb2_date->child_name.' HEPB2 Call for टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


} // Foreach Loop End 12


foreach($penta2_dates as $penta2_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$penta2_dates_posts[] = array(
'vaccine' => 'Penta2',
'child_id' => $penta2_date->child_unq_id,
'mobile' => $penta2_date->child_contact,
'message' => $penta2_date->child_name.' Penta2 टीका का  Sunday Call For टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$penta2_dates_posts[] = array(
'vaccine' => 'Penta2',
'child_id' => $penta2_date->child_unq_id,
'mobile' => $penta2_date->child_contact,
'message' => $penta2_date->child_name.' Penta2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$penta2_dates_posts[] = array(
'vaccine' => 'Penta2',
'child_id' => $penta2_date->child_unq_id,
'mobile' => $penta2_date->child_contact,
'message' => $penta2_date->child_name.' Penta2 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$penta2_dates_posts[] = array(
'vaccine' => 'Penta2',
'child_id' => $penta2_date->child_unq_id,
'mobile' => $penta2_date->child_contact,
'message' => $penta2_date->child_name.' Penta2 Call for टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


} // Foreach Loop End 13



// Bucket 4 Ends Here


// Bucket 5 Starts Here 

foreach($rvv3_dates as $rvv3_date) {  // Foreach Loop Starts  14

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$rvv3_dates_posts[] = array(
'vaccine' => 'RVV3',
'child_id' => $rvv3_date->child_unq_id,
'mobile' => $rvv3_date->child_contact,
'message' => $rvv3_date->child_name.' Sunday Call For का RVV3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$rvv3_dates_posts[] = array(
'vaccine' => 'RVV3',
'child_id' => $rvv3_date->child_unq_id,
'mobile' => $rvv3_date->child_contact,
'message' => $rvv3_date->child_name.' Tuesday for  RVV3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$rvv3_dates_posts[] = array(
'vaccine' => 'RVV3',
'child_id' => $rvv3_date->child_unq_id,
'mobile' => $rvv3_date->child_contact,
'message' => $rvv3_date->child_name.' Wednesday Call for का RVV3 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$rvv3_dates_posts[] = array(
'vaccine' => 'RVV3',
'child_id' => $rvv3_date->child_unq_id,
'mobile' => $rvv3_date->child_contact,
'message' => $rvv3_date->child_name.' Thursday Call for RVV3 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


} // Foreach Loop End 14


foreach($ipv2_dates as $ipv2_date) {  // Foreach Loop Starts 15

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$ipv2_dates_posts[] = array(
'vaccine' => 'IPV2',
'child_id' => $ipv2_date->child_unq_id,
'mobile' => $ipv2_date->child_contact,
'message' => $ipv2_date->child_name.' Sunday Call For का IPV2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$ipv2_dates_posts[] = array(
'vaccine' => 'IPV2',
'child_id' => $ipv2_date->child_unq_id,
'mobile' => $ipv2_date->child_contact,
'message' => $ipv2_date->child_name.' Tuesday for  IPV2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$ipv2_dates_posts[] = array(
'vaccine' => 'IPV2',
'child_id' => $ipv2_date->child_unq_id,
'mobile' => $ipv2_date->child_contact,
'message' => $ipv2_date->child_name.'  Wednesday Call for का IPV1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$ipv2_dates_posts[] = array(
'vaccine' => 'IPV2',
'child_id' => $ipv2_date->child_unq_id,
'mobile' => $ipv2_date->child_contact,
'message' => $ipv2_date->child_name.' Thursday Call for IPV1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


} // Foreach Loop End 15


foreach($opv3_dates as $opv3_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$opv3_dates_posts[] = array(
'vaccine' => 'OPV3',
'child_id' => $opv3_date->child_unq_id,
'mobile' => $opv3_date->child_contact,
'message' => $opv3_date->child_name.' OPV3 Sunday Call For  टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$opv3_dates_posts[] = array(
'vaccine' => 'OPV3',
'child_id' => $opv3_date->child_unq_id,
'mobile' => $opv3_date->child_contact,
'message' => $opv3_date->child_name.' OPV3 Tuesday for  OPV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$opv3_dates_posts[] = array(
'vaccine' => 'OPV3',
'child_id' => $opv3_date->child_unq_id,
'mobile' => $opv3_date->child_contact,
'message' => $opv3_date->child_name.' OPV3 Wednesday Call for का OPV1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$opv3_dates_posts[] = array(
'vaccine' => 'OPV3',
'child_id' => $opv3_date->child_unq_id,
'mobile' => $opv3_date->child_contact,
'message' => $opv3_date->child_name.' OPV3 Thursday Call for OPV1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


} // Foreach Loop End 16



foreach($dpt3_dates as $dpt3_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$dpt3_dates_posts[] = array(
'vaccine' => 'DPT3',
'child_id' => $dpt3_date->child_unq_id,
'mobile' => $dpt3_date->child_contact,
'message' => $dpt3_date->child_name.' DPT3 टीका  का  Sunday Call For  टीकाटीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$dpt3_dates_posts[] = array(
'vaccine' => 'DPT3',
'child_id' => $dpt3_date->child_unq_id,
'mobile' => $dpt3_date->child_contact,
'message' => $dpt3_date->child_name.' DPT3 Tuesday for  IPV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$dpt3_dates_posts[] = array(
'vaccine' => 'DPT3',
'child_id' => $dpt3_date->child_unq_id,
'mobile' => $dpt3_date->child_contact,
'message' => $dpt3_date->child_name.' DPT3 Call for का IPV1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$dpt3_dates_posts[] = array(
'vaccine' => 'DPT3',
'child_id' => $dpt3_date->child_unq_id,
'mobile' => $dpt3_date->child_contact,
'message' => $dpt3_date->child_name.' DPT3 Call for  टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


} // Foreach Loop End 17



foreach($hepb3_dates as $hepb3_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$hepb3_dates_posts[] = array(
'vaccine' => 'HepB3',
'child_id' => $hepb3_date->child_unq_id,
'mobile' => $hepb3_date->child_contact,
'message' => $hepb3_date->child_name.' HEPB3 टीका का  Sunday Call For  टीकाटीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$hepb3_dates_posts[] = array(
'vaccine' => 'HepB3',
'child_id' => $hepb3_date->child_unq_id,
'mobile' => $hepb3_date->child_contact,
'message' => $hepb3_date->child_name.' HEPB3 Tuesday for टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$hepb3_dates_posts[] = array(
'vaccine' => 'HepB3',
'child_id' => $hepb3_date->child_unq_id,
'mobile' => $hepb3_date->child_contact,
'message' => $hepb3_date->child_name.' HEPB3 Call for का IPV1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$hepb3_dates_posts[] = array(
'vaccine' => 'HepB3',
'child_id' => $hepb3_date->child_unq_id,
'mobile' => $hepb3_date->child_contact,
'message' => $hepb3_date->child_name.' HEPB3 Call for टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


} // Foreach Loop End 18


foreach($penta3_dates as $penta3_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$penta3_dates_posts[] = array(
'vaccine' => 'Penta3',
'child_id' => $penta3_date->child_unq_id,
'mobile' => $penta3_date->child_contact,
'message' => $penta3_date->child_name.' Penta3 टीका का  Sunday Call For टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$penta3_dates_posts[] = array(
'vaccine' => 'Penta3',
'child_id' => $penta3_date->child_unq_id,
'mobile' => $penta3_date->child_contact,
'message' => $penta3_date->child_name.' Penta3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$penta3_dates_posts[] = array(
'vaccine' => 'Penta3',
'child_id' => $penta3_date->child_unq_id,
'mobile' => $penta3_date->child_contact,
'message' => $penta3_date->child_name.' Penta3 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$penta3_dates_posts[] = array(
'vaccine' => 'Penta3',
'child_id' => $penta3_date->child_unq_id,
'mobile' => $penta3_date->child_contact,
'message' => $penta3_date->child_name.' Penta3 Call for टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


} // Foreach Loop End 19




// Bucket 5 Ends Here 




// Bucket 6 Starts Here 

foreach($mmr_dates as $mmr_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$mmr_dates_posts[] = array(
'vaccine' => 'MMR',
'child_id' => $mmr_date->child_unq_id,
'mobile' => $mmr_date->child_contact,
'message' => $mmr_date->child_name.' MMR टीका  का  Sunday Call For  टीकाटीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$mmr_dates_posts[] = array(
'vaccine' => 'MMR',
'child_id' => $mmr_date->child_unq_id,
'mobile' => $mmr_date->child_contact,
'message' => $mmr_date->child_name.' MMR Tuesday for  IPV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$mmr_dates_posts[] = array(
'vaccine' => 'MMR',
'child_id' => $mmr_date->child_unq_id,
'mobile' => $mmr_date->child_contact,
'message' => $mmr_date->child_name.' MMR Call for का IPV1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$mmr_dates_posts[] = array(
'vaccine' => 'MMR',
'child_id' => $mmr_date->child_unq_id,
'mobile' => $mmr_date->child_contact,
'message' => $mmr_date->child_name.' MMR Call for  टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


} // Foreach Loop End 17



foreach($vit_a_1_dates as $vit_a_1_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$vit_a_1_dates_posts[] = array(
'vaccine' => 'VITA1',
'child_id' => $vit_a_1_date->child_unq_id,
'mobile' => $vit_a_1_date->child_contact,
'message' => $vit_a_1_date->child_name.' VITAMIN A 1 टीका का  Sunday Call For  टीकाटीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$vit_a_1_dates_posts[] = array(
'vaccine' => 'VITA1',
'child_id' => $vit_a_1_date->child_unq_id,
'mobile' => $vit_a_1_date->child_contact,
'message' => $vit_a_1_date->child_name.' VITAMIN A 1 Tuesday for टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$vit_a_1_dates_posts[] = array(
'vaccine' => 'VITA1',
'child_id' => $vit_a_1_date->child_unq_id,
'mobile' => $vit_a_1_date->child_contact,
'message' => $vit_a_1_date->child_name.' VITAMIN A 1 Call for का IPV1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$vit_a_1_dates_posts[] = array(
'vaccine' => 'VITA1',
'child_id' => $vit_a_1_date->child_unq_id,
'mobile' => $vit_a_1_date->child_contact,
'message' => $vit_a_1_date->child_name.' VITAMIN A 1 Call for टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


} // Foreach Loop End 18


foreach($je1_dates as $je1_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$je1_dates_posts[] = array(
'vaccine' => 'JE1',
'child_id' => $je1_date->child_unq_id,
'mobile' => $je1_date->child_contact,
'message' => $je1_date->child_name.' JE1 टीका का  Sunday Call For टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$je1_dates_posts[] = array(
'vaccine' => 'JE1',
'child_id' => $je1_date->child_unq_id,
'mobile' => $je1_date->child_contact,
'message' => $je1_date->child_name.' JE1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$je1_dates_posts[] = array(
'vaccine' => 'JE1',
'child_id' => $je1_date->child_unq_id,
'mobile' => $je1_date->child_contact,
'message' => $je1_date->child_name.' JE1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$je1_dates_posts[] = array(
'vaccine' => 'JE1',
'child_id' => $je1_date->child_unq_id,
'mobile' => $je1_date->child_contact,
'message' => $je1_date->child_name.' JE1 Call for टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


}

// Bucket 6 Ends Here

// Bucket 7 Starts Here


foreach($dpt1_booster_dates as $dpt1_booster_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$dpt1_booster_dates_posts[] = array(
'vaccine' => 'DPT1 Booster',
'child_id' => $dpt1_booster_date->child_unq_id,
'mobile' => $dpt1_booster_date->child_contact,
'message' => $dpt1_booster_date->child_name.' DPT1 Booster टीका  का  Sunday Call For  टीकाटीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$dpt1_booster_dates_posts[] = array(
'vaccine' => 'DPT1 Booster',
'child_id' => $dpt1_booster_date->child_unq_id,
'mobile' => $dpt1_booster_date->child_contact,
'message' => $dpt1_booster_date->child_name.' DPT1 Booster Tuesday for  टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$dpt1_booster_dates_posts[] = array(
'vaccine' => 'DPT1 Booster',
'child_id' => $dpt1_booster_date->child_unq_id,
'mobile' => $dpt1_booster_date->child_contact,
'message' => $dpt1_booster_date->child_name.' DPT1 Booster Call for  टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$dpt1_booster_dates_posts[] = array(
'vaccine' => 'DPT1 Booster',
'child_id' => $dpt1_booster_date->child_unq_id,
'mobile' => $dpt1_booster_date->child_contact,
'message' => $dpt1_booster_date->child_name.' DPT1 Booster Call for  टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


} // Foreach Loop End 17



foreach($mmr2_dates as $mmr2_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$mmr2_dates_posts[] = array(
'vaccine' => 'MMR2',
'child_id' => $mmr2_date->child_unq_id,
'mobile' => $mmr2_date->child_contact,
'message' => $mmr2_date->child_name.' MMR2 टीका का  Sunday Call For टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$mmr2_dates_posts[] = array(
'vaccine' => 'MMR2',
'child_id' => $mmr2_date->child_unq_id,
'mobile' => $mmr2_date->child_contact,
'message' => $mmr2_date->child_name.' MMR2 Tuesday for टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$mmr2_dates_posts[] = array(
'vaccine' => 'MMR2',
'child_id' => $mmr2_date->child_unq_id,
'mobile' => $mmr2_date->child_contact,
'message' => $mmr2_date->child_name.' MMR2 Call for  टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$mmr2_dates_posts[] = array(
'vaccine' => 'MMR2',
'child_id' => $mmr2_date->child_unq_id,
'mobile' => $mmr2_date->child_contact,
'message' => $mmr2_date->child_name.' MMR2 Call for टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


} // Foreach Loop End 18


foreach($opv_booster_dates as $opv_booster_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$opv_booster_dates[] = array(
'vaccine' => 'OPV_BOOSTER',
'child_id' => $opv_booster_date->child_unq_id,
'mobile' => $opv_booster_date->child_contact,
'message' => $opv_booster_date->child_name.' OPV_BOOSTER टीका का  Sunday Call For टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$opv_booster_dates[] = array(
'vaccine' => 'OPV_BOOSTER',
'child_id' => $opv_booster_date->child_unq_id,
'mobile' => $opv_booster_date->child_contact,
'message' => $opv_booster_date->child_name.' OPV_BOOSTER टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$opv_booster_dates[] = array(
'vaccine' => 'OPV_BOOSTER',
'child_id' => $opv_booster_date->child_unq_id,
'mobile' => $opv_booster_date->child_contact,
'message' => $opv_booster_date->child_name.' OPV_BOOSTER टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$opv_booster_dates[] = array(
'vaccine' => 'OPV_BOOSTER',
'child_id' => $opv_booster_date->child_unq_id,
'mobile' => $opv_booster_date->child_contact,
'message' => $opv_booster_date->child_name.' OPV_BOOSTER Call for टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


}


foreach($je2_dates as $je2_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$je2_dates_posts[] = array(
'vaccine' => 'JE2',
'child_id' => $je2_date->child_unq_id,
'mobile' => $je2_date->child_contact,
'message' => $je2_date->child_name.' JE2 टीका का  Sunday Call For टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$je2_dates_posts[] = array(
'vaccine' => 'JE2',
'child_id' => $je2_date->child_unq_id,
'mobile' => $je2_date->child_contact,
'message' => $je2_date->child_name.' JE2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$je2_dates_posts[] = array(
'vaccine' => 'JE2',
'child_id' => $je2_date->child_unq_id,
'mobile' => $je2_date->child_contact,
'message' => $je2_date->child_name.' JE2 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$je2_dates_posts[] = array(
'vaccine' => 'JE2',
'child_id' => $je2_date->child_unq_id,
'mobile' => $je2_date->child_contact,
'message' => $je2_date->child_name.' JE2 Call for टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


}



// Bucket 7 Ends Here

// Bucket 8 Starts Here

foreach($vit_a_2_dates as $vit_a_2_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$vit_a_2_dates_posts[] = array(
'vaccine' => 'VITA2',
'child_id' => $vit_a_2_date->child_unq_id,
'mobile' => $vit_a_2_date->child_contact,
'message' => $vit_a_2_date->child_name.' VITAMIN A 2 टीका का  Sunday Call For  टीकाटीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$vit_a_2_dates_posts[] = array(
'vaccine' => 'VITA2',
'child_id' => $vit_a_2_date->child_unq_id,
'mobile' => $vit_a_2_date->child_contact,
'message' => $vit_a_2_date->child_name.' VITAMIN A 2 Tuesday for टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$vit_a_2_dates_posts[] = array(
'vaccine' => 'VITA2',
'child_id' => $vit_a_2_date->child_unq_id,
'mobile' => $vit_a_2_date->child_contact,
'message' => $vit_a_2_date->child_name.' VITAMIN A 2 Call for का IPV1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$vit_a_2_dates_posts[] = array(
'vaccine' => 'VITA2',
'child_id' => $vit_a_2_date->child_unq_id,
'mobile' => $vit_a_2_date->child_contact,
'message' => $vit_a_2_date->child_name.' VITAMIN A 2 Call for टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


}



foreach($vit_a_3_dates as $vit_a_3_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$vit_a_3_dates_posts[] = array(
'vaccine' => 'VITA3',
'child_id' => $vit_a_3_date->child_unq_id,
'mobile' => $vit_a_3_date->child_contact,
'message' => $vit_a_3_date->child_name.' VITAMIN A 3 टीका का  Sunday Call For  टीकाटीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$vit_a_3_dates_posts[] = array(
'vaccine' => 'VITA3',
'child_id' => $vit_a_3_date->child_unq_id,
'mobile' => $vit_a_3_date->child_contact,
'message' => $vit_a_3_date->child_name.' VITAMIN A 3 Tuesday for टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$vit_a_3_dates_posts[] = array(
'vaccine' => 'VITA3',
'child_id' => $vit_a_3_date->child_unq_id,
'mobile' => $vit_a_3_date->child_contact,
'message' => $vit_a_3_date->child_name.' VITAMIN A 3 Call for का IPV1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$vit_a_3_dates_posts[] = array(
'vaccine' => 'VITA3',
'child_id' => $vit_a_3_date->child_unq_id,
'mobile' => $vit_a_3_date->child_contact,
'message' => $vit_a_3_date->child_name.' VITAMIN A 3 Call for टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


}




foreach($vit_a_4_dates as $vit_a_4_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$vit_a_4_dates_posts[] = array(
'vaccine' => 'VITA4',
'child_id' => $vit_a_4_date->child_unq_id,
'mobile' => $vit_a_4_date->child_contact,
'message' => $vit_a_4_date->child_name.' VITAMIN A 4 टीका का  Sunday Call For  टीकाटीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$vit_a_4_dates_posts[] = array(
'vaccine' => 'VITA4',
'child_id' => $vit_a_4_date->child_unq_id,
'mobile' => $vit_a_4_date->child_contact,
'message' => $vit_a_4_date->child_name.' VITAMIN A 4 Tuesday for टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$vit_a_4_dates_posts[] = array(
'vaccine' => 'VITA4',
'child_id' => $vit_a_4_date->child_unq_id,
'mobile' => $vit_a_4_date->child_contact,
'message' => $vit_a_4_date->child_name.' VITAMIN A 4 Call for का IPV1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$vit_a_4_dates_posts[] = array(
'vaccine' => 'VITA4',
'child_id' => $vit_a_4_date->child_unq_id,
'mobile' => $vit_a_4_date->child_contact,
'message' => $vit_a_4_date->child_name.' VITAMIN A 4 Call for टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


}






foreach($vit_a_5_dates as $vit_a_5_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$vit_a_5_dates_posts[] = array(
'vaccine' => 'VITA5',
'child_id' => $vit_a_5_date->child_unq_id,
'mobile' => $vit_a_5_date->child_contact,
'message' => $vit_a_5_date->child_name.' VITAMIN A 5 टीका का  Sunday Call For  टीकाटीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$vit_a_5_dates_posts[] = array(
'vaccine' => 'VITA5',
'child_id' => $vit_a_5_date->child_unq_id,
'mobile' => $vit_a_5_date->child_contact,
'message' => $vit_a_5_date->child_name.' VITAMIN A 5  Tuesday for टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$vit_a_5_dates_posts[] = array(
'vaccine' => 'VITA5',
'child_id' => $vit_a_5_date->child_unq_id,
'mobile' => $vit_a_5_date->child_contact,
'message' => $vit_a_5_date->child_name.' VITAMIN A 5  Call for टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$vit_a_5_dates_posts[] = array(
'vaccine' => 'VITA5',
'child_id' => $vit_a_5_date->child_unq_id,
'mobile' => $vit_a_5_date->child_contact,
'message' => $vit_a_5_date->child_name.' VITAMIN A 5  Call for टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


}




foreach($vit_a_6_dates as $vit_a_6_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$vit_a_6_dates_posts[] = array(
'vaccine' => 'VITA6',
'child_id' => $vit_a_6_date->child_unq_id,
'mobile' => $vit_a_6_date->child_contact,
'message' => $vit_a_6_date->child_name.' VITAMIN A 6 टीका का  Sunday Call For  टीकाटीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$vit_a_6_dates_posts[] = array(
'vaccine' => 'VITA6',
'child_id' => $vit_a_6_date->child_unq_id,
'mobile' => $vit_a_6_date->child_contact,
'message' => $vit_a_6_date->child_name.' VITAMIN A 6 Tuesday for टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$vit_a_6_dates_posts[] = array(
'vaccine' => 'VITA6',
'child_id' => $vit_a_6_date->child_unq_id,
'mobile' => $vit_a_6_date->child_contact,
'message' => $vit_a_6_date->child_name.' VITAMIN A 6 Call for का IPV1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$vit_a_6_dates_posts[] = array(
'vaccine' => 'VITA6',
'child_id' => $vit_a_6_date->child_unq_id,
'mobile' => $vit_a_6_date->child_contact,
'message' => $vit_a_6_date->child_name.' VITAMIN A 6 Call for टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


}


foreach($vit_a_7_dates as $vit_a_7_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$vit_a_7_dates_posts[] = array(
'vaccine' => 'VITA7',
'child_id' => $vit_a_7_date->child_unq_id,
'mobile' => $vit_a_7_date->child_contact,
'message' => $vit_a_7_date->child_name.' VITAMIN A 7 टीका का  Sunday Call For  टीकाटीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$vit_a_7_dates_posts[] = array(
'vaccine' => 'VITA7',
'child_id' => $vit_a_7_date->child_unq_id,
'mobile' => $vit_a_7_date->child_contact,
'message' => $vit_a_7_date->child_name.' VITAMIN A 7 Tuesday for टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$vit_a_7_dates_posts[] = array(
'vaccine' => 'VITA7',
'child_id' => $vit_a_7_date->child_unq_id,
'mobile' => $vit_a_7_date->child_contact,
'message' => $vit_a_7_date->child_name.' VITAMIN A 7 Call for का IPV1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$vit_a_7_dates_posts[] = array(
'vaccine' => 'VITA7',
'child_id' => $vit_a_7_date->child_unq_id,
'mobile' => $vit_a_7_date->child_contact,
'message' => $vit_a_7_date->child_name.' VITAMIN A 7 Call for टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


}




foreach($vit_a_8_dates as $vit_a_8_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$vit_a_8_dates_posts[] = array(
'vaccine' => 'VITA8',
'child_id' => $vit_a_8_date->child_unq_id,
'mobile' => $vit_a_8_date->child_contact,
'message' => $vit_a_8_date->child_name.' VITAMIN A 8 टीका का  Sunday Call For  टीकाटीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$vit_a_8_dates_posts[] = array(
'vaccine' => 'VITA8',
'child_id' => $vit_a_8_date->child_unq_id,
'mobile' => $vit_a_8_date->child_contact,
'message' => $vit_a_8_date->child_name.' VITAMIN A 8 Tuesday for टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$vit_a_8_dates_posts[] = array(
'vaccine' => 'VITA8',
'child_id' => $vit_a_8_date->child_unq_id,
'mobile' => $vit_a_8_date->child_contact,
'message' => $vit_a_8_date->child_name.' VITAMIN A 8 Call for का IPV1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$vit_a_8_dates_posts[] = array(
'vaccine' => 'VITA8',
'child_id' => $vit_a_8_date->child_unq_id,
'mobile' => $vit_a_8_date->child_contact,
'message' => $vit_a_8_date->child_name.' VITAMIN A 8 Call for टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


}



foreach($vit_a_9_dates as $vit_a_9_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$vit_a_9_dates_posts[] = array(
'vaccine' => 'VITA9',
'child_id' => $vit_a_9_date->child_unq_id,
'mobile' => $vit_a_9_date->child_contact,
'message' => $vit_a_9_date->child_name.' VITAMIN A 9 टीका का  Sunday Call For  टीकाटीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$vit_a_9_dates_posts[] = array(
'vaccine' => 'VITA9',
'child_id' => $vit_a_9_date->child_unq_id,
'mobile' => $vit_a_9_date->child_contact,
'message' => $vit_a_9_date->child_name.' VITAMIN A 9 Tuesday for टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$vit_a_9_dates_posts[] = array(
'vaccine' => 'VITA9',
'child_id' => $vit_a_9_date->child_unq_id,
'mobile' => $vit_a_9_date->child_contact,
'message' => $vit_a_9_date->child_name.' VITAMIN A 9 Call for का IPV1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 //Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$vit_a_9_dates_posts[] = array(
'vaccine' => 'VITA9',
'child_id' => $vit_a_9_date->child_unq_id,
'mobile' => $vit_a_9_date->child_contact,
'message' => $vit_a_9_date->child_name.' VITAMIN A 9 Call for टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


}



// Bucket 8 Ends Here

// Bucket 9 Starts Here



foreach($dpt2_booster_dates as $dpt2_booster_date) {  // Foreach Loop Starts 

if ($dinnank == "Mon") {	 //Sunday OutBound Calls  For Wednesday
$dpt2_booster_dates_posts[] = array(
'vaccine' => 'DPT 2 BOOSTER',
'child_id' => $dpt2_booster_date->child_unq_id,
'mobile' => $dpt2_booster_date->child_contact,
'message' => $dpt2_booster_date->child_name.' DPT 2 BOOSTER टीका का  Sunday Call For  टीकाटीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Tue") {	 //Tuesday OutBound Calls For Wednesday
$dpt2_booster_dates_posts[] = array(
'vaccine' => 'DPT 2 BOOSTER',
'child_id' => $dpt2_booster_date->child_unq_id,
'mobile' => $dpt2_booster_date->child_contact,
'message' => $dpt2_booster_date->child_name.' DPT 2 BOOSTER Tuesday for टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed") {	//Wednesday OutBound Calls For Saturday
$dpt2_booster_dates_posts[] = array(
'vaccine' => 'DPT 2 BOOSTER',
'child_id' => $dpt2_booster_date->child_unq_id,
'mobile' => $dpt2_booster_date->child_contact,
'message' => $dpt2_booster_date->child_name.' DPT 2 BOOSTER Call for का IPV1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Fri") {	 
//Friday OutBound Calls For Saturday (It Will Be Friday after Completion Of the Logic )
$dpt2_booster_dates_posts[] = array(
'vaccine' => 'DPT 2 BOOSTER',
'child_id' => $dpt2_booster_date->child_unq_id,
'mobile' => $dpt2_booster_date->child_contact,
'message' => $dpt2_booster_date->child_name.' DPT 2 BOOSTER Call for टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


}

// Bucket 9 Ends Here



// ==========================22 August ======================

// echo "RVV1-".$rvv1."\n";
// echo "RVV2-".$rvv2."\n";
// echo "RVV3-".$rvv3."\n";
// echo "OPV1-".$opv1."\n";
// echo "OPV2-".$opv2."\n";
// echo "OPV3-".$opv3."\n";
// echo "DPT1-".$dpt1."\n";
// echo "DPT2-".$dpt2."\n";
// echo "DPT3-".$dpt3."\n";
// echo "Hepb1-".$hepb1."\n";
// echo "Hepb2-".$hepb2."\n";
// echo "Hepb3-".$hepb3."\n";
// echo "IPV1-".$ipv1."\n";
// echo "IPV2-".$ipv2."\n";
// echo "PENTA1-".$penta1."\n";
// echo "PENTA2-".$penta2."\n";
// echo "PENTA3-".$penta3."\n";
// echo "MMR1-".$mmr."\n";
// echo "MMR2-".$mmr2."\n";
// echo "JE1-".$je1."\n";
// echo "JE2-".$je2."\n";

// echo "VIT_A_1-".$vit_a_1."\n";
// echo "VIT_A_2-".$vit_a_2."\n";
// echo "VIT_A_3-".$vit_a_3."\n";
// echo "VIT_A_4-".$vit_a_4."\n";
// echo "VIT_A_5-".$vit_a_5."\n";
// echo "VIT_A_6-".$vit_a_6."\n";
// echo "VIT_A_7-".$vit_a_7."\n";
// echo "VIT_A_8-".$vit_a_8."\n";
// echo "VIT_A_9-".$vit_a_9."\n";

// echo "OPV_BOOSTER-".$opv_booster."\n";

// echo "DPT1_BOOSTER-".$dpt1_booster."\n";
// echo "DPT2_BOOSTER-".$dpt2_booster."\n";



// $total_array = array_merge($bcg_dates_posts,$opv_dates_posts,$opv1_dates_posts,$opv2_dates_posts,$opv3_dates_posts,$dpt1_dates_posts,$dpt2_dates_posts,$dpt3_dates_posts,$hepb1_dates_posts,$hepb2_dates_posts,$hepb3_dates_posts,$rvv1_dates_posts,$rvv2_dates_posts,$rvv3_dates_posts,$ipv1_dates_posts,$ipv2_dates_posts,$penta1_dates_posts,$penta2_dates_posts,$penta3_dates_posts,$mmr_dates_posts,$mmr2_dates_posts,$je1_dates_posts,$je2_dates_posts,$vit_a_1_dates_posts,$vit_a_2_dates_posts,$vit_a_3_dates_posts,$vit_a_4_dates_posts,$vit_a_5_dates_posts,$vit_a_6_dates_posts,$vit_a_7_dates_posts,$vit_a_8_dates_posts,$vit_a_9_dates_posts,$opv_booster_dates_posts,$je1_dates_posts,$je2_dates_posts,$dpt1_booster_dates_posts,$dpt2_booster_dates_posts);
//========================== 21 August ================================================

//print_r($checkbucket3); 

// $checkrvv1 = json_decode($checkrvv1);

// print_r($checkrvv1);

// if($isValidToken) {

$total_array = array_merge($rvv1_dates_posts,$checkrvv1,$checkbucket3);



$this->output
->set_status_header(200)
->set_content_type('application/json')
//->set_output(json_encode($rvv1,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
 ->set_output(json_encode($total_array,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)); 
//}
}












public function getOutCallData(){

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: authorization, Content-Type");

$token = $this->input->get_request_header('Authorization');

$isValidToken = $this->api_model->checkToken($token);

// $today = date('Y-m-d');
// $wed = date('Y-m-d', strtotime('next Wednesday'));
// $last_wed = date('Y-m-d', strtotime('last Wednesday'));
// $fri = date('Y-m-d', strtotime('next Friday'));
// $last_fri = date('Y-m-d', strtotime('last Friday'));



 $dinnank = date('D');
$samay = date('H');


if(date('D') == 'Wed') { 
$wed = date('Y-m-d');
}else{
$wed = date('Y-m-d', strtotime('next Wednesday')); }


if(date('D') == 'Sat') { 
$sat = date('Y-m-d');
}else{
$sat = date('Y-m-d', strtotime('next Saturday')); }


$BUCKET2_MESSAGES = array();
$BUCKET3_MESSAGES = array();
$BUCKET4_MESSAGES = array();
$BUCKET5_MESSAGES = array();
$BUCKET6_MESSAGES = array();
$BUCKET7_MESSAGES = array();
$BUCKET8_MESSAGES = array();
$BUCKET9_MESSAGES = array();



 $child_details = $this->api_model->get_child_details();


// START OF CHILD DETAILS FOREACH LOOP


foreach ($child_details as $child_details) {
$child_id = $child_details->child_id;
$bucket2_data = $this->api_model->get_bucket2_dates_id($child_id);
$ocd_child_id = $this->api_model->get_ocd_child_id($child_id);
$atmpt_ocd_child_id = $this->api_model->get_atmpt_ocd_child_id($child_id);
 //print_r(count($atmpt_ocd_child_id));
//  print_r($atmpt_ocd_child_id['0']);
// print_r($atmpt_ocd_child_id['1']);
// print_r($atmpt_ocd_child_id['2']);
// echo $atmpt_ocd_child_id['ocd_child_id'];
$bucket3_data = $this->api_model->get_bucket3_dates_id($child_id);
$bucket4_data = $this->api_model->get_bucket4_dates_id($child_id);
$bucket5_data = $this->api_model->get_bucket5_dates_id($child_id);
$bucket6_data = $this->api_model->get_bucket6_dates_id($child_id);
$bucket7_data = $this->api_model->get_bucket7_dates_id($child_id);
$bucket8_data = $this->api_model->get_bucket8_dates_id($child_id);
$bucket9_data = $this->api_model->get_bucket9_dates_id($child_id);


// ==================+STARTING WITH BUCKET 2 MESSAGES +=============================================

foreach ($bucket2_data as $bucket2_data) {

$OPV_O = $bucket2_data->OPV_O;
$OPV_O_date = $bucket2_data->OPV_O_date;
$OPV_O_last_date = $bucket2_data->OPV_O_last_date;
$child_contact_no = $bucket2_data->child_contact;
$childname = $bucket2_data->child_name;
$mothername = $bucket2_data->mother_name;

if($today >= $OPV_O_date AND $today <= $OPV_O_last_date ){

$bucket2status = array($OPV_O);

 $countbck2 = array_count_values($bucket2status);

 //echo "BUCKET 2 FALSE VALUES = ".$countbck2['false']."\n";
if($countbck2['false']=='1')
{

if($OPV_O=='false'){

	$BUCKET2_MESSAGES[] = array(
"child_id" => $child_id,
"message_for"=> "OPV0",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को ओ पी बी 0 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);
}
}elseif($countbck2['false'] > '1'){


$BUCKET2_MESSAGES[] = array(
"child_id" => $child_id,
"message_for"=> "BUCKET2",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को बकेट 2 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);

}

}

}

// ==================+ENDING FOR BUCKET 2 MESSAGES +=============================================















// ==================+STARTING WITH BUCKET 3 +=============================================

foreach ($bucket3_data as $bucket3_data) {

$RVV1 = $bucket3_data->RVV1;
$RVV1_date = $bucket3_data->RVV1_date;
$RVV1_last_date = $bucket3_data->RVV1_last_date;

$IPV1 = $bucket3_data->IPV1;
$IPV1_date = $bucket3_data->IPV1_date;
$IPV1_last_date = $bucket3_data->IPV1_last_date;

$OPV1 = $bucket3_data->OPV1;
$OPV1_date = $bucket3_data->OPV1_date;
$OPV1_last_date = $bucket3_data->OPV1_last_date;

$DPT1 = $bucket3_data->DPT1;
$DPT1_date = $bucket3_data->DPT1_date;
$DPT1_last_date = $bucket3_data->DPT1_last_date;

$PENTA1 = $bucket3_data->PENTA1;
$PENTA1_date = $bucket3_data->PENTA1_date;
$PENTA1_last_date = $bucket3_data->PENTA1_last_date;

$child_contact_no = $bucket3_data->child_contact;
$childname = $bucket3_data->child_name;
$mothername = $bucket3_data->mother_name;



if($today >= $RVV1_date OR $today <= $RVV1_last_date && $today >= $IPV1_date OR $today <= $IPV1_last_date && $today >= $OPV1_date OR $today <= $OPV1_last_date && $today >= $DPT1_date OR $today <= $DPT1_last_date || $today >= $PENTA1_date OR $today <= $PENTA1_last_date){

$bucket3status = array($RVV1,$IPV1,$OPV1,$DPT1,$PENTA1);

 $countbck3 = array_count_values($bucket3status);

 //echo "FALSE VALUES = ".$countbck3['false']."\n";
if ($dinnank == "Mon" || $dinnank == "Tue"|| $dinnank == "Wed"|| $dinnank == "Thu"|| $dinnank == "Fri"|| $dinnank == "Sat"|| $dinnank == "Sun") {

if($countbck3['false']=='1')
{


if($RVV1=='false'){

	$BUCKET3_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "RVV1",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को आर बी बी 1 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);
}elseif($IPV1=='false'){

	$BUCKET3_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "IPV1",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को आई पी बी 1 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);
}elseif($OPV1=='false'){

	$BUCKET3_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "OPV1",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को ओ पी बी 1 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);
}elseif($DPT1=='false'|| $PENTA1=='false'){

	$BUCKET3_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "DPT-PENTA",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को डी पी टी या पेंटा का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);
}

}elseif($countbck3['false'] > '1'){


	$BUCKET3_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "BUCKET3",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को बकेट 3 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);

}


}




// 	if($RVV1== 'false' && $IPV1== 'false' && $OPV1== 'false' && $DPT1== 'false' || $PENTA1== 'false' ) {

// $BUCKET3_MESSAGES[] = array( 
// 	'vaccine' => 'DPT 2 BOOSTER',
// 	'Message' => "BUCKET 3 KA tika AJ K DATE PE H" );

// }elseif($RVV1== 'true' && $IPV1== 'false' && $OPV1== 'false' && $DPT1== 'false' || $PENTA1== 'false' ) {

// $BUCKET3_MESSAGES[] = array( 
// 	'vaccine' => 'DPT 2 BOOSTER',
// 	'Message' => "BUCKET 3 KA tika AJ K DATE PE H" );

// }elseif($RVV1== 'true' && $IPV1== 'true' && $OPV1== 'false' && $DPT1== 'false' || $PENTA1== 'false' ) {

// $BUCKET3_MESSAGES[] = array( 
// 	'vaccine' => 'DPT 2 BOOSTER',
// 	'Message' => "BUCKET 3 KA tika AJ K DATE PE H" );

// }elseif($RVV1== 'true' && $IPV1== 'true' && $OPV1== 'true' && $DPT1== 'false' || $PENTA1== 'false' ) {

// $BUCKET3_MESSAGES[] = array( 
// 	'vaccine' => 'DPT 2 BOOSTER',
// 	'Message' => "BUCKET 3 KA tika AJ K DATE PE H" );

// }elseif($RVV1== 'true' && $IPV1== 'true' && $OPV1== 'true' && $DPT1== 'false' || $PENTA1== 'false' ) {

// $BUCKET3_MESSAGES[] = array( 
// 	'vaccine' => 'DPT 2 BOOSTER',
// 	'Message' => "BUCKET 3 KA tika AJ K DATE PE H" );

// }






}

// ==================+ENDING FOR BUCKET 3 MESSAGES +=============================================

//print_r($bucket4_data);
// ==================+STARTING WITH BUCKET 4 MESSAGES +=============================================

foreach ($bucket4_data as $bucket4_data) {

$RVV2 = $bucket4_data->RVV2;
$RVV2_date = $bucket4_data->RVV2_date;
$RVV2_last_date = $bucket4_data->RVV2_last_date;

$OPV2 = $bucket4_data->OPV2;
$OPV2_date = $bucket4_data->OPV2_date;
$OPV2_last_date = $bucket4_data->OPV2_last_date;

$DPT2 = $bucket4_data->DPT2;
$DPT2_date = $bucket4_data->DPT2_date;
$DPT2_last_date = $bucket4_data->DPT2_last_date;

$PENTA2 = $bucket4_data->PENTA2;
$PENTA2_date = $bucket4_data->PENTA2_date;
$PENTA2_last_date = $bucket4_data->PENTA2_last_date;

$child_contact_no = $bucket4_data->child_contact;
$childname = $bucket4_data->child_name;
$mothername = $bucket4_data->mother_name;


if($today >= $RVV2_date AND $today <= $RVV2_last_date && $today >= $OPV2_date AND $today <= $OPV2_last_date && $today >= $DPT2_date AND $today <= $DPT2_last_date || $today >= $PENTA2_date AND $today <= $PENTA2_last_date){

$bucket4status = array($RVV2,$OPV2,$DPT2,$PENTA2);

 $countbck4 = array_count_values($bucket4status);

 //echo "FALSE VALUES = ".$countbck4['false']."\n";

if ($dinnank == "Mon" || $dinnank == "Tue"|| $dinnank == "Wed"|| $dinnank == "Thu"|| $dinnank == "Fri"|| $dinnank == "Sat"|| $dinnank == "Sun") {
if($countbck4['false']=='1')
{

if($RVV2=='false'){

	$BUCKET4_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "RVV2",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को आर बी बी 2 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);

}elseif($OPV2=='false'){

	$BUCKET4_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "OPV2",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को ओ पी बी 2 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);
}elseif($DPT2=='false'|| $PENTA2=='false'){

	$BUCKET4_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "DPT2-PENTA2",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को डी पी टी 2 या  पेंटा 2 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);
}

}elseif($countbck4['false'] > '1'){


	$BUCKET4_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "BUCKET4",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को बकेट 4  का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);

}

}//IF DAY IS MONDAY OR TUESDAY

}

}

// ==================+ENDING FOR BUCKET 4 MESSAGES +=============================================


//print_r($bucket5_data);
// ==================+STARTING WITH BUCKET 5 MESSAGES +=============================================

foreach ($bucket5_data as $bucket5_data) {

$RVV3 = $bucket5_data->RVV3;
$RVV3_date = $bucket5_data->RVV3_date;
$RVV3_last_date = $bucket5_data->RVV3_last_date;

$IPV2 = $bucket5_data->IPV2;
$IPV2_date = $bucket5_data->IPV2_date;
$IPV2_last_date = $bucket5_data->IPV2_last_date;

$OPV3 = $bucket5_data->OPV3;
$OPV3_date = $bucket5_data->OPV3_date;
$OPV3_last_date = $bucket5_data->OPV3_last_date;

$DPT3 = $bucket5_data->DPT3;
$DPT3_date = $bucket5_data->DPT3_date;
$DPT3_last_date = $bucket5_data->DPT3_last_date;

$PENTA3 = $bucket5_data->PENTA3;
$PENTA3_date = $bucket5_data->PENTA3_date;
$PENTA3_last_date = $bucket5_data->PENTA3_last_date;

$child_contact_no = $bucket5_data->child_contact;
$childname = $bucket5_data->child_name;
$mothername = $bucket5_data->mother_name;


if($today >= $RVV3_date AND $today <= $RVV3_last_date && $today >= $IPV2_date AND $today <= $IPV2_last_date && $today >= $OPV3_date AND $today <= $OPV3_last_date && $today >= $DPT3_date AND $today <= $DPT3_last_date || $today >= $PENTA3_date AND $today <= $PENTA3_last_date){

$bucket5status = array($RVV3,$IPV2,$OPV3,$DPT3,$PENTA3);

 $countbck5 = array_count_values($bucket5status);

 //echo "BUKET 5 FALSE VALUES = ".$countbck5['false']."\n";

if ($dinnank == "Mon" || $dinnank == "Tue"|| $dinnank == "Wed"|| $dinnank == "Thu"|| $dinnank == "Fri"|| $dinnank == "Sat"|| $dinnank == "Sun") {

if($countbck5['false']=='1')
{

if($RVV3=='false'){

	$BUCKET5_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "RVV3",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को आर बी बी 3  का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);
}elseif($IPV2=='false'){

	$BUCKET5_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "IPV3",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को आई पी बी 3  का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);
}elseif($OPV3=='false'){

	$BUCKET5_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "OPV3",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को ओ पी बी 3 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);
}elseif($DPT3=='false'|| $PENTA3=='false'){

	$BUCKET5_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "DPT3-PENTA3",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को डी पी टी 3 या पेंटा 3 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);
}

}elseif($countbck5['false'] > '1'){


	$BUCKET5_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "BUCKET5",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को बकेट 5 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);

}

}//End Of DAys Conditions

}

}

// ==================+ENDING FOR BUCKET 5 MESSAGES +=============================================



//print_r($bucket6_data);
// ==================+STARTING WITH BUCKET 6 MESSAGES +=============================================

foreach ($bucket6_data as $bucket6_data) {

$MMR = $bucket6_data->MMR;
$MMR_date = $bucket6_data->MMR_date;
$MMR_last_date = $bucket6_data->MMR_last_date;

$VIT_A_1 = $bucket6_data->VIT_A_1;
$VIT_A_1_date = $bucket6_data->VIT_A_1_date;
$VIT_A_1_last_date = $bucket6_data->VIT_A_1_last_date;

$JE1 = $bucket6_data->JE1;
$JE1_date = $bucket6_data->JE1_date;
$JE1_last_date = $bucket6_data->JE1_last_date;


$child_contact_no = $bucket6_data->child_contact;
$childname = $bucket6_data->child_name;
$mothername = $bucket6_data->mother_name;


if($today >= $MMR_date AND $today <= $MMR_last_date && $today >= $VIT_A_1_date AND $today <= $VIT_A_1_last_date && $today >= $JE1_date AND $today <= $JE1_last_date ){

$bucket6status = array($MMR,$VIT_A_1,$JE1);

 $countbck6 = array_count_values($bucket6status);

 //echo "BUKET 6 FALSE VALUES = ".$countbck6['false']."\n";

if ($dinnank == "Mon" || $dinnank == "Tue"|| $dinnank == "Wed"|| $dinnank == "Thu"|| $dinnank == "Fri"|| $dinnank == "Sat"|| $dinnank == "Sun") {

if($countbck6['false']=='1')
{

if($MMR=='false'){

	$BUCKET6_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "MMR",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को एम् एम् आर का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);
}elseif($VIT_A_1=='false'){

	$BUCKET6_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "VIT_A_1",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को विटामिन ऐ 1 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);
}elseif($JE1=='false'){

	$BUCKET6_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "JE1",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को जे इ 1 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);

}
}elseif($countbck6['false'] > '1'){


	$BUCKET6_MESSAGES[] = array(
"child_id" => $child_id,
"call_for" => "BUCKET6",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को बकेट 6 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);

}

}//End Of DAys Condition 

}

}

// ==================+ENDING FOR BUCKET 6 MESSAGES +=============================================

// ==================+STARTING WITH BUCKET 7 MESSAGES +=============================================

foreach ($bucket7_data as $bucket7_data) {

$MMR2 = $bucket7_data->MMR2;
$MMR2_date = $bucket7_data->MMR2_date;
$MMR2_last_date = $bucket7_data->MMR2_last_date;

$DPT_1_BOOSTER = $bucket7_data->DPT_1_BOOSTER;
$DPT_1_BOOSTER_date = $bucket7_data->DPT_1_BOOSTER_date;
$DPT_1_BOOSTER_last_date = $bucket7_data->DPT_1_BOOSTER_last_date;

$JE2 = $bucket7_data->JE2;
$JE2_date = $bucket7_data->JE2_date;
$JE2_last_date = $bucket7_data->JE2_last_date;


$OPV_BOOSTER = $bucket7_data->OPV_BOOSTER;
$OPV_BOOSTER_date = $bucket7_data->OPV_BOOSTER_date;
$OPV_BOOSTER_last_date = $bucket7_data->OPV_BOOSTER_last_date;



$child_contact_no = $bucket7_data->child_contact;
$childname = $bucket7_data->child_name;
$mothername = $bucket7_data->mother_name;


if($today >= $MMR2_date OR $today <= $MMR2_last_date && $today >= $DPT_1_BOOSTER_date OR $today <= $DPT_1_BOOSTER_last_date && $today >= $JE2_date OR $today <= $JE2_last_date && $today >= $OPV_BOOSTER_date OR $today <= $OPV_BOOSTER_last_date ){

$bucket7status = array($MMR2,$DPT_1_BOOSTER,$JE12,$OPV_BOOSTER);

 $countbck7 = array_count_values($bucket7status);

 //echo "BUCKET 7 FALSE VALUES = ".$countbck7['false']."\n";

if ($dinnank == "Mon" || $dinnank == "Tue"|| $dinnank == "Wed"|| $dinnank == "Thu"|| $dinnank == "Fri"|| $dinnank == "Sat"|| $dinnank == "Sun") {

if($countbck7['false']=='1')
{

if($MMR2=='false'){

	$BUCKET7_MESSAGES[] = array(
"child_id" => $child_id,
"call_for" => "MMR2",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को एम् एम् आर 2 बूस्टर  का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);
}elseif($DPT_1_BOOSTER=='false'){

	$BUCKET7_MESSAGES[] = array(
"child_id" => $child_id,
"call_for" => "DPT_1_BOOSTER",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को डी पी टी 1 बूस्टर  का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);
}elseif($JE2=='false'){

	$BUCKET7_MESSAGES[] = array(
"child_id" => $child_id,
"call_for" => "JE2",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को जे इ 2 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'
// "Message" => "JE2 BCHA HUA H"

	);

}elseif($OPV_BOOSTER=='false'){

	$BUCKET7_MESSAGES[] = array(
"child_id" => $child_id,
"call_for" => "OPV_BOOSTER",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को ओ पी बी बूस्टर का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);

}
}elseif($countbck7['false'] > '1'){


	$BUCKET7_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "BUCKET7",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को बकेट 7 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);

}

}//End Of Days Condition

}

}

// ==================+ENDING FOR BUCKET 7 MESSAGES +=============================================

// ==================+STARTING WITH BUCKET 8 MESSAGES +=============================================

foreach ($bucket8_data as $bucket8_data) {

$VIT_A_2 = $bucket8_data->VIT_A_2;
$VIT_A_2_date = $bucket8_data->VIT_A_2_date;
$VIT_A_2_last_date = $bucket8_data->VIT_A_2_last_date;

$VIT_A_3 = $bucket8_data->VIT_A_3;
$VIT_A_3_date = $bucket8_data->VIT_A_3_date;
$VIT_A_3_last_date = $bucket8_data->VIT_A_3_last_date;

$VIT_A_4 = $bucket8_data->VIT_A_4;
$VIT_A_4_date = $bucket8_data->VIT_A_4_date;
$VIT_A_4_last_date = $bucket8_data->VIT_A_4_last_date;

$VIT_A_5 = $bucket8_data->VIT_A_5;
$VIT_A_5_date = $bucket8_data->VIT_A_5_date;
$VIT_A_5_last_date = $bucket8_data->VIT_A_5_last_date;


$VIT_A_6 = $bucket8_data->VIT_A_6;
$VIT_A_6_date = $bucket8_data->VIT_A_6_date;
$VIT_A_6_last_date = $bucket8_data->VIT_A_6_last_date;


$VIT_A_7 = $bucket8_data->VIT_A_7;
$VIT_A_7_date = $bucket8_data->VIT_A_7_date;
$VIT_A_7_last_date = $bucket8_data->VIT_A_7_last_date;


$VIT_A_8 = $bucket8_data->VIT_A_8;
$VIT_A_8_date = $bucket8_data->VIT_A_8_date;
$VIT_A_8_last_date = $bucket8_data->VIT_A_8_last_date;



$VIT_A_9 = $bucket8_data->VIT_A_9;
$VIT_A_9_date = $bucket8_data->VIT_A_9_date;
$VIT_A_9_last_date = $bucket8_data->VIT_A_9_last_date;



$child_contact_no = $bucket8_data->child_contact;
$childname = $bucket8_data->child_name;
$mothername = $bucket8_data->mother_name;


if($today >= $VIT_A_2_date OR $today <= $VIT_A_2_last_date && $today >= $VIT_A_3_date OR $today <= $VIT_A_3_last_date && $today >= $VIT_A_4_date OR $today <= $VIT_A_4_last_date && $today >= $VIT_A_5_date OR $today <= $VIT_A_5_last_date && $today >= $VIT_A_6_date OR $today <= $VIT_A_6_last_date && $today >= $VIT_A_7_date OR $today <= $VIT_A_7_last_date && $today >= $VIT_A_8_date OR $today <= $VIT_A_8_last_date && $today >= $VIT_A_9_date OR $today <= $VIT_A_9_last_date ){

$bucket8status = array($VIT_A_2,$VIT_A_3,$VIT_A_4,$VIT_A_5,$VIT_A_6,$VIT_A_7,$VIT_A_8,$VIT_A_9);

 $countbck8 = array_count_values($bucket8status);

 //echo "BUCKET 8 FALSE VALUES = ".$countbck8['false']."\n";


if ($dinnank == "Mon" || $dinnank == "Tue"|| $dinnank == "Wed"|| $dinnank == "Thu"|| $dinnank == "Fri"|| $dinnank == "Sat"|| $dinnank == "Sun") {


if($countbck8['false']=='1')
{

if($VIT_A_2=='false'){

	$BUCKET8_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "VIT_A_2",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को विटामिन ऐ 2 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);
}elseif($VIT_A_3=='false'){

	$BUCKET8_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "VIT_A_3",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को विटामिन ऐ 3 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);
}elseif($VIT_A_4=='false'){

	$BUCKET8_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "VIT_A_4",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को विटामिन ऐ 4 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);
}elseif($VIT_A_5=='false'){

	$BUCKET8_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "VIT_A_5",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को विटामिन ऐ 5 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);
}elseif($VIT_A_6=='false'){

	$BUCKET8_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "VIT_A_6",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को विटामिन ऐ 6 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);
}elseif($VIT_A_7=='false'){

	$BUCKET8_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "VIT_A_7",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को विटामिन ऐ 7 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);
}elseif($VIT_A_8=='false'){

	$BUCKET8_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "VIT_A_8",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को विटामिन ऐ 8 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'
// "Message" => "VITAMIN A 8 BCHA HUA H"

	);
}elseif($VIT_A_9=='false'){

	$BUCKET8_MESSAGES[] = array(
"child_id" => $child_id,
"call_for" => "VIT_A_9",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को विटामिन ऐ 9 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'
//"Message" => "VITAMIN A 9 BCHA HUA H"

	);
}
}elseif($countbck8['false'] > '1'){


	$BUCKET8_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "BUCKET8",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
// "Message" => "BUCKET 8 KA MESAAGES JAYEGA YAHA SE H"
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को बकेट 8 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

	);

}

}//End Of Days Conditions

}

}

// ==================+ENDING FOR BUCKET 8 MESSAGES +=============================================



// ==================+STARTING WITH BUCKET 9 MESSAGES +=============================================

foreach ($bucket9_data as $bucket9_data) {



$DPT_2_BOOSTER = $bucket9_data->DPT_2_BOOSTER;
$DPT_2_BOOSTER_date = $bucket9_data->DPT_2_BOOSTER_date;
$DPT_2_BOOSTER_last_date = $bucket9_data->DPT_2_BOOSTER_last_date;





$child_contact_no = $bucket9_data->child_contact;
$childname = $bucket9_data->child_name;
$mothername = $bucket9_data->mother_name;


if($today >= $DPT_2_BOOSTER_date AND $today <= $DPT_2_BOOSTER_last_date ){

$bucket9status = array($DPT_2_BOOSTER);

 $countbck9 = array_count_values($bucket9status);

 //echo "BUCKET 9 FALSE VALUES = ".$countbck9['false']."\n";

if ($dinnank == "Mon" || $dinnank == "Tue"|| $dinnank == "Wed"|| $dinnank == "Thu"|| $dinnank == "Fri"|| $dinnank == "Sat"|| $dinnank == "Sun") {


if($countbck9['false']=='1')
{

if($DPT_2_BOOSTER=='false'){

	$BUCKET9_MESSAGES[] = array(
"child_id" => $child_id,
"call_for" => "DPT_2_BOOSTER",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को डी पी टी 2 बूस्टर का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'
// "Message" => "DPT_2_BOOSTER BCHA HUA H"

	);
}
}elseif($countbck9['false'] > '1'){


$BUCKET9_MESSAGES[] = array(
"child_id" => $child_id,
"message_for" => "BUCKET9",
"follow_up" => "no",
"mobile_no" => $child_contact_no,
"Message" => 'नमस्ते '.$mothername.' जी आपके बच्चे '.$childname.' को बकेट 9 का टीका जल्द से जल्द लगवाएँ| यह टीका आपके बच्चे को बीमारियों के ख़तरे से बचाता है | यह टीका आने वाले दिनांक '.$wed.' को लगाया जाएगा, कृपया अपनी आशा बहन से संपर्क करें|'

//$bcg_date->child_name.' Sunday For का BCG टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|'

	);

}

}// End Of Days Condition

}

}

// ==================+ENDING FOR BUCKET 9 MESSAGES +=============================================











	# code...
}  //END OF CHILD FOREACH


// $total_array = array_merge($bcg_dates, $opv_o_dates1,$hep_b_dates,$opv1_dates,$opv2_dates,$opv3_dates,$penta1_dates,$penta2_dates,$penta3_dates,$ipv_dates,$mmr_dates,$je1_dates,$je2_dates,$vit_a_1_dates,$vit_a_2_dates,$opv_booster_dates,$dpt1_booster_dates,$dpt2_booster_dates);

//if($isValidToken) {

$total_array = array_merge($BUCKET2_MESSAGES,$BUCKET3_MESSAGES,$BUCKET4_MESSAGES,$BUCKET5_MESSAGES,$BUCKET6_MESSAGES,$BUCKET7_MESSAGES,$BUCKET8_MESSAGES,$BUCKET9_MESSAGES);


$this->output
->set_status_header(200)
->set_content_type('application/json')
->set_output(json_encode($total_array,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)); 

//}


}











}




public function getFollowCallData()
{
header("Access-Control-Allow-Origin: *");
header("Access-Control-Request-Headers: GET,POST,OPTIONS,DELETE,PUT");
header("Access-Control-Allow-Headers: authorization, Content-Type");

$token = $this->input->get_request_header('Authorization');

$isValidToken = $this->api_model->checkToken($token);

if($isValidToken) { 
$total_array2 = array();


$total_array2[] = array(
        "child_id" => "19",
        "message_for"=> "BUCKET2",
        "follow_up"=> "yes",
        "mobile_no"=>"9810789821",
        "Message"=> "नमस्ते सुरुचि जी आपके बच्चे शिला का टीकाकरण कराने के लिए धन्यवाद| कृपया ए एन एम दीदी द्वारा दी गयी सलाह का पालन करें और अपने बच्चे के स्वस्थ को सुरक्षित करें |"
    );

$total_array2[] = 
    array(
        "child_id"=> "20",
        "message_for"=> "BUCKET3",
        "follow_up"=> "yes",
        "mobile_no"=> "7668006774",
        "Message"=> "नमस्ते रीता जी आपके बच्चे सोनाली का टीकाकरण कराने के लिए धन्यवाद| कृपया ए एन एम दीदी द्वारा दी गयी सलाह का पालन करें और अपने बच्चे के स्वस्थ को सुरक्षित करें |"
    );


$total_array3 = array_merge($total_array2);



$this->output
->set_status_header(200)
->set_content_type('application/json')
->set_output(json_encode($total_array3,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)); 



} //if valid Token


}




public function uploadChild(){



$this->load->view('uploadexcel/Childexcelupload');


// UPLOAD AND INSERT EXCEL FILE INTO DB

if (isset($_POST['uploadexcelf'])) {

//echo "OKKKKKKKKKKKKKKKKKKKKKKk";


//   $ngo = $_SESSION['ngo_base'];
//   $chw_name = $_POST['chw_name'];

//print_r($_FILES);

if($_FILES['file']['name'])
 {
  $filename = explode(".", $_FILES['file']['name']);
  if($filename[1] == 'csv' || $filename[1] == 'xls')
  {
   $handle = fopen($_FILES['file']['tmp_name'], "r");
   while($data = fgetcsv($handle))
   {
//echo(rand(7,77777));

    // $datam['mthr_id'] = $this->db->escape_str($data[0]); 

    $datam['mthr_id'] = (rand(1000,9999));  
    $datam['mother_name'] = $this->db->escape_str($data[1]);
    $datam['child_contact'] = $this->db->escape_str($data[2]);
    $datam['child_unq_id'] = 'UNIQUE00001';

    $datam['child_name'] = $this->db->escape_str($data[3]);



$excldob = $this->db->escape_str($data[4]);
$dateOFBirth = str_replace('/', '-', $excldob);
$cdob =  date('Y-m-d', strtotime($dateOFBirth));

    $datam['child_dob'] = $cdob;



    




    $datam['child_status'] = $this->db->escape_str($data[5]);

    $datam['is_vacinated_before'] = $this->db->escape_str($data[6]);

    $datam['Hep_B'] = $this->db->escape_str($data[7]);
    $datam['Hep_B_date'] = date('Y-m-d', strtotime($cdob));
	$datam['Hep_B_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1 day'));
    $datam['Hep_B_done_date'] = $this->db->escape_str($data[8]);

    $datam['BCG'] = $this->db->escape_str($data[9]);
    $datam['BCG_date'] =  date('Y-m-d', strtotime($cdob));
	$datam['BCG_last_date'] =  date('Y-m-d', strtotime($cdob. ' + 1 year'));
    $datam['BCG_done_date'] = $this->db->escape_str($data[10]);



$datam['OPV_O'] = $this->db->escape_str($data[11]);
$datam['OPV_O_done_date'] = $this->db->escape_str($data[12]);
$datam['OPV_O_date'] = date('Y-m-d', strtotime($cdob));
$datam['OPV_O_last_date'] = date('Y-m-d', strtotime($cdob. ' + 15 days'));



$datam['RVV1'] = $this->db->escape_str($data[13]);
$datam['RVV1_date'] = date('Y-m-d', strtotime($cdob. ' + 42 days'));
$datam['RVV1_last_date'] = date('Y-m-d', strtotime($cdob. ' + 365 days'));
$datam['RVV1_done_date'] = $this->db->escape_str($data[14]);



$datam['IPV1'] = $this->db->escape_str($data[15]);
$datam['IPV1_date'] = date('Y-m-d', strtotime($cdob. ' + 42 days'));
$datam['IPV1_last_date'] = date('Y-m-d', strtotime($cdob. ' + 365 days'));
$datam['IPV1_done_date'] = $this->db->escape_str($data[16]);



$datam['OPV1'] = $this->db->escape_str($data[17]);
$datam['OPV1_date'] = date('Y-m-d', strtotime($cdob. ' + 42 days'));//
$datam['OPV1_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));
$datam['OPV1_done_date'] = $this->db->escape_str($data[18]);


$datam['DPT1'] = $this->db->escape_str($data[19]);
$datam['DPT1_date'] = date('Y-m-d', strtotime($cdob. ' + 42 days'));//
$datam['DPT1_last_date'] = date('Y-m-d', strtotime($cdob. ' + 730 days'));
$datam['DPT1_done_date'] = $this->db->escape_str($data[20]);


$datam['PENTA1'] = $this->db->escape_str($data[21]);
$datam['PENTA1_date'] =  date('Y-m-d', strtotime($cdob. ' + 42 days'));
$datam['PENTA1_last_date'] =  date('Y-m-d', strtotime($cdob. ' + 365 days'));
$datam['PENTA1_done_date'] = $this->db->escape_str($data[22]);


// 2 =====================

$datam['RVV2'] = $this->db->escape_str($data[23]);
$datam['RVV2_date'] = date('Y-m-d', strtotime($cdob. ' + 70 days'));
$datam['RVV2_last_date'] = date('Y-m-d', strtotime($cdob. ' + 365 days'));
$datam['RVV2_done_date'] = $this->db->escape_str($data[24]);

$datam['OPV2'] = $this->db->escape_str($data[25]);
$datam['OPV2_date'] = date('Y-m-d', strtotime($cdob. ' + 70 days'));//
$datam['OPV2_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));
$datam['OPV2_done_date'] = $this->db->escape_str($data[26]);



$datam['DPT2'] = $this->db->escape_str($data[27]);
$datam['DPT2_date'] = date('Y-m-d', strtotime($cdob. ' + 70 days'));//
$datam['DPT2_last_date'] = date('Y-m-d', strtotime($cdob. ' + 730 days'));
$datam['DPT2_done_date'] = $this->db->escape_str($data[28]);


$datam['PENTA2'] = $this->db->escape_str($data[29]);
$$datam['PENTA2_date'] =  date('Y-m-d', strtotime($cdob. ' + 70 days'));
$datam['PENTA2_last_date'] =  date('Y-m-d', strtotime($cdob. ' + 365 days'));
$datam['PENTA2_done_date'] = $this->db->escape_str($data[30]);



// 3 =========================================================

$datam['RVV3'] = $this->db->escape_str($data[31]);
$datam['RVV3_date'] = date('Y-m-d', strtotime($cdob. ' + 98 days'));
$datam['RVV3_last_date'] = date('Y-m-d', strtotime($cdob. ' + 365 days'));
$datam['RVV3_done_date'] = $this->db->escape_str($data[32]);


$datam['IPV2'] = $this->db->escape_str($data[33]);
$datam['IPV2_date'] = date('Y-m-d', strtotime($cdob. ' + 98 days'));
$datam['IPV2_last_date'] = date('Y-m-d', strtotime($cdob. ' + 365 days'));
$datam['IPV2_done_date'] = $this->db->escape_str($data[34]);



$datam['OPV3'] = $this->db->escape_str($data[35]);
$datam['OPV3_date'] = date('Y-m-d', strtotime($cdob. ' + 98 days'));//
$datam['OPV3_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));
$datam['OPV3_done_date'] = $this->db->escape_str($data[36]);


$datam['DPT3'] = $this->db->escape_str($data[37]);
$datam['DPT3_date'] = date('Y-m-d', strtotime($cdob. ' + 98 days'));//
$datam['DPT3_last_date'] = date('Y-m-d', strtotime($cdob. ' + 365 days'));
$datam['DPT3_done_date'] = $this->db->escape_str($data[38]);


$datam['PENTA3'] = $this->db->escape_str($data[39]);
$datam['PENTA3_date'] =  date('Y-m-d', strtotime($cdob. ' + 98 days'));
$datam['PENTA3_last_date'] =  date('Y-m-d', strtotime($cdob. ' + 365 days'));
$datam['PENTA3_done_date'] = $this->db->escape_str($data[40]);


// Bucket 6 =================================================

$datam['MMR'] = $this->db->escape_str($data[41]);
$datam['MMR_date'] = date('Y-m-d', strtotime($cdob. ' + 270 days'));
$datam['MMR_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));
$datam['MMR_done_date'] = $this->db->escape_str($data[42]);


$datam['VIT_A_1'] = $this->db->escape_str($data[43]);
$datam['VIT_A_1_date'] = date('Y-m-d', strtotime($cdob. ' + 270 days'));
$datam['VIT_A_1_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));
$datam['VIT_A_1_done_date'] = $this->db->escape_str($data[44]);


$datam['JE1'] = $this->db->escape_str($data[45]);
$datam['JE1_date'] = date('Y-m-d', strtotime($cdob. ' + 270 days'));
$datam['JE1_last_date'] = date('Y-m-d', strtotime($cdob. ' + 5475 days'));
$datam['JE1_done_date'] = $this->db->escape_str($data[46]);


// Bucket 7 =================================================


$datam['DPT_1_BOOSTER'] = $this->db->escape_str($data[47]);
$datam['DPT_1_BOOSTER_date'] = date('Y-m-d', strtotime($cdob. ' + 480 days'));
$datam['DPT_1_BOOSTER_last_date'] = date('Y-m-d', strtotime($cdob. ' + 2555 days'));
$datam['DPT_1_BOOSTER_done_date'] = $this->db->escape_str($data[48]);




$datam['MMR2'] = $this->db->escape_str($data[49]);
$datam['MMR2_date'] = date('Y-m-d', strtotime($cdob. ' + 480 days'));
$datam['MMR2_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));
$datam['MMR2_done_date'] = $this->db->escape_str($data[50]);


$datam['OPV_BOOSTER'] = $this->db->escape_str($data[51]);
$datam['OPV_BOOSTER_date'] = date('Y-m-d', strtotime($cdob. ' + 480 days'));
$datam['OPV_BOOSTER_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));
$datam['OPV_BOOSTER_done_date'] = $this->db->escape_str($data[52]);


$datam['JE2'] = $this->db->escape_str($data[53]);
$datam['JE2_date'] = date('Y-m-d', strtotime($cdob. ' + 480 days'));
$datam['JE2_last_date'] = date('Y-m-d', strtotime($cdob. ' + 5475 days'));
$datam['JE2_done_date'] = $this->db->escape_str($data[54]);



// Bucket 8 =================================================

$datam['VIT_A_2'] = $this->db->escape_str($data[55]);
$datam['VIT_A_2_date'] = date('Y-m-d', strtotime($cdob. ' + 540 days'));
$datam['VIT_A_2_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));
$datam['VIT_A_2_done_date'] = $this->db->escape_str($data[56]);


$datam['VIT_A_3'] = $this->db->escape_str($data[57]);
$datam['VIT_A_3_date'] = date('Y-m-d', strtotime($cdob. ' + 720 days'));
$datam['VIT_A_3_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));
$datam['VIT_A_3_done_date'] = $this->db->escape_str($data[58]);


$datam['VIT_A_4'] = $this->db->escape_str($data[59]);
$datam['VIT_A_4_date'] = date('Y-m-d', strtotime($cdob. ' + 900 days'));
$datam['VIT_A_4_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));
$datam['VIT_A_4_done_date'] = $this->db->escape_str($data[60]);


$datam['VIT_A_5'] = $this->db->escape_str($data[61]);
$datam['VIT_A_5_date'] = date('Y-m-d', strtotime($cdob. ' + 1080 days'));
$datam['VIT_A_5_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));
$datam['VIT_A_5_done_date'] = $this->db->escape_str($data[62]);

$datam['VIT_A_6'] = $this->db->escape_str($data[63]);
$datam['VIT_A_6_date'] = date('Y-m-d', strtotime($cdob. ' + 1260 days'));
$datam['VIT_A_6_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));
$datam['VIT_A_6_done_date'] = $this->db->escape_str($data[64]);

$datam['VIT_A_7'] = $this->db->escape_str($data[65]);
$datam['VIT_A_7_date'] = date('Y-m-d', strtotime($cdob. ' + 1340 days'));
$datam['VIT_A_7_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));
$datam['VIT_A_7_done_date'] = $this->db->escape_str($data[66]);

$datam['VIT_A_8'] = $this->db->escape_str($data[67]);
$datam['VIT_A_8_date'] = date('Y-m-d', strtotime($cdob. ' + 1520 days'));
$datam['VIT_A_8_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));
$datam['VIT_A_8_done_date'] = $this->db->escape_str($data[68]);

$datam['VIT_A_9'] = $this->db->escape_str($data[69]);
$datam['VIT_A_9_date'] = date('Y-m-d', strtotime($cdob. ' + 1700 days'));
$datam['VIT_A_9_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));
$datam['VIT_A_9_done_date'] = $this->db->escape_str($data[70]);



// Bucket 9 ============================================

$datam['DPT_2_BOOSTER'] = $this->db->escape_str($data[71]);
$datam['DPT_2_BOOSTER_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));
$datam['DPT_2_BOOSTER_last_date'] = date('Y-m-d', strtotime($cdob. ' + 2190 days'));
$datam['DPT_2_BOOSTER_done_date'] = $this->db->escape_str($data[72]); 

$datam['added_time'] = date('Y-m-d H:i:s');



// End OF DATAM

  


$this->db->insert('children_details', $datam);

    
   }
   fclose($handle);
   echo "<script>alert('Child Data Imported');</script>";
  }
 }


 redirect('/api/uploadChild' ,'refresh');

}
















// END OF EXCEL DATA UPLOAD 








}






// ==================UPLOAD MOTHER ====================================



public function uploadMother(){



$this->load->view('uploadexcel/Motherexcelupload');


// UPLOAD AND INSERT EXCEL FILE INTO DB

if (isset($_POST['uploadexcelm'])) {

//echo "OKKKKKKKKKKKKKKKKKKKKKKk";


//   $ngo = $_SESSION['ngo_base'];
//   $chw_name = $_POST['chw_name'];

//print_r($_FILES);

if($_FILES['file']['name'])
 {
  $filename = explode(".", $_FILES['file']['name']);
  if($filename[1] == 'csv' || $filename[1] == 'xls')
  {
   $handle = fopen($_FILES['file']['tmp_name'], "r");
   while($data = fgetcsv($handle))
   {
//echo(rand(7,77777));

    // $datam['mthr_id'] = $this->db->escape_str($data[0]); 

    $datam['mthrs_unq_no'] = (rand(7,7777));  
    $datam['mthrs_name'] = $this->db->escape_str($data[1]);
    $datam['mthrs_last_name'] = $this->db->escape_str($data[2]);
    $datam['mthrs_mbl_no'] = $this->db->escape_str($data[3]);
    $datam['mthrs_optn_mbl_no'] = $this->db->escape_str($data[4]);
    $datam['mthrs_passwrd'] = '0706123';

    $datam['age'] = $this->db->escape_str($data[5]);
    $datam['area'] = $this->db->escape_str($data[6]);
    $datam['area_code'] = $this->db->escape_str($data[7]);

    $datam['anm_name'] = $this->db->escape_str($data[8]);
    $datam['anm_contact'] = $this->db->escape_str($data[9]);
    $datam['asha_name'] = $this->db->escape_str($data[10]);
    $datam['asha_contact'] = $this->db->escape_str($data[11]);
    $datam['mthr_status'] = '1';
    
    


$this->db->insert('mothers_details', $datam);

    
   }
   fclose($handle);
   echo "<script>alert('Child Data Imported');</script>";
  }
 }


 redirect('/api/uploadMother' ,'refresh');

}




// END OF EXCEL DATA UPLOAD 








}











// ======================= END OF UPLOAD MOTHER =========================








































// end of 	claa Api
}
