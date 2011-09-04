<?php
/*
 * You can use the following variables
 * 
 * $user: the user model
 * $site_name: the name of the site
 * $activation_url: the url the user should go to to activate her account
 * 
 */

?> 
<p>Hello, <?php echo $user->username ?></p>
<p>You have registered as player on <?php echo $site_name ?> please click on the following link to activate your account</p>
<p><a href="<?php echo $activation_url ?>"><?php echo $activation_url ?></a></p>
<p>Thank you very much!</p>