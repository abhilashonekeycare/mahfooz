<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_model extends CI_Model 
{
public function get_blogs($featured, $recentpost)
{
$this->db->select('blog.*, cat.category_name, u.first_name, u.last_name');
$this->db->from('blogs blog');
$this->db->join('users u', 'u.id=blog.user_id');
$this->db->join('categories cat', 'cat.id=blog.category_id', 'left');
$this->db->where('blog.is_active', 1);

if($featured) {
$this->db->where('blog.is_featured', 1);
}
if($recentpost){
$this->db->order_by('blog.created_at', 'desc');
$this->db->limit($recentpost);
}
$query = $this->db->get();
return $query->result();
}

public function get_blog($id)
{
$this->db->select('blog.*, cat.category_name, u.first_name, u.last_name');
$this->db->from('blogs blog');
$this->db->join('users u', 'u.id=blog.user_id');
$this->db->join('categories cat', 'cat.id=blog.category_id', 'left');
$this->db->where('blog.is_active', 1);
$this->db->where('blog.id', $id);
$query = $this->db->get();
return $query->row();
}

public function get_categories()
{
$query = $this->db->get('categories');
return $query->result();
}

public function get_page($slug)
{
$this->db->where('slug', $slug);
$query = $this->db->get('pages');
return $query->row();
}

public function insert_contact($contactData)
{
$this->db->insert('contacts', $contactData);
return $this->db->insert_id();
}

public function login($username, $password) 
{
$this->db->where('username', $username);
$this->db->where('password', md5($password));
$query = $this->db->get('users');

if($query->num_rows() == 1) {
return $query->row();
}
}

public function get_admin_blogs()
{
$this->db->select('blog.*, u.first_name, u.last_name');
$this->db->from('blogs blog');
$this->db->join('users u', 'u.id=blog.user_id');
$this->db->order_by('blog.created_at', 'desc');
$query = $this->db->get();
return $query->result();
}


public function get_mothers()
{
$this->db->select('*');
$this->db->from('mothers_details');
//$this->db->join('users u', 'u.id=blog.user_id');
//$this->db->order_by('blog.created_at', 'desc');
$query = $this->db->get();
return $query->result();
}


// getting Vaccinations Date And Schedules ++==================================================

public function get_bcg_date()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));
$last_wed = date('Y-m-d', strtotime('last Wednesday'));


$this->db->select('BCG_date,child_name,child_contact,child_unq_id');
$this->db->from('children_details');
if(date('D') == 'Wed') {
$this->db->where('BCG_date >=', $last_wed);
$this->db->where('BCG_date <=', $today);

}else{
$this->db->where('BCG_date >=', $today);
$this->db->where('BCG_date <=', $wed);}
$this->db->where('BCG =','false');
//$this->db->join('users u', 'u.id=blog.user_id');
//$this->db->order_by('blog.created_at', 'desc');
$query = $this->db->get();
return $query->result();
}




public function get_opv_o_date()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));
$last_wed = date('Y-m-d', strtotime('last Wednesday'));


$this->db->select('vaccine_step1_start,vaccine_step1_end,child_name,child_contact,child_unq_id');
$this->db->from('child_details');


if(date('D') == 'Wed') {
$this->db->where('vaccine_step1_start >=', $last_wed);
// $this->db->where('OPV_O_date <=', $today);

}else{
$this->db->where('vaccine_step1_end >=', $today);
//$this->db->where('vaccine_step1_end <=', $wed);
}


// $this->db->where('OPV_O_date >=', $today);
//       $this->db->where('OPV_O_date <=', $wed);


//$this->db->where('OPV_O =','false');
//$this->db->join('users u', 'u.id=blog.user_id');
//$this->db->order_by('blog.created_at', 'desc');
$query = $this->db->get();
return $query->result();
}























// getting vaccines date ================================================================================

public function get_bcg_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));
$last_wed = date('Y-m-d', strtotime('last Wednesday'));


$this->db->select('BCG_date,child_name,child_contact,child_unq_id');
$this->db->from('children_details');
if(date('D') == 'Wed') {
$this->db->where('BCG_date >=', $last_wed);
$this->db->where('BCG_date <=', $today);

}else{
$this->db->where('BCG_date >=', $today);
$this->db->where('BCG_date <=', $wed);}
$this->db->where('BCG =','false');
//$this->db->join('users u', 'u.id=blog.user_id');
//$this->db->order_by('blog.created_at', 'desc');
$query = $this->db->get();
return $query->result();
}




public function get_opv_o_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));
$last_wed = date('Y-m-d', strtotime('last Wednesday'));


$this->db->select('OPV_O_date,child_name,child_contact,child_unq_id');
$this->db->from('children_details');


if(date('D') == 'Wed') {
$this->db->where('OPV_O_date >=', $last_wed);
$this->db->where('OPV_O_date <=', $today);

}else{
$this->db->where('OPV_O_date >=', $today);
$this->db->where('OPV_O_date <=', $wed);
}


// $this->db->where('OPV_O_date >=', $today);
//       $this->db->where('OPV_O_date <=', $wed);


$this->db->where('OPV_O =','false');
//$this->db->join('users u', 'u.id=blog.user_id');
//$this->db->order_by('blog.created_at', 'desc');
$query = $this->db->get();
return $query->result();
}



public function get_hep_b_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$this->db->select('Hep_B_date,child_name,child_contact,child_unq_id');
$this->db->from('children_details');
$this->db->where('Hep_B_date >=', $today);
$this->db->where('Hep_B_date <=', $wed);
$this->db->where('Hep_B =','false');
//$this->db->join('users u', 'u.id=blog.user_id');
//$this->db->order_by('blog.created_at', 'desc');
$query = $this->db->get();
return $query->result();
}


public function get_opv1_dates()
{
$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));


$this->db->select('OPV1_date,child_name,child_contact,child_unq_id');
$this->db->from('children_details');
$this->db->where('OPV1_date >=', $today);
$this->db->where('OPV1_date <=', $wed);
$this->db->where('OPV1 =','false');
//$this->db->join('users u', 'u.id=blog.user_id');
//$this->db->order_by('blog.created_at', 'desc');
$query = $this->db->get();
return $query->result();
}


public function get_opv2_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$this->db->select('OPV2_date,child_name,child_contact,child_unq_id');
$this->db->from('children_details');
$this->db->where('OPV2_date >=', $today);
$this->db->where('OPV2_date <=', $wed);
$this->db->where('OPV2 =','false');
//$this->db->join('users u', 'u.id=blog.user_id');
//$this->db->order_by('blog.created_at', 'desc');
$query = $this->db->get();
return $query->result();
}


public function get_opv3_dates()
{
$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));


$this->db->select('OPV3_date,child_name,child_contact,child_unq_id');
$this->db->from('children_details');
$this->db->where('OPV3_date >=', $today);
$this->db->where('OPV3_date <=', $wed);
$this->db->where('OPV3 =','false');
//$this->db->join('users u', 'u.id=blog.user_id');
//$this->db->order_by('blog.created_at', 'desc');
$query = $this->db->get();
return $query->result();
}



public function get_penta1_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$this->db->select('PENTA1_date,child_name,child_contact,child_unq_id');
$this->db->from('children_details');
$this->db->where('PENTA1_date >=', $today);
$this->db->where('PENTA1_date <=', $wed);
$this->db->where('PENTA1 =','false');
//$this->db->join('users u', 'u.id=blog.user_id');
//$this->db->order_by('blog.created_at', 'desc');
$query = $this->db->get();
return $query->result();
}

public function get_penta2_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$this->db->select('PENTA2_date,child_name,child_contact,child_unq_id');
$this->db->from('children_details');
$this->db->where('PENTA2_date >=', $today);
$this->db->where('PENTA2_date <=', $wed);
$this->db->where('PENTA2 =','false');
//$this->db->join('users u', 'u.id=blog.user_id');
//$this->db->order_by('blog.created_at', 'desc');
$query = $this->db->get();
return $query->result();
}



public function get_penta3_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$this->db->select('PENTA3_date,child_name,child_contact,child_unq_id');
$this->db->from('children_details');
$this->db->where('PENTA3_date >=', $today);
$this->db->where('PENTA3_date <=', $wed);
$this->db->where('PENTA3 =','false');
//$this->db->join('users u', 'u.id=blog.user_id');
//$this->db->order_by('blog.created_at', 'desc');
$query = $this->db->get();
return $query->result();
}

public function get_ipv_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$this->db->select('IPV_date,child_name,child_contact,child_unq_id');
$this->db->from('children_details');
$this->db->where('IPV_date >=', $today);
$this->db->where('IPV_date <=', $wed);
$this->db->where('IPV =','false');
//$this->db->join('users u', 'u.id=blog.user_id');
//$this->db->order_by('blog.created_at', 'desc');
$query = $this->db->get();
return $query->result();
}

public function get_mmr_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$this->db->select('MMR_date,child_name,child_contact,child_unq_id');
$this->db->from('children_details');
$this->db->where('MMR_date >=', $today);
$this->db->where('MMR_date <=', $wed);
$this->db->where('MMR =','false');
//$this->db->join('users u', 'u.id=blog.user_id');
//$this->db->order_by('blog.created_at', 'desc');
$query = $this->db->get();
return $query->result();
}


public function get_je1_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$this->db->select('JE1_date,child_name,child_contact,child_unq_id');
$this->db->from('children_details');
$this->db->where('JE1_date >=', $today);
$this->db->where('JE1_date <=', $wed);
$this->db->where('JE1 =','false');
//$this->db->join('users u', 'u.id=blog.user_id');
//$this->db->order_by('blog.created_at', 'desc');
$query = $this->db->get();
return $query->result();
}


public function get_je2_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$this->db->select('JE2_date,child_name,child_contact,child_unq_id');
$this->db->from('children_details');
$this->db->where('JE2_date >=', $today);
$this->db->where('JE2_date <=', $wed);
$this->db->where('JE2 =','false');
//$this->db->join('users u', 'u.id=blog.user_id');
//$this->db->order_by('blog.created_at', 'desc');
$query = $this->db->get();
return $query->result();
}


public function get_vit_a_1_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$this->db->select('VIT_A_1_date,child_name,child_contact,child_unq_id');
$this->db->from('children_details');
$this->db->where('VIT_A_1_date >=', $today);
$this->db->where('VIT_A_1_date <=', $wed);
$this->db->where('VIT_A_1 =','false');
//$this->db->join('users u', 'u.id=blog.user_id');
//$this->db->order_by('blog.created_at', 'desc');
$query = $this->db->get();
return $query->result();
}



public function get_vit_a_2_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));


$this->db->select('VIT_A_2_date,child_name,child_contact,child_unq_id');
$this->db->from('children_details');
$this->db->where('VIT_A_2_date >=', $today);
$this->db->where('VIT_A_2_date <=', $wed);
$this->db->where('VIT_A_2 =','false');
//$this->db->join('users u', 'u.id=blog.user_id');
//$this->db->order_by('blog.created_at', 'desc');
$query = $this->db->get();
return $query->result();
}


public function get_opv_booster_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$this->db->select('OPV_BOOSTER_date,child_name,child_contact,child_unq_id');
$this->db->from('children_details');
$this->db->where('OPV_BOOSTER_date >=', $today);
$this->db->where('OPV_BOOSTER_date <=', $wed);
$this->db->where('OPV_BOOSTER =','false');
//$this->db->join('users u', 'u.id=blog.user_id');
//$this->db->order_by('blog.created_at', 'desc');
$query = $this->db->get();
return $query->result();
}



public function get_dpt1_booster_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$this->db->select('DPT_1_BOOSTER_date,child_name,child_contact,child_unq_id');
$this->db->from('children_details');
$this->db->where('DPT_1_BOOSTER_date >=', $today);
$this->db->where('DPT_1_BOOSTER_date <=', $wed);
$this->db->where('DPT_1_BOOSTER =','false');
//$this->db->join('users u', 'u.id=blog.user_id');
//$this->db->order_by('blog.created_at', 'desc');
$query = $this->db->get();
return $query->result();
}


public function get_dpt2_booster_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$this->db->select('DPT_2_BOOSTER_date,child_name,child_contact,child_unq_id');
$this->db->from('children_details');
$this->db->where('DPT_2_BOOSTER_date >=', $today);
$this->db->where('DPT_2_BOOSTER_date <=', $wed);
$this->db->where('DPT_2_BOOSTER =','false');
//$this->db->join('users u', 'u.id=blog.user_id');
//$this->db->order_by('blog.created_at', 'desc');
$query = $this->db->get();
return $query->result();
}











// end of getting vaccines date ===============================================================================








public function get_childs()
{
$this->db->select('*');
$this->db->from('child_details');
//$this->db->join('users u', 'u.id=blog.user_id');
//$this->db->order_by('blog.created_at', 'desc');
$query = $this->db->get();
return $query->result();
}




public function get_mothers_id($id)
{   

//echo "string";
$this->db->select('*');
$this->db->from('mothers_details');
$this->db->where('mthrs_db_id', $id);
$query = $this->db->get();

//print_r($query);
return $query->row();


// $this->db->select('blog.*, u.first_name, u.last_name');
// $this->db->from('blogs blog');
// $this->db->join('users u', 'u.id=blog.user_id');
// $this->db->where('blog.id', $id);
// $query = $this->db->get();
// return $query->row();
}

public function get_mothers_mobile($mobile)
{   

//echo "string";
$this->db->select('*');
$this->db->from('mothers_details');
$this->db->where('mthrs_mbl_no', $mobile);
$query = $this->db->get();

//print_r($query);
return $query->row();


// $this->db->select('blog.*, u.first_name, u.last_name');
// $this->db->from('blogs blog');
// $this->db->join('users u', 'u.id=blog.user_id');
// $this->db->where('blog.id', $id);
// $query = $this->db->get();
// return $query->row();
}



public function get_mothers_unq_no($mobile)
{   

//echo "string";
$this->db->select('*');
$this->db->from('mothers_details');
$this->db->where('mthrs_unq_no', $mobile);
$query = $this->db->get();

//print_r($query);
return $query->row();


// $this->db->select('blog.*, u.first_name, u.last_name');
// $this->db->from('blogs blog');
// $this->db->join('users u', 'u.id=blog.user_id');
// $this->db->where('blog.id', $id);
// $query = $this->db->get();
// return $query->row();
}












public function get_child_id($id)
{   

//echo "string";
$this->db->select('*');
$this->db->from('child_details');
$this->db->where('mthr_id', $id);
$query = $this->db->get();

//print_r($query);
// return $query->row();
return $query->result();


// $this->db->select('blog.*, u.first_name, u.last_name');
// $this->db->from('blogs blog');
// $this->db->join('users u', 'u.id=blog.user_id');
// $this->db->where('blog.id', $id);
// $query = $this->db->get();
// return $query->row();
}























public function get_admin_blog($id)
{
$this->db->select('blog.*, u.first_name, u.last_name');
$this->db->from('blogs blog');
$this->db->join('users u', 'u.id=blog.user_id');
$this->db->where('blog.id', $id);
$query = $this->db->get();
return $query->row();
}

public function checkToken($token)
{
$this->db->where('token', $token);
$query = $this->db->get('users');

if($query->num_rows() == 1) {
return true;
}
return false;
}

public function insertBlog($blogData)
{
$this->db->insert('blogs', $blogData);
return $this->db->insert_id();
}

public function insertMother($motherData)
{
$this->db->insert('mothers_details', $motherData);
return $this->db->insert_id();
}

public function updateBlog($id, $blogData)
{
$this->db->where('id', $id);
$this->db->update('blogs', $blogData);
}


public function updateMother_s($id, $motherData)
{
$this->db->where('mthrs_db_id', $id);
$this->db->update('mothers_details', $motherData);
}

public function deleteMother($id)
{

//echo $id;
$this->db->where('mthrs_db_id', $id);
$this->db->delete('mothers_details');
}

public function deleteBlog($id)
{
$this->db->where('id', $id);
$this->db->delete('blogs');
}
}
