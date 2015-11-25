<?php
__autoloadDB('Db');
class Event extends Db
{
	public function SaveEvent($dataForm)
	{
		global $mySession;
		$db=new Db();
		$chkQry=$db->runQuery("select * from ".EVENTS." where event_title='".mysql_escape_string($dataForm['event_title'])."'");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;
		}
		else
		{
			$EventImagesPath="";
			for($counter=1;$counter<=10;$counter++)
			{
				$eventImage=$dataForm['event_image_Path'.$counter];
				if($dataForm['event_image'.$counter]!="")
				{
				$eventImage=time()."_".$dataForm['event_image'.$counter];
				@rename(SITE_ROOT.'images/events/'.$dataForm['event_image'.$counter],SITE_ROOT.'images/events/'.$eventImage);
				}
				if($eventImage!=""){
				$EventImagesPath.=$eventImage.",";
				}
			}
			if($EventImagesPath!="")
			{
				$EventImagesPath=substr($EventImagesPath,0,strlen($EventImagesPath)-1);
			}
		if($mySession->LoggedUserId>0)
		{
			$dataInsert['user_id']=$mySession->LoggedUserId;
		}				
			$dataInsert['event_title']=$dataForm['event_title'];
			$dataInsert['event_description']=$dataForm['event_description'];
			$dataInsert['event_date']=changeDate($dataForm['event_date'],0);
			$dataInsert['event_time_from']=$dataForm['hour_from']."::".$dataForm['minute_from']."::".$dataForm['ampm_from'];
			$dataInsert['event_time_to']=$dataForm['hour_to']."::".$dataForm['minute_to']."::".$dataForm['ampm_to'];
			$dataInsert['event_image']=$EventImagesPath;	
			$dataInsert['event_venue']=$dataForm['event_venue'];
			$dataInsert['event_location']=$dataForm['address'];
			$dataInsert['event_status']='1';
			$dataInsert['date_event_added']=date('Y-m-d H:i:s');	
			$dataInsert['event_lat']=$dataForm['latitude'];
			$dataInsert['event_long']=$dataForm['longitude'];
			$db->save(EVENTS,$dataInsert);
		return $db->lastInsertId();
		}
	}
	public function UpdateEvent($dataForm,$eventId)
	{
		global $mySession;
		$db=new Db();
		$chkQry=$db->runQuery("select * from ".EVENTS." where event_title='".mysql_escape_string($dataForm['event_title'])."' and event_id!='".$eventId."'");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;
		}
		else
		{
			$EventImagesPath="";
			for($counter=1;$counter<=10;$counter++)
			{
				$eventImage=$dataForm['event_image_Path'.$counter];
				if($dataForm['event_image'.$counter]!="")
				{
					if($eventImage!="" && file_exists(SITE_ROOT.'images/events/'.$eventImage))
					{
						unlink(SITE_ROOT.'images/events/'.$eventImage);
					}
				$eventImage=time()."_".$dataForm['event_image'.$counter];
				@rename(SITE_ROOT.'images/events/'.$dataForm['event_image'.$counter],SITE_ROOT.'images/events/'.$eventImage);				
				}
				if($eventImage!=""){
				$EventImagesPath.=$eventImage.",";
				}
			}
			if($EventImagesPath!="")
			{
				$EventImagesPath=substr($EventImagesPath,0,strlen($EventImagesPath)-1);
			}
		$dataUpdate['event_title']=$dataForm['event_title'];
		$dataUpdate['event_description']=$dataForm['event_description'];
		$dataUpdate['event_date']=changeDate($dataForm['event_date'],0);
		$dataUpdate['event_time_from']=$dataForm['hour_from']."::".$dataForm['minute_from']."::".$dataForm['ampm_from'];
		$dataUpdate['event_time_to']=$dataForm['hour_to']."::".$dataForm['minute_to']."::".$dataForm['ampm_to'];
		$dataUpdate['event_image']=$EventImagesPath;	
		$dataUpdate['event_venue']=$dataForm['event_venue'];
		$dataUpdate['event_location']=$dataForm['address'];
		$dataUpdate['event_status']='1';
		$dataUpdate['event_lat']=$dataForm['latitude'];
		$dataUpdate['event_long']=$dataForm['longitude'];
		$updateCondition="event_id='".$eventId."'";//user_id='".$mySession->LoggedUserId."' and
		$db->modify(EVENTS,$dataUpdate,$updateCondition);
		return 1;
		}
	}
}
?>