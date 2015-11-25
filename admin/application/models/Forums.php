<?php
__autoloadDB('Db');
class Forums extends Db
{
	public function SaveTopic($dataForm)
	{
		global $mySession;
		$db=new Db();
		$chkQry=$db->runQuery("select * from ".FORUM_TOPICS." where topic_title='".$dataForm['topic_title']."'");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;	
		}
		else
		{
		$dataInsert['topic_title']=$dataForm['topic_title'];
		$dataInsert['topic_description']=$dataForm['topic_description'];
		$dataInsert['topic_status']=$dataForm['topic_status'];
		$db->save(FORUM_TOPICS,$dataInsert);
		return 1;	
		}
	}
	public function UpdateTopic($dataForm,$topicId)	
	{
		global $mySession;
		$db=new Db();
		$chkQry=$db->runQuery("select * from ".FORUM_TOPICS." where topic_title='".$dataForm['topic_title']."' and topic_id!='".$topicId."'");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;	
		}
		else
		{
		$dataUpdate['topic_title']=$dataForm['topic_title'];
		$dataUpdate['topic_description']=$dataForm['topic_description'];
		$dataUpdate['topic_status']=$dataForm['topic_status'];
		$update_condition="topic_id='".$topicId."'";
		$db->modify(FORUM_TOPICS,$dataUpdate,$update_condition);
		return 1;	
		}
	}
}
?>