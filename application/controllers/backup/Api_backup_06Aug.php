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
		// if($isValidToken) {
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
					'BCG'=> $child->BCG,
					'BCG_date'=> $child->BCG_date,
					'BCG_done_date' => $child->BCG_done_date,

					'OPV_O'=> $child->OPV_O,
					'OPV_O_date'=> $child->OPV_O_date,
					'OPV_O_done_date' => $child->OPV_O_done_date,
					'Hep_B'=> $child->Hep_B,
					'Hep_B_date'=> $child->Hep_B_date,
					'Hep_B_done_date' => $child->Hep_B_done_date,
					'OPV1'=> $child->OPV1,
					'OPV1_date'=> $child->OPV1_date,
					'OPV1_done_date' => $child->OPV1_done_date,

					'OPV2'=> $child->OPV2,
					'OPV2_date'=> $child->OPV2_date,
					'OPV2_done_date' => $child->OPV2_done_date,


					'PENTA1'=> $child->PENTA1,
					'PENTA1_date'=> $child->PENTA1_date,
					'PENTA1_done_date' => $child->PENTA1_done_date,


					'PENTA2'=> $child->PENTA2,
					'PENTA2_date'=> $child->PENTA2_date,
					'PENTA2_done_date' => $child->PENTA2_done_date,


					'PENTA3'=> $child->PENTA3,
					'PENTA3_date'=> $child->PENTA3_date,
					'PENTA3_done_date' => $child->PENTA3_done_date,


					'OPV3'=> $child->OPV3,
					'OPV3_date'=> $child->OPV3_date,
					'OPV3_done_date' => $child->OPV3_done_date,


					'IPV'=> $child->IPV,
					'IPV_date'=> $child->IPV_date,
					'IPV_done_date' => $child->IPV_done_date,


					'MMR'=> $child->MMR,
					'MMR_date'=> $child->MMR_date,
					'MMR_done_date' => $child->MMR_done_date,


					'JE1'=> $child->JE1,
					'JE1_date'=> $child->JE1_date,
					'JE1_done_date' => $child->JE1_done_date,

					'VIT_A_1'=> $child->VIT_A_1,
					'VIT_A_1_date'=> $child->VIT_A_1_date,
					'VIT_A_1_done_date' => $child->VIT_A_1_done_date,

					'OPV_BOOSTER'=> $child->OPV_BOOSTER,
					'OPV_BOOSTER_date'=> $child->OPV_BOOSTER_date,
					'OPV_BOOSTER_done_date' => $child->OPV_BOOSTER_done_date,



					'JE2'=> $child->JE2,
					'JE2_date'=> $child->JE2_date,
					'JE2_done_date' => $child->JE2_done_date,

					'VIT_A_2'=> $child->VIT_A_2,
					'VIT_A_2_date'=> $child->VIT_A_2_date,
					'VIT_A_2_done_date' => $child->VIT_A_2_done_date,

					'OPV_BOOSTER'=> $child->OPV_BOOSTER,
					'OPV_BOOSTER_date'=> $child->OPV_BOOSTER_date,
					'OPV_BOOSTER_done_date' => $child->OPV_BOOSTER_done_date,

					'DPT_1_BOOSTER'=> $child->DPT_1_BOOSTER,
					'DPT_1_BOOSTER_date'=> $child->DPT_1_BOOSTER_date,
					'DPT_1_BOOSTER_done_date' => $child->DPT_1_BOOSTER_done_date,

					'DPT_2_BOOSTER_BOOSTER'=> $child->DPT_2_BOOSTER,
					'DPT_2_BOOSTER_date'=> $child->DPT_2_BOOSTER_date,
					'DPT_2_BOOSTER_done_date' => $child->DPT_2_BOOSTER_done_date











					// 'image' => base_url('media/images/'.$blog->image),
					// 'created_at' => $blog->created_at
				);
			//}

			$this->output
				->set_status_header(200)
				->set_content_type('application/json')
				->set_output(json_encode($posts)); 
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
$childData['JE1_done_time'] = $this->input->post('je1_done_date');
$childData['VIT_A_1_done_date'] = $this->input->post('vit_a_1_done_date');
$childData['OPV_BOOSTER_done_date'] = $this->input->post('opv_booster_done_date');
$childData['DPT_1_BOOSTER_done_date'] = $this->input->post('dpt_1_done_date');
$childData['JE2_done_date'] = $this->input->post('je_2_done_date');
$childData['VIT_A_2_done_date'] = $this->input->post('vit_a_2_done_date');
$childData['DPT_2_BOOSTER_done_date'] = $this->input->post('dpt_2_done_date');














// end of if vaccinated before 


















$dob_date = $this->input->post('childdob');

$childData['BCG_date'] =  date('Y-m-d', strtotime($dob_date. ' + 1 year'));
$childData['OPV_O_date'] = date('Y-m-d', strtotime($dob_date. ' + 15 days'));
$childData['Hep_B_date'] = date('Y-m-d', strtotime($dob_date. ' + 1 day'));
$childData['OPV1_date'] = date('Y-m-d', strtotime($dob_date. ' + 6 weeks'));
$childData['OPV2_date'] = date('Y-m-d', strtotime($dob_date. ' + 10 weeks'));
$childData['PENTA1_date'] =  date('Y-m-d', strtotime($dob_date. ' + 6 weeks'));
$childData['PENTA2_date'] =  date('Y-m-d', strtotime($dob_date. ' + 10 weeks'));
$childData['OPV3_date'] =  date('Y-m-d', strtotime($dob_date. ' + 14 weeks'));
$childData['PENTA3_date'] = date('Y-m-d', strtotime($dob_date. ' + 14 weeks'));
//$childData['MEASLES'] = date('Y-m-d', strtotime($dob_date. ' + 12 month'));
$childData['IPV_date'] = date('Y-m-d', strtotime($dob_date. ' + 14 weeks'));
$childData['MMR_date'] = date('Y-m-d', strtotime($dob_date. ' + 12 month'));
$childData['JE1_date'] = date('Y-m-d', strtotime($dob_date. ' + 12 month'));
$childData['VIT_A_1_date'] = date('Y-m-d', strtotime($dob_date. ' + 9 month'));
$childData['OPV_BOOSTER_date'] = date('Y-m-d', strtotime($dob_date. ' + 24 month'));
$childData['DPT_1_BOOSTER_date'] = date('Y-m-d', strtotime($dob_date. ' + 24 month'));
$childData['JE2_date'] = date('Y-m-d', strtotime($dob_date. ' + 24 month'));
$childData['VIT_A_2_date'] = date('Y-m-d', strtotime($dob_date. ' + 16 month'));
$childData['DPT_2_BOOSTER_date'] = date('Y-m-d', strtotime($dob_date. ' + 6 year'));


























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
					'BCG' => $child->BCG,
					'BCG_date' => $child->BCG_date,
					'BCG_done_date' => $child->BCG_done_date,
					'OPV_O' => $child->OPV_O,
					'OPV_O_date' => $child->OPV_O_date,
					'OPV_O_done_date' => $child->OPV_O_done_date
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


	public function getWednesLists()
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

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
			$opv_booster_dates = $this->api_model->get_opv_booster_dates();
			$dpt1_booster_dates = $this->api_model->get_dpt1_booster_dates();
			$dpt2_booster_dates = $this->api_model->get_dpt2_booster_dates();
			
			//print_r($bcg_dates);
			



// ==========================================start of different arrays ================================



// foreach($bcg_dates as $bcg_date) {
// 				$bcg_dates_posts[] = array(
// 					'bcg_date' => $bcg_date->BCG_date,
// 					'child_name' => $bcg_date->child_name,
// 					// 'opv_o' => $bcg_date->OPV_O_date,
// 					// 'image' => base_url('media/images/'.$blog->image),
// 					// 'created_at' => $blog->created_at
// 				);
// 			}
//$bcg_dates_posts

// foreach($opv_o_dates as $opv_o_date) {
// 				$opv_o_dates[] = array(
// 					'opv_o_date' => $opv_o_date->OPV_O_date,
// 					'child_name' => $opv_o_date->child_name,
// 					// 'opv_o' => $bcg_date->OPV_O_date,
// 					// 'image' => base_url('media/images/'.$blog->image),
// 					// 'created_at' => $blog->created_at
// 				);
// 			}


// __________________________________________________________________________________________




//$hep_b_dates = $this->api_model->get_hep_b_dates();



foreach($hep_b_dates as $hep_b_date) {
				$hep_b_dates[] = array(
					'hep_b_date' => $hep_b_date->Hep_B_date,
					'child_name' => $hep_b_date->child_name,
					// 'opv_o' => $bcg_date->OPV_O_date,
					// 'image' => base_url('media/images/'.$blog->image),
					// 'created_at' => $blog->created_at
				);
			}


// $opv1_dates = $this->api_model->get_opv1_dates();

foreach($opv1_dates as $opv1_date) {
				$opv1_dates[] = array(
					'opv1_date' => $opv1_date->OPV1_date,
					'child_name' => $opv1_date->child_name,
					// 'opv_o' => $bcg_date->OPV_O_date,
					// 'image' => base_url('media/images/'.$blog->image),
					// 'created_at' => $blog->created_at
				);
			}


// $opv2_dates = $this->api_model->get_opv2_dates();

foreach($opv2_dates as $opv2_date) {
				$opv2_dates[] = array(
					'opv2_date' => $opv2_date->OPV2_date,
					'child_name' => $opv2_date->child_name,
					// 'opv_o' => $bcg_date->OPV_O_date,
					// 'image' => base_url('media/images/'.$blog->image),
					// 'created_at' => $blog->created_at
				);
			}
// $opv3_dates = $this->api_model->get_opv3_dates();
foreach($opv3_dates as $opv3_date) {
				$opv3_dates[] = array(
					'opv3_date' => $opv3_date->OPV3_date,
					'child_name' => $opv3_date->child_name,
					// 'opv_o' => $bcg_date->OPV_O_date,
					// 'image' => base_url('media/images/'.$blog->image),
					// 'created_at' => $blog->created_at
				);
			}



// $penta1_dates = $this->api_model->get_penta1_dates();

foreach($penta1_dates as $penta1_date) {
				$penta1_dates[] = array(
					'penta1_date' => $penta1_date->PENTA1_date,
					'child_name' => $penta1_date->child_name,
					// 'opv_o' => $bcg_date->OPV_O_date,
					// 'image' => base_url('media/images/'.$blog->image),
					// 'created_at' => $blog->created_at
				);
			}


// $penta2_dates = $this->api_model->get_penta2_dates();

foreach($penta2_dates as $penta2_date) {
				$penta2_dates[] = array(
					'penta2_date' => $penta2_date->PENTA2_date,
					'child_name' => $penta2_date->child_name,
					// 'opv_o' => $bcg_date->OPV_O_date,
					// 'image' => base_url('media/images/'.$blog->image),
					// 'created_at' => $blog->created_at
				);
			}


// $penta3_dates = $this->api_model->get_penta3_dates();

foreach($penta3_dates as $penta3_date) {
				$penta3_dates[] = array(
					'penta3_date' => $penta3_date->PENTA3_date,
					'child_name' => $penta3_date->child_name,
					// 'opv_o' => $bcg_date->OPV_O_date,
					// 'image' => base_url('media/images/'.$blog->image),
					// 'created_at' => $blog->created_at
				);
			}

// $ipv_dates = $this->api_model->get_ipv_dates();


foreach($ipv_dates as $ipv_date) {
				$ipv_dates[] = array(
					'ipv_date' => $ipv_date->IPV_date,
					'child_name' => $ipv_date->child_name,
					// 'opv_o' => $bcg_date->OPV_O_date,
					// 'image' => base_url('media/images/'.$blog->image),
					// 'created_at' => $blog->created_at
				);
			}

// $mmr_dates = $this->api_model->get_mmr_dates();

foreach($mmr_dates as $mmr_date) {
				$mmr_dates[] = array(
					'mmr_date' => $mmr_date->MMR_date,
					'child_name' => $mmr_date->child_name,
					// 'opv_o' => $bcg_date->OPV_O_date,
					// 'image' => base_url('media/images/'.$blog->image),
					// 'created_at' => $blog->created_at
				);
			}

// $je1_dates = $this->api_model->get_je1_dates();


foreach($je1_dates as $je1_date) {
				$je1_dates[] = array(
					'je1_date' => $je1_date->JE1_date,
					'child_name' => $je1_date->child_name,
					// 'opv_o' => $bcg_date->OPV_O_date,
					// 'image' => base_url('media/images/'.$blog->image),
					// 'created_at' => $blog->created_at
				);
			}


// $je2_dates = $this->api_model->get_je2_dates();

 foreach($je2_dates as $je2_date) {
				$je2_dates[] = array(
					'je2_date' => $je2_date->JE2_date,
					'child_name' => $je2_date->child_name,
					// 'opv_o' => $bcg_date->OPV_O_date,
					// 'image' => base_url('media/images/'.$blog->image),
					// 'created_at' => $blog->created_at
				);
			}


// $vit_a_1_dates = $this->api_model->get_vit_a_1_dates();

 foreach($vit_a_1_dates as $vit_a_1_date) {
				$vit_a_1_dates[] = array(
					'vit_a_1_date' => $vit_a_1_date->VIT_A_1_date,
					'child_name' => $vit_a_1_date->child_name,
					// 'opv_o' => $bcg_date->OPV_O_date,
					// 'image' => base_url('media/images/'.$blog->image),
					// 'created_at' => $blog->created_at
				);
			}

// $vit_a_2_dates = $this->api_model->get_vit_a_2_dates();

foreach($vit_a_2_dates as $vit_a_2_date) {
				$vit_a_2_dates[] = array(
					'vit_a_2_date' => $vit_a_2_date->VIT_A_2_date,
					'child_name' => $vit_a_2_date->child_name,
					// 'opv_o' => $bcg_date->OPV_O_date,
					// 'image' => base_url('media/images/'.$blog->image),
					// 'created_at' => $blog->created_at
				);
			}


// $opv_booster_dates = $this->api_model->get_opv_booster_dates();

 foreach($opv_booster_dates as $opv_booster_date) {
				$opv_booster_dates[] = array(
					'opv_booster_date' => $opv_booster_date->OPV_BOOSTER_date,
					'child_name' => $opv_booster_date->child_name,
					// 'opv_o' => $bcg_date->OPV_O_date,
					// 'image' => base_url('media/images/'.$blog->image),
					// 'created_at' => $blog->created_at
				);
			}




// $dpt1_booster_dates = $this->api_model->get_dpt1_booster_dates();

foreach($dpt1_booster_dates as $dpt1_booster_date) {
				$dpt1_booster_dates[] = array(
					'dpt1_booster_date' => $dpt1_booster_date->DPT_1_BOOSTER_date,
					'child_name' => $dpt1_booster_date->child_name,
					// 'opv_o' => $bcg_date->OPV_O_date,
					// 'image' => base_url('media/images/'.$blog->image),
					// 'created_at' => $blog->created_at
				);
			}


// $dpt2_booster_dates = $this->api_model->get_dpt2_booster_dates();

foreach($dpt2_booster_dates as $dpt2_booster_date) {
				$dpt2_booster_dates[] = array(
					'dpt2_booster_date' => $dpt2_booster_date->DPT_2_BOOSTER_date,
					'child_name' => $dpt2_booster_date->child_name,
					// 'opv_o' => $bcg_date->OPV_O_date,
					// 'image' => base_url('media/images/'.$blog->image),
					// 'created_at' => $blog->created_at
				);
			}













// ____________________________________________________________________________________________





// ==========================================End of different arrays ====================================

	//$total_array = array();

	// $total_array = array_merge($bcg_dates_posts, $opv_o_dates,$hep_b_dates,$opv1_dates,$opv2_dates,$opv3_dates,$penta1_dates,$penta2_dates,$penta3_dates,$ipv_dates,$mmr_dates,$je1_dates,$je2_dates,$vit_a_1_dates,$vit_a_2_dates,$opv_booster_dates,$dpt1_booster_dates,$dpt2_booster_dates);

$total_array = array_merge($bcg_dates, $opv_o_dates1);


			$this->output
				->set_status_header(200)
				->set_content_type('application/json')
				->set_output(json_encode($total_array,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)); 
		//}
	}





// =====================================================End Of Next Wednesday Lists Data 














// end of 	claa Api
}
