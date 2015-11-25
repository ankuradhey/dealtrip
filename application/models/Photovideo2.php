<?php
__autoloadDB('Db');
class Photovideo2 extends Db
{
	public function SavePhoto($dataForm)
	{
		global $mySession;
		$db=new Db();
		$imageNewName=time()."_".$dataForm['photo_path'];
		@rename(SITE_ROOT.'images/photos/'.$dataForm['photo_path'],SITE_ROOT.'images/photos/'.$imageNewName);
		$dataInsert['user_id']=$mySession->LoggedUserId;
		//$dataInsert['photo_title']=$dataForm['photo_title'];
		$dataInsert['photo']=$imageNewName;
		//$dataInsert['date_uploaded']=date('Y-m-d H:i:s');
		//$dataInsert['photo_status']='1';
		$db->save(PHOTOTRY,$dataInsert);
		return true;
	}
}