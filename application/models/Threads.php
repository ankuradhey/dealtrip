<?php
__autoloadDB('Db');
class Threads extends Db
{
	public function SaveThread($dataForm)
	{
		global $mySession;
		$db=new Db();
		//print_r($dataForm);die;
		$Datainsert['topic_id']=$dataForm['topic_id']; 
		$Datainsert['user_id']=$dataForm['user_id']; 
		$Datainsert['date_added']=$dataForm['date_added']; 
		$Datainsert['thread_text']=$dataForm['thread_text']; 
		$db->save(FORUM_THREADS,$Datainsert);
		return 1;	
		 
	}
	
	
	public function UpdateThread($dataForm,$thread_id)
	{
		global $mySession;
		$db=new Db();
				
		$chkQry=$db->runQuery("select * from ".FORUM_THREADS." where thread_id=".$thread_id." ");
		
		if($chkQry!="" and count($chkQry)>0)
		{
		
		
		$dataUpdate['thread_text']=$dataForm['thread_text'];
		$updateCondition="thread_id='".$thread_id."'";
		$db->modify(FORUM_THREADS,$dataUpdate,$updateCondition);
		return 1;
		}
		else
		{	
		return 0;
		}
	}
	
	public function getlastThread($topic_id) 
	{
		global $mySession;
		$db=new Db();
		$forumThread=$db->runQuery("select * from ".FORUM_THREADS." inner join ".USERS." on ".USERS.".user_id = ".FORUM_THREADS.".user_id where topic_id=".$topic_id." order by thread_id desc limit 1");
		return $forumThread;	
	}
}
?>