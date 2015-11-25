<?php
__autoloadDB('Db');
class Subscription extends Db
{
	public function SaveSub($dataForm)
	{
		global $mySession;
		$db=new Db();
	   $value=$_POST['plan'];
		
		$dataForm=SetupMagicQuotes($dataForm);
		$chkQry=$db->runQuery("select * from ".SUBSCRIPTION." where email_address='".$dataForm['email_address']."'");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;
		}
		else
		{
		$dataInsert['first_name']=$dataForm['first_name'];
		$dataInsert['last_name']=$dataForm['last_name'];
		$dataInsert['email_address']=$dataForm['email_address'];
		$dataInsert['username']=$dataForm['username'];
		$dataInsert['password']=md5($dataForm['password_o']);
		$dataInsert['address']=$dataForm['address'];
		$dataInsert['country_id']=$dataForm['country_id'];
		//$dataInsert['state_id']=$dataForm['state_id'];
		//$dataInsert['city_id']=$dataForm['city_id'];
		$dataInsert['zipcode']=$dataForm['zipcode'];
		$dataInsert['phone_number']=$dataForm['phone_number'];
		$dataInsert['mobile_number']=$dataForm['mobile_number'];
		$dataInsert['id']=$value;
		$dataInsert['date_joined']=date('Y-m-d H:i:s');
		$dataInsert['user_status']='1';
		$dataInsert['user_type']=$dataForm['signup_type'];
		$db->save(USERS,$dataInsert);
		$UserId=$db->lastInsertId();
		//code to send registration email
		$fullName=$dataForm['first_name'].' '.$dataForm['last_name'];
		$Url='<a href="'.APPLICATION_URL.'">'.APPLICATION_URL.'</a>';
		$templateData=$db->runQuery("select * from ".EMAIL_TEMPLATES." where template_id='2'");
		$messageText=$templateData[0]['email_body'];
		$subject=$templateData[0]['email_subject'];
		$messageText=str_replace("[NAME]","<strong>".$fullName."</strong>",$messageText);
		$messageText=str_replace("[SITENAME]","<strong>".SITE_NAME."</strong>",$messageText);
		$messageText=str_replace("[LOGINNAME]","<strong>".$dataForm['username']."</strong>",$messageText);
		$messageText=str_replace("[PASSWORD]","<strong>".$dataForm['password_o']."</strong>",$messageText);
		$messageText=str_replace("[SITEURL]","<strong>".$Url."</strong>",$messageText);
		SendEmail($dataForm['email_address'],$subject,$messageText);
		//code to send registration email
		return $UserId;
		}
	}
	public function UpdateSub($dataForm,$userId)
	{
		global $mySession;
		$db=new Db();
		$dataForm=SetupMagicQuotes($dataForm);
		$chkQry=$db->runQuery("select * from ".SUBSCRIPTION." where mobile_number='".$dataForm['mobile_number']."' and user_id!='".$userId."'");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;
		}
		else
		{
		$dataUpdate['first_name']=$dataForm['first_name'];
		$dataUpdate['last_name']=$dataForm['last_name'];
		$dataUpdate['email_address']=$dataForm['email_address'];
		$dataUpdate['username']=$dataForm['username'];		
		$dataUpdate['address']=$dataForm['address'];
		$dataUpdate['country_id']=$dataForm['country_id'];
		$dataUpdate['state_id']=$dataForm['state_id'];
		$dataUpdate['city_id']=$dataForm['city_id'];
		$dataUpdate['zipcode']=$dataForm['zipcode'];
		$dataUpdate['phone_number']=$dataForm['phone_number'];
		$dataUpdate['mobile_number']=$dataForm['mobile_number'];
		$dataUpdate['newsletter_subscribe']=$dataForm['subscribe'];
		$conditionUpdate="user_id='".$userId."'";
		$db->modify(USERS,$dataUpdate,$conditionUpdate);
		return $userId;
		}
	}

	
	
}
?>