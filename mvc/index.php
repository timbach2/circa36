<?php
error_reporting(E_ALL ^ E_NOTICE);
require('mvc/config.php');

switch(@$argv[0]) {
	
	case 'about':
	
		switch(@$argv[1]) {
			
			case 'orderinfo':
				$T->set_var('page_title','Circa 36 Ordering Info');
				$T->set_file('orderinfo','views/orderinfo.ihtml');
				$output = $T->parse('output','orderinfo');
				break;
				
			case 'guarantee':
				$T->set_var('page_title','Circa 36 Guarantee');
				$T->set_file('guarantee','views/guarantee.ihtml');
				$output = $T->parse('output','guarantee');
				break;
				
			case 'acquisitions':
				$T->set_var('page_title','Circa 36 Buying and Selling Assistance');
				$T->set_file('acq','views/acquisitions.ihtml');
				$output = $T->parse('output','acq');
				break;
			
			case 'contact':
				include('views/contact.php');
				break;
				
			default:
				$T->set_var('page_title','About Circa 36');
				$T->set_file('about','views/about.ihtml');
				$output = $T->parse('output','about');
				break;
		}
		break;
	
	case 'announcements':
		
		$T->set_var('page_title','Circa 36 Announcements');
		include('views/announcements.php');
		break;
	
	case 'reference':
		
		$T->set_var('page_title','Fiesta&reg; Reference Books');
		$T->set_file('ref','views/reference.ihtml');
		$output = $T->parse('output','ref');
		break;
	
	case 'cart':
		
		$action=@$argv[1];
		$id = @$argv[2];
		
		// define class BEFORE calling session; oh, wait a minute... session['cart'] is just a hash.
		include('models/cart.php');
		$Cart = new cart;
		
		session_start();
		
		// yeah, probably should have made a 'cart' controller...
		
		switch($action) {
			
			case 'add':
				
				$Cart->add($id);
				/*
				printf("location: http://" . $_SERVER['SERVER_NAME'] . '/c36/cart/view');
				printf("<pre>%s</pre>",print_r($_SERVER,true));
				die;
				*/
				// now, redirect to view the cart
				//header("location: http://" . $_SERVER['SERVER_NAME'] . '/c36/cart/view');
				header("location: http://" . $_SERVER['HTTP_HOST'] . '/c36/cart/view');
				exit;
				break;
			
			case 'delete':
				
				$Cart->delete($id);
				
				// now, redirect to view the cart
				header("location: http://" . $_SERVER['HTTP_HOST'] . '/c36/cart/view');
				exit;
				
			case 'info':
				$T->set_var('page_title','Shipping Information');
				include('views/checkout_info.php');
				break;
			
			case 'save_info':
				
				// save this info on user's cookie.
				$ser = serialize($_POST['info']);
				setcookie("Circa36_Info",$ser,mktime(0,0,0,date("m"),date("d"),(date("Y")+1)));
				$ser = '';
				$ser = serialize($_POST['ship_to']);
				setcookie("Circa36_ShipTo",$ser,mktime(0,0,0,date("m"),date("d"),(date("Y")+1)));
				
				// also put in session
				$_SESSION['info'] = $_POST['info'];
				$_SESSION['ship_to'] = $_POST['ship_to'];
				
				// redirect to 'review'
				header("location: http://" . $_SERVER['HTTP_HOST'] . '/c36/cart/review');
				exit;
				
			case 'review':
				
				if($Cart->blacklisted($_SESSION['info']['email'])) {
					// redirect to 'sorry'
					header("location: http://" . $_SERVER['HTTP_HOST'] . '/c36/cart/sorry');
					exit;
				}
				
				$T->set_var('page_title','Review Order and Shipping Information');
				$output = '';  // view and review use $output .= 'something'.  Therefore initialize it.
				include('views/cart.php');
				include('views/review_info.php');
				break;
			
			case 'process':
				
				if($Cart->blacklisted($_SESSION['info']['email'])) {
					// redirect to 'sorry'
					header("location: http://" . $_SERVER['HTTP_HOST'] . '/c36/cart/sorry');
					exit;
				}
				
				$output = '';  // view and review use $output .= 'something'.  Therefore initialize it.
				
				include('views/thankyou.php');
				include('views/cart.php');
				//$cart_contents = $output;
				
				include('views/review_info.php');
				
				$plain_text = sprintf(
					"%s\n\n%s",
					$cart_plain_text,
					$review_plain_text
					);
				
				//$Cart->process_order($cart_contents);
				$Cart->process_order($output,$plain_text); // include shipping info in database
				
				// redirect to 'thankyou'
				
				session_write_close();
				header("location: http://" . $_SERVER['HTTP_HOST'] . "/c36/cart/thankyou");
				exit;
				
			case 'thankyou':
				
				$T->set_var('page_title','Order Completed');
				$output = '';

				include('views/thankyou.php');
				include('views/cart.php');
								
				$Cart->delete_cart();
				break;
					
			case 'view':
			default:
				
				// NOTE:  tax and cart object are calculated in the cart VIEW...  it breaks form, but it was much easier to do here.  sorry.
				$output = '';
				include('views/cart.php');
				include('views/checkout_or_return.php');
		
		}
		break;
	
	case 'search':
	
		include('views/search.php');
		break;
			
	case 'display':
		
		include('views/display.php');
		break;
			
	case 'sitemap':
		
		include('views/sitemap.php');
		break;
		
	default:
	
		include('views/index.php');
		
}


$T->set_var('page_body',$output);
$T->pparse('output','wrapper');

page_close();
?>
