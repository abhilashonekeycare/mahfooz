<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Immune extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
public function index()
{


if (isset($_POST['mno'])) {

$mblno = $_POST['m_no'];

$mthr = json_decode(
file_get_contents('https://onekeycare.org/immunize/api/item/'.$mblno.'/')
);

//echo $mthr->mthr_contact;

if ($mthr !== NULL) {
	$datamother['mother_name'] = $mthr->mthr_name;
	$datamother['mother_id']= $mthr->mthr_id;
	$datamother['mother_unique_no']= $mthr->mthr_unq_id;
	$this->load->view('immune/dashboard', $datamother);


}else{
	$datamother['mother_name'] = "No Mother details Found";
	$this->load->view('immune/login2', $datamother);

}



//exit();
# code...
}else if(isset($_POST['uno'])){

$unqno = $_POST['u_no'];

$mthr = json_decode(
file_get_contents('https://onekeycare.org/immunize/api/unique/'.$unqno.'/')
);


if ($mthr !== NULL) {
	$datamother['mother_name'] = $mthr->mthr_name;
	$datamother['mother_id']= $mthr->mthr_id;
	$datamother['mother_unique_no']= $mthr->mthr_unq_id;
	$this->load->view('immune/dashboard', $datamother);


}else{
	$datamother['mother_name'] = "No Mother details Found";
	$this->load->view('immune/login2', $datamother);

}



}else{

	$this->load->view('immune/login');
}
}









public function vac_query($unq_no=0){

	if (isset($_POST['opt']) && $_POST['op_no']=='1' ) {
		
	$dob1 = '$dob';
	$dob2 = '$dob';
	$dob3 = '$dob';
	$dob4 = '$dob';
	$dob5 = '$dob';
	$dob6 = '$dob';
	$dob7 = '$dob';
	$dob='$dob';

if(!empty($unq_no)){

$child = json_decode(
file_get_contents('https://onekeycare.org/immunize/api/vacci/'.$unq_no.'/')
);

$mthr = json_decode(
file_get_contents('https://onekeycare.org/immunize/api/unique/'.$unq_no.'/')
);


//echo json_encode($child);



$datavacc['mother_unique_no']= $mthr->mthr_unq_id;
$datavacc['mthr_details'] = $mthr;
$datavacc['child_details'] = $child;

$child_dtls = $child;


// ==================Details Fetch Starts----------------------==

 $child1 = $child_dtls->child_name1;
 $child2 =  $child_dtls->child_name2;
 $child3 =  $child_dtls->child_name3; 
 $child4 =  $child_dtls->child_name4;
 $child5 =  $child_dtls->child_name5;
 $child6 =  $child_dtls->child_name6;
 $child7 =  $child_dtls->child_name7;
 $child8 =  $child_dtls->child_name8;
 $child9 =  $child_dtls->child_name9;

 $child_dob1 = $child_dtls->child_dob1;
 $child_dob2 =  $child_dtls->child_dob2;
 $child_dob3 =  $child_dtls->child_dob3; 
 $child_dob4 =  $child_dtls->child_dob4;
 $child_dob5 =  $child_dtls->child_dob5;
 $child_dob6 =  $child_dtls->child_dob6;
 $child_dob7 =  $child_dtls->child_dob7;
 $child_dob8 =  $child_dtls->child_dob8;
 $child_dob9 =  $child_dtls->child_dob9;


 $arr = array(
    0 => array('name'=>$child1,'date'=>$child_dob1),
    1 => array('name'=>$child2,'date'=>$child_dob2),
    2 => array('name'=>$child3,'date'=>$child_dob3),
    3 => array('name'=>$child4,'date'=>$child_dob4),
    4 => array('name'=>$child5,'date'=>$child_dob5),
    5 => array('name'=>$child6,'date'=>$child_dob6),
    6 => array('name'=>$child7,'date'=>$child_dob7),
    7 => array('name'=>$child8,'date'=>$child_dob8),
    8 => array('name'=>$child9,'date'=>$child_dob9)
    //2 => array('text2'=>$child1,'date'=>'2015-04-08 12:36:18',),
);

function sorta_by_date($a, $b) {
    $a = strtotime($a['date']);
    $b = strtotime($b['date']);
    if ($a == $b) {
        return 0;
    }
    return ($a < $b) ? -1 : 1;
}

usort($arr, 'sorta_by_date');

$arr = array_reverse($arr);

//print_r(json_encode($arr));

 count($arr);

$j='1';
$string = array();
foreach($arr as $aary){ 
//echo $dob.$j;
  $string[] = $aary['date'];

	}
 $j++;
 

// echo $term = $string;
 print_r($string['0']);

// if(isset($_POST['vac_details'])){

// echo $_POST['ch_no'];


// }












//echo $dob3;


// ------====================Details Fetch End --------------------------








	$this->load->view('immune/vaccination', $datavacc);
}else if($unq_no== NULL){

$datamother['message'] = "disable";
	$this->load->view('immune/vaccination', $datamother);

}

}//from start another page access

elseif (isset($_POST['opt']) && $_POST['op_no']=='2' ) {
		
echo "FAQ QUESTIONS HERE";	

if(!empty($unq_no)){

$child = json_decode(
file_get_contents('https://onekeycare.org/immunize/api/vacci/'.$unq_no.'/')
);

$mthr = json_decode(
file_get_contents('https://onekeycare.org/immunize/api/unique/'.$unq_no.'/')
);




$datavacc['mother_unique_no']= $mthr->mthr_unq_id;
$datavacc['mthr_details'] = $mthr;
$datavacc['child_details'] = $child;

	$this->load->view('immune/faq', $datavacc);
}else if($unq_no== NULL){

$datamother['message'] = "disable";
	$this->load->view('immune/faq', $datamother);

}

}
//
elseif ($unq_no !== '0' && $unq_no !== NULL  ) {



	$dob1 = '$dob';
	$dob2 = '$dob';
	$dob3 = '$dob';
	$dob4 = '$dob';
	$dob5 = '$dob';
	$dob6 = '$dob';
	$dob7 = '$dob';
	$dob='$dob';

if(!empty($unq_no)){

$child = json_decode(
file_get_contents('https://onekeycare.org/immunize/api/vacci/'.$unq_no.'/')
);

$mthr = json_decode(
file_get_contents('https://onekeycare.org/immunize/api/unique/'.$unq_no.'/')
);


//echo json_encode($child);



$datavacc['mother_unique_no']= $mthr->mthr_unq_id;
$datavacc['mthr_details'] = $mthr;
$datavacc['child_details'] = $child;

$child_dtls = $child;


// ==================Details Fetch Starts----------------------==

 $child1 = $child_dtls->child_name1;
 $child2 =  $child_dtls->child_name2;
 $child3 =  $child_dtls->child_name3; 
 $child4 =  $child_dtls->child_name4;
 $child5 =  $child_dtls->child_name5;
 $child6 =  $child_dtls->child_name6;
 $child7 =  $child_dtls->child_name7;
 $child8 =  $child_dtls->child_name8;
 $child9 =  $child_dtls->child_name9;

 $child_dob1 = $child_dtls->child_dob1;
 $child_dob2 =  $child_dtls->child_dob2;
 $child_dob3 =  $child_dtls->child_dob3; 
 $child_dob4 =  $child_dtls->child_dob4;
 $child_dob5 =  $child_dtls->child_dob5;
 $child_dob6 =  $child_dtls->child_dob6;
 $child_dob7 =  $child_dtls->child_dob7;
 $child_dob8 =  $child_dtls->child_dob8;
 $child_dob9 =  $child_dtls->child_dob9;


 $arr = array(
    0 => array('name'=>$child1,'date'=>$child_dob1),
    1 => array('name'=>$child2,'date'=>$child_dob2),
    2 => array('name'=>$child3,'date'=>$child_dob3),
    3 => array('name'=>$child4,'date'=>$child_dob4),
    4 => array('name'=>$child5,'date'=>$child_dob5),
    5 => array('name'=>$child6,'date'=>$child_dob6),
    6 => array('name'=>$child7,'date'=>$child_dob7),
    7 => array('name'=>$child8,'date'=>$child_dob8),
    8 => array('name'=>$child9,'date'=>$child_dob9)
    //2 => array('text2'=>$child1,'date'=>'2015-04-08 12:36:18',),
);

function sorta_by_date($a, $b) {
    $a = strtotime($a['date']);
    $b = strtotime($b['date']);
    if ($a == $b) {
        return 0;
    }
    return ($a < $b) ? -1 : 1;
}

usort($arr, 'sorta_by_date');

$arr = array_reverse($arr);

//print_r(json_encode($arr));

 count($arr);

$j='1';
$string = array();
foreach($arr as $aary){ 
//echo $dob.$j;
  $string[] = $aary['date'];

	}
 $j++;
 

// echo $term = $string;
 print_r($string['0']);

// if(isset($_POST['vac_details'])){

// echo $_POST['ch_no'];


// }












//echo $dob3;


// ------====================Details Fetch End --------------------------








	$this->load->view('immune/vaccination', $datavacc);
}else if($unq_no== NULL){

$datamother['message'] = "disable";
	$this->load->view('immune/vaccination', $datamother);

}













// from another page










//option1
}





}




public function vac_details(){

if (isset($_POST['vac_details'])) {

	echo $unq_no = $_POST['mthrid'];

	echo $ques_rspn = $_POST['ch_no']; 

	$child = json_decode(
file_get_contents('https://onekeycare.org/immunize/api/vacci/'.$unq_no.'/')
);


// =========== Fetching DOB ===========================

$child_dtls = $child;


// ==================Details Fetch Starts----------------------==

 $child1 = $child_dtls->child_name1;
 $child2 =  $child_dtls->child_name2;
 $child3 =  $child_dtls->child_name3; 
 $child4 =  $child_dtls->child_name4;
 $child5 =  $child_dtls->child_name5;
 $child6 =  $child_dtls->child_name6;
 $child7 =  $child_dtls->child_name7;
 $child8 =  $child_dtls->child_name8;
 $child9 =  $child_dtls->child_name9;

 $child_dob1 = $child_dtls->child_dob1;
 $child_dob2 =  $child_dtls->child_dob2;
 $child_dob3 =  $child_dtls->child_dob3; 
 $child_dob4 =  $child_dtls->child_dob4;
 $child_dob5 =  $child_dtls->child_dob5;
 $child_dob6 =  $child_dtls->child_dob6;
 $child_dob7 =  $child_dtls->child_dob7;
 $child_dob8 =  $child_dtls->child_dob8;
 $child_dob9 =  $child_dtls->child_dob9;


 $arr = array(
    0 => array('name'=>$child1,'date'=>$child_dob1),
    1 => array('name'=>$child2,'date'=>$child_dob2),
    2 => array('name'=>$child3,'date'=>$child_dob3),
    3 => array('name'=>$child4,'date'=>$child_dob4),
    4 => array('name'=>$child5,'date'=>$child_dob5),
    5 => array('name'=>$child6,'date'=>$child_dob6),
    6 => array('name'=>$child7,'date'=>$child_dob7),
    7 => array('name'=>$child8,'date'=>$child_dob8),
    8 => array('name'=>$child9,'date'=>$child_dob9)
    //2 => array('text2'=>$child1,'date'=>'2015-04-08 12:36:18',),
);

function sortb_by_date($a, $b) {
    $a = strtotime($a['date']);
    $b = strtotime($b['date']);
    if ($a == $b) {
        return 0;
    }
    return ($a < $b) ? -1 : 1;
}

usort($arr, 'sortb_by_date');

$arr = array_reverse($arr);

//print_r(json_encode($arr));

 count($arr);

$j='1';
$string = array();
foreach($arr as $aary){ 
//echo $dob.$j;
  $string[] = $aary['date'];

	}
 $j++;
 

// echo $term = $string;
 //print_r($string['0']);


 if ($ques_rspn == '1' ) {
 	$chdob1 = $string['0'];
$child_vacci_dtls = json_decode(
file_get_contents('https://onekeycare.org/immunize/api/child_vacci/'.$unq_no.'/'.$chdob1.'/')
);
 }elseif ($ques_rspn == '2' ) {
 	$chdob2 = $string['1'];
$child_vacci_dtls = json_decode(
file_get_contents('https://onekeycare.org/immunize/api/child_vacci/'.$unq_no.'/'.$chdob2.'/')
);
 }elseif ($ques_rspn == '3' ) {
 	$chdob3 = $string['2'];
$child_vacci_dtls = json_decode(
file_get_contents('https://onekeycare.org/immunize/api/child_vacci/'.$unq_no.'/'.$chdob3.'/')
);
 }elseif ($ques_rspn == '4' ) {
 	$chdob4 = $string['3'];
$child_vacci_dtls = json_decode(
file_get_contents('https://onekeycare.org/immunize/api/child_vacci/'.$unq_no.'/'.$chdob4.'/')
);
 }if ($ques_rspn == '5' ) {
 	$chdob5 = $string['4'];
$child_vacci_dtls = json_decode(
file_get_contents('https://onekeycare.org/immunize/api/child_vacci/'.$unq_no.'/'.$chdob5.'/')
);
 }


 if ($ques_rspn == '1' ) {
 	$chdob1 = $string['0'];
$child_vacci_mdtls = json_decode(
file_get_contents('https://onekeycare.org/immunize/api/child_vaccimore/'.$unq_no.'/'.$chdob1.'/')
);
 }elseif ($ques_rspn == '2' ) {
 	$chdob2 = $string['1'];
$child_vacci_mdtls = json_decode(
file_get_contents('https://onekeycare.org/immunize/api/child_vaccimore/'.$unq_no.'/'.$chdob2.'/')
);
 }elseif ($ques_rspn == '3' ) {
 	$chdob3 = $string['2'];
$child_vacci_mdtls = json_decode(
file_get_contents('https://onekeycare.org/immunize/api/child_vaccimore/'.$unq_no.'/'.$chdob3.'/')
);
 }elseif ($ques_rspn == '4' ) {
 	$chdob4 = $string['3'];
$child_vacci_mdtls = json_decode(
file_get_contents('https://onekeycare.org/immunize/api/child_vaccimore/'.$unq_no.'/'.$chdob4.'/')
);
 }if ($ques_rspn == '5' ) {
 	$chdob5 = $string['4'];
$child_vacci_mdtls = json_decode(
file_get_contents('https://onekeycare.org/immunize/api/child_vaccimore/'.$unq_no.'/'.$chdob5.'/')
);
 }
















// ================== Fetching DOB ======================





	# code...


//echo $ch_dob;
 $datavacc['ch_vac_dtls'] = $child_vacci_dtls;

 $datavacc['child_vacci_mdtls'] = $child_vacci_mdtls;
$datavacc['mother_unique_no'] = $unq_no;


	$this->load->view('immune/ch_details', $datavacc);

}


}

















}
