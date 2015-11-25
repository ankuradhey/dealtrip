<?php
__autoloadDB('Db');
class Formposts extends Db
{
	public function SaveThread($dataForm,$thread_userId)
	{
	
	
		global $mySession;
		$db=new Db();
		$Datainsert['topic_id']=$dataForm['topic_id']; 
		$Datainsert['thread_id']=$dataForm['thread_id']; 
		$Datainsert['replyof']=$dataForm['replyof']; 
		$Datainsert['user_id']=$dataForm['user_id']; 
		$Datainsert['date_posted']=$dataForm['date_added']; 
		$Datainsert['post_text']=$dataForm['post_text']; 
		
		$db->save(FORUM_POSTS,$Datainsert);
		
		$threadcommentuser=$db->runQuery("select * from ".FORUM_POSTS." AS TP  inner join ".USERS." AS U 
								ON TP.user_id=U.user_id where TP.user_id=".$dataForm['user_id']."");
								
		$comment_user=$threadcommentuser[0]['first_name']. $threadcommentuser[0]['last_name'];	
		
	
		$userData=$db->runQuery("select * from ".FORUM_THREADS." AS T  inner join ".USERS." AS U 
								ON T.user_id=U.user_id where T.thread_id=".$dataForm['thread_id']."");
		
		$fullName=$userData[0]['first_name'].$userData[0]['last_name'];	
		$commentuser=$comment_user;
		$useremail=	$userData[0]['email_address'];
		$threadtitle=$userData[0]['thread_text'];
				
		$templateData=$db->runQuery("select * from ".EMAIL_TEMPLATES." where template_id='5'"); 
		$messageText=$templateData[0]['email_body'];
		$subject=$templateData[0]['email_subject'];
	
		$messageText=str_replace("[USERNAME]",$fullName,$messageText);
		$messageText=str_replace("[THREADTITLE]",$threadtitle,$messageText);
		$messageText=str_replace("[COMMENTUSERNAME]",$commentuser,$messageText);
		$messageText=str_replace("[SITENAME]",SITE_NAME,$messageText);
		
		SendEmail($useremail,$subject,$messageText);
		
		return 1;	
		 
	}
	public function ChildPost($post_id,$thread_id)
		{
			global $mySession;
			$db=new Db();
			$forumPostlist=$db->runQuery("select * from 
			".FORUM_POSTS." inner join ".FORUM_THREADS." on ".FORUM_POSTS.".thread_id=".FORUM_THREADS.".thread_id 
			inner join ".FORUM_TOPICS." on ".FORUM_TOPICS.".topic_id = ".FORUM_THREADS.".topic_id  
				inner join ".USERS." on ".USERS.".user_id = ".FORUM_POSTS.".user_id
			 where topic_status='1' and ".FORUM_POSTS.".replyof=".$post_id." and ".FORUM_THREADS.".thread_id = ".$thread_id." order by ".FORUM_POSTS.".post_id desc" );
			 return $forumPostlist;
		}
	
}
?>