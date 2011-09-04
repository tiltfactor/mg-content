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
<p>You have requested a password reset for <?php echo $site_name ?> please click on the following link to reset your password</p>
<p><a href="<?php echo $activation_url ?>"><?php echo $activation_url ?></a></p>
<p>Thank you very much!</p>