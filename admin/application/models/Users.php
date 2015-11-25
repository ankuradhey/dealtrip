<?php
__autoloadDB('Db');
class Users extends Db
{
	public function SaveAdmin($dataForm)
	{
		global $mySession;
		$db=new Db();
		$chkQry=$db->runQuery("select * from ".ADMINISTRATOR." where admin_username='".$dataForm['username']."'");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;
		}
		else
		{
		$dataInsert['admin_first_name']=$dataForm['first_name'];
		$dataInsert['admin_last_name']=$dataForm['last_name'];
		$dataInsert['admin_email']=$dataForm['email_address'];
		$dataInsert['admin_username']=$dataForm['username'];
		$dataInsert['admin_password']=md5($dataForm['password_o']);
		$dataInsert['admin_status']=$dataForm['admin_status'];
		$db->save(ADMINISTRATOR,$dataInsert);
		return 1;
		}
	}
	public function UpdateAdmin($dataForm,$adminId)
	{
		global $mySession;
		$db=new Db();
		$chkQry=$db->runQuery("select * from ".ADMINISTRATOR." where admin_username='".$dataForm['username']."' and admin_id!='".$adminId."'");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;
		}
		else
		{
		$dataUpdate['admin_first_name']=$dataForm['first_name'];
		$dataUpdate['admin_last_name']=$dataForm['last_name'];
		$dataUpdate['admin_email']=$dataForm['email_address'];
		$dataUpdate['admin_username']=$dataForm['username'];
		if(isset($_REQUEST['ChangePass']))
		{
		$dataUpdate['admin_password']=md5($dataForm['password_o']);
		}
		$dataUpdate['admin_status']=$dataForm['admin_status'];
		$conditionUpdate="admin_id='".$adminId."'";
		$db->modify(ADMINISTRATOR,$dataUpdate,$conditionUpdate);
		return 1;
		}
	}
	public function SaveUser($dataForm)
	{
		global $mySession;
		$db=new Db();
		$dataForm = SetupMagicQuotesTrim($dataForm);
		$chkQry=$db->runQuery("select * from ".USERS." where email_address='".$dataForm['email_address']."'");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;
		}
		else
		{
		

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
			$dataInsert['uType'] =$dataForm['uType'];
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
			$dataInsert['web'] = $dataForm['webaddress'];
			$dataInsert['address'] = $dataForm['address'];
			$dataInsert['date_joined'] = date("Y-m-d H:i:s"); 
			$dataInsert['image'] = $noImage;	
			$dataInsert['user_status']='1';
			$db->save(USERS,$dataInsert);
                        return $db->lastInsertId();
		}
	}
	public function UpdateUser($dataForm,$userId)
	{
		global $mySession;
		$db=new Db();


		$chkQry=$db->runQuery("select * from ".USERS." where email_address='".$dataForm['email_address']."' and user_id !='".$userId."'");
		

		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;
		}
		else
		{
		
		if($dataForm['photo'] == "")
			{
				
			}
			else
			{
				$imageNewName=time()."_".$dataForm['photo'];
				@rename(SITE_ROOT.'images/'.$dataForm['photo'],SITE_ROOT.'images/'.$imageNewName);
				 if($dataForm['old_photo']!="defaultuserfemaleprofile.png" || $dataForm['old_photo']!="defaultusermaleprofile.png")
				 {
					 unlink(SITE_ROOT.'images/'.$dataForm['old_photo']);
				}
				
				
				$noImage = $imageNewName;
				$dataInsert['image'] = $noImage;
			}
			$dataInsert['username'] =$dataForm['username'];
			$dataInsert['first_name']=$dataForm['first_name'];
			$dataInsert['last_name']=$dataForm['last_name'];
			$dataInsert['title']=$dataForm['title'];
			$dataInsert['country_id']=$dataForm['country_id'];
			$dataInsert['state_id']=$dataForm['state_id'];
			$dataInsert['city_id']=$dataForm['city_id'];
			$dataInsert['zipcode']=$dataForm['zipcode'];
			$dataInsert['home_number']=$dataForm['home_number'];
			$dataInsert['work_number']=$dataForm['work_number'];
			$dataInsert['mobile_number']=$dataForm['mobile_number'];
			$dataInsert['web'] = $dataForm['webaddress'];
			$dataInsert['address'] = $dataForm['address'];			
			
			$conditionUpdate="user_id='".$userId."'";
			//prd($dataUpdate);
			$db->modify(USERS,$dataInsert,$conditionUpdate);
			
		
		return 1;
		}
	}
}
?>