<?php
//home page
	require_once("conf.php");
	require_once("gb.php");
	require_once("db.class.php");
        $ss="design";

function display_navigation($start,$num_pages) {
	
	print "<div class='page_nav'>\n";
	print "<div class='total'>Total Entries: ".$GLOBALS['gb']->total."</div>";
	print "		<div class='page'> Pages:&nbsp;&nbsp;&nbsp;</div>\n";
	print"		<div class='page_no'>";
				if($start > 1  && $start <=$num_pages)
	print"			<a href='".$_SERVER['PHP_SELF']."?start=".($start-1)."' class='pages'>Prev&lt;&lt;</a>&nbsp;&nbsp;\n";
				for($j=1;$j<=$num_pages;$j++) {
					if($start==$j)
	print"			<a href='".$_SERVER['PHP_SELF']."?start=".$j."' class='current'>".($j)."</a>&nbsp;&nbsp;\n";
					else
	print"			<a href='".$_SERVER['PHP_SELF']."?start=".$j."' class='pages'>".($j)."</a>&nbsp;&nbsp;\n";
				 }
				 if(!isset($start) || $start <=0)
					$l=1;
				else
					$l=$start+1;
				if($l<=$num_pages)
	print"			<a href='".$_SERVER['PHP_SELF']."?start=".$l."' class='pages'>&gt;&gt;Next</a>&nbsp;\n";
	print"			</div>\n";
	print"</div>\n";
	}



print <<<HTML_HEADER
<!DOCTYPE HTML>
<html>
<head>

<link rel="stylesheet" href="{$ss}.css" type="text/css">


<title>
LogBook

</head>
<body>

<div class='top_nav'>
    <div class='gb_title'>
        LogBook
    </div>
    <div class='top_nav_link'>
        <div class='title_link'><a href='{$_SERVER['PHP_SELF']}' class='title_link'>View LogBook</a>&nbsp;&nbsp;|
		&nbsp;&nbsp;<a href='{$_SERVER['PHP_SELF']}?action=add' class='title_link'>Sign LogBook</a>
		</div>

    </div>
</div>

HTML_HEADER;



	$db=new db($db_database,$db_host,$db_user,$db_passwd);
	$entry=new GuestBook();


	if(!isset($_GET['start']))
    {
        $start=1;
    }
    else
    {
        $start=$_GET['start'];
    }
	$action=$_GET['action'];
	$submit=$_POST['submit'];
	if(($submit=="submit") || ($submit=="Submit"))  {
		$error="";
		$ok=true;
		if(trim($_POST['entry_name'])==""){
			$ok=false;
			$error .="Enter your name <br>";
		}
		if(trim($_POST['entry_email'])==""){
			$ok=false;
			$error .="Enter your email <br>";
		}
		if(trim($_POST['entry_comments'])==""){
			$ok=false;
			$error .="Enter any comments <br>";
		}
		$entry->entry_dob=$_POST['entry_dob'];
			$entry->location=$_POST['entry_location'];
			$entry->author=$_POST['entry_name'];
			$entry->email=$_POST['entry_email'];
			$entry->url=$_POST['entry_website'];
			$entry->comments=$_POST['entry_comments'];
			$entry->referer=$_POST['entry_referer'];
			$entry->ip=$_SERVER['REMOTE_ADDR'];
		if($ok) {
			$entry->id=$gb->next_id();
			$entry->add_entry();
			$entry->update_total();
			$action='list';
		 } else {
			$action='add';
		 }
	}
		switch($action) {

			case "add":

					  $entry->display_add_form($error);
					  break;

			case "s":
					$id=$_GET['id'];
					if($entry->retrieve_entry($id)) {
						$entry->display_entry();

					} else {
						print"Entry does not exist";

					}
					break;
			case "d":
				    $id=$_GET['id'];
					if($entry->delete_entry($id))
						print"Entry Deleted!";
					else
						print"ERROR! Deletion Failed";
			case "list":
			default:
				   if($entry->total > 0) {
						if(!$display_single_page) {
						
						 if ($entry->total > $display) {
							$num_pages = ceil ($entry->total/$display);
						 } else {
							$num_pages = 1;
						 }

						 display_navigation($start,$num_pages);

					     if(!isset($start) || $start <=0)
					         $start=1;
							 $s=($start-1)*$display;
							 if($s>$entry->total)
							     $s=0;
						  	$entry->display_n_entries($s,$display);
							display_navigation($start,$num_pages);
						 } else {

							$entry->display_n_entries(-1,-1);

					     }
			       } else {
						print"<div class='message'>List is empty.</div>";
                   }
					break;



		}


print <<<FOOTER

</body>
</html>
FOOTER;


?>
