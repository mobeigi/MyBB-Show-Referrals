<?php
/* 
 *  ++++++++++++++ A PHP File For  displaying logged in user's referrals ++++++++++++++
 *
 *
 *
 *  Show Referral -  A Advance referral plugin to show user's referrals, display banners with their codes and send PM to user when a user join via referral link
 *  Plugin Author:- Rahul
 *
 *  Website:- http://fulltofunzone.tk
 *  Email:- rahulsaini.rlm@gmail.com
 *  Facebook:- http://www.facebook.com/rahul.saini91
 *
 * Plugin compatible with MyBB v1.6 and 1.8 series
 * This Script is Copyright, Please don't remove any code. All modification will made by Author
 *
 * Please register with us and get support of any type related to any script. Registerations are Free and always will be.
 * http://fulltofunzone.tk
 *
 * Thank You For Using This Plugin.!!
 * 
 */
define("IN_MYBB", 1);
define('THIS_SCRIPT', 'referrals.php');
require_once './global.php';
if($mybb->user['uid'] > 0)
{
	$plugins->run_hooks('showreferral_page');
}
else
{
	error_no_permission();
}


?>