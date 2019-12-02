<?php
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
$childData['child_name'] = $this->input->post('childname');
$childData['child_contact'] = $this->input->post('mothermblno');
$childData['child_dob'] = $this->input->post('childdob');
$childData['child_status'] = '1';
$childData['is_vacinated_before'] = $str;



// if vaciinated Before


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





// =====================================================Get the Next Wednesday data lists
public function getWedLists()
{
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: authorization, Content-Type");

$token = $this->input->get_request_header('Authorization');

$isValidToken = $this->api_model->checkToken($token);



if(date('D') == 'Wed') { 
$wed = date('Y-m-d');
}else{
$wed = date('Y-m-d', strtotime('next Wednesday')); }

$bcg_dates_posts = array();
//$opv_o_dates = array();

// if($isValidToken) {
$bcg_dates = $this->api_model->get_bcg_date();
$opv_o_dates1 = $this->api_model->get_opv_o_date(); 

$bcg_dates_posts = array();
foreach($bcg_dates as $bcg_date) {
$bcg_dates_posts[] = array(
'vaccine' => 'BCG',
'due_date' => $bcg_date->BCG_date,
'child_id' => $bcg_date->child_unq_id,
'mobile' => $bcg_date->child_contact,
'message' => $bcg_date->child_name.' का BCG टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// 'message' => $bcg_date->child_name.' का BCG टीका का टीकाकरण दिनांक '.$bcg_date->BCG_date.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}

$opv_dates_posts = array();
foreach($opv_o_dates1 as $opv_o_date) {
$bcg_dates_posts[] = array(
'vaccine' => 'OPV_O',
'due_date' => $opv_o_date->vaccine_step1_start,
'child_id' => $opv_o_date->child_unq_id,
'mobile' => $opv_o_date->child_contact,
'message' => $opv_o_date->child_name.' का OPV_O टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}





// $bcg_dates = array('vaccine'=>'BCG',
// 	'child Name' => $bcg_dates['1']);

$total_array = array_merge($bcg_dates_posts);


// $total_array = array_merge($bcg_dates, $opv_o_dates1,$hep_b_dates,$opv1_dates,$opv2_dates,$opv3_dates,$penta1_dates,$penta2_dates,$penta3_dates,$ipv_dates,$mmr_dates,$je1_dates,$je2_dates,$vit_a_1_dates,$vit_a_2_dates,$opv_booster_dates,$dpt1_booster_dates,$dpt2_booster_dates);


$this->output
->set_status_header(200)
->set_content_type('application/json')
->set_output(json_encode($total_array,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)); 
//}
}




public function getWednesLists()
{
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: authorization, Content-Type");

$token = $this->input->get_request_header('Authorization');

$isValidToken = $this->api_model->checkToken($token);



if(date('D') == 'Wed') { 
$wed = date('Y-m-d');
}else{
$wed = date('Y-m-d', strtotime('next Wednesday')); }

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

$bcg_dates_posts = array();
foreach($bcg_dates as $bcg_date) {
$bcg_dates_posts[] = array(
'vaccine' => 'BCG',
'due_date' => $bcg_date->BCG_date,
'child_id' => $bcg_date->child_unq_id,
'mobile' => $bcg_date->child_contact,
'message' => $bcg_date->child_name.' का BCG टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// 'message' => $bcg_date->child_name.' का BCG टीका का टीकाकरण दिनांक '.$bcg_date->BCG_date.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}

$opv_dates_posts = array();
foreach($opv_o_dates1 as $opv_o_date) {
$bcg_dates_posts[] = array(
'vaccine' => 'OPV_O',
'due_date' => $opv_o_date->OPV_O_date,
'child_id' => $opv_o_date->child_unq_id,
'mobile' => $opv_o_date->child_contact,
'message' => $opv_o_date->child_name.' का OPV_O टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}





// $bcg_dates = array('vaccine'=>'BCG',
// 	'child Name' => $bcg_dates['1']);

$total_array = array_merge($bcg_dates_posts);


// $total_array = array_merge($bcg_dates, $opv_o_dates1,$hep_b_dates,$opv1_dates,$opv2_dates,$opv3_dates,$penta1_dates,$penta2_dates,$penta3_dates,$ipv_dates,$mmr_dates,$je1_dates,$je2_dates,$vit_a_1_dates,$vit_a_2_dates,$opv_booster_dates,$dpt1_booster_dates,$dpt2_booster_dates);


$this->output
->set_status_header(200)
->set_content_type('application/json')
->set_output(json_encode($total_array,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)); 
//}
}





// =====================================================End Of Next Wednesday Lists Data 













public function getWednesdayData()
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

$bcg_dates_posts = array();
foreach($bcg_dates as $bcg_date) {

if ($dinnank == "Sun") {	
$bcg_dates_posts[] = array(
'vaccine' => 'BCG',
'child_id' => $bcg_date->child_unq_id,
'mobile' => $bcg_date->child_contact,
'message' => $bcg_date->child_name.' Sunday का BCG टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "10") {	 //8 to 10
$bcg_dates_posts[] = array(
'vaccine' => 'BCG',
'child_id' => $bcg_date->child_unq_id,
'mobile' => $bcg_date->child_contact,
'message' => $bcg_date->child_name.' का wednesday morning BCG टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed" && $samay >= "16" && $samay < "20") {	 //18 -20
$bcg_dates_posts[] = array(
'vaccine' => 'BCG',
'child_id' => $bcg_date->child_unq_id,
'mobile' => $bcg_date->child_contact,
'message' => $bcg_date->child_name.' का Wednesday Evening Follow Up Calls - BCG टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Thu") {	
$bcg_dates_posts[] = array(
'vaccine' => 'BCG',
'child_id' => $bcg_date->child_unq_id,
'mobile' => $bcg_date->child_contact,
'message' => $bcg_date->child_name.' Thursday Calls का BCG टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$bcg_dates_posts[] = array(
'vaccine' => 'BCG',
'child_id' => $bcg_date->child_unq_id,
'mobile' => $bcg_date->child_contact,
'message' => $bcg_date->child_name.' का Saturday morning BCG टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$bcg_dates_posts[] = array(
'vaccine' => 'BCG',
'child_id' => $bcg_date->child_unq_id,
'mobile' => $bcg_date->child_contact,
'message' => $bcg_date->child_name.' का Saturday Evening Follow Up Calls - BCG टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}









}


// if (date('D')== "Wed") {
// 	 $WedStatus = true;
// }

//     $arrLayout = array( 

// "message" => ($WedStatus ? "yes" : false),
// );

//     // $arrLayout = array(
//     //              "section1" => array(
//     //              ($LibraryStatus ? array("wLibrary" => array("title"   => "XMBC Library",
//     //                                                          "display" => "")) : false),
//     //              ($ControlStatus ? array("wControl" => array("title"   => "Control",
//     //                                                          "display" => "")) : false)));
    
//     print_r($arrLayout);


$opv_dates_posts = array();
foreach($opv_o_dates1 as $opv_o_date) {
// $opv_dates_posts[] = array(
// 'vaccine' => 'OPV_O',
// // 'due_date' => $opv_o_date->OPV_O_date,
// 'child_id' => $opv_o_date->child_unq_id,
// 'mobile' => $opv_o_date->child_contact,
// 'message' => $opv_o_date->child_name.' का OPV_O टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );

// OPV_O DATA STARTS FROM HERE 


if ($dinnank == "Sun") {	
$opv_dates_posts[] = array(
'vaccine' => 'OPV0',
'due_date' => $opv_o_date->OPV_O_date,
'child_id' => $opv_o_date->child_unq_id,
'mobile' => $opv_o_date->child_contact,
'message' => $opv_o_date->child_name.' Sunday का OPV0 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// 'message' => $bcg_date->child_name.' का BCG टीका का टीकाकरण दिनांक '.$bcg_date->BCG_date.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "16") {	 //8 to 10
$opv_dates_posts[] = array(
'vaccine' => 'OPV0',
'due_date' => $opv_o_date->OPV_O_date,
'child_id' => $opv_o_date->child_unq_id,
'mobile' => $opv_o_date->child_contact,
'message' => $opv_o_date->child_name.' Sunday का OPV0 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// 'message' => $bcg_date->child_name.' का BCG टीका का टीकाकरण दिनांक '.$bcg_date->BCG_date.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "18" && $samay < "20") {	 // 18 -20
$opv_dates_posts[] = array(
'vaccine' => 'OPV0',
'due_date' => $opv_o_date->OPV_O_date,
'child_id' => $opv_o_date->child_unq_id,
'mobile' => $opv_o_date->child_contact,
'message' => $opv_o_date->child_name.' Sunday का OPV0 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// 'message' => $bcg_date->child_name.' का BCG टीका का टीकाकरण दिनांक '.$bcg_date->BCG_date.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Thu") {	
$opv_dates_posts[] = array(
'vaccine' => 'OPV0',
'due_date' => $opv_o_date->OPV_O_date,
'child_id' => $opv_o_date->child_unq_id,
'mobile' => $opv_o_date->child_contact,
'message' => $opv_o_date->child_name.' Thuday का OPV0 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// 'message' => $bcg_date->child_name.' का BCG टीका का टीकाकरण दिनांक '.$bcg_date->BCG_date.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$opv_dates_posts[] = array(
'vaccine' => 'OPV0',
'due_date' => $opv_o_date->OPV_O_date,
'child_id' => $opv_o_date->child_unq_id,
'mobile' => $opv_o_date->child_contact,
'message' => $opv_o_date->child_name.' Saturday का OPV0  टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// 'message' => $bcg_date->child_name.' का BCG टीका का टीकाकरण दिनांक '.$bcg_date->BCG_date.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$opv_dates_posts[] = array(
'vaccine' => 'OPV0',
'due_date' => $opv_o_date->OPV_O_date,
'child_id' => $opv_o_date->child_unq_id,
'mobile' => $opv_o_date->child_contact,
'message' => $opv_o_date->child_name.' Saturday का OPV0  टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// 'message' => $bcg_date->child_name.' का BCG टीका का टीकाकरण दिनांक '.$bcg_date->BCG_date.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}

// OPV_O DATA ENDS HERE 



}


// ================Hepatitis B Vaccination Starts from here =====================

// $hep_b_dates_posts = array();
// foreach($hep_b_dates as $hep_b_date) {
// $hep_b_dates_posts[] = array(
// 'vaccine' => 'Hep_B',
// // 'due_date' => $hep_b_date->Hep_B_date,
// 'child_id' => $hep_b_date->child_unq_id,
// 'mobile' => $hep_b_date->child_contact,
// 'message' => $hep_b_date->child_name.' का Hepatitis B टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );
// }

// ================Hepatitis B Vaccination Ends  here =====================


$opv1_dates_posts = array();
foreach($opv1_dates as $opv1_date) {
// $opv1_dates_posts[] = array(
// 'vaccine' => 'OPV1',
// // 'due_date' => $hep_b_date->Hep_B_date,
// 'child_id' => $opv1_date->child_unq_id,
// 'mobile' => $opv1_date->child_contact,
// 'message' => $opv1_date->child_name.' का OPV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );
// ====================newly Added Codes ===========================================

if ($dinnank == "Sun") {	
$opv1_dates_posts[] = array(
'vaccine' => 'OPV1',
// 'due_date' => $hep_b_date->Hep_B_date,
'child_id' => $opv1_date->child_unq_id,
'mobile' => $opv1_date->child_contact,
'message' => $opv1_date->child_name.' का OPV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "16") {	 //8 to 10
$opv1_dates_posts[] = array(
'vaccine' => 'OPV1',
// 'due_date' => $hep_b_date->Hep_B_date,
'child_id' => $opv1_date->child_unq_id,
'mobile' => $opv1_date->child_contact,
'message' => $opv1_date->child_name.' का OPV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed" && $samay >= "18" && $samay < "20") {	 // 18 -20
$opv1_dates_posts[] = array(
'vaccine' => 'OPV1',
// 'due_date' => $hep_b_date->Hep_B_date,
'child_id' => $opv1_date->child_unq_id,
'mobile' => $opv1_date->child_contact,
'message' => $opv1_date->child_name.' का OPV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Thu") {	
$opv1_dates_posts[] = array(
'vaccine' => 'OPV1',
// 'due_date' => $hep_b_date->Hep_B_date,
'child_id' => $opv1_date->child_unq_id,
'mobile' => $opv1_date->child_contact,
'message' => $opv1_date->child_name.' का OPV1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$opv1_dates_posts[] = array(
'vaccine' => 'OPV1',
// 'due_date' => $hep_b_date->Hep_B_date,
'child_id' => $opv1_date->child_unq_id,
'mobile' => $opv1_date->child_contact,
'message' => $opv1_date->child_name.' का OPV1  टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$opv1_dates_posts[] = array(
'vaccine' => 'OPV1',
'child_id' => $opv1_date->child_unq_id,
'mobile' => $opv1_date->child_contact,
'message' => $opv1_date->child_name.' का OPV1  टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


// OPV1 DATA ENDS HERE 





















}




$opv2_dates_posts = array();
foreach($opv2_dates as $opv2_date) {
// $opv2_dates_posts[] = array(
// 'vaccine' => 'OPV2',
// 'child_id' => $opv2_date->child_unq_id,
// 'mobile' => $opv2_date->child_contact,
// 'message' => $opv2_date->child_name.' का OPV2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );

if ($dinnank == "Sun") {	
$opv2_dates_posts[] = array(
'vaccine' => 'OPV2',
'child_id' => $opv2_date->child_unq_id,
'mobile' => $opv2_date->child_contact,
'message' => $opv2_date->child_name.' का OPV2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "10") {	 //8 to 10
$opv2_dates_posts[] = array(
'vaccine' => 'OPV2',
'child_id' => $opv2_date->child_unq_id,
'mobile' => $opv2_date->child_contact,
'message' => $opv2_date->child_name.' का OPV2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed" && $samay >= "16" && $samay < "20") {	 //18 -20
$opv2_dates_posts[] = array(
'vaccine' => 'OPV2',
'child_id' => $opv2_date->child_unq_id,
'mobile' => $opv2_date->child_contact,
'message' => $opv2_date->child_name.' का OPV2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Thu") {	
$opv2_dates_posts[] = array(
'vaccine' => 'OPV2',
'child_id' => $opv2_date->child_unq_id,
'mobile' => $opv2_date->child_contact,
'message' => $opv2_date->child_name.' का OPV2 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$opv2_dates_posts[] = array(
'vaccine' => 'OPV2',
'child_id' => $opv2_date->child_unq_id,
'mobile' => $opv2_date->child_contact,
'message' => $opv2_date->child_name.' का OPV2 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$opv2_dates_posts[] = array(
'vaccine' => 'OPV2',
'child_id' => $opv2_date->child_unq_id,
'mobile' => $opv2_date->child_contact,
'message' => $opv2_date->child_name.' का OPV2 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}

}



$opv3_dates_posts = array();
foreach($opv3_dates as $opv3_date) {
// $opv3_dates_posts[] = array(
// 'vaccine' => 'OPV3',
// // 'due_date' => $hep_b_date->Hep_B_date,
// 'child_id' => $opv3_date->child_unq_id,
// 'mobile' => $opv3_date->child_contact,
// 'message' => $opv3_date->child_name.' का OPV3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );


if ($dinnank == "Sun") {	
$opv3_dates_posts[] = array(
'vaccine' => 'OPV3',
'child_id' => $opv3_date->child_unq_id,
'mobile' => $opv3_date->child_contact,
'message' => $opv3_date->child_name.' का OPV3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "10") {	 //8 to 10
$opv3_dates_posts[] = array(
'vaccine' => 'OPV3',
'child_id' => $opv3_date->child_unq_id,
'mobile' => $opv3_date->child_contact,
'message' => $opv3_date->child_name.' का OPV3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed" && $samay >= "16" && $samay < "20") {	 //18 -20
$opv3_dates_posts[] = array(
'vaccine' => 'OPV3',
'child_id' => $opv3_date->child_unq_id,
'mobile' => $opv3_date->child_contact,
'message' => $opv3_date->child_name.' का OPV3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Thu") {	
$opv3_dates_posts[] = array(
'vaccine' => 'OPV3',
'child_id' => $opv3_date->child_unq_id,
'mobile' => $opv3_date->child_contact,
'message' => $opv3_date->child_name.' का OPV3 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$opv3_dates_posts[] = array(
'vaccine' => 'OPV3',
'child_id' => $opv3_date->child_unq_id,
'mobile' => $opv3_date->child_contact,
'message' => $opv3_date->child_name.' का OPV3 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$opv3_dates_posts[] = array(
'vaccine' => 'OPV3',
'child_id' => $opv3_date->child_unq_id,
'mobile' => $opv3_date->child_contact,
'message' => $opv3_date->child_name.' का OPV3 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}

}


$dpt1_dates_posts = array();
foreach($dpt1_dates as $dpt1_date) {
// $dpt1_dates_posts[] = array(
// 'vaccine' => 'DPT1',
// 'child_id' => $dpt1_date->child_unq_id,
// 'mobile' => $dpt1_date->child_contact,
// 'message' => $dpt1_date->child_name.' का DPT 1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );


if ($dinnank == "Sun") {	
$dpt1_dates_posts[] = array(
'vaccine' => 'DPT1',
'child_id' => $dpt1_date->child_unq_id,
'mobile' => $dpt1_date->child_contact,
'message' => $dpt1_date->child_name.' का DPT 1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "10") {	 //8 to 10
$dpt1_dates_posts[] = array(
'vaccine' => 'DPT1',
'child_id' => $dpt1_date->child_unq_id,
'mobile' => $dpt1_date->child_contact,
'message' => $dpt1_date->child_name.' का DPT 1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed" && $samay >= "16" && $samay < "20") {	 //18 -20
$dpt1_dates_posts[] = array(
'vaccine' => 'DPT1',
'child_id' => $dpt1_date->child_unq_id,
'mobile' => $dpt1_date->child_contact,
'message' => $dpt1_date->child_name.' का DPT 1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Thu") {	
$dpt1_dates_posts[] = array(
'vaccine' => 'DPT1',
'child_id' => $dpt1_date->child_unq_id,
'mobile' => $dpt1_date->child_contact,
'message' => $dpt1_date->child_name.' का DPT 1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$dpt1_dates_posts[] = array(
'vaccine' => 'DPT1',
'child_id' => $dpt1_date->child_unq_id,
'mobile' => $dpt1_date->child_contact,
'message' => $dpt1_date->child_name.' का DPT 1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$dpt1_dates_posts[] = array(
'vaccine' => 'DPT1',
'child_id' => $dpt1_date->child_unq_id,
'mobile' => $dpt1_date->child_contact,
'message' => $dpt1_date->child_name.' का DPT 1 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}



}


$dpt2_dates_posts = array();
foreach($dpt2_dates as $dpt2_date) {
// $dpt2_dates_posts[] = array(
// 'vaccine' => 'DPT2',
// // 'due_date' => $hep_b_date->Hep_B_date,
// 'child_id' => $dpt2_date->child_unq_id,
// 'mobile' => $dpt2_date->child_contact,
// 'message' => $dpt2_date->child_name.' का DPT 2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );


if ($dinnank == "Sun") {	
$dpt2_dates_posts[] = array(
'vaccine' => 'DPT2',
'child_id' => $dpt2_date->child_unq_id,
'mobile' => $dpt2_date->child_contact,
'message' => $dpt2_date->child_name.' का DPT 2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "10") {	 //8 to 10
$dpt2_dates_posts[] = array(
'vaccine' => 'DPT2',
'child_id' => $dpt2_date->child_unq_id,
'mobile' => $dpt2_date->child_contact,
'message' => $dpt2_date->child_name.' का DPT 2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed" && $samay >= "16" && $samay < "20") {	 //18 -20
$dpt2_dates_posts[] = array(
'vaccine' => 'DPT2',
'child_id' => $dpt2_date->child_unq_id,
'mobile' => $dpt2_date->child_contact,
'message' => $dpt2_date->child_name.' का DPT 2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Thu") {	
$dpt2_dates_posts[] = array(
'vaccine' => 'DPT2',
'child_id' => $dpt2_date->child_unq_id,
'mobile' => $dpt2_date->child_contact,
'message' => $dpt2_date->child_name.' का DPT 2 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$dpt2_dates_posts[] = array(
'vaccine' => 'DPT2',
'child_id' => $dpt2_date->child_unq_id,
'mobile' => $dpt2_date->child_contact,
'message' => $dpt2_date->child_name.' का DPT 2 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$dpt2_dates_posts[] = array(
'vaccine' => 'DPT2',
'child_id' => $dpt2_date->child_unq_id,
'mobile' => $dpt2_date->child_contact,
'message' => $dpt2_date->child_name.' का DPT 2 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}


}

$dpt3_dates_posts = array();
foreach($dpt3_dates as $dpt3_date) {
// $dpt3_dates_posts[] = array(
// 'vaccine' => 'DPT3',
// 'child_id' => $dpt3_date->child_unq_id,
// 'mobile' => $dpt3_date->child_contact,
// 'message' => $dpt3_date->child_name.' का DPT 3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );





if ($dinnank == "Sun") {	
$dpt3_dates_posts[] = array(
'vaccine' => 'DPT3',
'child_id' => $dpt3_date->child_unq_id,
'mobile' => $dpt3_date->child_contact,
'message' => $dpt3_date->child_name.' का DPT 3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "10") {	 //8 to 10
$dpt3_dates_posts[] = array(
'vaccine' => 'DPT3',
'child_id' => $dpt3_date->child_unq_id,
'mobile' => $dpt3_date->child_contact,
'message' => $dpt3_date->child_name.' का DPT 3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Wed" && $samay >= "16" && $samay < "20") {	 //18 -20
$dpt3_dates_posts[] = array(
'vaccine' => 'DPT3',
'child_id' => $dpt3_date->child_unq_id,
'mobile' => $dpt3_date->child_contact,
'message' => $dpt3_date->child_name.' का DPT 3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Thu") {	
$dpt3_dates_posts[] = array(
'vaccine' => 'DPT3',
'child_id' => $dpt3_date->child_unq_id,
'mobile' => $dpt3_date->child_contact,
'message' => $dpt3_date->child_name.' का DPT 3 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$dpt3_dates_posts[] = array(
'vaccine' => 'DPT3',
'child_id' => $dpt3_date->child_unq_id,
'mobile' => $dpt3_date->child_contact,
'message' => $dpt3_date->child_name.' का DPT 3 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$dpt3_dates_posts[] = array(
'vaccine' => 'DPT3',
'child_id' => $dpt3_date->child_unq_id,
'mobile' => $dpt3_date->child_contact,
'message' => $dpt3_date->child_name.' का DPT 3 टीका का टीकाकरण दिनांक '.$sat.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',

);

}

}

//print_r($hepb1_dates);

$hepb1_dates_posts = array();
foreach($hepb1_dates as $hepb1_date) {
// $hepb1_dates_posts[] = array(
// 'vaccine' => 'HEPB1',
// 'child_id' => $hepb1_date->child_unq_id,
// 'mobile' => $hepb1_date->child_contact,
// 'message' => $hepb1_date->child_name.' का Hepb 1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );

if ($dinnank == "Sun") {	
$hepb1_dates_posts[] = array(
'vaccine' => 'HEPB1',
'child_id' => $hepb1_date->child_unq_id,
'mobile' => $hepb1_date->child_contact,
'message' => $hepb1_date->child_name.' का Hepb 1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "10") {	 //8 to 10
$hepb1_dates_posts[] = array(
'vaccine' => 'HEPB1',
'child_id' => $hepb1_date->child_unq_id,
'mobile' => $hepb1_date->child_contact,
'message' => $hepb1_date->child_name.' का Hepb 1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "16" && $samay < "20") {	 //18 -20
$hepb1_dates_posts[] = array(
'vaccine' => 'HEPB1',
'child_id' => $hepb1_date->child_unq_id,
'mobile' => $hepb1_date->child_contact,
'message' => $hepb1_date->child_name.' का Hepb 1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Thu") {	
$hepb1_dates_posts[] = array(
'vaccine' => 'HEPB1',
'child_id' => $hepb1_date->child_unq_id,
'mobile' => $hepb1_date->child_contact,
'message' => $hepb1_date->child_name.' का Hepb 1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$hepb1_dates_posts[] = array(
'vaccine' => 'HEPB1',
'child_id' => $hepb1_date->child_unq_id,
'mobile' => $hepb1_date->child_contact,
'message' => $hepb1_date->child_name.' का Hepb 1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$hepb1_dates_posts[] = array(
'vaccine' => 'HEPB1',
'child_id' => $hepb1_date->child_unq_id,
'mobile' => $hepb1_date->child_contact,
'message' => $hepb1_date->child_name.' का Hepb 1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}

}


$hepb2_dates_posts = array();
foreach($hepb2_dates as $hepb2_date) {
// $hepb2_dates_posts[] = array(
// 'vaccine' => 'HEPB2',
// 'child_id' => $hepb2_date->child_unq_id,
// 'mobile' => $hepb2_date->child_contact,
// 'message' => $hepb2_date->child_name.' का Hepb 2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );


if ($dinnank == "Sun") {	
$hepb2_dates_posts[] = array(
'vaccine' => 'HEPB2',
'child_id' => $hepb2_date->child_unq_id,
'mobile' => $hepb2_date->child_contact,
'message' => $hepb2_date->child_name.' का Hepb 2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "10") {	 //8 to 10
$hepb2_dates_posts[] = array(
'vaccine' => 'HEPB2',
'child_id' => $hepb2_date->child_unq_id,
'mobile' => $hepb2_date->child_contact,
'message' => $hepb2_date->child_name.' का Hepb 2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "16" && $samay < "20") {	 //18 -20
$hepb2_dates_posts[] = array(
'vaccine' => 'HEPB2',
'child_id' => $hepb2_date->child_unq_id,
'mobile' => $hepb2_date->child_contact,
'message' => $hepb2_date->child_name.' का Hepb 2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Thu") {	
$hepb2_dates_posts[] = array(
'vaccine' => 'HEPB2',
'child_id' => $hepb2_date->child_unq_id,
'mobile' => $hepb2_date->child_contact,
'message' => $hepb2_date->child_name.' का Hepb 2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$hepb2_dates_posts[] = array(
'vaccine' => 'HEPB2',
'child_id' => $hepb2_date->child_unq_id,
'mobile' => $hepb2_date->child_contact,
'message' => $hepb2_date->child_name.' का Hepb 2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$hepb2_dates_posts[] = array(
'vaccine' => 'HEPB2',
'child_id' => $hepb2_date->child_unq_id,
'mobile' => $hepb2_date->child_contact,
'message' => $hepb2_date->child_name.' का Hepb 2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}





}



$hepb3_dates_posts = array();
foreach($hepb3_dates as $hepb3_date) {
// $hepb3_dates_posts[] = array(
// 'vaccine' => 'HEPB3',
// 'child_id' => $hepb3_date->child_unq_id,
// 'mobile' => $hepb3_date->child_contact,
// 'message' => $hepb3_date->child_name.' का Hepb 3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );

if ($dinnank == "Sun") {	
$hepb3_dates_posts[] = array(
'vaccine' => 'HEPB3',
'child_id' => $hepb3_date->child_unq_id,
'mobile' => $hepb3_date->child_contact,
'message' => $hepb3_date->child_name.' का Hepb 3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "10") {	 //8 to 10
$hepb3_dates_posts[] = array(
'vaccine' => 'HEPB3',
'child_id' => $hepb3_date->child_unq_id,
'mobile' => $hepb3_date->child_contact,
'message' => $hepb3_date->child_name.' का Hepb 3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "16" && $samay < "20") {	 //18 -20
$hepb3_dates_posts[] = array(
'vaccine' => 'HEPB3',
'child_id' => $hepb3_date->child_unq_id,
'mobile' => $hepb3_date->child_contact,
'message' => $hepb3_date->child_name.' का Hepb 3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Thu") {	
$hepb3_dates_posts[] = array(
'vaccine' => 'HEPB3',
'child_id' => $hepb3_date->child_unq_id,
'mobile' => $hepb3_date->child_contact,
'message' => $hepb3_date->child_name.' का Hepb 3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$hepb3_dates_posts[] = array(
'vaccine' => 'HEPB3',
'child_id' => $hepb3_date->child_unq_id,
'mobile' => $hepb3_date->child_contact,
'message' => $hepb3_date->child_name.' का Hepb 3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$hepb3_dates_posts[] = array(
'vaccine' => 'HEPB3',
'child_id' => $hepb3_date->child_unq_id,
'mobile' => $hepb3_date->child_contact,
'message' => $hepb3_date->child_name.' का Hepb 3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}


}





//  RVV's Displays Starts From Here 


$rvv1_dates_posts = array();
foreach($rvv1_dates as $rvv1_date) {
// $rvv1_dates_posts[] = array(
// 'vaccine' => 'RVV1',
// 'child_id' => $rvv1_date->child_unq_id,
// 'mobile' => $rvv1_date->child_contact,
// 'message' => $rvv1_date->child_name.' का RVV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );


if ($dinnank == "Sun") {	
$rvv1_dates_posts[] = array(
'vaccine' => 'RVV1',
'child_id' => $rvv1_date->child_unq_id,
'mobile' => $rvv1_date->child_contact,
'message' => $rvv1_date->child_name.' का RVV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "10") {	 //8 to 10
$rvv1_dates_posts[] = array(
'vaccine' => 'RVV1',
'child_id' => $rvv1_date->child_unq_id,
'mobile' => $rvv1_date->child_contact,
'message' => $rvv1_date->child_name.' का RVV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "16" && $samay < "20") {	 //18 -20
$rvv1_dates_posts[] = array(
'vaccine' => 'RVV1',
'child_id' => $rvv1_date->child_unq_id,
'mobile' => $rvv1_date->child_contact,
'message' => $rvv1_date->child_name.' का RVV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Thu") {	
$rvv1_dates_posts[] = array(
'vaccine' => 'RVV1',
'child_id' => $rvv1_date->child_unq_id,
'mobile' => $rvv1_date->child_contact,
'message' => $rvv1_date->child_name.' का RVV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$rvv1_dates_posts[] = array(
'vaccine' => 'RVV1',
'child_id' => $rvv1_date->child_unq_id,
'mobile' => $rvv1_date->child_contact,
'message' => $rvv1_date->child_name.' का RVV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$rvv1_dates_posts[] = array(
'vaccine' => 'RVV1',
'child_id' => $rvv1_date->child_unq_id,
'mobile' => $rvv1_date->child_contact,
'message' => $rvv1_date->child_name.' का RVV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}











}



$rvv2_dates_posts = array();
foreach($rvv2_dates as $rvv2_date) {
// $rvv2_dates_posts[] = array(
// 'vaccine' => 'RVV2',
// 'child_id' => $rvv2_date->child_unq_id,
// 'mobile' => $rvv2_date->child_contact,
// 'message' => $rvv2_date->child_name.' का RVV2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );


if ($dinnank == "Sun") {	
$rvv2_dates_posts[] = array(
'vaccine' => 'RVV2',
'child_id' => $rvv2_date->child_unq_id,
'mobile' => $rvv2_date->child_contact,
'message' => $rvv2_date->child_name.' का RVV2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "10") {	 //8 to 10
$rvv2_dates_posts[] = array(
'vaccine' => 'RVV2',
'child_id' => $rvv2_date->child_unq_id,
'mobile' => $rvv2_date->child_contact,
'message' => $rvv2_date->child_name.' का RVV2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "16" && $samay < "20") {	 //18 -20
$rvv2_dates_posts[] = array(
'vaccine' => 'RVV2',
'child_id' => $rvv2_date->child_unq_id,
'mobile' => $rvv2_date->child_contact,
'message' => $rvv2_date->child_name.' का RVV2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Thu") {	
$rvv2_dates_posts[] = array(
'vaccine' => 'RVV2',
'child_id' => $rvv2_date->child_unq_id,
'mobile' => $rvv2_date->child_contact,
'message' => $rvv2_date->child_name.' का RVV2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$rvv2_dates_posts[] = array(
'vaccine' => 'RVV2',
'child_id' => $rvv2_date->child_unq_id,
'mobile' => $rvv2_date->child_contact,
'message' => $rvv2_date->child_name.' का RVV2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$rvv2_dates_posts[] = array(
'vaccine' => 'RVV2',
'child_id' => $rvv2_date->child_unq_id,
'mobile' => $rvv2_date->child_contact,
'message' => $rvv2_date->child_name.' का RVV2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}












}


$rvv3_dates_posts = array();
foreach($rvv3_dates as $rvv3_date) {
// $rvv3_dates_posts[] = array(
// 'vaccine' => 'RVV3',
// 'child_id' => $rvv3_date->child_unq_id,
// 'mobile' => $rvv3_date->child_contact,
// 'message' => $rvv3_date->child_name.' का RVV3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );



if ($dinnank == "Sun") {	
$rvv3_dates_posts[] = array(
'vaccine' => 'RVV3',
'child_id' => $rvv3_date->child_unq_id,
'mobile' => $rvv3_date->child_contact,
'message' => $rvv3_date->child_name.' का RVV3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "10") {	 //8 to 10
$rvv3_dates_posts[] = array(
'vaccine' => 'RVV3',
'child_id' => $rvv3_date->child_unq_id,
'mobile' => $rvv3_date->child_contact,
'message' => $rvv3_date->child_name.' का RVV3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "16" && $samay < "20") {	 //18 -20
$rvv3_dates_posts[] = array(
'vaccine' => 'RVV3',
'child_id' => $rvv3_date->child_unq_id,
'mobile' => $rvv3_date->child_contact,
'message' => $rvv3_date->child_name.' का RVV3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Thu") {	
$rvv3_dates_posts[] = array(
'vaccine' => 'RVV3',
'child_id' => $rvv3_date->child_unq_id,
'mobile' => $rvv3_date->child_contact,
'message' => $rvv3_date->child_name.' का RVV3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$rvv3_dates_posts[] = array(
'vaccine' => 'RVV3',
'child_id' => $rvv3_date->child_unq_id,
'mobile' => $rvv3_date->child_contact,
'message' => $rvv3_date->child_name.' का RVV3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$rvv3_dates_posts[] = array(
'vaccine' => 'RVV3',
'child_id' => $rvv3_date->child_unq_id,
'mobile' => $rvv3_date->child_contact,
'message' => $rvv3_date->child_name.' का RVV3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}




}


// End Of RVV's 



//  IPV's Displays Starts From Here 


$ipv1_dates_posts = array();
foreach($ipv1_dates as $ipv1_date) {
// $ipv1_dates_posts[] = array(
// 'vaccine' => 'IPV1',
// 'child_id' => $ipv1_date->child_unq_id,
// 'mobile' => $ipv1_date->child_contact,
// 'message' => $ipv1_date->child_name.' का IPV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );


if ($dinnank == "Sun") {	
$ipv1_dates_posts[] = array(
'vaccine' => 'IPV1',
'child_id' => $ipv1_date->child_unq_id,
'mobile' => $ipv1_date->child_contact,
'message' => $ipv1_date->child_name.' का IPV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "10") {	 //8 to 10
$ipv1_dates_posts[] = array(
'vaccine' => 'IPV1',
'child_id' => $ipv1_date->child_unq_id,
'mobile' => $ipv1_date->child_contact,
'message' => $ipv1_date->child_name.' का IPV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "16" && $samay < "20") {	 //18 -20
$ipv1_dates_posts[] = array(
'vaccine' => 'IPV1',
'child_id' => $ipv1_date->child_unq_id,
'mobile' => $ipv1_date->child_contact,
'message' => $ipv1_date->child_name.' का IPV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Thu") {	
$ipv1_dates_posts[] = array(
'vaccine' => 'IPV1',
'child_id' => $ipv1_date->child_unq_id,
'mobile' => $ipv1_date->child_contact,
'message' => $ipv1_date->child_name.' का IPV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$ipv1_dates_posts[] = array(
'vaccine' => 'IPV1',
'child_id' => $ipv1_date->child_unq_id,
'mobile' => $ipv1_date->child_contact,
'message' => $ipv1_date->child_name.' का IPV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$ipv1_dates_posts[] = array(
'vaccine' => 'IPV1',
'child_id' => $ipv1_date->child_unq_id,
'mobile' => $ipv1_date->child_contact,
'message' => $ipv1_date->child_name.' का IPV1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}


}



$ipv2_dates_posts = array();
foreach($ipv2_dates as $ipv2_date) {
// $ipv2_dates_posts[] = array(
// 'vaccine' => 'IPV2',
// 'child_id' => $ipv2_date->child_unq_id,
// 'mobile' => $ipv2_date->child_contact,
// 'message' => $ipv2_date->child_name.' का IPV2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );


if ($dinnank == "Sun") {	
$ipv2_dates_posts[] = array(
'vaccine' => 'IPV2',
'child_id' => $ipv2_date->child_unq_id,
'mobile' => $ipv2_date->child_contact,
'message' => $ipv2_date->child_name.' का IPV2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "10") {	 //8 to 10
$ipv2_dates_posts[] = array(
'vaccine' => 'IPV2',
'child_id' => $ipv2_date->child_unq_id,
'mobile' => $ipv2_date->child_contact,
'message' => $ipv2_date->child_name.' का IPV2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "16" && $samay < "20") {	 //18 -20
$ipv2_dates_posts[] = array(
'vaccine' => 'IPV2',
'child_id' => $ipv2_date->child_unq_id,
'mobile' => $ipv2_date->child_contact,
'message' => $ipv2_date->child_name.' का IPV2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Thu") {	
$ipv2_dates_posts[] = array(
'vaccine' => 'IPV2',
'child_id' => $ipv2_date->child_unq_id,
'mobile' => $ipv2_date->child_contact,
'message' => $ipv2_date->child_name.' का IPV2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$ipv2_dates_posts[] = array(
'vaccine' => 'IPV2',
'child_id' => $ipv2_date->child_unq_id,
'mobile' => $ipv2_date->child_contact,
'message' => $ipv2_date->child_name.' का IPV2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$ipv2_dates_posts[] = array(
'vaccine' => 'IPV2',
'child_id' => $ipv2_date->child_unq_id,
'mobile' => $ipv2_date->child_contact,
'message' => $ipv2_date->child_name.' का IPV2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}


}

// End Of RVV's 



$penta1_dates_posts = array();
foreach($penta1_dates as $penta1_date) {
// $penta1_dates_posts[] = array(
// 'vaccine' => 'PENTA1',
// 'child_id' => $penta1_date->child_unq_id,
// 'mobile' => $penta1_date->child_contact,
// 'message' => $penta1_date->child_name.' का PENTA 1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );

if ($dinnank == "Sun") {	
$penta1_dates_posts[] = array(
'vaccine' => 'PENTA1',
'child_id' => $penta1_date->child_unq_id,
'mobile' => $penta1_date->child_contact,
'message' => $penta1_date->child_name.' का PENTA 1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "10") {	 //8 to 10
$penta1_dates_posts[] = array(
'vaccine' => 'PENTA1',
'child_id' => $penta1_date->child_unq_id,
'mobile' => $penta1_date->child_contact,
'message' => $penta1_date->child_name.' का PENTA 1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "16" && $samay < "20") {	 //18 -20
$penta1_dates_posts[] = array(
'vaccine' => 'PENTA1',
'child_id' => $penta1_date->child_unq_id,
'mobile' => $penta1_date->child_contact,
'message' => $penta1_date->child_name.' का PENTA 1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Thu") {	
$penta1_dates_posts[] = array(
'vaccine' => 'PENTA1',
'child_id' => $penta1_date->child_unq_id,
'mobile' => $penta1_date->child_contact,
'message' => $penta1_date->child_name.' का PENTA 1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$penta1_dates_posts[] = array(
'vaccine' => 'PENTA1',
'child_id' => $penta1_date->child_unq_id,
'mobile' => $penta1_date->child_contact,
'message' => $penta1_date->child_name.' का PENTA 1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$penta1_dates_posts[] = array(
'vaccine' => 'PENTA1',
'child_id' => $penta1_date->child_unq_id,
'mobile' => $penta1_date->child_contact,
'message' => $penta1_date->child_name.' का PENTA 1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}



}



$penta2_dates_posts = array();
foreach($penta2_dates as $penta2_date) {
// $penta2_dates_posts[] = array(
// 'vaccine' => 'PENTA2',
// 'child_id' => $penta2_date->child_unq_id,
// 'mobile' => $penta2_date->child_contact,
// 'message' => $penta2_date->child_name.' का PENTA 2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );

if ($dinnank == "Sun") {	
$penta2_dates_posts[] = array(
'vaccine' => 'PENTA2',
'child_id' => $penta2_date->child_unq_id,
'mobile' => $penta2_date->child_contact,
'message' => $penta2_date->child_name.' का PENTA 2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "10") {	 //8 to 10
$penta2_dates_posts[] = array(
'vaccine' => 'PENTA2',
'child_id' => $penta2_date->child_unq_id,
'mobile' => $penta2_date->child_contact,
'message' => $penta2_date->child_name.' का PENTA 2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "16" && $samay < "20") {	 //18 -20
$penta2_dates_posts[] = array(
'vaccine' => 'PENTA2',
'child_id' => $penta2_date->child_unq_id,
'mobile' => $penta2_date->child_contact,
'message' => $penta2_date->child_name.' का PENTA 2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Thu") {	
$penta2_dates_posts[] = array(
'vaccine' => 'PENTA2',
'child_id' => $penta2_date->child_unq_id,
'mobile' => $penta2_date->child_contact,
'message' => $penta2_date->child_name.' का PENTA 2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$penta2_dates_posts[] = array(
'vaccine' => 'PENTA2',
'child_id' => $penta2_date->child_unq_id,
'mobile' => $penta2_date->child_contact,
'message' => $penta2_date->child_name.' का PENTA 2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$penta2_dates_posts[] = array(
'vaccine' => 'PENTA2',
'child_id' => $penta2_date->child_unq_id,
'mobile' => $penta2_date->child_contact,
'message' => $penta2_date->child_name.' का PENTA 2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}



}



$penta3_dates_posts = array();
foreach($penta3_dates as $penta3_date) {
// $penta3_dates_posts[] = array(
// 'vaccine' => 'PENTA3',
// 'child_id' => $penta3_date->child_unq_id,
// 'mobile' => $penta3_date->child_contact,
// 'message' => $penta3_date->child_name.' का PENTA 3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );


if ($dinnank == "Sun") {	
$penta3_dates_posts[] = array(
'vaccine' => 'PENTA3',
'child_id' => $penta3_date->child_unq_id,
'mobile' => $penta3_date->child_contact,
'message' => $penta3_date->child_name.' का PENTA 3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "10") {	 //8 to 10
$penta3_dates_posts[] = array(
'vaccine' => 'PENTA3',
'child_id' => $penta3_date->child_unq_id,
'mobile' => $penta3_date->child_contact,
'message' => $penta3_date->child_name.' का PENTA 3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "16" && $samay < "20") {	 //18 -20
$penta3_dates_posts[] = array(
'vaccine' => 'PENTA3',
'child_id' => $penta3_date->child_unq_id,
'mobile' => $penta3_date->child_contact,
'message' => $penta3_date->child_name.' का PENTA 3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Thu") {	
$penta3_dates_posts[] = array(
'vaccine' => 'PENTA3',
'child_id' => $penta3_date->child_unq_id,
'mobile' => $penta3_date->child_contact,
'message' => $penta3_date->child_name.' का PENTA 3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$penta3_dates_posts[] = array(
'vaccine' => 'PENTA3',
'child_id' => $penta3_date->child_unq_id,
'mobile' => $penta3_date->child_contact,
'message' => $penta3_date->child_name.' का PENTA 3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$penta3_dates_posts[] = array(
'vaccine' => 'PENTA3',
'child_id' => $penta3_date->child_unq_id,
'mobile' => $penta3_date->child_contact,
'message' => $penta3_date->child_name.' का PENTA 3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}



}


$ipv_dates_posts = array();
foreach($ipv_dates as $ipv_date) {
// $ipv_dates_posts[] = array(
// 'vaccine' => 'IPV',
// 'child_id' => $ipv_date->child_unq_id,
// 'mobile' => $ipv_date->child_contact,
// 'message' => $ipv_date->child_name.' का IPV टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );

if ($dinnank == "Sun") {	
$ipv_dates_posts[] = array(
'vaccine' => 'IPV',
'child_id' => $ipv_date->child_unq_id,
'mobile' => $ipv_date->child_contact,
'message' => $ipv_date->child_name.' का IPV टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "10") {	 //8 to 10
$ipv_dates_posts[] = array(
'vaccine' => 'IPV',
'child_id' => $ipv_date->child_unq_id,
'mobile' => $ipv_date->child_contact,
'message' => $ipv_date->child_name.' का IPV टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "16" && $samay < "20") {	 //18 -20
$ipv_dates_posts[] = array(
'vaccine' => 'IPV',
'child_id' => $ipv_date->child_unq_id,
'mobile' => $ipv_date->child_contact,
'message' => $ipv_date->child_name.' का IPV टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Thu") {	
$ipv_dates_posts[] = array(
'vaccine' => 'IPV',
'child_id' => $ipv_date->child_unq_id,
'mobile' => $ipv_date->child_contact,
'message' => $ipv_date->child_name.' का IPV टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$ipv_dates_posts[] = array(
'vaccine' => 'IPV',
'child_id' => $ipv_date->child_unq_id,
'mobile' => $ipv_date->child_contact,
'message' => $ipv_date->child_name.' का IPV टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$ipv_dates_posts[] = array(
'vaccine' => 'IPV',
'child_id' => $ipv_date->child_unq_id,
'mobile' => $ipv_date->child_contact,
'message' => $ipv_date->child_name.' का IPV टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}




}


$mmr_dates_posts = array();
foreach($mmr_dates as $mmr_date) {
// $mmr_dates_posts[] = array(
// 'vaccine' => 'MMR',
// 'child_id' => $mmr_date->child_unq_id,
// 'mobile' => $mmr_date->child_contact,
// 'message' => $mmr_date->child_name.' का MMR टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );

if ($dinnank == "Sun") {	
$mmr_dates_posts[] = array(
'vaccine' => 'MMR',
'child_id' => $mmr_date->child_unq_id,
'mobile' => $mmr_date->child_contact,
'message' => $mmr_date->child_name.' का MMR टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "10") {	 //8 to 10
$mmr_dates_posts[] = array(
'vaccine' => 'MMR',
'child_id' => $mmr_date->child_unq_id,
'mobile' => $mmr_date->child_contact,
'message' => $mmr_date->child_name.' का MMR टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "16" && $samay < "20") {	 //18 -20
$mmr_dates_posts[] = array(
'vaccine' => 'MMR',
'child_id' => $mmr_date->child_unq_id,
'mobile' => $mmr_date->child_contact,
'message' => $mmr_date->child_name.' का MMR टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Thu") {	
$mmr_dates_posts[] = array(
'vaccine' => 'MMR',
'child_id' => $mmr_date->child_unq_id,
'mobile' => $mmr_date->child_contact,
'message' => $mmr_date->child_name.' का MMR टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$mmr_dates_posts[] = array(
'vaccine' => 'MMR',
'child_id' => $mmr_date->child_unq_id,
'mobile' => $mmr_date->child_contact,
'message' => $mmr_date->child_name.' का MMR टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$mmr_dates_posts[] = array(
'vaccine' => 'MMR',
'child_id' => $mmr_date->child_unq_id,
'mobile' => $mmr_date->child_contact,
'message' => $mmr_date->child_name.' का MMR टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}


}


$mmr2_dates_posts = array();
foreach($mmr2_dates as $mmr2_date) {
// $mmr2_dates_posts[] = array(
// 'vaccine' => 'MMR2',
// 'child_id' => $mmr2_date->child_unq_id,
// 'mobile' => $mmr2_date->child_contact,
// 'message' => $mmr2_date->child_name.' का MMR2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );


if ($dinnank == "Sun") {	
$mmr2_dates_posts[] = array(
'vaccine' => 'MMR2',
'child_id' => $mmr2_date->child_unq_id,
'mobile' => $mmr2_date->child_contact,
'message' => $mmr2_date->child_name.' का MMR2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "10") {	 //8 to 10
$mmr2_dates_posts[] = array(
'vaccine' => 'MMR2',
'child_id' => $mmr2_date->child_unq_id,
'mobile' => $mmr2_date->child_contact,
'message' => $mmr2_date->child_name.' का MMR2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "16" && $samay < "20") {	 //18 -20
$mmr2_dates_posts[] = array(
'vaccine' => 'MMR2',
'child_id' => $mmr2_date->child_unq_id,
'mobile' => $mmr2_date->child_contact,
'message' => $mmr2_date->child_name.' का MMR2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Thu") {	
$mmr2_dates_posts[] = array(
'vaccine' => 'MMR2',
'child_id' => $mmr2_date->child_unq_id,
'mobile' => $mmr2_date->child_contact,
'message' => $mmr2_date->child_name.' का MMR2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$mmr2_dates_posts[] = array(
'vaccine' => 'MMR2',
'child_id' => $mmr2_date->child_unq_id,
'mobile' => $mmr2_date->child_contact,
'message' => $mmr2_date->child_name.' का MMR2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$mmr2_dates_posts[] = array(
'vaccine' => 'MMR2',
'child_id' => $mmr2_date->child_unq_id,
'mobile' => $mmr2_date->child_contact,
'message' => $mmr2_date->child_name.' का MMR2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}



}

$je1_dates_posts = array();
foreach($je1_dates as $je1_date) {
// $je1_dates_posts[] = array(
// 'vaccine' => 'JE1',
// 'child_id' => $je1_date->child_unq_id,
// 'mobile' => $je1_date->child_contact,
// 'message' => $je1_date->child_name.' का JE1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );

if ($dinnank == "Sun") {	
$je1_dates_posts[] = array(
'vaccine' => 'JE1',
'child_id' => $je1_date->child_unq_id,
'mobile' => $je1_date->child_contact,
'message' => $je1_date->child_name.' का JE1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "10") {	 //8 to 10
$je1_dates_posts[] = array(
'vaccine' => 'JE1',
'child_id' => $je1_date->child_unq_id,
'mobile' => $je1_date->child_contact,
'message' => $je1_date->child_name.' का JE1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "16" && $samay < "20") {	 //18 -20
$je1_dates_posts[] = array(
'vaccine' => 'JE1',
'child_id' => $je1_date->child_unq_id,
'mobile' => $je1_date->child_contact,
'message' => $je1_date->child_name.' का JE1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Thu") {	
$je1_dates_posts[] = array(
'vaccine' => 'JE1',
'child_id' => $je1_date->child_unq_id,
'mobile' => $je1_date->child_contact,
'message' => $je1_date->child_name.' का JE1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$je1_dates_posts[] = array(
'vaccine' => 'JE1',
'child_id' => $je1_date->child_unq_id,
'mobile' => $je1_date->child_contact,
'message' => $je1_date->child_name.' का JE1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$je1_dates_posts[] = array(
'vaccine' => 'JE1',
'child_id' => $je1_date->child_unq_id,
'mobile' => $je1_date->child_contact,
'message' => $je1_date->child_name.' का JE1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}



}


$je2_dates_posts = array();
foreach($je2_dates as $je2_date) {
// $je2_dates_posts[] = array(
// 'vaccine' => 'JE2',
// 'child_id' => $je2_date->child_unq_id,
// 'mobile' => $je2_date->child_contact,
// 'message' => $je2_date->child_name.' का JE2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );


if ($dinnank == "Sun") {	
$je2_dates_posts[] = array(
'vaccine' => 'JE2',
'child_id' => $je2_date->child_unq_id,
'mobile' => $je2_date->child_contact,
'message' => $je2_date->child_name.' का JE2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "10") {	 //8 to 10
$je2_dates_posts[] = array(
'vaccine' => 'JE2',
'child_id' => $je2_date->child_unq_id,
'mobile' => $je2_date->child_contact,
'message' => $je2_date->child_name.' का JE2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "16" && $samay < "20") {	 //18 -20
$je2_dates_posts[] = array(
'vaccine' => 'JE2',
'child_id' => $je2_date->child_unq_id,
'mobile' => $je2_date->child_contact,
'message' => $je2_date->child_name.' का JE2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Thu") {	
$je2_dates_posts[] = array(
'vaccine' => 'JE2',
'child_id' => $je2_date->child_unq_id,
'mobile' => $je2_date->child_contact,
'message' => $je2_date->child_name.' का JE2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$je2_dates_posts[] = array(
'vaccine' => 'JE2',
'child_id' => $je2_date->child_unq_id,
'mobile' => $je2_date->child_contact,
'message' => $je2_date->child_name.' का JE2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$je2_dates_posts[] = array(
'vaccine' => 'JE2',
'child_id' => $je2_date->child_unq_id,
'mobile' => $je2_date->child_contact,
'message' => $je2_date->child_name.' का JE2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}


}





$vit_a_1_dates_posts = array();
foreach($vit_a_1_dates as $vit_a_1_date) {
// $vit_a_1_dates_posts[] = array(
// 'vaccine' => 'VIT_A_1',
// 'child_id' => $vit_a_1_date->child_unq_id,
// 'mobile' => $vit_a_1_date->child_contact,
// 'message' => $vit_a_1_date->child_name.' का VITAMIN A 1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );


if ($dinnank == "Sun") {	
$vit_a_1_dates_posts[] = array(
'vaccine' => 'VIT_A_1',
'child_id' => $vit_a_1_date->child_unq_id,
'mobile' => $vit_a_1_date->child_contact,
'message' => $vit_a_1_date->child_name.' का VITAMIN A 1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "10") {	 //8 to 10
$vit_a_1_dates_posts[] = array(
'vaccine' => 'VIT_A_1',
'child_id' => $vit_a_1_date->child_unq_id,
'mobile' => $vit_a_1_date->child_contact,
'message' => $vit_a_1_date->child_name.' का VITAMIN A 1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "16" && $samay < "20") {	 //18 -20
$vit_a_1_dates_posts[] = array(
'vaccine' => 'VIT_A_1',
'child_id' => $vit_a_1_date->child_unq_id,
'mobile' => $vit_a_1_date->child_contact,
'message' => $vit_a_1_date->child_name.' का VITAMIN A 1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Thu") {	
$vit_a_1_dates_posts[] = array(
'vaccine' => 'VIT_A_1',
'child_id' => $vit_a_1_date->child_unq_id,
'mobile' => $vit_a_1_date->child_contact,
'message' => $vit_a_1_date->child_name.' का VITAMIN A 1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$vit_a_1_dates_posts[] = array(
'vaccine' => 'VIT_A_1',
'child_id' => $vit_a_1_date->child_unq_id,
'mobile' => $vit_a_1_date->child_contact,
'message' => $vit_a_1_date->child_name.' का VITAMIN A 1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$vit_a_1_dates_posts[] = array(
'vaccine' => 'VIT_A_1',
'child_id' => $vit_a_1_date->child_unq_id,
'mobile' => $vit_a_1_date->child_contact,
'message' => $vit_a_1_date->child_name.' का VITAMIN A 1 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}

}


$vit_a_2_dates_posts = array();
foreach($vit_a_2_dates as $vit_a_2_date) {
// $vit_a_2_dates_posts[] = array(
// 'vaccine' => 'VIT_A_2',
// 'child_id' => $vit_a_2_date->child_unq_id,
// 'mobile' => $vit_a_2_date->child_contact,
// 'message' => $vit_a_2_date->child_name.' का VITAMIN A 2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
// );


if ($dinnank == "Sun") {	
$vit_a_2_dates_posts[] = array(
'vaccine' => 'VIT_A_2',
'child_id' => $vit_a_2_date->child_unq_id,
'mobile' => $vit_a_2_date->child_contact,
'message' => $vit_a_2_date->child_name.' का VITAMIN A 2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}elseif ($dinnank == "Wed" && $samay >= "8" && $samay < "10") {	 //8 to 10
$vit_a_2_dates_posts[] = array(
'vaccine' => 'VIT_A_2',
'child_id' => $vit_a_2_date->child_unq_id,
'mobile' => $vit_a_2_date->child_contact,
'message' => $vit_a_2_date->child_name.' का VITAMIN A 2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Wed" && $samay >= "16" && $samay < "20") {	 //18 -20
$vit_a_2_dates_posts[] = array(
'vaccine' => 'VIT_A_2',
'child_id' => $vit_a_2_date->child_unq_id,
'mobile' => $vit_a_2_date->child_contact,
'message' => $vit_a_2_date->child_name.' का VITAMIN A 2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Thu") {	
$vit_a_2_dates_posts[] = array(
'vaccine' => 'VIT_A_2',
'child_id' => $vit_a_2_date->child_unq_id,
'mobile' => $vit_a_2_date->child_contact,
'message' => $vit_a_2_date->child_name.' का VITAMIN A 2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}elseif ($dinnank == "Sat" && $samay >= "8" && $samay < "10") {	 //8-10
$vit_a_2_dates_posts[] = array(
'vaccine' => 'VIT_A_2',
'child_id' => $vit_a_2_date->child_unq_id,
'mobile' => $vit_a_2_date->child_contact,
'message' => $vit_a_2_date->child_name.' का VITAMIN A 2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);

}elseif ($dinnank == "Sat" && $samay >= "18" && $samay < "20") {	
$vit_a_2_dates_posts[] = array(
'vaccine' => 'VIT_A_2',
'child_id' => $vit_a_2_date->child_unq_id,
'mobile' => $vit_a_2_date->child_contact,
'message' => $vit_a_2_date->child_name.' का VITAMIN A 2 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);



}






}



$vit_a_3_dates_posts = array();
foreach($vit_a_3_dates as $vit_a_3_date) {
$vit_a_3_dates_posts[] = array(
'vaccine' => 'VIT_A_3',
// 'due_date' => $hep_b_date->Hep_B_date,
'child_id' => $vit_a_1_date->child_unq_id,
'mobile' => $vit_a_1_date->child_contact,
'message' => $vit_a_1_date->child_name.' का VITAMIN A 3 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}


$vit_a_4_dates_posts = array();
foreach($vit_a_4_dates as $vit_a_4_date) {
$vit_a_4_dates_posts[] = array(
'vaccine' => 'VIT_A_4',
// 'due_date' => $hep_b_date->Hep_B_date,
'child_id' => $vit_a_4_date->child_unq_id,
'mobile' => $vit_a_4_date->child_contact,
'message' => $vit_a_4_date->child_name.' का VITAMIN A 4 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}


$vit_a_5_dates_posts = array();
foreach($vit_a_5_dates as $vit_a_5_date) {
$vit_a_5_dates_posts[] = array(
'vaccine' => 'VIT_A_5',
// 'due_date' => $hep_b_date->Hep_B_date,
'child_id' => $vit_a_5_date->child_unq_id,
'mobile' => $vit_a_5_date->child_contact,
'message' => $vit_a_5_date->child_name.' का VITAMIN A 5 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}


$vit_a_6_dates_posts = array();
foreach($vit_a_6_dates as $vit_a_6_date) {
$vit_a_6_dates_posts[] = array(
'vaccine' => 'VIT_A_6',
// 'due_date' => $hep_b_date->Hep_B_date,
'child_id' => $vit_a_6_date->child_unq_id,
'mobile' => $vit_a_6_date->child_contact,
'message' => $vit_a_6_date->child_name.' का VITAMIN A 6 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}


$vit_a_7_dates_posts = array();
foreach($vit_a_7_dates as $vit_a_7_date) {
$vit_a_7_dates_posts[] = array(
'vaccine' => 'VIT_A_7',
// 'due_date' => $hep_b_date->Hep_B_date,
'child_id' => $vit_a_7_date->child_unq_id,
'mobile' => $vit_a_7_date->child_contact,
'message' => $vit_a_7_date->child_name.' का VITAMIN A 7 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}


$vit_a_8_dates_posts = array();
foreach($vit_a_8_dates as $vit_a_8_date) {
$vit_a_8_dates_posts[] = array(
'vaccine' => 'VIT_A_8',
// 'due_date' => $hep_b_date->Hep_B_date,
'child_id' => $vit_a_8_date->child_unq_id,
'mobile' => $vit_a_8_date->child_contact,
'message' => $vit_a_8_date->child_name.' का VITAMIN A 8 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}


$vit_a_9_dates_posts = array();
foreach($vit_a_9_dates as $vit_a_9_date) {
$vit_a_9_dates_posts[] = array(
'vaccine' => 'VIT_A_9',
// 'due_date' => $hep_b_date->Hep_B_date,
'child_id' => $vit_a_9_date->child_unq_id,
'mobile' => $vit_a_9_date->child_contact,
'message' => $vit_a_9_date->child_name.' का VITAMIN A 9 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}



$opv_booster_dates_posts = array();
foreach($opv_booster_dates as $opv_booster_date) {
$opv_booster_dates_posts[] = array(
'vaccine' => 'OPV_BOOSTER',
// 'due_date' => $hep_b_date->Hep_B_date,
'child_id' => $opv_booster_date->child_unq_id,
'mobile' => $opv_booster_date->child_contact,
'message' => $opv_booster_date->child_name.' का VITAMIN A 9 टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}



// DPT1
$dpt1_booster_dates_posts = array();
foreach($dpt1_booster_dates as $dpt1_booster_date) {
$dpt1_booster_dates_posts[] = array(
'vaccine' => 'DPT1_BOOSTER',
// 'due_date' => $hep_b_date->Hep_B_date,
'child_id' => $dpt1_booster_date->child_unq_id,
'mobile' => $dpt1_booster_date->child_contact,
'message' => $dpt1_booster_date->child_name.' का DPT 1 BOOSTER टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}



// DPT2

$dpt2_booster_dates_posts = array();
foreach($dpt2_booster_dates as $dpt2_booster_date) {
$dpt2_booster_dates_posts[] = array(
'vaccine' => 'DPT2_BOOSTER',
// 'due_date' => $hep_b_date->Hep_B_date,
'child_id' => $dpt2_booster_date->child_unq_id,
'mobile' => $dpt2_booster_date->child_contact,
'message' => $dpt2_booster_date->child_name.' का DPT 2 BOOSTER टीका का टीकाकरण दिनांक '.$wed.' को है कृपया अपने नजदीकी स्वस्थ्य केंद्र में जाकर बच्चे का टीकाकरण करवाए|',
);
}


// ==========================22 August ======================

$total_array = array_merge($bcg_dates_posts,$opv_dates_posts,$opv1_dates_posts,$opv2_dates_posts,$opv3_dates_posts,$dpt1_dates_posts,$dpt2_dates_posts,$dpt3_dates_posts,$hepb1_dates_posts,$hepb2_dates_posts,$hepb3_dates_posts,$rvv1_dates_posts,$rvv2_dates_posts,$rvv3_dates_posts,$ipv1_dates_posts,$ipv2_dates_posts,$penta1_dates_posts,$penta2_dates_posts,$penta3_dates_posts,$mmr_dates_posts,$mmr2_dates_posts,$je1_dates_posts,$je2_dates_posts,$vit_a_1_dates_posts,$vit_a_2_dates_posts,$vit_a_3_dates_posts,$vit_a_4_dates_posts,$vit_a_5_dates_posts,$vit_a_6_dates_posts,$vit_a_7_dates_posts,$vit_a_8_dates_posts,$vit_a_9_dates_posts,$opv_booster_dates_posts,$je1_dates_posts,$je2_dates_posts,$dpt1_booster_dates_posts,$dpt2_booster_dates_posts);
//========================== 21 August ================================================
// $total_array = array_merge($bcg_dates_posts,$opv_dates_posts,$hep_b_dates_posts,$opv1_dates_posts,$opv2_dates_posts,$opv3_dates_posts,$dpt1_dates_posts,$dpt2_dates_posts,$dpt3_dates_posts,$hepb1_dates_posts,$hepb2_dates_posts,$hepb3_dates_posts,$rvv1_dates_posts,$rvv2_dates_posts,$rvv3_dates_posts,$ipv1_dates_posts,$ipv2_dates_posts,$penta1_dates_posts,$penta2_dates_posts,$penta3_dates_posts,$mmr_dates_posts,$mmr2_dates_posts,$je1_dates_posts,$je2_dates_posts,$vit_a_1_dates_posts,$vit_a_2_dates_posts,$vit_a_3_dates_posts,$vit_a_4_dates_posts,$vit_a_5_dates_posts,$vit_a_6_dates_posts,$vit_a_7_dates_posts,$vit_a_8_dates_posts,$vit_a_9_dates_posts,$opv_booster_dates_posts,$je1_dates_posts,$je2_dates_posts,$dpt1_booster_dates_posts,$dpt2_booster_dates_posts);

// End Of 21st August ====================================================================


// $total_array = array_merge($bcg_dates, $opv_o_dates1,$hep_b_dates,$opv1_dates,$opv2_dates,$opv3_dates,$penta1_dates,$penta2_dates,$penta3_dates,$ipv_dates,$mmr_dates,$je1_dates,$je2_dates,$vit_a_1_dates,$vit_a_2_dates,$opv_booster_dates,$dpt1_booster_dates,$dpt2_booster_dates);


$this->output
->set_status_header(200)
->set_content_type('application/json')
->set_output(json_encode($total_array,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)); 
//}
}





// =====================================================End Of Next Wednesday Lists Data 
































// end of 	claa Api
}
