<?php

class GuestBook {
	//Total number of entries in guestbook this number is updated everytime new entry is added or deleted

		var $total=0;

		var $id=0;
		var $author='';
		var $email='';
		var $url='http://';
		var $dob;
		var $location;
		var $referer='';
		var $date;
		var $comments='';
		var $ip='0.0.0.0';
		var $hidden='0';
			/* this variable is used to hide, unhide a specific entry.
				by default the entry is viewable to everybody */

        function GuestBook() {
			// constructor can be used later
				$this->update_total();

		}

		function update_total() {
			$sql="select * from ".customer_comments." WHERE hidden='0'";
			$this->total=$GLOBALS["db"]->num_rows($GLOBALS["db"]->query($sql));

		}


	    function get($field) {
			return($this->$field);
        }

        function set($field,$value) {
			$this->$field=$value;
        }


        function retrieve_entry($id) {
			$sql="select * from ".customer_comments." where id=".$id;
			$result=$GLOBALS["db"]->query($sql);
            if($result && ($GLOBALS["db"]->num_rows($result)==1 )) {
				$data=$GLOBALS["db"]->fetch_object($result);
				$this->id=$data->id;
 	            $this->dob=$data->dob;
        		$this->location=$data->location;
  		        $this->author=$data->author;
				$this->email=$data->email;
				$this->url=$data->url;
				$this->comments=$data->comments;
				$this->date_submitted=$data->date_submitted;
				$this->referer=$data->referer;
				$this->ip=$data->ip;
				$this->hidden=$data->hidden;
				return true;

			} else {
					// no entry with such id exist
					return false;

     		}
			return false;

		}


		function display_n_entries($start=-1,$end=-1) {
        /* This function can be used to display all the entries in the guestbook
           or can be called with proper $start and $end values to return only
           specific no. of entries.
           when called with $start =-1 & $end =-1 will display all the entries
           on a single page
           */



		    // print "inside display n entries";
		    if($start==-1 && $end==-1) {


			$sql= "Select * from ".customer_comments." where hidden='0' ORDER BY date_submitted desc";

		    }
		    else {
		       $sql= "Select * from ".customer_comments." where hidden='0' ORDER BY date_submitted desc limit $start, $end";

		    }
		   // print "<br>$sql<br>";
			$result=$GLOBALS['db']->query($sql);
		    print("<div class='display_entries'>\n");

		          $class="even";
			while($data=$GLOBALS['db']->fetch_object($result)) {
			        print "<div class='".$class."entry'>\n";
						print"<div class='".$class."entry_top_row'>";
						      print("<div class='topquestion'>&nbsp;&nbsp;&nbsp;Name</div>\n");
			                  print("<div class='topresponse'>&nbsp;".$data->author."</div>\n");
						print"</div>\n";

						print("<div class='".$class."'>\n\n");
							  print("<div class='".$class."question'>&nbsp;&nbsp;&nbsp;ID</div>\n");
						      print("<div class='".$class."response'>&nbsp;".$data->id."</div>\n");
			            print("</div>\n");

					 if(trim($data->dob)!="") {
						print("<div class='".$class."'>\n\n");
							  print("<div class='".$class."question'>&nbsp;&nbsp;&nbsp;Birthday</div>\n");
						      print("<div class='".$class."response'>&nbsp;".$data->dob."</div>\n");
			            print("</div>\n");
					 }

					if(trim($data->location)!="") {
						print("<div class='".$class."'>\n\n");
							  print("<div class='".$class."question'>&nbsp;&nbsp;&nbsp;Location</div>\n");
						      print("<div class='".$class."response'>&nbsp;".$data->location."</div>\n");
			            print("</div>\n");
					}
					if(trim($data->email)!="") {
						print("<div class='".$class."'>\n\n");
							  print("<div class='".$class."question'>&nbsp;&nbsp;&nbsp;Email</div>\n");
						      print("<div class='".$class."response'>&nbsp;<a href='mailto:".$data->email."' class='link'>".$data->email."</a></div>\n");
			            print("</div>\n");
					}

		        	 if(trim($data->url)!=""  && trim($data->url)!="http://") {
						 print("<div class='".$class."'>\n\n");
							  print("<div class='".$class."question'>&nbsp;&nbsp;&nbsp;Website</div>\n");
						      print("<div class='".$class."response'>&nbsp;<a href='".$data->url."' class='link' target='_blank'>".$data->url."</a></div>\n");
			            print("</div>\n");
			         }
                    if(trim($data->referer)!="") {
						print("<div class='".$class."'>\n\n");
							  print("<div class='".$class."question'>&nbsp;&nbsp;&nbsp;Knew about site from </div>\n");
						      print("<div class='".$class."response'>&nbsp;".$data->referer."</div>\n");
			            print("</div>\n");
			        }
			        if(trim($data->comments)!="") {
						print("<div class='".$class."'>\n\n");
							  print("<div class='".$class."question'>&nbsp;&nbsp;&nbsp;Comments</div>\n");
						      print("<div class='".$class."response'>&nbsp;".$data->comments."</div>\n");
			            print("</div>\n");
			        }

						print("<div class='".$class."'>\n\n");
							  print("<div class='".$class."question'>&nbsp;&nbsp;&nbsp;Signed On</div>\n");
						      print("<div class='".$class."response'>&nbsp;".$data->date_submitted."</div>\n");
			            print("</div>\n");
			            if($class=="even")
						     $class="odd";
	                     else if($class=="odd")
				           $class="even";
				print"&nbsp;</div>\n";
			}
            print"     </div>\n";



		}

		function delete_entry($id) {
		// function to delete an entry

				$sql="delete from ".customer_comments." where id=".$id;
				if($GLOBALS["db"]->query($sql))
			   {
					$this->update_total();
					return true;

			   } else {
				   $this->update_total();
				   return false;
               }
         }

		function add_entry() {

		    // update existing user's data
		    // do not use update() had password been changed,
			// instead, use password()

			//INSERT INTO `entry_detail` ( `entry_id` , `entry_author` , `entry_dob` , `entry_email` , `entry_url` //, `entry_location` , `entry_date` , `entry_comment` , `entry_referer` )
			$sql = "INSERT INTO ".ENTRY_TABLE." "
					." (id, author,dob, email, url, location, date, comments, referer,ip,hidden) "
					." VALUES ($this->id,"
                                . "'$this->author',"
								. "'$this->dob',"
								. "'$this->email',"
							   	. "'$this->url',"
								. "'$this->location',"
					     		. "NOW(),"
								. "'$this->comments',"
								. "'$this->referer',"
								. "'$this->ip',"
								. "'$this->hidden'"
							." ) ";
				//print "\n$sql\n";

				$GLOBALS["db"]->query($sql);
				$this->update_total();
		 }


		function hide_entry($id) {
				$ret=false;
				$sql = "update ".ENTRY_TABLE."	set hidden='1' where id=".$id;
				if($GLOBALS['db']->query($sql)) {
					$ret=true;
					$this->update_total();
				}
				return $ret;


		}

		function unhide_entry($id) {
				$ret=false;
				$sql = "update ".customer_comments."	set hidden='0' where id=".$id;
				if($GLOBALS['db']->query($sql)) {
					$ret=true;
					$this->update_total();
				}
				return $ret;


		}



		function modify_entry() {

			$sql = "UPDATE ".customer_comments." "
				 . " SET"
			  	 . " dob = '$this->dob',"
                 . " location = '$this->location',"
                 . " author = '$this->author',"
				 . " url = '$this->url',"
			     . " comments = '$this->comments',"
				 . " email = '$this->email',"
			     . " referer = '$this->referer'"
				 . " where id=$this->id";
			//print"<BR>$sql<br>";
				$GLOBALS["db"]->query($sql);
		}


		function next_id(){
			$sql="Select max(id) from ".customer_comments;
			$data=mysql_fetch_array($GLOBALS["db"]->query($sql));
			$id=$data[0];
			//print "<br>id = ".$id."<br>";
			return ++$id;
		}



		function display_add_form($error="") {
			if(trim($error)!="") {
				print"<div class='error'>Please correct following errors:<br>$error</div>\n";

			}
			//print"this is the add form total current entries = ".$GLOBALS["gb"]->total;

			print " <div class='add_entry'>
					<form action='{$_SERVER['PHP_SELF']}' method=\"post\">

					    <div class='add_row'>&nbsp;
				           <div class='label'> Name * </div>
				           <div class='value'>
				           <input class='text' name='entry_name' type='text' size='35' value='{$this->author}'>
				           </div>
				        </div>

					    <div class='add_row'>&nbsp;
				            <div class='label'> Email * </div>
				           <div class='value'>
			                <input class='text' name='entry_email' type='text' size='35' value='{$this->email}'>
				            </div>
				        </div>

					    <div class='add_row'>&nbsp;
						     <div class='label'> Location </div>
						     <div class='value'>
					         <input class='text' name='entry_location' type='text' size='35' value='{$this->location}'>
						     </div>
					    </div>

						<div class='add_row'>&nbsp;
						     <div class='label'> Birthday </div>
						     <div class='value'>
					         <input class='text' name='entry_dob' type='text' size='35' value='{$this->dob}'>
						     </div>
					    </div>

					    <div class='add_row'>&nbsp;
					         <div class='label'>Knew abt this site from? </div>
					         <div class='value'>
					         <input class='text' name='entry_referer' type='text' size='35' value='{$this->referer}'>
						     </div>
						</div>

						<div class='add_row'>&nbsp;
					        <div class='label'> Website(if any) </div>
						    <div class='value'>
				            <input class='text' name='entry_website' type='text' size='35'
value='{$this->url}'>
					        </div>
					    </div>

						<div class='add_row'>&nbsp;
					        <div class='label'> Message/Comments *</div>
					        <div class='value'>
					        <textarea class='text' name='entry_comments' cols='30' rows='7' class='tbl4' value='{$this->comments}'></textarea>
					        </div>
					    </div>

						<div class='add_row'>&nbsp;
					        <div  class='label'>
					        <input class='text' name='submit' type='submit' value='Submit' class='btn'>

					        </div>
					    </div>

					<input type='hidden' name='mode' value='2'>
				  </form>
				&nbsp;</div>			";

		}

}
?>