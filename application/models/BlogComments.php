<?php
__autoloadDB('Db');
class BlogComments extends Db
{
	public function saveComment($dataForm)
	{//echo 'hello'; exit();
		global $mySession;
		$db=new Db();
//	prd($dataForm);
		$db->save(BLOG_COMMENTS,$dataForm);
		
		
								
		$commentuser=$db->runQuery("select * from  ".BLOG_COMMENTS." AS BC 
								inner join ".USERS." as U ON BC.post_by=U.user_id
								where  BC.post_by=".$dataForm['post_by']."");	
								
		$comment_user=$commentuser[0]['first_name']. $commentuser[0]['last_name'];
		
		
		$userData=$db->runQuery("select * from ".BLOG_POST." AS BP  inner join ".BLOG_COMMENTS." AS BC 
								ON BP.blog_post_id=BC.blog_post_id 
								inner join ".USERS." as U ON BP.user_id=U.user_id
								where BP.status='1' and BP.activeBlog='1' and BP.blog_post_id=".$dataForm['blog_post_id']."");
		
		$fullName=$userData[0]['first_name'].$userData[0]['last_name'];	
		$commentbyuser=$comment_user;
		$useremail=	$userData[0]['email_address'];
		$threadtitle=$userData[0]['title'];
		
				
		$templateData=$db->runQuery("select * from ".EMAIL_TEMPLATES." where template_id='6'");
		$messageText=$templateData[0]['email_body'];
		$subject=$templateData[0]['email_subject'];
	
		$messageText=str_replace("[USERNAME]",$fullName,$messageText);
		$messageText=str_replace("[BLOGTITLE]",$threadtitle,$messageText);
		$messageText=str_replace("[COMMENTUSERNAME]",$commentbyuser,$messageText);
		$messageText=str_replace("[SITENAME]",SITE_NAME,$messageText);
	
		SendEmail($useremail,$subject,$messageText);
		
		
		return 1;	
		 
	}
	public function SavePost($dataForm)
	{	//echo "hello"; exit();
		global $mySession;
		$db=new Db();
	    prd($dataForm);
		$db->save(BLOG_POST,$dataForm);
		return 1;	
		 
	}
    
}
?>