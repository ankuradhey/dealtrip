<?php
__autoloadDB('Db');
class Users extends Db
{
	public function SaveUser($dataForm, $SignUpfor)
	{
		
		global $mySession;
		$db=new Db();
		
		$dataForm = SetupMagicQuotesTrim($dataForm);

		if($dataForm['photo'] == "")
		{
			if($dataForm['sex']=='2')
			{
			 $noImage="defaultuserfemaleprofile.png";
			}
			else
			{
			$noImage="defaultusermaleprofile.png";	
			}
		}
		else
		{
			$imageNewName=time()."_".$dataForm['photo'];
			@rename(SITE_ROOT.'images/'.$dataForm['photo'],SITE_ROOT.'images/'.$imageNewName);
			$noImage = $imageNewName;
		}
	//	$chkQry=$db->runQuery("select * from ".USERS." where email_address='".mysql_escape_string($dataForm['email_address'])."'");
/*		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;
		}
		else
		{		
*/			$dataInsert['uType'] = $SignUpfor;
			//$dataInsert['username']=$dataForm['username'];
			$dataInsert['first_name']=$dataForm['first_name'];
			$dataInsert['last_name']=$dataForm['last_name'];
			$dataInsert['title']=$dataForm['title'];
			$dataInsert['email_address']=$dataForm['email_address'];
			$dataInsert['password']=md5($dataForm['password']);
			$dataInsert['country_id']=$dataForm['country_id'];
			$dataInsert['state_id']=$dataForm['state_id'];
			$dataInsert['city_id']=$dataForm['city_id'];
			$dataInsert['zipcode']=$dataForm['zipcode'];
			$dataInsert['home_number']=$dataForm['home_number'];
			$dataInsert['work_number']=$dataForm['work_number'];
			$dataInsert['mobile_number']=$dataForm['mobile_number'];
			//$dataInsert['sex']=$dataForm['sex'];
			
			$dataInsert['address'] = $dataForm['address'];
			$dataInsert['web'] = $dataForm['webaddress'];
			$dataInsert['date_joined'] = date("Y-m-d H:i:s"); 
			$dataInsert['image'] = $noImage;	
		

		$dataInsert['user_status']='0';
		/*$lat_long = getLatLongFromAddress($dataInsert['country_id'],$dataInsert['state_id'],$dataInsert['city_id'],$dataInsert['address']);
		$lat_long = explode("::",$lat_long);
		$dataInsert['cletitude'] = $lat_long[0];
		$dataInsert['clongitude'] = $lat_long[1];*/
		
		$db->save(USERS,$dataInsert);
		$UserId=$db->lastInsertId();
		
		$dataUpdate['password_reset'] = md5($UserId);
		$condition = " user_id = ".$UserId;
		$db->modify(USERS,$dataUpdate,$condition);
	
	
		$fullName= $dataForm['email_address'];
		$Url='<a href="'.APPLICATION_URL.'">'.APPLICATION_URL.'</a>';

		//$ActivationLink='<a href="'.APPLICATION_URL.'signup/activate/cId/'.md5($UserId).':'.$SignUpfor.'">'.APPLICATION_URL.'signup/activate/cId/'.md5($UserId).':'.$SignUpfor.'</a>';
		$ActivationLink= APPLICATION_URL.'signup/activate/cId/'.md5($UserId);
		
		$templateData=$db->runQuery("select * from ".EMAIL_TEMPLATES." where template_id='4'");
		$messageText=$templateData[0]['email_body'];
		$subject=$templateData[0]['email_subject'];
		
		$messageText=str_replace("[NAME]",$fullName,$messageText);
		$messageText=str_replace("[SITENAME]",SITE_NAME,$messageText);
		$messageText=str_replace("[SITEURL]",$Url,$messageText);
		$messageText=str_replace("[ACTIVATIONLINK]",$ActivationLink,$messageText);		
		if(IS_LIVE)
                $retnvalue =  SendEmail($dataForm['email_address'],$subject,$messageText);
		return $UserId;
		
	}
	
	
	
	
	
	
	public function UpdateUser($dataForm,$SignUpfor)
	{
		global $mySession;
		$db=new Db();
		$chkQry=$db->runQuery("select * from ".USERS." where user_id = '".$mySession->LoggedUserId."'");
		

		if($dataForm['photo'] != "")
		{	
			unlink(SITE_ROOT.'images/'.$chkQry[0]['image']);
			$profileImage = time()."_".$dataForm['photo'];
			@rename(SITE_ROOT.'images/'.$dataForm['photo'],SITE_ROOT.'images/'.$profileImage);
			$dataInsert['image'] = $profileImage;	
			$mySession->LoggedUser['image'] = $profileImage;
		}
		
		$dataInsert['first_name']=$dataForm['first_name'];
		$dataInsert['last_name']=$dataForm['last_name'];
		$dataInsert['country_id']=$dataForm['country_id'];
		$dataInsert['state_id']=$dataForm['state_id'];
		$dataInsert['city_id']=$dataForm['city_id'];
		$dataInsert['zipcode']=$dataForm['zipcode'];
		$dataInsert['home_number']=$dataForm['home_number'];
		$dataInsert['work_number']=$dataForm['work_number'];
		$dataInsert['mobile_number']=$dataForm['mobile_number'];
		//$dataInsert['address'] = $dataForm['address'];
		$dataInsert['web'] = $dataForm['webaddress'];
		
		$conditionUpdate=" user_id ='".$mySession->LoggedUserId."'";
		$db->modify(USERS,$dataInsert,$conditionUpdate);
		return 1;
	}
	
	
	
	//change Email
	/*	public function UpdateEmail($email,$changeEmail)
		{ global $mySession;
		$db=new Db();
		
			$chkQry=$db->runQuery("select * from ".USERS." where email_address='".mysql_escape_string($email)."'");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;
		}
		else
		{		
		$userData=$db->runQuery("select * from ".USERS." where user_id='".$changeEmail."'");
		$dataUpdate['email_address']=$email;
		
		$conditionUpdate="user_id='".$changeEmail."'";
		$db->modify(USERS,$dataUpdate,$conditionUpdate);
		$SignUpfor=$userData[0]['user_type'];
		$fullName=$userData[0]['first_name'].' '.$userData[0]['last_name'];
				$Url='<a href="'.APPLICATION_URL.'">'.APPLICATION_URL.'</a>';

				$ActivationLink='<a href="'.APPLICATION_URL.'signup/activate/cId/'.md5($changeEmail).':'.$SignUpfor.'">'.APPLICATION_URL.'signup/activate/cId/'.md5($changeEmail).':'.$SignUpfor.'</a>';

		
				$templateData=$db->runQuery("select * from ".EMAIL_TEMPLATES." where template_id='2'");
				$messageText=$templateData[0]['email_body'];
				$subject=$templateData[0]['email_subject'];
				
				$messageText=str_replace("[NAME]",$fullName,$messageText);
				$messageText=str_replace("[SITENAME]",SITE_NAME,$messageText);
				$messageText=str_replace("[LOGINNAME]",$userData[0]['email_address'],$messageText);
				$messageText=str_replace("[PASSWORD]",$userData[0]['password_o'],$messageText);
				$messageText=str_replace("[SITEURL]",$Url,$messageText);
				$messageText=str_replace("[ACTIVATIONLINK]",$ActivationLink,$messageText);		
				//prd($messageText);
				
				SendEmail($userData[0]['email_address'],$subject,$messageText);
		return 1;
		
		}
			
			
		}*/
	
	public function CheckLogin($dataForm)
	{
		global $mySession;
		$db=new Db();
		$sql="select * from ".USERS." where email_address='".mysql_escape_string($dataForm['email_address'])."' and password='".md5($dataForm['password'])."' ";		
		$result=$db->runQuery($sql);

		return $result;
		
	}
	public function CheckForgotData($dataForm)
	{
		global $mySession;
		$db=new Db();
		$chkQry=$db->runQuery("select * from ".USERS." where email_address ='".mysql_escape_string($dataForm['email_address'])."' ");
		
		$dataUpdate['password_reset']=md5($chkQry[0]['user_id']);
		$conditionUpdate="user_id='".$chkQry[0]['user_id']."'";
		$db->modify(USERS,$dataUpdate,$conditionUpdate);
		//code to send password reset email
		
		// Reset Password link
		$fullName=$chkQry[0]['email_address'];
		$Url= "<a href = '".APPLICATION_URL."signup/resetpassword/requestId/".md5($chkQry[0]['user_id'])."'>click here</a>";
		
		$templateData=$db->runQuery("select * from ".EMAIL_TEMPLATES." where template_id='3'");
		
		$subject = $templateData[0]['email_subject'];
		$messageText = $templateData[0]['email_body'];
		$messageText=str_replace("[NAME]",$fullName,$messageText);
		$messageText=str_replace("[PASSWORDRESETURL]",$Url,$messageText);
		$messageText=str_replace("[SITENAME]", SITE_NAME,$messageText);
		SendEmail($chkQry[0]['email_address'],$subject,$messageText);
				//code to send password reset email
		return $chkQry[0]['user_id'];
	}
	
	public function ReceivePassword($dataForm)
	{
			$db = new Db();

			$chkQry = $db->runQuery("select * from ".USERS." where email_address = '".$dataForm['email_address']."' ");

			$fullName=$dataForm['email_address'];
			//$Url= "<a href = '".APPLICATION_URL."signup/resetpassword/requestId/".md5($chkQry[0]['user_id'])."'>click here</a>";
			$Url = generatePassword();
			
			//change password in database
			$dataUpdate = array();
			$dataUpdate['password'] = md5($Url);
			$condition = "user_id = ".$dataForm['user_id'];

			$db->modify(USERS,$dataUpdate, $condition);
			$templateData=$db->runQuery("select * from ".EMAIL_TEMPLATES." where template_id = '5'");
			$subject = $templateData[0]['email_subject'];
			$messageText = $templateData[0]['email_body'];
			$messageText=str_replace("[NAME]",$fullName,$messageText);
			$messageText=str_replace("[PASSWORD]",$Url,$messageText);
			$messageText=str_replace("[SITENAME]", SITE_NAME,$messageText);
			SendEmail($dataForm['email_address'],$subject,$messageText);
		
		
	}
	
	public function ResetNewPassword($dataForm,$requestId)
	{
		global $mySession;
		$db=new Db();
		$chkData=$db->runQuery("select * from ".USERS." where password_reset='".$requestId."'");
		if($chkData!="" and count($chkData)>0)
		{
			$dataUpdate['password']=md5($dataForm['new_password']);
			$dataUpdate['password_reset']="";
			$conditionUpdate="user_id='".$chkData[0]['user_id']."'";
			$db->modify(USERS,$dataUpdate,$conditionUpdate);	
		}
		return true;
	}
	public function ChangePassword($dataForm)
	{
		global $mySession;
		$db=new Db();

		$chkQuery = $db->runQuery("select * from ".USERS." where user_id = '".$mySession->LoggedUserId."' and password = '".md5($dataForm['o_password'])."' ");

		if($chkQuery != "" && count($chkQuery) > 0)
		{
			$dataUpdate['password']=md5($dataForm['new_password']);
			$conditionUpdate="user_id='".$mySession->LoggedUserId."'";
			$db->modify(USERS,$dataUpdate,$conditionUpdate);	
			
			
			//code for sending mail
			
			$fullName=$chkQuery[0]['email_address'];
			$Url= APPLICATION_URL."signup/resetpassword/requestId/".md5($chkQuery[0]['user_id']);
			$RUrl= APPLICATION_URL."signup/receivepassword/requestId/".md5($chkQuery[0]['user_id']);
			$templateData=$db->runQuery("select * from ".EMAIL_TEMPLATES." where template_id='6'");
			
			$subject = $templateData[0]['email_subject'];
			$messageText = $templateData[0]['email_body'];
			$messageText=str_replace("[NAME]",$fullName,$messageText);
			$messageText=str_replace("[PASSWORDRESENDURL]",$RUrl,$messageText);
			$messageText=str_replace("[PASSWORDRESETURL]",$Url,$messageText);
			$messageText=str_replace("[SITENAME]", SITE_NAME,$messageText);
			SendEmail($chkQuery[0]['email_address'],$subject,$messageText);
			
			
			
			
			
			
			
			return '1';
		}
		else
		return '0';
	
	}
	
	public function contactus($dataForm)
	{
		global $mySession;
		$db=new Db();
		
		$adminArr = $db->runQuery("select * from ".ADMINISTRATOR." ");
		//code to send password reset email
		$dataForm = SetupMagicQuotes($dataForm);
		$fullName= $dataForm['full_name'];
		
		$subject = "Dealatrip:".$dataForm['subject'];
		$messageText = "Full Name : ".$dataForm['full_name']."<br>";
		//$messageText .= "Phone : ".$dataForm['phone']."<br>";
		$messageText .= "Email : ".$dataForm['email_address']."<br>";
		$messageText .= "Message : ".$dataForm['enquiry'];
		//$messageText .= "Enquiry : ".$dataForm['comment'];
		
		SendEmail($adminArr[0]['admin_email'],$subject,$messageText, $dataForm['email_address']);
		SendEmail("customersupport@destinationamerica.co.uk",$subject,$messageText, $dataForm['email_address']);
		
		//return $chkQry[0]['user_id'];	
		
	}
	
	public function ownercontactus($dataForm)
	{
		global $mySession;
		$db=new Db();
		
		

		
		$adminArr = $db->runQuery("select * from ".ADMINISTRATOR." ");
		//code to send password reset email
		$dataForm = SetupMagicQuotes($dataForm);
		$fullName= $dataForm['full_name'];
		
		$subject = "Dealatrip:".$dataForm['subject'];
		$messageText = "Full Name : ".$dataForm['full_name']."<br>";
		$messageText .= "Email : ".$dataForm['email_address']."<br>";
		$messageText .= "Phone : ".$dataForm['phone']."<br>";
		if($dataForm['property_no'] != "")
		{
			$messageText .= "Property Number : ".$dataForm['property_no']."<br>";	
		}
		$messageText .= "Message : ".$dataForm['question'];
		//$messageText .= "Enquiry : ".$dataForm['comment'];
		
		SendEmail($adminArr[0]['admin_email'],$subject,$messageText, $dataForm['email_address']);
		SendEmail("customersupport@destinationamerica.co.uk",$subject,$messageText, $dataForm['email_address']);
		
		//return $chkQry[0]['user_id'];	
		
	}
	
	
	public function deleteUser($dataForm,$uType)
	{
		$db = new Db();
		
		$condition = "email_address = '".$dataForm['email_address']."' and uType = '".$uType."' ";
		$db->delete(USERS,$condition)	;
		return;
		
	}
}
?>