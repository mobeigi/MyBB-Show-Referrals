<?php

/* 
 *  ++++++++++++++ Show Referral Plugin Core File ++++++++++++++
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
 * Please register with us and get support of any type related to any script. Registerations are Free and always will be.
 * http://fulltofunzone.tk
 *
 * Thank You For Using This Plugin.!!
 * 
 */
if(!defined("IN_MYBB"))
	die("This file cannot be accessed directly.");

//$plugins->add_hook("usercp_start", "showreferral_recent");    //remove usercp component
//$plugins->add_hook("usercp_menu", "showreferral_usercp_menu"); //remove usercp component
$plugins->add_hook("showreferral_page","showreferral_load");
$plugins->add_hook("member_do_register_end", "showreferral_sendpm");
$plugins->add_hook("member_profile_start", "showreferral_profile");
function showreferral_info()
{
	return array(
		"name" 		=> "Show Referrals",
		"description"	=> "A Advance referral system which is allow user to display their referrals, referral banners with their Codes (HTML and BB), send PM and so on. <font size='1'>For any kind of support please discuss in our community <a href='http://fulltofunzone.tk' target='_blank'>http://fulltofunzone.tk</a><br>OR Contact me:- rahulsaini.rlm@gmail.com</font>",
		"website"		=> "http://fulltofunzone.tk",
		"author"		=> "Rahul",
		"authorsite" 	=> "http://fulltofunzone.tk",
		"version"		=> "1.41-Byte",
		"guid" 		=> "",
		"compatibility"	=> "1*"
	);
}
function showreferral_activate()
{
	global $db, $lang;
	// create settings group
	$insertarray = array(
		'name' 		=> 'showreferral', 
		'title' 		=> 'Show Referral', 
		'description' 	=> "Settings for Show Referrals", 
		'disporder' 	=> 1, 
		'isdefault' 		=> 0
	);
	$gid = $db->insert_query("settinggroups", $insertarray);
	// add settings

	$setting[] = array(
		"name"			=> "showreferral_disabled",
		"title"			=> "Show Referral Disabled?",
		"description"		=> "Set to yes if want to disable this plugin.",
		"optionscode"		=> "yesno",
		"value"			=> 0,
		"disporder"		=> 1,
		"gid"			=> $gid
	);
	$setting[] = array(
		"name"			=> "showreferral_profile_enable",
		"title"			=> "Show Referral in Profile",
		"description"		=> "Set to No if you don\'t want to display in user\'s profile \"",
		"optionscode"		=> "yesno",
		"value"			=> 1,
		"disporder"		=> 2,
		"gid"			=> $gid
	);
	$setting[] = array(
		"name"			=> "showreferral_showreferrer",
		"title"			=> "Show Referred By",
		"description"		=> "Set to No if you don\'t want to display \"Referred By: \"",
		"optionscode"		=> "yesno",
		"value"			=> 1,
		"disporder"		=> 2,
		"gid"			=> $gid
	);
	$setting[] = array(
		"name"			=> "showreferral_totalreferral",
		"title"			=> "Show Total Referrals",
		"description"		=> "Set to No if you don\'t want to display \"Total Referrals\"",
		"optionscode"		=> "yesno",
		"value"			=> 1,
		"disporder"		=> 3,
		"gid"			=> $gid
	);
	$setting[] = array(
		"name"			=> "showreferral_banners",
		"title"			=> "Enable Banners",
		"description"		=> "Set to No if you want to turn off Referral Banner Page",
		"optionscode"		=> "yesno",
		"value"			=> 1,
		"disporder"		=> 4,
		"gid"			=> $gid
	);
	$setting[] = array(
		"name"			=> "showreferral_bannerdir",
		"title"			=> "Banner\'s Directory",
		"description"		=> "Change banner\'s directory if you have another. (Leave blank for default directory)",
		"optionscode"		=> "text",
		"value"			=> 'images/banners',
		"disporder"		=> 5,
		"gid"			=> $gid
	);
	$setting[] = array(
		"name"			=> "showreferral_perpage",
		"title"			=> "Per Page",
		"description"		=> "No. of Referrals on Per Page",
		"optionscode"		=> "text",
		"value"			=> 10,
		"disporder"		=> 6,
		"gid"			=> $gid
	);
	$setting[] = array(
		"name"			=> "showreferral_permission",
		"title"			=> "Group Permission",
		"description"		=> "Enter Group ids seperated by comma to enable only for selected usergroups. leave blank for enable all groups.",
		"optionscode"		=> "text",
		"value"			=> '',
		"disporder"		=> 7,
		"gid"			=> $gid
	);
	$setting[] = array(
		"name"			=> "showreferral_usercp_enable",
		"title"			=> "Recent Referrals",
		"description"		=> "Select yes to display recent referrals in usercp",
		"optionscode"		=> "yesno",
		"value"			=> 1,
		"disporder"		=> 8,
		"gid"			=> $gid
	);
	$setting[] = array(
		"name"			=> "showreferral_usercp_limit",
		"title"			=> "Display Limit",
		"description"		=> "No. of referrals displayed at UserCP, Leave blank for default value (5)",
		"optionscode"		=> "text",
		"value"			=> 5,
		"disporder"		=> 9,
		"gid"			=> $gid
	);
	$setting[] = array(
		"name"			=> "showreferral_sendpm_enable",
		"title"			=> "Enable PM System?",
		"description"		=> "A Private Message will send automatically when a user signup through referral username or link. You can customize PM Subject and Messages with custom text and Varialbes. You Can use following Variables:
					        <br>\$referrer = Referrer User
					        <br>\$referrer_link = Referrer User with Profile Hyberlink
				 	        <br>\$referral = Referral User (New Registration from referral link)
				 	        <br>\$referral_link = Referral User with profile link
				 	        <br>\$board_name = Your Forum Name
				 	        <br>\$board_url = Your Forum URL.",
		"optionscode"		=> "yesno",
		"value"			=> 1,
		"disporder"		=> 10,
		"gid"			=> $gid
	);
	$setting[] = array(
		"name"			=> "showreferral_sendpm_admin",
		"title"			=> "Notify Staff",
		"description"		=> "Notify Admin when a user signup via invitation / referral link",
		"optionscode"		=> "yesno",
		"value"			=> 1,
		"disporder"		=> 11,
		"gid"			=> $gid
	);


	$setting[] = array(
		"name"			=> "showreferral_sendpm_admin_id",
		"title"			=> "Staff\'s User Id",
		"description"		=> "Enter Mods / Staff id, separated by comma. Default id is 1. <br><small>Please don\'t enter user id more then 5-6, maximum id can be make slower your site.</small>",
		"optionscode"		=> "text",
		"value"			=> '',
		"disporder"		=> 12,
		"gid"			=> $gid
	);
	$setting[] = array(
		"name"			=> "showreferral_sendpm_admin_sub",
		"title"			=> "Subject",
		"description"		=> "Customize Subject for staff",
		"optionscode"		=> "text",
		"value"			=> $db->escape_string(message("admin_sub")),
		"disporder"		=> 13,
		"gid"			=> $gid
	);
	$setting[] = array(
		"name"			=> "showreferral_sendpm_admin_msg",
		"title"			=> "Message",
		"description"		=> "Customize Message for staff",
		"optionscode"		=> "textarea",
		"value"			=> $db->escape_string(message("admin_msg")),
		"disporder"		=> 14,
		"gid"			=> $gid
	);
	$setting[] = array(
		"name"			=> "showreferral_sendpm1_enable",
		"title"			=> "PM [1] Enable",
		"description"		=> "Enable/Disable PM [1] which will send to referrer",
		"optionscode"		=> "yesno",
		"value"			=> 1,
		"disporder"		=> 15,
		"gid"			=> $gid
	);
	$setting[] = array(
		"name"			=> "showreferral_sendpm_subject1",
		"title"			=> "Subject [1]",
		"description"		=> "Customize PM subject for Referrer , Leave Blank for Default value.",
		"optionscode"		=> "text",
		"value"			=> $db->escape_string(message("referrer_sub")),
		"disporder"		=> 16,
		"gid"			=> $gid
	);

	$setting[] = array(
		"name"			=> "showreferral_sendpm_message1",
		"title"			=> "PM Message [1]",
		"description"		=> "Customize PM message for Referrer , Leave Blank for Default value.",
		"optionscode"		=> "textarea",
		"value"			=> $db->escape_string(message("referrer_msg")),
		"disporder"		=> 17,
		"gid"			=> $gid
	);
	$setting[] = array(
		"name"			=> "showreferral_sendpm2_enable",
		"title"			=> "PM [2] Enable",
		"description"		=> "Enable/Disable PM [2] which will send to referral",
		"optionscode"		=> "yesno",
		"value"			=> 1,
		"disporder"		=> 18,
		"gid"			=> $gid
	);
	$setting[] = array(
		"name"			=> "showreferral_sendpm_subject2",
		"title"			=> "Subject [2]",
		"description"		=> "Customize PM subject for Referral, Leave Blank for Default value.",
		"optionscode"		=> "text",
		"value"			=> $db->escape_string(message("referral_sub")),
		"disporder"		=> 19,
		"gid"			=> $gid
	);
	$setting[] = array(
		"name"			=> "showreferral_sendpm_message2",
		"title"			=> "PM Message [2]",
		"description"		=> "Customize PM message for Referrer, Leave Blank for Default value. ",
		"optionscode"		=> "textarea",
		"value"			=> $db->escape_string(message("referral_msg")),
		"disporder"		=> 20,
		"gid"			=> $gid
	);

	$db->insert_query_multiple("settings", $setting);

	rebuild_settings();
	add_template_showreferral();	
}

function showreferral_deactivate()
{
	global $db, $mybb;
	// delete settings group
	$db->delete_query("settinggroups", "name = 'showreferral'");

	// remove settings
	$db->delete_query('settings', "name like 'showreferral_%'");
	require_once MYBB_ROOT.'/inc/adminfunctions_templates.php';
	remove_template_showreferral();
	rebuild_settings();
}
function add_template_showreferral()
{
global $db;
$temp = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><!-- start: index -->
<html xml:lang="en" lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<head>
	<title>{$showreferral_title}</title>
	{$headerinclude}
</head>
<body>
{$header}
	<table align="center" width="100%">
    			<tr>
			    <th class="thead" style="border-radius:5px;" align="left">{$showreferral_sub_title}</th>
			</tr>
			    <tr>
			<td valign="top" width="180" class="trow1">
				{$showreferral}
			</td>
		</tr>
	</table>
{$footer}
</body>
</html>';
$recent = '{$referred_by}</td></tr></table><br>
<table border="0" width="100%" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
  <tr >
  <th class="thead">
       {$lang->showreferral_usercp_table_heading}
       <div style="float:right;">
            <a href="{$mybb->settings[\'bburl\']}/referrals.php" title="{$lang->showreferral_usecp_table_viewmore}">{$lang->showreferral_usercp_table_viewmore}</a>
       </div>
  </th>
  </tr>
  <tr>
      <td class="trow1">{$showref_recent}';

$profile = 	'{$referrals}{$referredby}';

	$ins[] = array ("title" => 'show_referral' , "template" => $db->escape_string($temp) , "sid" => "-1" , "version" => $mybb->version + 1, "dateline" => time());
	$ins[] = array ("title" => 'show_referral_recent' , "template" => $db->escape_string($recent) , "sid" => "-1" , "version" => $mybb->version + 1, "dateline" => time());
	$ins[] = array ("title" => 'show_referral_profile' , "template" => $db->escape_string($profile) , "sid" => "-1" , "version" => $mybb->version + 1, "dateline" => time());

	$db->insert_query_multiple("templates" ,$ins);

	require_once MYBB_ROOT.'/inc/adminfunctions_templates.php';

	find_replace_templatesets('headerinclude', "#".preg_quote('{$stylesheets}')."#", 	'{$stylesheets}
<script src="{$mybb->settings[\'bburl\']}/jscripts/showreferral.js"></script>'); 
	find_replace_templatesets("usercp", '#'.preg_quote('{$referral_info}').'#', 	'{$referral_info}{$showreferral_recent}');
	find_replace_templatesets("member_profile", '#'.preg_quote('{$referrals}').'#',  '{$referrals}{$showreferral_profile}');
	

}
function  remove_template_showreferral()
{
	global $db;
	$db->delete_query("templates" , "`title` like 'show_referral%' AND sid ='-1'" );
	require_once MYBB_ROOT.'/inc/adminfunctions_templates.php';
	find_replace_templatesets('headerinclude', "#".preg_quote('<script src="{$mybb->settings[\'bburl\']}/jscripts/showreferral.js"></script>')."#", ''); 
	find_replace_templatesets('headerinclude', "#".preg_quote('

')."#", '
'); 
	find_replace_templatesets("usercp", '#'.preg_quote('{$showreferral_recent}').'#', '');
	find_replace_templatesets("member_profile", '#'.preg_quote('{$showreferral_profile}').'#', '');
	

}
function message($type)
{
	global $mybb;

	if($type == "admin_sub")
	{
		if($mybb->settings['showreferral_sendpm_admin_sub'] == "")
		{
			$message = "Just Registered \$referral Invited by \$referrer";
		}
		else
		{
			$message = $mybb->settings['showreferral_sendpm_admin_sub'];
		}
	}
	if($type == "admin_msg")
	{
		if($mybb->settings['showreferral_sendpm_admin_msg'] == "")
		{
			$message = "Dear \$board_name Staff,".PHP_EOL.
				    "\$referral_link just joined \$board_name, \$referral was invited by \$referrer_link".PHP_EOL.PHP_EOL.
		     		      "\$board_name Team".PHP_EOL.
		                                            "\$board_url";
		}
		else
		{
			$message = $mybb->settings['showreferral_sendpm_admin_msg'];
		}
	}
	if($type == "referrer_sub")
	{

		if($mybb->settings['showreferral_sendpm_subject1'] == "")
		{
			$message = "You've just invited \$referral";
		}
		else
		{
			$message = $mybb->settings['showreferral_sendpm_subject1'];
		}
	}
	if($type == "referrer_msg")
	{
		if($mybb->settings['showreferral_sendpm_message1'] == "")
		{
			$message = "Dear \$referrer_link,".PHP_EOL.
		   		     "You've just invited \$referral_link in our forum. You can see your referred users by clicking here [URL=\$board_url/referrals.php]\$board_url/referrals.php[/URL]".PHP_EOL.
 		  		     "Thanks!".PHP_EOL.PHP_EOL.
		     		     "\$board_name Team".PHP_EOL.
		     		     "\$board_url";
		}
		else
		{
			$message = $mybb->settings['showreferral_sendpm_message1'];
		}
	}
	if($type == "referral_sub")
	{
		if($mybb->settings['showreferral_sendpm_subject2'] == "")
		{
			$message = "Welcome to \$board_name";
		}
		else
		{
			$message = $mybb->settings['showreferral_sendpm_subject2'];
		}
	}
	if($type == "referral_msg")
	{
		if($mybb->settings['showreferral_sendpm_message2'] == "")
		{
			$message = "Dear \$referral_link,".PHP_EOL.
		     		      "Welcome to \$board_name. You were invited by \$referrer_link. Glad to see you are a member of our community.".PHP_EOL.
		     		      "Thanks!".PHP_EOL.PHP_EOL.
		     		      "\$board_name Team".PHP_EOL.
		                                            "\$board_url";
		} 
		else
		{
			$message = $mybb->settings['showreferral_sendpm_message2'];
		}
	}

	return $message;
}

function showreferral_load()
{ 
	global $mybb, $templates,  $db, $mybb, $header, $headerinclude, $footer, $theme, $lang, $plugins;
	$lang->load('showreferral');
	$copyright = "";

	if($mybb->settings['showreferral_banners'] == 1)
		$banner_title = '<div style="float:right;"><a href="./referrals.php?action=banners" title="'.$lang->showreferral_title_sub_title.'">'.$lang->showreferral_title_sub_title.'</a></div>';
	else
		$banner_title = '';
	$showreferral_title = $lang->showreferral_title;
	$showreferral_sub_title = "<a href='./referrals.php'>".$lang->showreferral_sub_title.$banner_title."</a>";
	$group = group_perm();
	if($mybb->settings['usereferrals'] == 1 && $mybb->settings['showreferral_disabled'] == 0 && $group == "yes")
	{ 
		$action = $mybb->input['action'];
		if(!$action)
		{

			//Referred by
			$referred_by = referred_by("page");

			//Show Referrals
			$query = $db->simple_select("users", "count(uid) as total", "`referrer` = '".$mybb->user['uid']."'");
			
			$count = $db->fetch_field($query, "total");
			$total_rows = $count;
			if($mybb->settings['showreferral_perpage'] == "")
				$per_page = 10;
			else
				$per_page = intval($mybb->settings['showreferral_perpage']);

			if($mybb->input['page'] && $mybb->input['page'] > 1)
			{
				$mybb->input['page'] = intval($mybb->input['page']);
				$start = ($mybb->input['page']*$per_page)-$per_page;
			}
			else
			{
				$mybb->input['page'] = 1;
				$start = 0;
			}
			$referred_users .= multipage( $total_rows, $per_page, $mybb->input['page'], "referrals.php?page={page}");
			$query =  $db->simple_select("users", "*", "`referrer` = '".$mybb->user['uid']."'", array('order_by' => 'regdate', 'order_dir' => 'DESC', 'limit' => $start.','.$per_page));

			$referred_users .= '
			<table border="0" width="100%" style="text-align:center;border:1px solid black;">
					<tr class="tcat">
					<th>'.$lang->showreferral_table_sno.'</th>
					<th>'.$lang->showreferral_table_name.'</th>
					<th>'.$lang->showreferral_table_regdate.'</th>
					<th>'.$lang->showreferral_table_onlinetime.'</th>
					<th>'.$lang->showreferral_table_lastactive.'</th>
					<th>'.$lang->showreferral_table_totalpost.'</th>
				</tr>
				<tr>
					';
			if($count > 0)
			{
				$i = 1;		
				while($referral = $db->fetch_array($query))
				{
					$referred_users .= '<tr>';
					$referred_users .= '<td class="trow1">'.$i.'</td>';
					$referred_users .= '<td class="trow1">'.build_profile_link($referral['username'], $referral['uid']).'</td>';
					$referred_users .= '<td class="trow1">'.my_date($mybb->settings['dateformat'], $referral['regdate']).'</td>';
					$referred_users .= '<td class="trow1">'.nice_time($referral['timeonline']).'</td>';
					$referred_users .= '<td class="trow1">'.my_date($mybb->settings['dateformat'], $referral['lastactive'])." ".my_date($mybb->settings['timeformat'], $referral['lastactive']).'</td>';
					$referred_users .= '<td class="trow1">'.$referral['postnum'].'</td>';
					$referred_users .= '</tr>';
					$i++;
				}
			}
			else
			{
				$referred_users .= '<tr><td colspan="7">'.$lang->showreferral_table_noreferrals.'</td></tr>';
			}
			if($mybb->settings['showreferral_totalreferral'] == 1)
				$count = $lang->showreferral_totalreferrals.$count;
			else
				$count = "";

			$referred_users .= "<tr>
					<td colspan='4' align='left'>".$referred_by."</td>
					<td colspan='3' align='right'>".$count."</table>";
			$showreferral = $referred_users;
		}
		
		if($action == "banners")
		{
			$showreferral_title = $lang->showreferral_title_banners;
			$showreferral_sub_title = "<a href='./referrals.php'>".$lang->showreferral_title."</a> &raquo; ".$lang->showreferral_sub_title_banners;
			if($mybb->settings['showreferral_banners'] == 1)
			{
				if($mybb->settings['showreferral_bannerdir'] == "")
					$dir = "images/banners";
				else
					$dir = $mybb->settings['showreferral_bannerdir'];
				$thumbs = glob($dir."/*.{jpg,png,gif}", GLOB_BRACE);
				$banners .= '<table border="0" align="center" width="100%">
						<tr>
							<td class="trow1" align="center"><b>Direct Link:<br></b><div><a href="'.$mybb->settings['bburl'].'/'.'member.php?action=register&referrer='.$mybb->user['uid'].'" target="_blank" title="'.$mybb->settings['bbname'].'">'.$mybb->settings['bburl'].'/'.'member.php?action=register&referrer='.$mybb->user['uid'].'</a></div>';
						$banners .= '
							<div><b>HTML Code:</b></div>
							<div><textarea style="width:80%;height:50px;"  onclick="this.select();" onblur="this.select();" onfocus="this.select();" readonly="true" ><a href="'.$mybb->settings['bburl'].'/'.'member.php?action=register&referrer='.$mybb->user['uid'].'" target="_blank" title="'.$mybb->settings['bbname'].'">'.$mybb->settings['bburl'].'/'.'member.php?action=register&referrer='.$mybb->user['uid'].'</a></textarea></div>
							<div><b>BB Code:</b></div>
							<div><textarea style="width:80%;height:50px;"  onclick="this.select();" onblur="this.select();" onfocus="this.select();" readonly="true" >[url='.$mybb->settings['bburl'].'/'.'member.php?action=register&referrer='.$mybb->user['uid'].']'.$mybb->settings['bburl'].'/'.'member.php?action=register&referrer='.$mybb->user['uid'].'[/URL]</textarea></div>
						</tr>';
			
				if(count($thumbs))
				{
					natcasesort($thumbs);
					foreach($thumbs as $thumb)
					{
		
						$banners .= '<tr>
								<td class="trow1" align="center" ><div class="item">
		        							<img src="'.$thumb.'" alt="'.$thumb.'" />
									<div><b>HTML Code:</b></div>
									<div><textarea style="width:80%;height:50px;" onclick="this.select();" onblur="this.select();" onfocus="this.select();" readonly="true" ><a href="'.$mybb->settings['bburl'].'/'.'member.php?action=register&referrer='.$mybb->user['uid'].'" target="_blank" title="'.$mybb->settings['bbname'].'"><img src="'.$mybb->settings['bburl'].'/'.$thumb.'" alt="'.$mybb->settings['bbname'].'" title="'.$mybb->settings['bbname'].'"/></a></textarea></div>
									<div></div>
									<div><b>BB Code:</b></div>
									<div><textarea style="width:80%;height:50px;" onclick="this.select();" onblur="this.select();" onfocus="this.select();" readonly="true" >[URL='.$mybb->settings['bburl'].'/'.'member.php?action=register&referrer='.$mybb->user['uid'].'][IMG]'.$mybb->settings['bburl'].'/'.$thumb.'[/IMG][/URL]</textarea></div>
								</td>
							</tr>';
					}
			

		
				}
				else
				{
  					$banners .= '<tr><td class="trow1" align="center">'.$lang->showreferral_banners_notfound.'</td></tr>';
				}	 
				$banners .= "</table>";
				$showreferral = $banners;
			}
			else
			{
				$showreferral = "<div class='error'><b>".$lang->showreferral_error."</b><ul>";
				$showreferral .= "<li>".$lang->showreferral_error_disable_banners;
				$showreferral .= "</div>";
			}
		}
			
	}
	else
	{
		$showreferral = "<div class='error'><b>".$lang->showreferral_error."</b><ul>";
		if($mybb->settings['usereferrals'] == 0)
			$showreferral .= "<li>".$lang->showreferral_error_disable_referral_system;
		if($mybb->settings['showreferral_disabled'] == 1)
			$showreferral .= "<li>".$lang->showreferral_error_disable_showreferral;
		if(stristr($mybb->settings['showreferral_permission'], $mybb->user['usergroup']) == FALSE)
			$showreferral .= "<li>".$lang->showreferral_error_nopermission;
		$showreferral .= "</li></ul></div>";
		
		
	}
	$showreferral .= $copyright;
	eval("\$page = \"".$templates->get("show_referral")."\";");
	output_page($page);
}
function group_perm()
{
	global $mybb;
	if(stristr($mybb->settings['showreferral_permission'], ",") == true)
	{
		$exp_val = explode(",",$mybb->settings['showreferral_permission']);
		$group = array_search($mybb->user['usergroup'], $exp_val);
		if($group == "")
		{
			$group = "no";
		}
		else
		{
			$group = "yes";
		}
	}
	elseif($mybb->user['usergroup'] == intval($mybb->settings['showreferral_permission']))
	{
		$group = "yes";
	}
	elseif($mybb->settings['showreferral_permission'] == "")
	{
		$group = "yes";
	}
	return $group;
}
function showreferral_usercp_menu()
{
	global $db, $mybb, $usercpmenu;
	$group = group_perm();
	if($group == "yes")
	{
		$referral= '<tr><td class="trow1 smalltext"><a href="'.$mybb->settings['bburl'].'/referrals.php" class="usercp_nav_item" style="background: url('.$mybb->settings['bburl'].'/images/referrals.gif) no-repeat scroll left center transparent;">Referrals</a></td></tr>';
	}
	$usercpmenu .= $referral;
	

}
function showreferral_recent()
{
	global $mybb, $templates,  $db, $mybb, $header, $headerinclude, $footer, $theme, $lang, $plugins,$showreferral_recent;
	$group = group_perm();
	if($mybb->settings['showreferral_usercp_enable'] == 1 && $group == "yes")
	{
		
		$lang->load('showreferral');
		$referred_users = '<table border="0" width="100%" style="text-align:center;">
				<tr class="tcat">
					<th>'.$lang->showreferral_table_sno.'</th>
					<th>'.$lang->showreferral_table_name.'</th>
					<th>'.$lang->showreferral_table_regdate.'</th>
					<th>'.$lang->showreferral_table_onlinetime.'</th>
					<th>'.$lang->showreferral_table_lastactive.'</th>
					<th>'.$lang->showreferral_table_totalpost.'</th>
				</tr>
				<tr>';
		if($mybb->settings['showreferral_usercp_limit'] == "")
			$limit = 5;
		else
			$limit = $mybb->settings['showreferral_usercp_limit'];
		
		$query = $db->simple_select("users", "*", "`referrer` = '".$mybb->user['uid']."'", array('order_by' => 'regdate', 'order_dir' => 'DESC', 'limit' => '0,'.$limit));
		$count = $db->num_rows($query);
		if($count > 0)
		{
			$i = 1;		
			while($referral = $db->fetch_array($query))
			{
				$referred_users .= '<tr>';
				$referred_users .= '<td class="trow1">'.$i.'</td>';
				$referred_users .= '<td class="trow1">'.build_profile_link($referral['username'], $referral['uid']).'</td>';
				$referred_users .= '<td class="trow1">'.my_date($mybb->settings['dateformat'], $referral['regdate']).'</td>';
				$referred_users .= '<td class="trow1">'.nice_time($referral['timeonline']).'</td>';
				$referred_users .= '<td class="trow1">'.my_date($mybb->settings['dateformat'], $referral['lastactive'])." ".my_date($mybb->settings['timeformat'], $referral['lastactive']).'</td>';
				$referred_users .= '<td class="trow1">'.$referral['postnum'].'</td>';
				$referred_users .= '</tr>';
				$i++;
			}
			
		}
		else
		{
			$referred_users .= '<tr><td colspan="7">'.$lang->showreferral_table_noreferrals.'</td></tr>';
		}
		$referred_users .= "</table>";
		$showref_recent = $referred_users;
		
		
		eval("\$showreferral_recent = \"".$templates->get("show_referral_recent")."\";");

	}
}

function insertpm($pm, $fromid = 0)
{
	global $lang, $mybb, $db;
		
	if (!is_array($pm))
		return false;
		
	if (!$pm['subject'] ||!$pm['message'] || !$pm['touid'])
		return false;
	
	$lang->load('messages');
	
	require_once MYBB_ROOT."inc/datahandlers/pm.php";
	
	$pmhandler = new PMDataHandler();
	
	$subject = $pm['subject'];
	$message = $pm['message'];
	$toid = $pm['touid'];
	
	if (is_array($toid))
		$recipients_to = $toid;
	else
		$recipients_to = array($toid);
		
	$recipients_bcc = array();
	
	if (intval($fromid) == 0)
		$fromid = intval($mybb->user['uid']);
	elseif (intval($fromid) < 0)
		$fromid = 0;
	
	$pm = array(
		"subject" => $subject,
		"message" => $message,
		"icon" => -1,
		"fromid" => $fromid,
		"toid" => $recipients_to,
		"bccid" => $recipients_bcc,
		"do" => '',
		"pmid" => ''
	);
	
	$pm['options'] = array(
		"signature" => 0,
		"disablesmilies" => 0,
		"savecopy" => 0,
		"readreceipt" => 0
	);
	
	$pm['saveasdraft'] = 0;
	$pmhandler->admin_override = 1;
	$pmhandler->set_data($pm);
	if($pmhandler->validate_pm())
	{
		$pmhandler->insert_pm();
	}
	else
	{
		return false;
	}
	
	return true;
}

function showreferral_sendpm()
{	
	global $lang, $mybb, $db,$user,$user_info,$plugins;
	if($mybb->settings['showreferral_sendpm_enable'] == 1)
	{
		if($user['referrer'] != "" && isset($user_info))
		{		
			//Fetch Referrer Information
			$query = $db->simple_select("users", "uid,username" , "`username`='".$user['referrer']."'");
			$to1 = $db->fetch_array($query);
			//Set newly registered user information (Referral)
			$to2 = $user_info;
			//Private Message Can be customized by using Custm Variables:
			/* Subject1 = Referrer
			 * Subject2 = Referral
			 * Apply Same in Messages
			 * Referrer Username = $referrer 
			 * Referrer Username with link = $referrer_link
			 * Referral Username = $referral
			 * Referral Username with link = $referral_link
			 * Board Name = $board_name
			 * Board Link = $board_url
			*/
			$board_name = $mybb->settings['bbname'];
			$board_url = $mybb->settings['bburl'];
			$referrer = $to1['username'];
			$referrer_link = "[URL=".$mybb->settings['bburl']."/member.php?action=profile&uid=".$to1['uid']."]".$to1['username']."[/URL]";
			$referral = $to2['username'];
			$referral_link = "[URL=".$mybb->settings['bburl']."/member.php?action=profile&uid=".$to2['uid']."]".$to2['username']."[/URL]";
			if($mybb->settings['showreferral_sendpm1_enable'] == 1)
			{
				$subject1 = message("referrer_sub");					
				$message1 = message("referrer_msg");

				eval("\$subject1 = \"$subject1\";");
				eval("\$message1 = \"$message1\";");
				//Send PM to referrer
				$pm1['touid']		=	$to1['uid'];
				$pm1['subject'] 		=	$subject1;
				$pm1['message']		=	$message1;
				insertpm($pm1, 1);
				unset($pm1);

			}
			if($mybb->settings['showreferral_sendpm2_enable'] == 1)
			{
					
				$subject2 = message("referral_sub");
				$message2 = message("referral_msg");
				eval("\$message2 = \"$message2\";");
				eval("\$subject2 = \"$subject2\";");
				//Send PM to Referral
				$pm2['touid']		=	$to2['uid'];
				$pm2['subject'] 		=	$subject2;
				$pm2['message']		=	$message2;
				insertpm($pm2, 1);
				unset($pm2);
			}
			if($mybb->settings['showreferral_sendpm_admin'] == 1)
			{
				$subject = message("admin_sub");	
				$message = message("admin_msg");
				eval("\$subject = \"$subject\";");
				eval("\$message = \"$message\";");

				if(stristr($mybb->settings['showreferral_sendpm_admin_id'], ",") == True)
				{
					$id = explode(",",$mybb->settings['showreferral_sendpm_admin_id']);
				}
				elseif($mybb->settings['showreferral_sendpm_admin_id'] == "")
				{
					$id = 1;
				}
				else
				{
					$id = $mybb->settings['showreferral_sendpm_admin_id'];
				}
				$pm['subject'] = $subject;
				$pm['message'] = $message;
				if(is_array($id))
				{
					foreach($id as $to)
					{
						$pm['touid'] = $to;
						insertpm($pm, "-1");
					}
				}
				else
				{
					$pm['touid'] = $id;
					insertpm($pm, "-1");
				}

			}
				
				
		}
		
	}
}
function showreferral_profile()
{
	global $db,$mybb,$showreferral_profile,$referredby,$referrals,$templates,$lang,$memprofile;
	$lang->load('showreferral');
	if($mybb->settings['showreferral_profile_enable'] == 1)
	{
		$ref = referrals();
		if($ref)
		{
		 	$referrals = '<tr><td class="trow1"><b>'.$lang->showreferral_profile_referrals.'</b></td>'.PHP_EOL.
				'<td class="trow1"><div align="right"><input style="border-radius:5px;" type="button" value="Show" id="control" onclick="showhide(\'referrals\');"></div>'.PHP_EOL.		//<div id="show" style="cursor:pointer;">'.$lang->showreferral_profile_showreferral.'</div>'.PHP_EOL.
				//'<div id="hide" style="display:none;cursor:pointer;text-align:right;" onclick="showreferral('referrals');">'.$lang->showreferral_profile_hidereferral.'</div>'.PHP_EOL.
				'<div id="referrals" style="display:none;float:left;"  >'.referrals().'</div>'.PHP_EOL.
				'</td></tr>';
		} 
	}
	else
	{ 
		$referrals = "";
	} 
	$referredby = referred_by("profile");
	eval("\$showreferral_profile = \"".$templates->get("show_referral_profile")."\";");
}
function referred_by($type)
{
	global $db,$mybb,$lang;
	if($mybb->settings['showreferral_showreferrer'] == 1)
	{

		if($type == "page")
		{
			$query = $db->simple_select("users", "referrer", "`uid`='".$mybb->user['uid']."' and `referrer` != '0' and `referrer` != '' ");
			$referred = $db->fetch_field($query, "referrer");
			if($referred > 0)
			{
				$query		= $db->simple_select("users", "username,uid", " `uid`='".$referred."'");
				$get_user		= $db->fetch_array($query);
				$referredby 	=  build_profile_link($get_user['username'], $get_user['uid']);
			}
			else
			{
				$referredby = "";
			}
			if($referredby != "")
			{
				return $lang->showreferral_referredby.$referredby;
			}
		}
		if($type == "profile")
		{
			$query = $db->simple_select("users", "referrer", "`uid`='".$mybb->input['uid']."' and `referrer` != '0' and `referrer` != '' ");
			$count = $db->fetch_field($query, "referrer");
			if($count > 0)
			{
				$query		= $db->simple_select("users", "username,uid", " `uid`='".$count."'");
				$get_user		= $db->fetch_array($query);
				$referredby 	=  build_profile_link($get_user['username'], $get_user['uid']);
				if($referredby != "")
				{
					return '<tr><td class="trow1"><b>'.$lang->showreferral_profile_referredby.'</b></td>
						<td class="trow1">'.$referredby.'</td></tr>';
				}
			}
		}
	}
	else
	{
		$referredby = "";
	}
	
}
function referrals()
{
	global $db,$mybb,$lang;
	if($mybb->settings['showreferral_totalreferral'] == 1)
	{
    $uid = $mybb->input['uid'];
    
    //If no uid provided, use current users uid
    if (empty($mybb->input['uid'])) {
      $uid = $mybb->user['uid'];
    }
    
		$query = $db->simple_select("users", "count(uid) as total", "`referrer` = '".$uid."'");
		$count = $db->fetch_field($query, "total");
		if($count > 0)
		{
			$query = $db->simple_select("users", "uid,username", "`referrer` = '".$uid."'", array('order_by' => 'uid', 'order_dir' => 'DESC'));
			while($ref = $db->fetch_array($query))
			{
				$refer[] = build_profile_link
        ($ref['username'], $ref['uid']);
			}
			$referrals = implode(", ",$refer);
		}
	}
	return $referrals;
}
?>