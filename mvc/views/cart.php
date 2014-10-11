<?php

/****************************************
*
* I have broken a rule of MVC here!  The 
* cart total and tax is calculated in this script!
*
****************************************/

$tax_rate = (float)(.076);



$T->set_file('cart','views/cart.ihtml');

$T->set_block('cart','CartRow','CRow');

$T->set_var('CRow','');


$T->set_file('pt_cart','views/cart_plain_text.ihtml');
$T->set_block('pt_cart','PlainTextCartRow','PTCRow');
$T->set_var('PTCRow','');


$T->set_var('SCheckout','');

$T->set_var('class','');
$T->set_var('id','');
$T->set_var('c36id','');
$T->set_var('description','');
$T->set_var('price','');
$T->set_var('shipping','');
$T->set_var('tax','');
$T->set_var('grand_total','');

/*
$T->set_var('discount','');
$T->set_var('total','');
$T->set_var('pname','');
$T->set_var('sname','');
$T->set_var('paddr','');
$T->set_var('saddr','');
$T->set_var('pcity','');
$T->set_var('pstate','');
$T->set_var('scity','');
$T->set_var('sstate','');
$T->set_var('pzip','');
$T->set_var('szip','');
$T->set_var('email','');
*/

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$max = 0;
$running_shipping = 0;
$total = 0;

$class = 'darkRow';
$no_results = true;
foreach($cart as $id=>$v) {
	
	$class = $class=='lightRow' ? 'darkRow' : 'lightRow';
	$T->set_var('class',$class);
	$T->set_var('id',$id);
	$T->set_var('c36id','C36'.sprintf("%04d",$id));
	$T->set_var('description',	$v['description']);
	$T->set_var('price',		number_format($v['unit_price'],2,'.',''));
	$T->set_var('shipping',		number_format($v['shipping'],2,'.',''));
	
	// OK, this is actually model stuff, but it's too convenient to not do it here...
	
	$total += $v['unit_price'];
	$max = $v['shipping']>$max ? $v['shipping'] : $max;
	$running_shipping += round(($v['shipping']/2),2);
	
	// back to view stuff
	
	$T->parse('CRow','CartRow',true);
	$T->parse('PTCRow','PlainTextCartRow',true);
	$no_results = false;
}

// all shipping is half price; now add half of the max shipping amount
// which equals full max shipping amount plus half of the others.

$running_shipping += round($max/2,2);
$T->set_var('shipping',	number_format($running_shipping,2,'.',''));


// figure tax

$tax = @$info['state']=="MO" ? number_format(($total*$tax_rate),2,'.','') : '0.00';
$T->set_var('tax',			$tax);
$T->set_var('grand_total',	number_format(($total+$running_shipping+$tax), 2, '.', ''));

// this must happen before thankyou.php is called!
$_SESSION['grand_total'] = 	number_format(($total+$running_shipping+$tax), 2, '.', '');


//$output .= sprintf("<pre>%s</pre>",print_r($cart,true));

if($no_results) {
	$T->set_var('page_title','Cart empty');
	$output .= "<h3>Your cart is empty!</h3>";
} else {
	$output .= $T->parse('output','cart');
}

$cart_plain_text = $T->parse('output','pt_cart');

?>
