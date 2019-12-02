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

$query = $this->db->query("SELECT `BCG`,`BCG_date`,`BCG_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `BCG_date` AND '".$today."' <= `BCG_last_date` AND `BCG` = 'false'");

return $query->result();

// $this->db->select('BCG_date,child_name,child_contact,child_unq_id');
// $this->db->from('children_details');
// $this->db->where("$today".'>=','BCG_date');
// $this->db->where("$today".'<=','BCG_last_date');
// $this->db->where('BCG =','false');
// SELECT `BCG_date`,`BCG_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '2019-08-20' >= `BCG_date` AND '2019-08-20' <= `BCG_last_date` AND `BCG` = 'false'

// $query = $this->db->query("SELECT `BCG_date`,`BCG_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `BCG_date` AND '".$today."' <= `BCG_last_date` AND `BCG` = 'false'");

// //$query = $this->db->get();
// return $query->result();

// // if(date('D') == 'Wed') {
// // $this->db->where('BCG_date >=', $last_wed);
// // $this->db->where('BCG_date <=', $today);

// // }else{
// // $this->db->where('BCG_date >=', $today);
// // $this->db->where('BCG_date <=', $wed);}
// $this->db->where('BCG =','false');
// //$this->db->join('users u', 'u.id=blog.user_id');
// //$this->db->order_by('blog.created_at', 'desc');
// $query = $this->db->get();
// return $query->result();
}




public function get_opv_o_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));
$last_wed = date('Y-m-d', strtotime('last Wednesday'));


$query = $this->db->query("SELECT `OPV_O`,`OPV_O_date`,`OPV_O_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `OPV_O_date` AND '".$today."' <= `OPV_O_last_date` AND `OPV_O` = 'false' AND `BCG` = 'true'");

return $query->result();

// $this->db->select('OPV_O_date,child_name,child_contact,child_unq_id');
// $this->db->from('children_details');


// // if(date('D') == 'Wed') {
// // $this->db->where('OPV_O_date >=', $last_wed);
// // $this->db->where('OPV_O_date <=', $today);

// // }else{
// // $this->db->where('OPV_O_date >=', $today);
// // $this->db->where('OPV_O_date <=', $wed);
// // }


// // $this->db->where('OPV_O_date >=', $today);
// //       $this->db->where('OPV_O_date <=', $wed);


// $this->db->where('OPV_O =','false');
// //$this->db->join('users u', 'u.id=blog.user_id');
// //$this->db->order_by('blog.created_at', 'desc');
// $query = $this->db->get();
// return $query->result();
}



public function get_child_details()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

// $this->db->select('Hep_B_date,child_name,child_contact,child_unq_id');
// $this->db->from('children_details');
$query = $this->db->query("SELECT * FROM `children_details` ");

return $query->result();
// $this->db->where('Hep_B_date >=', $today);
// $this->db->where('Hep_B_date <=', $wed);
// $this->db->where('Hep_B =','false');
// //$this->db->join('users u', 'u.id=blog.user_id');
// //$this->db->order_by('blog.created_at', 'desc');
// $query = $this->db->get();
// return $query->result();
}



public function get_hep_b_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

// $this->db->select('Hep_B_date,child_name,child_contact,child_unq_id');
// $this->db->from('children_details');
$query = $this->db->query("SELECT `Hep_B`,`Hep_B_date`,`Hep_B_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `Hep_B_date` AND '".$today."' <= `Hep_B_last_date` AND `Hep_B` = 'false'");

return $query->result();
// $this->db->where('Hep_B_date >=', $today);
// $this->db->where('Hep_B_date <=', $wed);
// $this->db->where('Hep_B =','false');
// //$this->db->join('users u', 'u.id=blog.user_id');
// //$this->db->order_by('blog.created_at', 'desc');
// $query = $this->db->get();
// return $query->result();
}



public function get_bucket2_dates_id($child_id)
{
$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));


$query = $this->db->query("SELECT `OPV_O`,`OPV_O_date`,`OPV_O_last_date`,`mother_name`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE  `child_id` = '".$child_id."'");

return $query->result();

}


public function get_ocd_child_id($child_id)
{

$query = $this->db->query("SELECT call_status FROM `outbound_calls_data` WHERE  `ocd_child_id` = '".$child_id."'");

return $query->result();

}

public function get_atmpt_ocd_child_id($child_id)
{

$query = $this->db->query("SELECT  ocd_child_id, call_for FROM `outbound_calls_data` WHERE  `ocd_child_id` = '".$child_id."'");

return $query->result();

}




public function get_bucket3_dates_id($child_id)
{
$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));


$query = $this->db->query("SELECT `RVV1`,`RVV1_date`,`RVV1_last_date`,`IPV1`,`IPV1_date`,`IPV1_last_date`,`OPV1`,`OPV1_date`,`OPV1_last_date`,`DPT1`,`DPT1_date`,`DPT1_last_date`,`PENTA1`,`PENTA1_date`,`PENTA1_last_date`,`mother_name`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE  `child_id` = '".$child_id."'   ");


// $query = $this->db->query("SELECT `RVV1`,`RVV1_date`,`RVV1_last_date`,`OPV1`,`OPV1_date`,`OPV1_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `OPV1_date` AND '".$today."' <= `OPV1_last_date` AND `OPV1` = 'false'  AND `OPV_O` = 'true' AND `child_id` = '".$child_id."' ");

return $query->result();


// $this->db->select('OPV1_date,child_name,child_contact,child_unq_id');
// $this->db->from('children_details');
// $this->db->where('OPV1_date >=', $today);
// $this->db->where('OPV1_date <=', $wed);
// $this->db->where('OPV1 =','false');
// //$this->db->join('users u', 'u.id=blog.user_id');
// //$this->db->order_by('blog.created_at', 'desc');
// $query = $this->db->get();
// return $query->result();
}







// ===================================Bucket 4 ========


public function get_bucket4_dates_id($child_id)
{
$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));


$query = $this->db->query("SELECT `RVV2`,`RVV2_date`,`RVV2_last_date`,`OPV2`,`OPV2_date`,`OPV2_last_date`,`DPT2`,`DPT2_date`,`DPT2_last_date`,`PENTA2`,`PENTA2_date`,`PENTA2_last_date`,`mother_name`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE  `child_id` = '".$child_id."'");

return $query->result();

}


// =================BUCKET 5 ===========================

public function get_bucket5_dates_id($child_id)
{
$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));


$query = $this->db->query("SELECT `RVV3`,`RVV3_date`,`RVV3_last_date`,`IPV2`,`IPV2_date`,`IPV2_last_date`,`OPV3`,`OPV3_date`,`OPV3_last_date`,`DPT3`,`DPT3_date`,`DPT3_last_date`,`PENTA3`,`PENTA3_date`,`PENTA3_last_date`,`mother_name`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE  `child_id` = '".$child_id."'");

return $query->result();

}

//==================BUCKET 6 ===========================

public function get_bucket6_dates_id($child_id)
{
$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));


$query = $this->db->query("SELECT `MMR`,`MMR_date`,`MMR_last_date`,`VIT_A_1`,`VIT_A_1_date`,`VIT_A_1_last_date`,`JE1`,`JE1_date`,`JE1_last_date`,`mother_name`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE  `child_id` = '".$child_id."'");

return $query->result();

}


//==================BUCKET 7 ===========================
public function get_bucket7_dates_id($child_id)
{
$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));
$last_wed = date('Y-m-d', strtotime('last Wednesday'));
$query = $this->db->query("SELECT `DPT_1_BOOSTER`,`DPT_1_BOOSTER_date`,`DPT_1_BOOSTER_last_date`,`MMR2`,`MMR2_date`,`MMR2_last_date`,`OPV_BOOSTER`,`OPV_BOOSTER_date`,`OPV_BOOSTER_last_date`,`JE2`,`JE2_date`,`JE2_last_date`,`mother_name`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE  `child_id` = '".$child_id."'");
return $query->result();

}

//==================BUCKET 8 ===========================

public function get_bucket8_dates_id($child_id)
{
$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));
$last_wed = date('Y-m-d', strtotime('last Wednesday'));
$query = $this->db->query("SELECT `VIT_A_2`,`VIT_A_2_date`,`VIT_A_2_last_date`,`VIT_A_3`,`VIT_A_3_date`,`VIT_A_3_last_date`,`VIT_A_4`,`VIT_A_4_date`,`VIT_A_4_last_date`,`VIT_A_5`,`VIT_A_5_date`,`VIT_A_5_last_date`,`VIT_A_6`,`VIT_A_6_date`,`VIT_A_6_last_date`,`VIT_A_7`,`VIT_A_7_date`,`VIT_A_7_last_date`,`VIT_A_8`,`VIT_A_8_date`,`VIT_A_8_last_date`,`VIT_A_9`,`VIT_A_9_date`,`VIT_A_9_last_date`,`mother_name`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE  `child_id` = '".$child_id."'");
return $query->result();

}

//==================BUCKET 9 ===========================

public function get_bucket9_dates_id($child_id)
{
$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));
$last_wed = date('Y-m-d', strtotime('last Wednesday'));
$query = $this->db->query("SELECT `DPT_2_BOOSTER`,`DPT_2_BOOSTER_date`,`DPT_2_BOOSTER_last_date`,`mother_name`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE  `child_id` = '".$child_id."'");
return $query->result();

}

























public function get_opv1_dates()
{
$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `OPV1`,`OPV1_date`,`OPV1_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `OPV1_date` AND '".$today."' <= `OPV1_last_date` AND `OPV1` = 'false'   ");

return $query->result();


// $this->db->select('OPV1_date,child_name,child_contact,child_unq_id');
// $this->db->from('children_details');
// $this->db->where('OPV1_date >=', $today);
// $this->db->where('OPV1_date <=', $wed);
// $this->db->where('OPV1 =','false');
// //$this->db->join('users u', 'u.id=blog.user_id');
// //$this->db->order_by('blog.created_at', 'desc');
// $query = $this->db->get();
// return $query->result();
}


public function get_opv2_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `OPV2`,`OPV2_date`,`OPV2_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `OPV2_date` AND '".$today."' <= `OPV2_last_date` AND `OPV2` = 'false'   AND `RVV1` = 'true'  AND `IPV1` = 'true' AND `OPV1` = 'true' OR `DPT1` = 'true' OR `HepB1` = 'true' OR `PENTA1` = 'true'");

return $query->result();

// $this->db->select('OPV2_date,child_name,child_contact,child_unq_id');
// $this->db->from('children_details');
// $this->db->where('OPV2_date >=', $today);
// $this->db->where('OPV2_date <=', $wed);
// $this->db->where('OPV2 =','false');
// //$this->db->join('users u', 'u.id=blog.user_id');
// //$this->db->order_by('blog.created_at', 'desc');
// $query = $this->db->get();
// return $query->result();
}


public function get_opv3_dates()
{
$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `OPV3`,`OPV3_date`,`OPV3_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `OPV3_date` AND '".$today."' <= `OPV3_last_date` AND `OPV3` = 'false'
	AND `RVV2` = 'true'  AND `OPV2` = 'true' OR `DPT2` = 'true' OR `HepB2` = 'true' OR `PENTA2` = 'true'");

return $query->result();


// $this->db->select('OPV3_date,child_name,child_contact,child_unq_id');
// $this->db->from('children_details');
// $this->db->where('OPV3_date >=', $today);
// $this->db->where('OPV3_date <=', $wed);
// $this->db->where('OPV3 =','false');
// //$this->db->join('users u', 'u.id=blog.user_id');
// //$this->db->order_by('blog.created_at', 'desc');
// $query = $this->db->get();
// return $query->result();
}



public function get_penta1_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `PENTA1`,`PENTA1_date`,`PENTA1_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `PENTA1_date` AND '".$today."' <= `PENTA1_last_date` AND `PENTA1` = 'false' ");

return $query->result();

// $this->db->select('PENTA1_date,child_name,child_contact,child_unq_id');
// $this->db->from('children_details');
// $this->db->where('PENTA1_date >=', $today);
// $this->db->where('PENTA1_date <=', $wed);
// $this->db->where('PENTA1 =','false');
// //$this->db->join('users u', 'u.id=blog.user_id');
// //$this->db->order_by('blog.created_at', 'desc');
// $query = $this->db->get();
// return $query->result();
}

public function get_penta2_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `PENTA2`,`PENTA2_date`,`PENTA2_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `PENTA2_date` AND '".$today."' <= `PENTA2_last_date` AND `PENTA2` = 'false' AND `RVV1` = 'true'  AND `IPV1` = 'true' AND `OPV1` = 'true' OR `DPT1` = 'true' OR `HepB1` = 'true' OR `PENTA1` = 'true' ");

return $query->result();

// $this->db->select('PENTA2_date,child_name,child_contact,child_unq_id');
// $this->db->from('children_details');
// $this->db->where('PENTA2_date >=', $today);
// $this->db->where('PENTA2_date <=', $wed);
// $this->db->where('PENTA2 =','false');
// //$this->db->join('users u', 'u.id=blog.user_id');
// //$this->db->order_by('blog.created_at', 'desc');
// $query = $this->db->get();
// return $query->result();
}



public function get_penta3_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `PENTA3`,`PENTA3_date`,`PENTA3_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `PENTA3_date` AND '".$today."' <= `PENTA3_last_date` AND `PENTA3` = 'false' AND `RVV2` = 'true'  AND `OPV2` = 'true' OR `DPT2` = 'true' OR `HepB2` = 'true' OR `PENTA2` = 'true'");

return $query->result();

// $this->db->select('PENTA3_date,child_name,child_contact,child_unq_id');
// $this->db->from('children_details');
// $this->db->where('PENTA3_date >=', $today);
// $this->db->where('PENTA3_date <=', $wed);
// $this->db->where('PENTA3 =','false');
// //$this->db->join('users u', 'u.id=blog.user_id');
// //$this->db->order_by('blog.created_at', 'desc');
// $query = $this->db->get();
// return $query->result();
}

public function get_ipv_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `IPV`,`IPV_date`,`IPV_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `IPV_date` AND '".$today."' <= `IPV_last_date` AND `IPV` = 'false'");

return $query->result();

// $this->db->select('IPV_date,child_name,child_contact,child_unq_id');
// $this->db->from('children_details');
// $this->db->where('IPV_date >=', $today);
// $this->db->where('IPV_date <=', $wed);
// $this->db->where('IPV =','false');
// //$this->db->join('users u', 'u.id=blog.user_id');
// //$this->db->order_by('blog.created_at', 'desc');
// $query = $this->db->get();
// return $query->result();
}



public function get_ipv1_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `IPV1`,`IPV1_date`,`IPV1_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `IPV1_date` AND '".$today."' <= `IPV1_last_date` AND `IPV1` = 'false'  AND  ");

return $query->result();


}


public function get_ipv2_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `IPV2`,`IPV2_date`,`IPV2_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `IPV2_date` AND '".$today."' <= `IPV2_last_date` AND `IPV2` = 'false'
	AND `RVV2` = 'true'  AND `OPV2` = 'true' OR `DPT2` = 'true' OR `HepB2` = 'true' OR `PENTA2` = 'true'");

return $query->result();


}





public function get_mmr2_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `MMR2`,`MMR2_date`,`MMR2_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `MMR2_date` AND '".$today."' <= `MMR2_last_date` AND `MMR2` = 'false'
	AND `MMR` = 'true'  AND `VIT_A_1` = 'true'  AND `JE1` = 'true'");

return $query->result();


}




























public function get_mmr_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `MMR`,`MMR_date`,`MMR_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `MMR_date` AND '".$today."' <= `MMR_last_date` AND `MMR` = 'false'
	AND `RVV3` = 'true'  AND `IPV2` = 'true'  AND `OPV3` = 'true' OR `DPT3` = 'true' OR `HepB3` = 'true' OR `PENTA3` = 'true'");

return $query->result();

// $this->db->select('MMR_date,child_name,child_contact,child_unq_id');
// $this->db->from('children_details');
// $this->db->where('MMR_date >=', $today);
// $this->db->where('MMR_date <=', $wed);
// $this->db->where('MMR =','false');
// //$this->db->join('users u', 'u.id=blog.user_id');
// //$this->db->order_by('blog.created_at', 'desc');
// $query = $this->db->get();
// return $query->result();
}


public function get_je1_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `JE1`,`JE1_date`,`JE1_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `JE1_date` AND '".$today."' <= `JE1_last_date` AND `JE1` = 'false'
	AND `RVV3` = 'true'  AND `IPV2` = 'true'  AND `OPV3` = 'true' OR `DPT3` = 'true' OR `HepB3` = 'true' OR `PENTA3` = 'true'");

return $query->result();

// $this->db->select('JE1_date,child_name,child_contact,child_unq_id');
// $this->db->from('children_details');
// $this->db->where('JE1_date >=', $today);
// $this->db->where('JE1_date <=', $wed);
// $this->db->where('JE1 =','false');
// //$this->db->join('users u', 'u.id=blog.user_id');
// //$this->db->order_by('blog.created_at', 'desc');
// $query = $this->db->get();
// return $query->result();
}


public function get_je2_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `JE2`,`JE2_date`,`JE2_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `JE2_date` AND '".$today."' <= `JE2_last_date` AND `JE2` = 'false'
	AND `MMR` = 'true'  AND `VIT_A_1` = 'true'  AND `JE1` = 'true'");

return $query->result();

// $this->db->select('JE2_date,child_name,child_contact,child_unq_id');
// $this->db->from('children_details');
// $this->db->where('JE2_date >=', $today);
// $this->db->where('JE2_date <=', $wed);
// $this->db->where('JE2 =','false');
// //$this->db->join('users u', 'u.id=blog.user_id');
// //$this->db->order_by('blog.created_at', 'desc');
// $query = $this->db->get();
// return $query->result();
}


public function get_vit_a_1_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `VIT_A_1`,`VIT_A_1_date`,`VIT_A_1_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `VIT_A_1_date` AND '".$today."' <= `VIT_A_1_last_date` AND `VIT_A_1` = 'false' AND `RVV3` = 'true'  AND `IPV2` = 'true'  AND `OPV3` = 'true' OR `DPT3` = 'true' OR `HepB3` = 'true' OR `PENTA3` = 'true' ");

return $query->result();

// $this->db->select('VIT_A_1_date,child_name,child_contact,child_unq_id');
// $this->db->from('children_details');
// $this->db->where('VIT_A_1_date >=', $today);
// $this->db->where('VIT_A_1_date <=', $wed);
// $this->db->where('VIT_A_1 =','false');
// //$this->db->join('users u', 'u.id=blog.user_id');
// //$this->db->order_by('blog.created_at', 'desc');
// $query = $this->db->get();
// return $query->result();
}



public function get_vit_a_2_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `VIT_A_2`,`VIT_A_2_date`,`VIT_A_2_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `VIT_A_2_date` AND '".$today."' <= `VIT_A_2_last_date` AND `VIT_A_2` = 'false' AND `DPT_1_BOOSTER` = 'true'  AND `MMR2` = 'true'  AND `OPV_BOOSTER` = 'true' AND `JE2` = 'true' ");

return $query->result();


// $this->db->select('VIT_A_2_date,child_name,child_contact,child_unq_id');
// $this->db->from('children_details');
// $this->db->where('VIT_A_2_date >=', $today);
// $this->db->where('VIT_A_2_date <=', $wed);
// $this->db->where('VIT_A_2 =','false');
// //$this->db->join('users u', 'u.id=blog.user_id');
// //$this->db->order_by('blog.created_at', 'desc');
// $query = $this->db->get();
// return $query->result();
}


public function get_vit_a_3_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `VIT_A_3`,`VIT_A_3_date`,`VIT_A_3_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `VIT_A_3_date` AND '".$today."' <= `VIT_A_3_last_date` AND `VIT_A_3` = 'false' AND `VIT_A_2` = 'true' ");

return $query->result();

}


// 4 ================================================================


public function get_vit_a_4_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `VIT_A_4`,`VIT_A_4_date`,`VIT_A_4_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `VIT_A_4_date` AND '".$today."' <= `VIT_A_4_last_date` AND `VIT_A_4` = 'false' AND `VIT_A_3` = 'true' ");

return $query->result();

}


// 5 ==================================================================
public function get_vit_a_5_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `VIT_A_5`,`VIT_A_5_date`,`VIT_A_5_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `VIT_A_5_date` AND '".$today."' <= `VIT_A_5_last_date` AND `VIT_A_5` = 'false' AND `VIT_A_4` = 'true'");

return $query->result();

}





// 6 ==================================================================

public function get_vit_a_6_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `VIT_A_6`,`VIT_A_6_date`,`VIT_A_6_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `VIT_A_6_date` AND '".$today."' <= `VIT_A_6_last_date` AND `VIT_A_6` = 'false' AND `VIT_A_5` = 'true' ");

return $query->result();

}



// 7 ==================================================================

public function get_vit_a_7_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `VIT_A_7`,`VIT_A_7_date`,`VIT_A_7_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `VIT_A_7_date` AND '".$today."' <= `VIT_A_7_last_date` AND `VIT_A_7` = 'false' AND `VIT_A_6` = 'true'");

return $query->result();

}



// 8 ==================================================================

public function get_vit_a_8_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `VIT_A_8`,`VIT_A_8_date`,`VIT_A_8_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `VIT_A_8_date` AND '".$today."' <= `VIT_A_8_last_date` AND `VIT_A_8` = 'false' AND `VIT_A_7` = 'true' ");

return $query->result();

}






// 9 ==================================================================

public function get_vit_a_9_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `VIT_A_9`,`VIT_A_9_date`,`VIT_A_9_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `VIT_A_9_date` AND '".$today."' <= `VIT_A_9_last_date` AND `VIT_A_9` = 'false' AND `VIT_A_8` = 'true' ");

return $query->result();

}



public function get_RVV1_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `RVV1`,`IPV1`,`OPV1`,`DPT1`,`HepB1`,`PENTA1`,`RVV1`, `RVV1_date`,`RVV1_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `RVV1_date` AND '".$today."' <= `RVV1_last_date` AND `RVV1` = 'false' ");

return $query->result();

}

public function get_RVV2_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `RVV2`,`RVV2_date`,`RVV2_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `RVV2_date` AND '".$today."' <= `RVV2_last_date` AND `RVV2` = 'false'  AND `RVV1` = 'true'  AND `IPV1` = 'true' AND `OPV1` = 'true' OR `DPT1` = 'true' OR `HepB1` = 'true' OR `PENTA1` = 'true'  ");

return $query->result();

}



public function get_RVV3_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `RVV3`,`RVV3_date`,`RVV3_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `RVV3_date` AND '".$today."' <= `RVV3_last_date` AND `RVV3` = 'false'
	AND `RVV2` = 'true'  AND `OPV2` = 'true' OR `DPT2` = 'true' OR `HepB2` = 'true' OR `PENTA2` = 'true' ");

return $query->result();

}



public function get_HepB1_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `HepB1`,`HepB1_date`,`HepB1_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `HepB1_date` AND '".$today."' <= `HepB1_last_date` AND `HepB1` = 'false' ");

return $query->result();

}



public function get_HepB2_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `HepB2`,`HepB2_date`,`HepB2_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `HepB2_date` AND '".$today."' <= `HepB2_last_date` AND `HepB2` = 'false'
 AND `RVV1` = 'true'  AND `IPV1` = 'true' AND `OPV1` = 'true' OR `DPT1` = 'true' OR `HepB1` = 'true' OR `PENTA1` = 'true'	");

return $query->result();

}

public function get_HepB3_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `HepB3`, `HepB3_date`,`HepB3_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `HepB3_date` AND '".$today."' <= `HepB3_last_date` AND `HepB3` = 'false'
	AND `RVV2` = 'true'  AND `OPV2` = 'true' OR `DPT2` = 'true' OR `HepB2` = 'true' OR `PENTA2` = 'true'");

return $query->result();

}




public function get_DPT1_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `DPT1`,`DPT1_date`,`DPT1_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `DPT1_date` AND '".$today."' <= `DPT1_last_date` AND `DPT1` = 'false'   ");

return $query->result();

}

public function get_DPT2_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `DPT2`,`DPT2_date`,`DPT2_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `DPT2_date` AND '".$today."' <= `DPT2_last_date` AND `DPT2` = 'false' AND `RVV1` = 'true'  AND `IPV1` = 'true' AND `OPV1` = 'true' OR `DPT1` = 'true' OR `HepB1` = 'true' OR `PENTA1` = 'true'");

return $query->result();

}


public function get_DPT3_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `DPT3`,`DPT3_date`,`DPT3_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `DPT3_date` AND '".$today."' <= `DPT3_last_date` AND `DPT3` = 'false'
	AND `RVV2` = 'true'  AND `OPV2` = 'true' OR `DPT2` = 'true' OR `HepB2` = 'true' OR `PENTA2` = 'true'");

return $query->result();

}
















































public function get_opv_booster_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `OPV_BOOSTER`,`OPV_BOOSTER_date`,`OPV_BOOSTER_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `OPV_BOOSTER_date` AND '".$today."' <= `OPV_BOOSTER_last_date` AND `OPV_BOOSTER` = 'false' AND `MMR` = 'true'  AND `VIT_A_1` = 'true'  AND `JE1` = 'true'");

return $query->result();

// $this->db->select('OPV_BOOSTER_date,child_name,child_contact,child_unq_id');
// $this->db->from('children_details');
// $this->db->where('OPV_BOOSTER_date >=', $today);
// $this->db->where('OPV_BOOSTER_date <=', $wed);
// $this->db->where('OPV_BOOSTER =','false');
// //$this->db->join('users u', 'u.id=blog.user_id');
// //$this->db->order_by('blog.created_at', 'desc');
// $query = $this->db->get();
// return $query->result();
}



public function get_dpt1_booster_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));

$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `DPT_1_BOOSTER`,`DPT_1_BOOSTER_date`,`DPT_1_BOOSTER_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `DPT_1_BOOSTER_date` AND '".$today."' <= `DPT_1_BOOSTER_last_date` AND `DPT_1_BOOSTER` = 'false' AND `MMR` = 'true'  AND `VIT_A_1` = 'true'  AND `JE1` = 'true'");

return $query->result();

// $this->db->select('DPT_1_BOOSTER_date,child_name,child_contact,child_unq_id');
// $this->db->from('children_details');
// $this->db->where('DPT_1_BOOSTER_date >=', $today);
// $this->db->where('DPT_1_BOOSTER_date <=', $wed);
// $this->db->where('DPT_1_BOOSTER =','false');
// //$this->db->join('users u', 'u.id=blog.user_id');
// //$this->db->order_by('blog.created_at', 'desc');
// $query = $this->db->get();
// return $query->result();
}


public function get_dpt2_booster_dates()
{

$today = date('Y-m-d');
$wed = date('Y-m-d', strtotime('next Wednesday'));


$last_wed = date('Y-m-d', strtotime('last Wednesday'));

$query = $this->db->query("SELECT `DPT_2_BOOSTER`,`DPT_2_BOOSTER_date`,`DPT_2_BOOSTER_last_date`, `child_name`, `child_contact`, `child_unq_id` FROM `children_details` WHERE '".$today."' >= `DPT_2_BOOSTER_date` AND '".$today."' <= `DPT_2_BOOSTER_last_date` AND `DPT_2_BOOSTER` = 'false' AND `VIT_A_9` = 'true' ");

return $query->result();

// $this->db->select('DPT_2_BOOSTER_date,child_name,child_contact,child_unq_id');
// $this->db->from('children_details');
// $this->db->where('DPT_2_BOOSTER_date >=', $today);
// $this->db->where('DPT_2_BOOSTER_date <=', $wed);
// $this->db->where('DPT_2_BOOSTER =','false');
// //$this->db->join('users u', 'u.id=blog.user_id');
// //$this->db->order_by('blog.created_at', 'desc');
// $query = $this->db->get();
// return $query->result();
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


public function insertOCDdata($ocdData)
{
$this->db->insert('outbound_calls_data', $ocdData);
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
