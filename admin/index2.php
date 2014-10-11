<?php

include('admin_header.php');
$db = new DB_C36;
$records = 10; // how many to display for updating
$count = @(int)($_GET['count']); // zero if not set, only digits allowed
$action = isset($_POST['action']) ? $_POST['action'] : $_GET['action'];
//$post_action = @$_GLOBAL['post_action'];
//$show_action = isset($_POST['action']) ? 'action is a POST variable' : 'action is a GET variable';
//print "<h1>$show_action</h1>";

$T->set_file('list','for_sale_list.ihtml');
$T->set_block('list','ForSaleList','FSList');

$T->set_var('start','');
$T->set_var('end','');
$T->set_var('prev','');
$T->set_var('next','');
$T->set_var('self',$PHP_SELF);
$T->set_var('iter','');
$T->set_var('description','');

$T->set_file('detail','for_sale_detail.ihtml');
$T->set_block('detail','ColorList','CList');
$T->set_block('detail','LineList','LList');
$T->set_block('detail','ItemList','IList');

$T->set_var('value','');
$T->set_var('selected','');
$T->set_var('option','');
$T->set_var('id','');
$T->set_var('item_description','');
$T->set_var('price','');
$T->set_var('shipping','');
$T->set_var('total','');




// take action!!

if($action=='delete') {
	// used javascript to confirm...
	$db->query(sprintf("delete from sale_items where id='%d'",
					$_GET['id']
					)
			);
	// fall through to list
}

if($action=='modify') {
	// display existing data
	
	$db->query(sprintf("select * from sale_items where id='%d'",
					$_GET['id']
					)
			);
	$db->next_record();
	
	// extract all variables before doing the drop down boxes...
	$T->set_var('id',				$db->f('id'));
	$T->set_var('item_description',	htmlentities(stripslashes($db->f('description'))));
	$T->set_var('price',			$db->f('price'));

	$default_item = $db->f('item');
	$default_color= $db->f('color');
	$default_line = $db->f('line') ? $db->f('line') : 1;
	$title= stripslashes($db->f('title'));
	// drop down boxes
	// get array of fiesta lines
	$db->query("select id,line from fiesta_lines order by line");
	while($db->next_record()) {
		$line_options[] = $db->f('id');
		$line = $db->f('line');
		$color_options[$line][0] = sprintf("new Array(\"%s\",%d)",
									'null','null');
		$item_options[$line][0] = sprintf("new Array(\"%s\",%d)",
									'null','null');

		$T->set_var('value',	$db->f('id'));
		$T->set_var('option',	htmlentities(stripslashes($db->f('line'))));
		$T->set_var('selected',	$db->f('id')==$default_line ? ' selected="selected"' : '');
		$T->parse('LList','LineList',true);
	}
	// get array of (fiesta) colors
	$db->query("select id,color,line from fiesta_colors order by color");
	while($db->next_record()) {
		$line = $db->f('line');
		$color= $db->f('color'); // must leave it addslashed as in the db
		$id	 = $db->f('id');
		$color_options[$line][] = sprintf("new Array(\"%s\",%d)",
									$color,$id);
		if($db->f('line')==$default_line) {
			$T->set_var('value',	$db->f('id'));
			$T->set_var('option',	$db->f('color'));
			$T->set_var('selected',	$db->f('id')==$default_color ? ' selected="selected"' : '');
			$T->parse('CList','ColorList',true);
		}
	}
	// get array of fiesta items
	$db->query("select id,item,line from fiesta_items order by item");
	while($db->next_record()) {
		$line = $db->f('line');
		$item = $db->f('item'); // must leave it addslashed as in the db
		$id	 = $db->f('id');
		$item_options[$line][] = sprintf("new Array(\"%s\",%d)",
									$item,$id);
		if($db->f('line')==$default_line) {
			$T->set_var('value',	$db->f('id'));
			$T->set_var('option',	htmlentities(stripslashes($db->f('item'))));
			$T->set_var('selected',	$db->f('id')==$default_item ? ' selected="selected"' : '');
			$T->parse('IList','ItemList',true);
		}
	}
	
	// DHTML javascript stuff...
	// color 
	for($i=0;$i<=sizeof($line_options);$i++) {
		settype($color_options[$i],"array");
		$temp_color[$i] = "          new Array(\n               " . 
		 implode(",\n               ",$color_options[$i]) . 
				"\n          )";
	}
	$T->set_var('color_array',"new Array(\n".implode(",\n",$temp_color)."\n)");	
	//print "new Array(\n".implode(",\n",$temp_color)."\n)";	
			
	// item 
	for($i=0;$i<=sizeof($line_options);$i++) {
		settype($item_options[$i],"array");
		$temp_item[$i] = "          new Array(\n               " . 
		 implode(",\n               ",$item_options[$i]) . 
				"\n          )";
	}
	$T->set_var('item_array',"new Array(\n".implode(",\n",$temp_item)."\n)");	
	//print "\n\n\nnew Array(\n".implode(",\n",$temp_item)."\n)";	
		
	$T->set_var('page_title',"Detail: $title");
	
	$T->set_var('body',$T->parse('output','detail'));
	$T->pparse('output','header');
	page_close();
	die;
}
	
if($action=='update') {
	$query=sprintf("update sale_items set description='%s',price='%s'," . 
				"color='%d',item='%d',line='%d',post_date='%s' WHERE id='%d'",
				addslashes($_POST['item_description']),
				addslashes($_POST['price']),
				$_POST['color'],
				$_POST['item'],
				$_POST['line'],
				date("Y-m-d"),
				$_POST['id']
				);
	$db->query($query);
	$query=sprintf("select a.line,b.item,c.color " . 
				"from sale_items as d " . 
				"left join fiesta_lines as a on d.line=a.id " . 
				"left join fiesta_items as b on d.item=b.id " . 
				"left join fiesta_colors as c on d.color=c.id " . 
				"where d.id='%d'",
				$_POST['id']
				);
	$db->query($query);
	$db->next_record();
	$line = $db->f('line');
	$item = $db->f('item');
	$color= $db->f('color');
	
	$query=sprintf("update sale_items set title='%s' " . 
				"WHERE id='%d'",
				"$line $color $item",
				$_POST['id']
				);
	$db->query($query);
}	
	


// default listing

// how many total for sale items?
$db->query("select count(*) as total from sale_items");
$db->next_record();
$total = $db->f('total');

// previous/next buttons
$T->set_var('start',($count-$records)>0 ? $count-$records : 0);
$T->set_var('prev',($count-$records)>=0 ? 'Previous' : '');
$T->set_var('end',$total>($records+$count) ? $count + $records : $count);
$T->set_var('next',$total>($records+$count) ? "Next" : '');

$query=sprintf("select id,title,description from sale_items order by title limit %d,%d",
			$count,$records
			);
$db->query($query);
$iter = $count;
$flag = true;
while($db->next_record()) {
	$flag=false;
	$iter++;
	$T->set_var('iter', $iter);
	$T->set_var('id',	$db->f('id'));
	$T->set_var('item',	htmlentities(stripslashes($db->f('title'))));
	$T->set_var('description', substr(htmlentities(stripslashes($db->f('description'))),0,50));
	$T->parse('FSList','ForSaleList',true);
}
if($flag) {
	$T->set_var('FSList','No items available');
}
$T->set_var('page_title','List of For Sale Items');

$T->set_var('body',$T->parse('output','list'));
$T->pparse('output','header');
page_close();
?>