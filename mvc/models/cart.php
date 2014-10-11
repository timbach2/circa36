<?php

class cart {
	
	var $DB = 'DB_C36';
	
  function cart() {
  	
  	// hmm...
  	$this->db = new $this->DB;
  	
  }
  
  function add($id) {
	$db = new $this->DB;
	$db->query(sprintf(
		"SELECT a.*,b.shipping " . 
		"FROM sale_items as a " . 
		"left join fiesta_items as b " . 
		"on a.item=b.id " . 
		"WHERE a.id='%d'",
		$id
		));
	$db->next_record();
	$id = $db->f('id');
	$_SESSION['cart'][$id] = array(
		'description'	=>stripslashes($db->f('title')),
		'unit_price'	=>$db->f('price'),
		'shipping'	=>$db->f('shipping'),
		'discount'	=>0,
		'total'		=>($db->f('price')+$db->f('shipping'))
		);
	//$sess->register("cart");
  }
  
  function delete_cart() {
  	
  	
  	global $sess;
	$sess->unregister("info");
	$sess->unregister("ship_to");
	$sess->unregister("cart");
  	
  	unset($_SESSION['cart']);
  	
  }
  
  function delete($id) {
  	
  	global $sess;
	$sess->unregister("info");
	$sess->unregister("ship_to");
	$sess->unregister("cart");
  	
  	unset($_SESSION['cart'][$id]);
  	
  }
  
  function blacklisted($email) {
	$db = new DB_C36;
	preg_match("/^([a-zA-Z0-9_-]*\.*[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+)/",$email,$matches);
	$from_email = addslashes($matches[1]);
	$query="select email from blacklist where email='$from_email'";
	$db->query($query);
	$flag = $db->next_record();
	//print "query = $query<br>flag = $flag<hr>";	
	return $flag;
  }
  
  function process_order($html,$plain_text) {
  	
  	global $sess;
  	$this->record_transaction($plain_text);
  	$this->set_as_sold($_SESSION['cart']);
	$this->send_mail($html,$plain_text);
	
	
	
	//$this->send_pear_mail($output);
			
	//$sess->unregister("info");
	//$sess->unregister("ship_to");
	//$sess->unregister("cart");
  	
  	
  }
  
  function record_transaction($plain_text) {
	
	// send to database for backup
	$this->db->query(sprintf(
		"insert into accounting (order_text,order_date) values ('%s','%s')",
		addslashes($plain_text),
		date("YmdHis")
		));
  	
  }
  
  function set_as_sold($cart) {
  	
	$in = implode("','",array_keys($cart));  
	$query=sprintf(
		"UPDATE sale_items SET sold_date='%s' WHERE id IN('%s')",
		date('Y-m-d'),
		$in
		);
	$this->db->query($query);
  
  }
  
  function send_mail($html,$plain_text) {
  	
	// get submitted email address		
	preg_match(
		"/^([a-zA-Z0-9_-]*\.*[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+)/",
		$_SESSION['info']['email'],
		$matches
		);
	$customer_email = $matches[1];
  	
  	$to = array (
  		'tim@timlyd.com',
  		'george@circa36.com',
  		'c36_orders@circa36.com'
  		);
  	
  	$to_email = implode(', ',$to);
  	
	// subject
	$subject = 'Circa36 Order';
	
	// message
	//$message = '';
	
	// To send HTML mail, the Content-type header must be set
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	
	// Additional headers
	//$headers .= 'To: Mary <mary@example.com>, Kelly <kelly@example.com>' . "\r\n";
	//$headers .= 'From: Birthday Reminder <birthday@example.com>' . "\r\n";
	//$headers .= 'Cc: birthdayarchive@example.com' . "\r\n";
	//$headers .= 'Bcc: birthdaycheck@example.com' . "\r\n";
	
	$headers .= "From: Circa36<george@circa36.com>" . "\r\n";

	// Mail it to George
	mail($to_email, $subject, $html, $headers);
	
	// Mail it to Customer
	mail($customer_email,'Circa36 Order Confirmation',$html,$headers);
	
  }
  
  function send_pear_mail($html_text) {
  	
	// get submitted email address		
	preg_match(
		"/^([a-zA-Z0-9_-]*\.*[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+)/",
		$_SESSION['info']['email'],
		$matches
		);
	$from_email = $matches[1];
	
	// not used...
	//$from_name = $_SESSION['info']['name'];
		
	// set up pear mail	
	
	include('Mail.php');
	include('Mail/mime.php');
	
	$crlf = "\n";
	$hdrs = array(
	              'From'    => $from_email,
	              'Subject' => 'Confirmation of your Circa36 Order',
	              'To'      => 'George Vincel <george@circa36.com>',
	              'X-TWD'   => 'Using PEAR Mail_Mime'
	              );
	
	$mime = new Mail_mime($crlf);
	if (PEAR::isError($mime)) {
	  print $mime->get-Message();
	}
	
	
	//$mime->setTXTBody($plain_text);
	$mime->setHTMLBody($html_text);
	
	$body = $mime->get();
	$hdrs = $mime->headers($hdrs);
	
	$mail =& Mail::factory('mail');
	
	if (PEAR::isError($mail)) {
	  print $mail->get-Message();
	}
	
	printf("<pre>%s<hr>%s<hr>%s</pre>",print_r($mail,true),print_r($hdrs,true),print_r($body,true));
	
	$result = $mail->send('tim@timlyd.com', $hdrs, $body);
	//$result = $mail->send('george@circa36.com', $hdrs, $body);
	//printf("<pre>hdrs: %s<hr>body: %s</pre>", print_r($hdrs,true),$body );
	
	/*
    */
	if (PEAR::isError($result)) {
	  print $result->getMessage();
      print "<h3>Order Failed</h3>email failure, please contact <a href='mailto:george@circa36.com'>Circa 36</a>";
      //die;
	}
  	
  }
  
  function send_smtp_mail($html_text) {
  	
	// get submitted email address		
	preg_match("/^([a-zA-Z0-9_-]*\.*[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+)/",$info['email'],$matches);
	$from_name = $info['name'];
	$from_email = $matches[1];
		
	// set up smtp mail	
	
	include('Mail.php');
	include('Mail/mime.php');
	
	$crlf = "\n";
	$hdrs = array(
	              'From'    => $from_email,
	              'Subject' => 'Confirmation of your Circa36 Order',
	              'To'      => 'George Vincel <georg@circa36.com>',
	              'X-TWD'   => 'Using PEAR Mail_Mime'
	              );
	
	$smtp = array( 'host' => 'smtp.circa36.com',
	                //'auth'    => true,
	               'username' => 'c36_orders',
	                'password'=>'ciflanze'
	                );
	
	$mime = new Mail_mime($crlf);
	
	//$mime->setTXTBody($plain_text);
	$mime->setHTMLBody($html_text);
	
	$body = $mime->get();
	$hdrs = $mime->headers($hdrs);
	
	$mail =& Mail::factory('smtp', $smtp);
	if (PEAR::isError($mail)) {
	  print $mail->get-Message();
	}
	
	$result = $mail->send('tim@timlyd.com', $hdrs, $body);
	$result = $mail->send('george@circa36.com', $hdrs, $body);
	
	if (PEAR::isError($result)) {
	  print $result->getMessage();
      print "<h3>Order Failed</h3>email failure, please contact <a href='mailto:george@circa36.com'>Circa 36</a>";
      die;
	}
	
  	
  }
  
}

?>