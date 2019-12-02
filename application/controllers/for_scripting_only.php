<?php 
$datam['Hep_B_date'] = date('Y-m-d', strtotime($cdob));
$datam['Hep_B_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1 day'));

$datam['BCG_date'] =  date('Y-m-d', strtotime($cdob));
$datam['BCG_last_date'] =  date('Y-m-d', strtotime($cdob. ' + 1 year'));

$datam['OPV_O_date'] = date('Y-m-d', strtotime($cdob));
$datam['OPV_O_last_date'] = date('Y-m-d', strtotime($cdob. ' + 15 days'));

$datam['RVV1_date'] = date('Y-m-d', strtotime($cdob. ' + 42 days'));
$datam['RVV1_last_date'] = date('Y-m-d', strtotime($cdob. ' + 365 days'));

$datam['IPV1_date'] = date('Y-m-d', strtotime($cdob. ' + 42 days'));
$datam['IPV1_last_date'] = date('Y-m-d', strtotime($cdob. ' + 365 days'));

$datam['OPV1_date'] = date('Y-m-d', strtotime($cdob. ' + 42 days'));//
$datam['OPV1_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));

$datam['DPT1_date'] = date('Y-m-d', strtotime($cdob. ' + 42 days'));//
$datam['DPT1_last_date'] = date('Y-m-d', strtotime($cdob. ' + 730 days'));

$datam['HepB1_date'] = date('Y-m-d', strtotime($cdob. ' + 42 days'));//
$datam['HepB1_last_date'] = date('Y-m-d', strtotime($cdob. ' + 365 days'));

$datam['PENTA1_date'] =  date('Y-m-d', strtotime($cdob. ' + 42 days'));
$datam['PENTA1_last_date'] =  date('Y-m-d', strtotime($cdob. ' + 365 days'));



// Bucket 4 =================================================================
$datam['RVV2_date'] = date('Y-m-d', strtotime($cdob. ' + 70 days'));
$datam['RVV2_last_date'] = date('Y-m-d', strtotime($cdob. ' + 365 days'));


$datam['OPV2_date'] = date('Y-m-d', strtotime($cdob. ' + 70 days'));//
$datam['OPV2_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));

$datam['DPT2_date'] = date('Y-m-d', strtotime($cdob. ' + 70 days'));//
$datam['DPT2_last_date'] = date('Y-m-d', strtotime($cdob. ' + 730 days'));

$datam['HepB2_date'] = date('Y-m-d', strtotime($cdob. ' + 70 days'));//
$datam['HepB2_last_date'] = date('Y-m-d', strtotime($cdob. ' + 365 days'));

$datam['PENTA2_date'] =  date('Y-m-d', strtotime($cdob. ' + 70 days'));
$datam['PENTA2_last_date'] =  date('Y-m-d', strtotime($cdob. ' + 365 days'));


// Bucket 5 =================================================================

$datam['RVV3_date'] = date('Y-m-d', strtotime($cdob. ' + 98 days'));
$datam['RVV3_last_date'] = date('Y-m-d', strtotime($cdob. ' + 365 days'));

$datam['IPV2_date'] = date('Y-m-d', strtotime($cdob. ' + 98 days'));
$datam['IPV2_last_date'] = date('Y-m-d', strtotime($cdob. ' + 365 days'));

$datam['OPV3_date'] = date('Y-m-d', strtotime($cdob. ' + 98 days'));//
$datam['OPV3_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));

$datam['DPT3_date'] = date('Y-m-d', strtotime($cdob. ' + 98 days'));//
$datam['DPT3_last_date'] = date('Y-m-d', strtotime($cdob. ' + 365 days'));

$datam['HepB3_date'] = date('Y-m-d', strtotime($cdob. ' + 98 days'));//
$datam['HepB3_last_date'] = date('Y-m-d', strtotime($cdob. ' + 365 days'));

$datam['PENTA3_date'] =  date('Y-m-d', strtotime($cdob. ' + 98 days'));
$datam['PENTA3_last_date'] =  date('Y-m-d', strtotime($cdob. ' + 365 days'));


// Bucket 6 =================================================================
$datam['MMR_date'] = date('Y-m-d', strtotime($cdob. ' + 270 days'));
$datam['MMR_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));


$datam['JE1_date'] = date('Y-m-d', strtotime($cdob. ' + 270 days'));
$datam['JE1_last_date'] = date('Y-m-d', strtotime($cdob. ' + 5475 days'));


$datam['VIT_A_1_date'] = date('Y-m-d', strtotime($cdob. ' + 270 days'));
$datam['VIT_A_1_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));





// Bucket 7 =================================================================

$datam['OPV_BOOSTER_date'] = date('Y-m-d', strtotime($cdob. ' + 480 days'));
$datam['OPV_BOOSTER_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));


$datam['DPT_1_BOOSTER_date'] = date('Y-m-d', strtotime($cdob. ' + 480 days'));
$datam['DPT_1_BOOSTER_last_date'] = date('Y-m-d', strtotime($cdob. ' + 2555 days'));

$datam['MMR2_date'] = date('Y-m-d', strtotime($cdob. ' + 480 days'));
$datam['MMR2_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));


$datam['JE2_date'] = date('Y-m-d', strtotime($cdob. ' + 480 days'));
$datam['JE2_last_date'] = date('Y-m-d', strtotime($cdob. ' + 5475 days'));

// VITAMIN BUCKET =========================================================

$datam['VIT_A_2_date'] = date('Y-m-d', strtotime($cdob. ' + 540 days'));
$datam['VIT_A_2_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));

$datam['VIT_A_3_date'] = date('Y-m-d', strtotime($cdob. ' + 720 days'));
$datam['VIT_A_3_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));


$datam['VIT_A_4_date'] = date('Y-m-d', strtotime($cdob. ' + 900 days'));
$datam['VIT_A_4_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));


$datam['VIT_A_5_date'] = date('Y-m-d', strtotime($cdob. ' + 1080 days'));
$datam['VIT_A_5_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));


$datam['VIT_A_6_date'] = date('Y-m-d', strtotime($cdob. ' + 1260 days'));
$datam['VIT_A_6_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));

$datam['VIT_A_7_date'] = date('Y-m-d', strtotime($cdob. ' + 1340 days'));
$datam['VIT_A_7_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));


$datam['VIT_A_8_date'] = date('Y-m-d', strtotime($cdob. ' + 1520 days'));
$datam['VIT_A_8_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));


$datam['VIT_A_9_date'] = date('Y-m-d', strtotime($cdob. ' + 1700 days'));
$datam['VIT_A_9_last_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));



// Bucket 8 starts ==========================================================


$datam['DPT_2_BOOSTER_date'] = date('Y-m-d', strtotime($cdob. ' + 1825 days'));
$datam['DPT_2_BOOSTER_last_date'] = date('Y-m-d', strtotime($cdob. ' + 2190 days'));