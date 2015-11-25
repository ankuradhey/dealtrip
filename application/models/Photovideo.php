<?php
__autoloadDB('Db');
class Photovideo extends Db
{
	public function SavePhoto($dataForm)
	{
		global $mySession;
		$db=new Db();
		$imageNewName=time()."_".$dataForm['photo_path'];
		@rename(SITE_ROOT.'images/photos/'.$dataForm['photo_path'],SITE_ROOT.'images/photos/'.$imageNewName);
		$dataInsert['user_id']=$mySession->LoggedUserId;
		$dataInsert['photo_title']=$dataForm['photo_title'];
		$dataInsert['photo_path']=$imageNewName;
		$dataInsert['date_uploaded']=date('Y-m-d H:i:s');
		$dataInsert['photo_status']='1';
		$db->save(PHOTOGALLERY,$dataInsert);
		return true;
	}
	public function SaveVideo($dataForm)
	{
		global $mySession;
		$db=new Db();
		if($dataForm['path_type']=='1')
		{
		$myVideoName=time()."_".$dataForm['video_path'];
		@rename(SITE_ROOT.'images/videos/'.$dataForm['video_path'],SITE_ROOT.'images/videos/'.$myVideoName);	
		}
		else
		{
		$myVideoName=$dataForm['you_tube_url'];
		}
		
		$dataInsert['user_id']=$mySession->LoggedUserId;
		$dataInsert['video_title']=$dataForm['video_title'];
		$dataInsert['path_type']=$dataForm['path_type'];
		$dataInsert['video_path']=$myVideoName;
		$dataInsert['date_uploaded']=date('Y-m-d H:i:s');
		$dataInsert['video_status']='1';
		$db->save(VIDEOGALLERY,$dataInsert);
		return true;
	}
}
?>