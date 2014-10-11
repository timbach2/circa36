circa36
=======

This store with shopping cart was built for a friend who sold fiesta.  Due to family issues, the store is no longer in business.  

I have retained the code out of nostalgia-- it is the first shopping cart I created.

Structure
=========

This is built with an early understanding of MVC.  All requestes are sent to the controller, index.php, which then loads the appropriate view based on the url parameters.
It attempts separation of logic and presentation by having most of the logic in a monolithic "cart" object, which the views can then access as needed.


PHP-LIB
=======

I used phplib for database abstraction and templating.  The templates had the effect of splitting the view into two files:  
<ol>
<li>The _included_ html (files ending in .ihtml) </li><li>and the php file to feed variable into the ithml file.</li></ol>
This enabled me to completely remove html from the logic; however, it also made it tempting to put logic into the php file that called the view.

The PHP-LIB folder is not included in this repo


CSS
===

This was built before CSS became standard; it uses tables for presentation.


