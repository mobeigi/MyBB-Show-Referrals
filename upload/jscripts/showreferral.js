/* 
 * ++++++++++++++ JavaScript Function File ++++++++++++++
 *
 *  Show Referral -  A Advance referral plugin to show user's referrals, display banners with their codes and send PM to user when a user join via referral link
 *  Plugin Author:- Rahul

 *  Website:- http://fulltofunzone.tk
 *  Email:- rahulsaini.rlm@gmail.com
 *  Facebook:- http://www.facebook.com/rahul.saini91
 *
 * Plugin compatible with MyBB v1.6 and 1.8 series
 * This Script is Copyright, Please don't remove any code. All modification will made by Author
 *
 * WHAT'S NEW IN v1.3
 *----------------------------------------
 *	1. Notify Staff when a user signup via referral link.
 *	2. Customizable Staff id.
 *	3. Referrals and Referred By on user's profile
 *	4. Error Fixed.
 *
 * * WHAT'S NEW IN v1.4
 *----------------------------------------
 *	1. All Error Fixed.
 *
 *
 *
 * Please register with us and get support of any type related to any script. Registerations are Free and always will be.
 * http://fulltofunzone.tk
 *
 * Thank You For Using This Plugin.!!
 * 
 */
// Show / Hide Function
function showhide(content)
 {
  var txt;
  txt = document.getElementById("control").value;
   if(txt == "Show")
   {
   	document.getElementById(content).style.display = "block";
	document.getElementById("control").value = "Hide";
   }
   if(txt == "Hide")
   {
   	document.getElementById(content).style.display = "none";
	document.getElementById("control").value = "Show";
   }
 }