<?php
__autoloadDB('Db');
class Photovideo1 extends Db
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
		//$dataInsert['date_uploaded']=date('Y-m-d H:i:s');
		//$dataInsert['photo_status']='1';
		$db->save(PHOTOGALLERY1,$dataInsert);
		return true;
	}
}