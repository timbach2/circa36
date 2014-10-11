<?php

//require('../phplib/c36_prepend.php');

page_open(array("sess" => "C36_Session",
		"auth" => "C36_Default_Auth"));

$db = new DB_C36;
$base = $_SERVER['DOCUMENT_ROOT'] . '/mvc';  
$output = 'Sorry, the page failed to load.  This is the default output.';
// garbage collection

$db->query(sprintf("DELETE FROM sale_items WHERE sold_date!='0000-00-00' AND sold_date<'%s'",
				date("Y-m-d",mktime(0,0,0,date("m"),date("d")-15,date("Y")))
				)
		);
		
// set up template 

/************************************************
* 
* I am extending the template's parse function
* to intercept all instances of registered trademarks.
* 
* These will be checked for the register mark and
* have it added if necessary
* 
************************************************/

class template_r extends template {
  function parse($target, $handle, $append = false) {
    if (!is_array($handle)) {
      $str = $this->subst($handle);
      if ($append) {
        $this->set_var($target, $this->get_var($target) . $str);
      } else {
        $this->set_var($target, $str);
      }
    } else {
      reset($handle);
      while(list($i, $h) = each($handle)) {
        $str = $this->subst($h);
        $this->set_var($target, $str);
      }
    }
	
	// added tjm 1/10/2006 for C36
	// adds ® (#AE) to selected words
	
	$targets = array(
		"@Fiesta,@i",
		"@Fiesta\s([^&])@i",
		"@Harlequin,@i",
		"@Harlequin\s([^&])@i",
		);
	
	// could use &reg; instead of &#174;	
	$replacements = array(
		'Fiesta &#174;,',
		"Fiesta &#174; \\1",
		'Harlequin &#174;,',
		"Harlequin &#174; \\1",
		);
		
	$str = preg_replace($targets,$replacements,$str);
		
    return $str;
  }

}
		
$T = new template_r($base);
$T->set_file('wrapper','views/wrapper.ihtml'); 
$T->set_block('wrapper','ImageRotate','IRotate');

$T->set_var('page_title','Circa 36');  // overwrite this on individual pages
$T->set_var('meta_keywords','fiesta dinnerware, fiestaware, fiestaware soup bowls, fiestaware dish, fiesta ware, dinnerware and fiesta, vintage fiesta dinnerware, fiesta dinnerware for sale, buy fiesta dinnerware, fiesta ware store, fiesta pottery');
$T->set_var('meta_description','Collecting vintage Fiesta &reg;? Buy or Sell vintage Fiesta &reg; bowls, plates, casseroles, and more at Circa 36 fiestaware.  We have items from Harlequin &reg;, Riviera &reg;, Kitchen Kraft &reg;, and more!');

// displays the logo and random images on header
/*
$logo   = array('./images/c36logogreen.gif',
		'./images/c36logolilac.gif',
		'./images/c36logored.gif',
		'./images/c36logoturq.gif',
		'./images/c36logoyellow.gif'
		);
*/
$logo   = array(
		'/images/c36_logo_new_big.gif',
		'/images/c36_logo_new_big.gif',
		'/images/c36_logo_new_big.gif',
		'/images/c36_logo_new_big.gif',
		'/images/c36_logo_new_big.gif',
		);

$image[]=array('src'	=>'/images/turq_vase.jpg',
		'width'	=>'80',
		'height'=>'138',
		'valign' => 'middle',
		'align' => 'left'
		);
$image[]=array('src'	=>'/images/yellow_tripod_candle.jpg',
		'width'	=>'85',
		'height'=>'85',
		'valign' => 'bottom',
		'align' => 'left'
		);
$image[]=array('src'	=>'/images/red_marm.jpg',
		'width'	=>'85',
		'height'=>'85',
		'valign' => 'middle',
		'align' => 'center'
		);
$image[]=array('src'	=>'/images/green_1.jpg',
		'width'	=>'116',
		'height'=>'85',
		'valign' => 'top',
		'align' => 'right'
		);

// select random logo
$index = rand(0,4);
$T->set_var('logo',$logo[$index]);

// select 4 random images

for($i=1;$i<=4;$i++) {
	//$index = rand(0,(sizeof($image)-1));
	$index = $i-1;
	$T->set_var('src',	$image[$index]['src']);
	$T->set_var('width',	$image[$index]['width']);
	$T->set_var('height',	$image[$index]['height']);  
	
	//$T->set_var('valign',	$valign[rand(0,1)]);
	//$T->set_var('align',	$align[rand(0,2)]);
	
	$T->set_var('valign',	$image[$index]['valign']);
	$T->set_var('align',	$image[$index]['align']);
	
	$T->parse('IRotate','ImageRotate',true);
}

?>