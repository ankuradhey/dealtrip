<?php
__autoloadDB('Db');
class Officialprofile extends Db
{
	public function SaveUser($dataForm,$SignUpfor)
	{
		global $mySession;
		$db=new Db();
		
		
		 if($dataForm['sex']=='2')
		{
		 $noImage="defaultuserfemaleprofile.gif";
		}
		else
		{
		$noImage="defaultusermaleprofile.gif";	
		}
		$chkQry=$db->runQuery("select * from ".USERS." where email_address='".mysql_escape_string($dataForm['email_address'])."'");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;
		}
		else
		{		
		$dataInsert['user_type']=$SignUpfor;
		$dataInsert['user_name']=$dataForm['user_name'];
		$dataInsert['first_name']=$dataForm['first_name'];
		$dataInsert['last_name']=$dataForm['last_name'];
		$dataInsert['email_address']=$dataForm['email_address'];
		$dataInsert['password']=md5($dataForm['password_o']);
		$dataInsert['password_o']=$dataForm['password_o'];
		$dataInsert['country_id']=$dataForm['country_id'];
		$dataInsert['zipcode']=$dataForm['zipcode'];
		$dataInsert['phone_number']=$dataForm['phone_number'];
		$dataInsert['sex']=$dataForm['sex'];
		$dataInsert['address']=$dataForm['address'];
		$dataInsert['profile_image']=$noImage;	
		//$dataInsert['newsletter_subscribe']=$dataForm['subscribe'];		
		/*if($SignUpfor=='2' || $SignUpfor=='3')
		{
		//$dataInsert['sex']=$dataForm['sex'];
		$date_of_birth=$dataForm['dob_year']."-".$dataForm['dob_month']."-".$dataForm['dob_day'];
		$dataInsert['date_of_birth']=$date_of_birth;
		$dataInsert['state_id']=$dataForm['state_id'];
		$dataInsert['city_id']=$dataForm['city_id'];
		$dataInsert['address']=$dataForm['address'];	
		}		
		if($SignUpfor=='3')
		{
		$dataInsert['website_address']=$dataForm['website_address'];
		$dataInsert['business_category_id']=$dataForm['business_category_id'];
		}*/
		$dataInsert['date_joined']=date('Y-m-d H:i:s');
		$dataInsert['user_status']='1';
		$dataInsert['is_active']='0';
		$db->save(USERS,$dataInsert);
		$UserId=$db->lastInsertId();
		
		//if($SignUpfor!='3')
		//{
			
		//code to send registration email
		$fullName=$dataForm['first_name'].' '.$dataForm['last_name'];
		$Url='<a href="'.APPLICATION_URL.'">'.APPLICATION_URL.'</a>';

		$ActivationLink='<a href="'.APPLICATION_URL.'signup/activate/cId/'.md5($UserId).':'.$SignUpfor.'">'.APPLICATION_URL.'signup/activate/cId/'.md5($UserId).':'.$SignUpfor.'</a>';

		
		$templateData=$db->runQuery("select * from ".EMAIL_TEMPLATES." where template_id='2'");
		$messageText=$templateData[0]['email_body'];
		$subject=$templateData[0]['email_subject'];
		
		$messageText=str_replace("[NAME]",$fullName,$messageText);
		$messageText=str_replace("[SITENAME]",SITE_NAME,$messageText);
		$messageText=str_replace("[LOGINNAME]",$dataForm['email_address'],$messageText);
		$messageText=str_replace("[PASSWORD]",$dataForm['password_o'],$messageText);
		$messageText=str_replace("[SITEURL]",$Url,$messageText);
		$messageText=str_replace("[ACTIVATIONLINK]",$ActivationLink,$messageText);		
		//prd($messageText);
		SendEmail($dataForm['email_address'],$subject,$messageText);
		//code to send registration email
		//}
		return $UserId;
		}
	}
	
	
	
	
	
	
	public function UpdateUser($dataForm,$SignUpfor,$userId)
	{
		global $mySession;
		$db=new Db();
		$chkQry=$db->runQuery("select * from ".USERS." where email_address='".mysql_escape_string($dataForm['email_address'])."' and user_id!='".$userId."'");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;
		}
		else
		{
		
		if($dataForm['profile_image']!="" && $dataForm['old_profile_image']!="")
		{
			unlink(SITE_ROOT.'images/profileimgs/'.$dataForm['old_profile_image']);
		}
		$profileImage=$dataForm['old_profile_image'];
		if($dataForm['profile_image']!="")
		{
		$profileImage=time()."_".$dataForm['profile_image'];
		@rename(SITE_ROOT.'images/profileimgs/'.$dataForm['profile_image'],SITE_ROOT.'images/profileimgs/'.$profileImage);
		}
		$dataUpdate['profile_image']=$profileImage;
		$dataUpdate['first_name']=$dataForm['first_name'];
		$dataUpdate['last_name']=$dataForm['last_name'];
		$dataUpdate['email_address']=$dataForm['email_address'];
		$dataUpdate['country_id']=$dataForm['country_id'];
		$dataUpdate['zipcode']=$dataForm['zipcode'];
		$dataUpdate['phone_number']=$dataForm['phone_number'];
		$dataUpdate['newsletter_subscribe']=$dataForm['subscribe'];		
		if($SignUpfor=='2' || $SignUpfor=='3')
		{
		$dataUpdate['sex']=$dataForm['sex'];
		$date_of_birth=$dataForm['dob_year']."-".$dataForm['dob_month']."-".$dataForm['dob_day'];
		$dataUpdate['date_of_birth']=$date_of_birth;
		$dataUpdate['state_id']=$dataForm['state_id'];
		$dataUpdate['city_id']=$dataForm['city_id'];
		$dataUpdate['address']=$dataForm['address'];	
		}		
		if($SignUpfor=='3')
		{
		$dataUpdate['website_address']=$dataForm['website_address'];
		$dataUpdate['business_category_id']=$dataForm['business_category_id'];
		}
		$conditionUpdate="user_id='".$userId."'";
		$db->modify(USERS,$dataUpdate,$conditionUpdate);
		return 1;
		}
	}
	
	
	//change Email
		public function UpdateEmail($email,$changeEmail)
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
		
		$fullName=$userData[0]['first_name'].' '.$userData[0]['last_name'];
				$Url='<a href="'.APPLICATION_URL.'">'.APPLICATION_URL.'</a>';

				$ActivationLink='<a href="'.APPLICATION_URL.'signup/activate/cId/'.md5($userId).':'.$SignUpfor.'">'.APPLICATION_URL.							'signup/activate/cId/'.md5($userId).':'.$SignUpfor.'</a>';

		
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
			
			
		}
	
	public function CheckLogin($dataForm)
	{
		global $mySession;
		$db=new Db();
		$sql="select * from ".USERS." where email_address='".mysql_escape_string($dataForm['email_address'])."' and password='".md5($dataForm['password'])."' ";		
		$result=$db->runQuery($sql);
		return $result;
//		if(count($result)>0)
//		{
//			if($result[0]['is_active']==0)
//			{
//				//code to send registration email
//				$fullName=$result[0]['first_name'].' '.$result[0]['last_name'];
//				$UserId=$result[0]['user_id'];
//				$Url='<a href="'.APPLICATION_URL.'">'.APPLICATION_URL.'</a>';
//				$ActivationLink='<a href="'.APPLICATION_URL.'signup/activate/cId/'.md5($UserId).':'.$SignUpfor.'">'.APPLICATION_URL.'signup/activate/cId/'.md5($UserId).'</a>';
//				
//				$templateData=$db->runQuery("select * from ".EMAIL_TEMPLATES." where template_id='2'");
//				$messageText=$templateData[0]['email_body'];
//				$subject=$templateData[0]['email_subject'];				
//				$messageText=str_replace("[NAME]",$fullName,$messageText);
//				$messageText=str_replace("[SITENAME]",SITE_NAME,$messageText);
//				$messageText=str_replace("[LOGINNAME]",$dataForm['email_address'],$messageText);
//				$messageText=str_replace("[PASSWORD]",$dataForm['password_o'],$messageText);
//				$messageText=str_replace("[SITEURL]",$Url,$messageText);
//				$messageText=str_replace("[ACTIVATIONLINK]",$ActivationLink,$messageText);		
////				echo "hello"; exit();
//				SendEmail($result[0]['email_address'],$subject,$messageText);				
//				//code to send registration email
//			}			
//			else
//			{
//			  return $result[0]['user_id']	;
//			}
//		}
//		else
//		{
//		    return 0;	
//		}
		
	}
	public function CheckForgotData($dataForm)
	{
		global $mySession;
		$db=new Db();
		$chkQry=$db->runQuery("select * from ".USERS." where email_address='".mysql_escape_string($dataForm['email_address'])."'");
		if($chkQry!="" and count($chkQry)>0)
		{
			$dataUpdate['password_reset']=md5($chkQry[0]['user_id']);
			$conditionUpdate="user_id='".$chkQry[0]['user_id']."'";
			$db->modify(USERS,$dataUpdate,$conditionUpdate);
			//code to send password reset email
			$fullName=$chkQry[0]['first_name'].' '.$chkQry[0]['last_name'];
			$Url='<a href="'.APPLICATION_URL.'forgotpassword/reset/requestId/'.md5($chkQry[0]['user_id']).'">'.APPLICATION_URL.'forgotpassword/reset/requestId/'.md5($chkQry[0]['user_id']).'</a>';
			$templateData=$db->runQuery("select * from ".EMAIL_TEMPLATES." where template_id='3'");
			$messageText=$templateData[0]['email_body'];
			$messageText=str_replace("[NAME]",$fullName,$messageText);
			$messageText=str_replace("[PASSWORDRESETURL]",$Url,$messageText);
			$messageText=str_replace("[SITENAME]",SITE_NAME,$messageText);
			SendEmail($dataForm['email_address'],$subject,$messageText);
			//code to send password reset email
			
			return $chkQry[0]['user_id'];
		}
		else
		{
			return 0;
		}
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
		$dataUpdate['password']=md5($dataForm['new_password']);
		$conditionUpdate="user_id='".$mySession->LoggedUserId."'";
		$db->modify(USERS,$dataUpdate,$conditionUpdate);	
		return true;
	}
	public function SaveBusiness($dataForm)
	{
		global $mySession;
		$db=new Db();
		"select * from ".SERVICE_BUSINESS." where business_title='".mysql_escape_string($dataForm['business_title'])."' and zipcode='".$dataForm['zipcode']."'" ; 
//		$chkQry=$db->runQuery("select * from ".SERVICE_BUSINESS." where business_title='".mysql_escape_string($dataForm['business_title'])."' and zipcode='".$dataForm['zipcode']."'");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;
		}
		else
		{
			$BusinessImagesPath="";
			for($counter=1;$counter<=10;$counter++)
			{
				$businessImage=$dataForm['old_business_image'.$counter];
				if($dataForm['business_image'.$counter]!="")
				{
					$businessImage=time()."_".$dataForm['business_image'.$counter];
					@rename(SITE_ROOT.'images/businesses/'.$dataForm['business_image'.$counter],SITE_ROOT.'images/businesses/'.$businessImage);
				}
				if($businessImage!=""){
				$BusinessImagesPath.=$businessImage.",";
				}
			}
			if($BusinessImagesPath!="")
			{
				$BusinessImagesPath=substr($BusinessImagesPath,0,strlen($BusinessImagesPath)-1);
			}
		$myLatLongData=getLatLongFromAddress($dataForm['country_id'],$dataForm['state_id'],$dataForm['city_name'],$dataForm['address']);

		$explode=explode("::",$myLatLongData);
		$Lat=$explode[0];
		$Long=$explode[1];
		
		$dataInsert['user_id']=$mySession->LoggedUserId;
		$dataInsert['business_title']=strip_magic_slashes($dataForm['business_title']);		
		$dataInsert['business_category_id']=$dataForm['business_category_id'];
		$dataInsert['business_subcategory_id']=$dataForm['business_subcategory_id'];
		$dataInsert['description']=strip_magic_slashes($dataForm['description']);
		$dataInsert['search_keywords']=strip_magic_slashes($dataForm['search_keywords']);
		$dataInsert['address']=strip_magic_slashes($dataForm['address']);
		$dataInsert['city_name']=strip_magic_slashes($dataForm['city_name']);
		$dataInsert['zipcode']=$dataForm['zipcode'];
		$dataInsert['state_id']=$dataForm['state_id'];
		$dataInsert['country_id']=$dataForm['country_id'];
		$dataInsert['phone_number']=$dataForm['phone_number'];
		$dataInsert['email_address']=$dataForm['email_address'];
		$dataInsert['website']=$dataForm['website'];
		$dataInsert['business_image']=$BusinessImagesPath;
		$dataInsert['business_lat']=$Lat;
		$dataInsert['business_long']=$Long;
		$dataInsert['business_status']='1';
		$dataInsert['date_business_added']=date('Y-m-d H:i:s');
		
//echo	"imagepath=".$dataInsert['business_image'];exit();		
		$db->save(SERVICE_BUSINESS,$dataInsert);
		return $db->lastInsertId();
		}
	}
	public function UpdateBusiness($dataForm,$businessId)
	{
		global $mySession;
		$db=new Db();
//		$chkQry=$db->runQuery("select * from ".SERVICE_BUSINESS." where business_title='".mysql_escape_string($dataForm['business_title'])."' and business_id!='".$businessId."'");
//		if($chkQry!="" and count($chkQry)>0)
//		{
//		return 0;
//		}
//		else
//		{
			$BusinessImagesPath="";
			for($counter=1;$counter<=10;$counter++)
			{
				$businessImage=$dataForm['old_business_image'.$counter];
				if($dataForm['business_image'.$counter]!="")
				{
					if($businessImage!="" && file_exists(SITE_ROOT.'images/events/'.$businessImage))
					{
						unlink(SITE_ROOT.'images/events/'.$businessImage);
					}
				$businessImage=time()."_".$dataForm['business_image'.$counter];
				@rename(SITE_ROOT.'images/businesses/'.$dataForm['business_image'.$counter],SITE_ROOT.'images/businesses/'.$businessImage);
				}
				if($businessImage!=""){
				$BusinessImagesPath.=$businessImage.",";
				}
			}
			if($BusinessImagesPath!="")
			{
				$BusinessImagesPath=substr($BusinessImagesPath,0,strlen($BusinessImagesPath)-1);
			}
		$myLatLongData=getLatLongFromAddress($dataForm['country_id'],$dataForm['state_id'],$dataForm['city_name'],$dataForm['address']);
		$explode=explode("::",$myLatLongData);
		$Lat=$explode[0];
		$Long=$explode[1];
		$dataUpdate['business_title']=strip_magic_slashes($dataForm['business_title']);		
		$dataUpdate['business_category_id']=$dataForm['business_category_id'];
		$dataUpdate['description']=strip_magic_slashes($dataForm['description']);
		$dataUpdate['search_keywords']=strip_magic_slashes($dataForm['search_keywords']);
		$dataUpdate['address']=strip_magic_slashes($dataForm['address']);
		$dataUpdate['city_name']=strip_magic_slashes($dataForm['city_name']);
		$dataUpdate['zipcode']=$dataForm['zipcode'];
		$dataUpdate['state_id']=$dataForm['state_id'];
		$dataUpdate['country_id']=$dataForm['country_id'];
		$dataUpdate['phone_number']=$dataForm['phone_number'];
		$dataUpdate['email_address']=$dataForm['email_address'];
		$dataUpdate['website']=$dataForm['website'];
		$dataUpdate['business_image']=$BusinessImagesPath;
		$dataUpdate['business_lat']=$Lat;
		$dataUpdate['business_long']=$Long;
		$dataUpdate['business_status']='1';
		$dataUpdate['business_subcategory_id']=$dataForm['business_subcategory_id'];
//		prd($dataUpdate);
		$conditionUpdate="business_id='".$businessId."'";		
		$db->modify(SERVICE_BUSINESS,$dataUpdate,$conditionUpdate);
		return 1;
		//}
	}
}
?>