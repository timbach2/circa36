<?php


/****************************************************************
*
*  VALIDATE USER INPUT, SET DEFAULT VALUES
*
* set search parameters as session variables--
* therefore, use get vars if supplied, 
* or session variables if no get vars.
*
*****************************************************************/

$records = 25; // how many to display for updating
$expires = 10; // days until sold items are removed
$expire_date = date('Y-m-d',mktime(0,0,0,date("m"),(date("d")-$expires),date("Y")));

// if session variables have not been registered, clear them out.

if(!isset($searchQuery)) {
	$count=$searchQuery=$search_type=$item=$line=$count=$color=$id=$show_hide = '';
}


if(isset($_GET['count'])) {
	$count = $_GET['count'];
	$sess->register("count");
}

if(isset($_GET['set_search_type'])) { 
	// cleanse variables
	$search_type 	= $_GET['set_search_type'];   // switch()'d below
	$color 		= @(int)($_GET['color']);
	$item		= @(int)($_GET['item']);
	$line  		= @(int)($_GET['line']);
	$id			= @(int)($_GET['id']);
	$show_hide = @$_GET['show_hide']=='hide' ? 'hide' : 'show';
	
	//preg_match("/(.*)[;]*/",$_GET['searchQuery'],$matches);  // no sql allowed
	//$searchQuery = $matches[1];
	
	$searchQuery = str_replace(array("\"","'",";"),"",@$_GET['searchQuery']);  // no sql allowed
	$count 		= @(int)($_GET['count']); // zero if not set, only digits allowed
	   
	$sess->register("search_type");
	$sess->register("color");
	$sess->register("item");
	$sess->register("line");
	$sess->register("id");
	$sess->register("show_hide");
	$sess->register("count");
	$sess->register("searchQuery");
}






/**************************************************************
*
* set up templates
*
**************************************************************/


$T->set_file('list','views/search_list.ihtml');

$T->set_block('list','NewSold','NSold');
$T->set_block('list','ShowPic','SPic');
$T->set_block('list','SearchList','SList');

$T->set_var('NSold','');
$T->set_var('id','');
$T->set_var('title','');
$T->set_var('description','');
$T->set_var('thumbnail','');
$T->set_var('img_height','');
$T->set_var('img_width','');

$T->set_var('self',$_SERVER['PHP_SELF']);
$T->set_var('search_type',$search_type);
$T->set_var('searchQuery',$searchQuery);
$T->set_var('color',$color);
$T->set_var('item',$item);
$T->set_var('show_hide',$show_hide);
$T->set_var('toggle_show_hide',$show_hide=='hide' ? 'show' : 'hide');
$T->set_var('count',$count);
$T->set_var('search_id',$id);


$T->set_block('list','SearchResults','SResults');






/***********************************************************
*
* Which kind of sort?  Build query...
*
***********************************************************/


switch($search_type) {
	
	case 'AFS':		if($color) { $where[] = "color='$color' "; }
					if($item) { $where[] = "item='$item' "; }
					$where[] = 1;
					$where=sprintf(" WHERE %s AND (sold_date='0000-00-00' OR sold_date>='%s') AND title!='' " . 
								"order by title",
								implode(" AND ",$where),
								$expire_date
								);
					break;
	case 'line':		$where=sprintf(" WHERE a.line='%d' AND (sold_date='0000-00-00' OR sold_date>='%s') AND title!='' " . 
								"ORDER BY title",
								$line,
								$expire_date
								);
					break;
	case 'fulltext':	$where=sprintf(" WHERE MATCH (title,description) AGAINST ('%s') " . 
								"AND (sold_date='0000-00-00' OR sold_date>='%s')",
								$searchQuery,
								$expire_date
								);
					break;
	case 'item':		$where=sprintf(" WHERE a.id='%d' ",$id);
					break;
	default:			if($color) { $where[] = "color='$color' "; }
					if($item) { $where[] = "item='$item' "; }
					$where[] = 1;
					$where=sprintf(" WHERE %s AND (sold_date='0000-00-00' OR sold_date>='%s') AND title!='' " . 
								"order by title",
								implode(" AND ",$where),
								$expire_date
								);
					break;
} 


// how many total?
$query = sprintf(
			"select count(*) as total FROM sale_items as a " . 
			"left join fiesta_lines as b on a.line=b.id %s",
			$where
			);
		
	if(@$_GET['debug']=='twd') { print "$query<hr>"; }

	$db->query($query);		
	$db->next_record();

$total = $db->f('total');



// previous/next buttons
$T->set_var('start',($count-$records)>0 ? $count-$records : 0);
$T->set_var('prev',($count-$records)>=0 ? 'Previous' : '');
$T->set_var('end',$total>($records+$count) ? $count + $records : $count);
$T->set_var('next',$total>($records+$count) ? "Next" : '');
// pass searches along
$T->set_var('searchQuery',	$searchQuery);
$T->set_var('search_type',	$search_type);
$T->set_var('color',		$color);
$T->set_var('item',			$item);
$T->set_var('line',			$line);


// get record subset 

$query = sprintf(
			"SELECT a.id,a.title,a.price,th_fileheight,th_filewidth," . 
			"post_date,sold_date,feature,b.line as line_name " . 
			"FROM sale_items as a " . 
			"left join fiesta_lines as b on a.line=b.id  " . 
			"%s LIMIT %d,%d",
			$where,
			$count,
			$records);

$db->query($query);
$result=0;
$class ='';
while($db->next_record()) {
	$result++;
	$class=$class=='darkRow'?'lightRow':'darkRow';
	$new = (strtotime($db->f('post_date'))>mktime(0,0,0,date("m"),(date("d")-10),date("Y")));
	$sold_date = strtotime($db->f('sold_date'));
	$sold = ($db->f('sold_date')!='0000-00-00' && $sold_date<=mktime());

	if($new) { $T->parse('NSold','NewSold'); }
	if($sold) { $T->set_var('NSold','<font color="red"><b>...SOLD</b></font>'); }
	if($db->f('feature')) { $class = 'purple'; }
	$T->set_var('class',		$class);
	$T->set_var('id',			$db->f('id'));
	$T->set_var('title',		stripslashes($db->f('title')));
	$T->set_var('price',		sprintf("\$%7.2f",$db->f('price')));
	$T->set_var('line',			stripslashes($db->f('line_name')));
	$T->set_var('thumbnail',		'./images/spacer.gif');
	if($show_hide=='hide') {
		$T->set_var('SPic','');
	} else {
		$T->set_var('img_height',	$db->f('th_fileheight'));
		$T->set_var('img_width',		$db->f('th_filewidth'));
		$T->parse('SPic','ShowPic');
	}
		
	
	$T->parse('SList','SearchList',true);
	$T->set_var('NSold','');
}
if(!$result) {
	$T->set_var('SResults','Search yielded no results.  Please try again. Note that frequently used words such as "fiesta" are considered "noise words".');
	//$T->set_var('SResults',"DEBUG: search type: $search_type, searchQuery: $searchQuery, color: $color, item: $item, line: $line,id: $id, count: $count");
} else {
	$T->parse('SResults','SearchResults');
}

$output = $T->parse('output','list');

?>