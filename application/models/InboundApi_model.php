<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class InboundApi_model extends CI_Model 
{
public function get_categories()
{
$query = $this->db->get('categories');
return $query->result();
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

}



public function get_child_mobile($mobile){

$this->db->select('*');
$this->db->from('children_details');
$this->db->where('child_contact', $mobile);
$query = $this->db->get();

//print_r($query);
return $query->result();

}


public function get_child__by_dob_mobile($mobile){

$this->db->select('*');
$this->db->from('children_details');
$this->db->where('child_contact', $mobile);
$this->db->order_by('child_dob','DESC');
$query = $this->db->get();

//print_r($query);
return $query->result();

}



}