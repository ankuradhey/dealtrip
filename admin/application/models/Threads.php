<?php
__autoloadDB('Db');
class Threads extends Db
{
	public function SaveThread($dataForm)
	{
		global $mySession;
		$db=new Db();
		$db->save(FORUM_THREADS,$dataForm);
		return 1;	
		 
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