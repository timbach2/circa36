<?php

/********************************************
*
* required and optional variables to be passed in:
*
* $_COOKIE['Circa36_Info'] (optional)
* (array)$states           (required)
*
********************************************/

$states = array('AK','AL','AR','AZ','CA','CO','CT','DC','DE','FL','GA','HI','IA','ID','IL','IN','KS','KY','LA','MA','MD','ME','MI','MN','MO','MS','MT','NC','ND','NE','NH','NJ','NM','NV','NY','OH','OK','OR','PA','RI','SC','SD','TN','TX','UT','VA','VT','WA','WI','WV','WY');
$selected_state = '';
$cookie_flag = false;

$T->set_file('chkout','views/checkout_info.ihtml');
$T->set_block('chkout','OptionList','OList');
$T->set_block('chkout','OptionList2','OList2');
$T->set_block('chkout','PersonalInfo','PInfo');
$T->set_block('chkout','ShippingInfo','SInfo');


	// fill in personal info from cookies, else blank
	if(isset($_COOKIE['Circa36_Info'])) {
		$ser = stripslashes($_COOKIE['Circa36_Info']); // must be stripslashed once more than addslashed above?
		$unser = unserialize($ser);
		foreach($unser as $key=>$value) {
			if($key=='state') { $selected_state = $value; }
			$T->set_var($key,htmlspecialchars($value));
		}
		$cookie_flag = true;
	}
	
	// fill in states for personal info
	for($i=0;$i<sizeof($states);$i++) {
		$T->set_var('value', $states[$i]);
		$T->set_var('option',$states[$i]);
		$T->set_var('selected', $states[$i]==$selected_state ? ' selected="selected"' : '');
		$T->parse('OList','OptionList',true);
	}
	$T->parse('PInfo','PersonalInfo');
	
	// print form for shipping info (not memorized, no values entered yet.)
	$T->set_var('OList','');
	$selected_state = '';
	$T->set_var('name','');
	$T->set_var('addr1','');
	$T->set_var('addr2','');
	$T->set_var('email','');
	$T->set_var('city','');
	$T->set_var('zip','');
	$T->set_var('option','');
	$T->set_var('value','');
	$T->set_var('selected','');
	// print state list
	for($i=0;$i<sizeof($states);$i++) {
		$T->set_var('value', $states[$i]);
		$T->set_var('option',$states[$i]);
		$T->set_var('selected', $states[$i]==$selected_state ? ' selected="selected"' : '');
		$T->parse('OList2','OptionList2',true);
	}
	$T->parse('SInfo','ShippingInfo');
	
	$T->set_var('message',	$cookie_flag ? 'Check form for correctness' : 'Please enter your personal information');
	
	$output = $T->parse('output','chkout');
?>