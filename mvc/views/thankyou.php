<?php

// cart must be called first!
//printf("<pre>%s</pre>",print_r($_SESSION,true));
//print $_SESSION['grand_total'];
//$temptotal = $_SESSION['grand_total'];
//print "<br>$temp";



$T->set_file('thanks','views/thankyou.ihtml');


$T->set_var('grand_total',$_SESSION['grand_total']);



$output .= $T->parse('output','thanks');

?>