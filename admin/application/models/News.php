<?php
__autoloadDB('Db');
class News extends Db
{
	public function SaveNews($dataForm)
	{
		global $mySession;
		$db=new Db();
//		$dataInsert['posted_on']=date('Y-m-d',strtotime($dataForm['posted_on']));
		$dataInsert['posted_on']=date('Y-m-d');
		$dataInsert['subject']=$dataForm['subject'];
		$dataInsert['news_update']=$dataForm['news'];
		
		$chkQry=$db->runQuery("select * from ".NEWS." where posted_on='".$dataInsert['posted_on']."'");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;
		}
		else
		{
		$db->save(NEWS,$dataInsert);
		return 1;
		}
	}
	
	public function UpdateNews($dataForm,$newsId)
	{
		global $mySession;
		$db=new Db();
		$data_update['posted_on']=date('Y-m-d',strtotime($dataForm['posted_on']));
		$data_update['subject']=$dataForm['subject'];
		$data_update['news_update']=$dataForm['news'];
		
		$chkQry=$db->runQuery("select * from ".NEWS." where news_id!='".$newsId."' and  posted_on='".$data_update['posted_on']."'");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;
		}
		else
		{
		$condition="news_id='".$newsId."'";
		$db->modify(NEWS,$data_update,$condition);
		return 1;
		}
	}
	
	
  public function deleteNews($ptyleId)
  {
   	 global $mySession;
	 $db=new Db();
	 $condition1="news_id='".$ptyleId."'"; 
	 $db->delete(NEWS,$condition1);
	 
  }
	
	#-----------------------------------------------------------#
	# Status Property Type Function
	
	// Here Property Type status changed from PROPERTYTYPE table.
	
	#-----------------------------------------------------------#
	
  public function statusNews($status,$ptyleId)
  {
   	 global $mySession;
	 $db=new Db();
	 $data_update['status'] = $status; 
	 $condition= "news_id='".$ptyleId."'";
	 $db->modify(NEWS,$data_update,$condition);
  }	
	
	
}
?>