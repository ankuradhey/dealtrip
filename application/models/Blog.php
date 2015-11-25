<?php
__autoloadDB('Db');
class Blog extends Db
{
   public function Showblog()
  { 
    global $mySession;
	$db=new Db();
	
	
	$Blogdata=$db->runQuery("SELECT *,(SELECT count(`comment_id`) s FROM ".BLOG_COMMENTS." WHERE ".BLOG_COMMENTS.".blog_post_id= ".BLOG_POST.".blog_post_id) as commentcount FROM  ".BLOG_POST." inner join  ".USERS." on ".BLOG_POST.".user_id=".USERS.".`user_id`   where ".BLOG_POST.".status='1' and ".BLOG_POST.".user_id=".$mySession->LoggedUserId." order by blog_post_id desc"); //
	return $Blogdata; 
  }
  
   public function ShowIndexblog()
  { 
    global $mySession;
	$db=new Db();
	
	/*echo "SELECT *,(SELECT count(`comment_id`) s FROM ".BLOG_COMMENTS." WHERE ".BLOG_COMMENTS.".blog_post_id= ".BLOG_POST.".blog_post_id) as commentcount FROM  ".BLOG_POST." inner join  ".USERS." on ".BLOG_POST.".user_id=".USERS.".`user_id`   where ".BLOG_POST.".status='1' and ".BLOG_POST.".activeBlog='1' order by cr_date Desc";die;*/
	
	$Blogdata=$db->runQuery("SELECT *,(SELECT count(`comment_id`) s FROM ".BLOG_COMMENTS." inner join ".USERS." on ".USERS.".user_id= ".BLOG_COMMENTS.".post_by WHERE ".BLOG_COMMENTS.".blog_post_id= ".BLOG_POST.".blog_post_id) as commentcount FROM  ".BLOG_POST." inner join  ".USERS." on ".BLOG_POST.".user_id=".USERS.".`user_id`   where ".BLOG_POST.".status='1' and ".BLOG_POST.".activeBlog='1' order by cr_date Desc"); //
	return $Blogdata; 
  }
  
   public function ShowBlogComment($blog_id)
  { 
    global $mySession;
	$db=new Db();
		
		
		
	$Blogcommentdata=$db->runQuery("SELECT * FROM 
                        ".BLOG_POST." inner join  ".BLOG_COMMENTS." on ".BLOG_POST.".blog_post_id=".BLOG_COMMENTS.".`blog_post_id`  
                        inner join  `users` on ".BLOG_COMMENTS.".post_by=`users`.`user_id` where status=1 and ".BLOG_POST.".blog_post_id=".$blog_id." order by ".BLOG_COMMENTS.".comment_id desc"); //
	return $Blogcommentdata; 
  }
 
}
?>