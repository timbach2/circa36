<?php
//error_reporting(0);
//include('header.php');
//$db = new DB_C36;

$output = '<h1>Site Map</h1>';

$search = array ('@<script[^>]*?>.*?</script>@si', // Strip out javascript
                 '@<[\/\!]*?[^<>]*?>@si',          // Strip out HTML tags
                 '@([\r\n])[\s]+@',                // Strip out white space
                 '@&(quot|#34);@i',                // Replace HTML entities
                 '@&(amp|#38);@i',
                 '@&(lt|#60);@i',
                 '@&(gt|#62);@i',
                 '@&(nbsp|#160);@i',
                 '@&(iexcl|#161);@i',
                 '@&(cent|#162);@i',
                 '@&(pound|#163);@i',
                 '@&(copy|#169);@i',
                 '@&#(\d+);@e',
                 '@\n@',
                 '@\r@',
                 '@\t@'
                 );                    // evaluate as php

$replace = array ('',
                 ' ',
                 '\1',
                 '"',
                 '&',
                 '<',
                 '>',
                 ' ',
                 chr(161),
                 chr(162),
                 chr(163),
                 chr(169),
                 'chr(\1)',
                 '',
                 '',
                 ''
                 );


$output .= '<h2>Circa 36 Information</h2>';

$output .= '<div><a href="http://www.circa36.com/">Home</a></div>';
$output .= '<div><a href="http://www.circa36.com/c36/about">About Circa 36</a></div>';
$output .= '<div><a href="http://www.circa36.com/c36/about/orderinfo">Ordering Info</a></div>';
$output .= '<div><a href="http://www.circa36.com/c36/about/guarantee">Guarantee</a></div>';
$output .= '<div><a href="http://www.circa36.com/c36/about/acquisitions">Acquisitions</a></div>';
$output .= '<div><a href="http://www.circa36.com/c36/about/contact">Contact Us</a></div>';

/*
$output .= '<div><a href="http://www.circa36.com/mg/"></a></div>';
$output .= '<div><a href="http://www.circa36.com/mg/index.php?page=mg1.htm"></a></div>';
$output .= '<div><a href="http://www.circa36.com/mg/index.php?page=mg2.htm"></a></div>';
$output .= '<div><a href="http://www.circa36.com/mg/index.php?page=mg3.htm"></a></div>';
$output .= '<div><a href="http://www.circa36.com/mg/index.php?page=mg4.htm"></a></div>';
$output .= '<div><a href="http://www.circa36.com/mg/index.php?page=mg5.htm"></a></div>';
$output .= '<div><a href="http://www.circa36.com/mg/index.php?page=mg6.htm"></a></div>';
$output .= '<div><a href="http://www.circa36.com/mg/index.php?page=mg7.htm"></a></div>';
$output .= '<div><a href="http://www.circa36.com/mg/index.php?page=mg8.htm"></a></div>';
$output .= '<div><a href="http://www.circa36.com/mg/index.php?page=mg9.htm"></a></div>';
$output .= '<div><a href="http://www.circa36.com/mg/index.php?page=mg10.htm"></a></div>';
$output .= '<div><a href="http://www.circa36.com/mg/index.php?page=mg11.htm"></a></div>';
$output .= '<div><a href="http://www.circa36.com/mg/index.php?page=mg12.htm"></a></div>';
$output .= '<div><a href="http://www.circa36.com/mg/index.php?page=mg13.htm"></a></div>';
$output .= '<div><a href="http://www.circa36.com/mg/index.php?page=mg14.htm"></a></div>';
$output .= '<div><a href="http://www.circa36.com/mg/index.php?page=mg15.htm"></a></div>';
$output .= '<div><a href="http://www.circa36.com/mg/index.php?page=mg16.htm"></a></div>';

*/
$output .= '<div><a href="http://www.circa36.com/c36/reference">Reference</a></div>';
$output .= '<div><a href="http://www.circa36.com/c36/announcements">Announcements</a></div>';
	
$output .= '<h2>Items for sale</h2>';


$expires = 10; // days until sold items are removed
$expire_date = date('Y-m-d',mktime(0,0,0,date("m"),(date("d")-$expires),date("Y")));

$query = sprintf(
	"SELECT a.id,a.title,a.post_date,a.description,a.price,b.line as line_name " . 
	"FROM sale_items as a " . 
	"LEFT JOIN fiesta_lines as b on a.line=b.id " . 
	"WHERE (sold_date='0000-00-00' OR sold_date>='%s') AND " . 
	"title!='' " . 
	"ORDER BY title",
	$expire_date
	);

$db->query($query);

	
while($db->next_record()) {
		
	$output .= sprintf(
		"<div>" . 
		"  <a href=\"http://www.circa36.com/c36/display/%d\">%s</a>" . 
		"</div>\n",
		$db->f('id'),
		$db->f('title')
		);
}


?>
