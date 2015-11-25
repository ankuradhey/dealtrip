<?php
__autoloadDB('Db');
class SignupController extends AppController
{
	public function indexAction()
	{
		global $mySession;
		$db=new Db();
		$success = $this->getRequest()->getParam("success");
		$this->view->pageTitle="Customer Sign Up";
		$this->view->success = $success;
		$myform=new Form_User();
		
		$this->view->headMeta("Deal A Trip Customers and Property Owner Login and Registration", 'description');
                
		$varsuccess = 0;
		if ($this->getRequest()->isPost())
		{
	
			$request=$this->getRequest();
			if ($myform->isValid($request->getPost()))
			{	
				$dataForm=$myform->getValues();
				//prd($dataForm);
				$sql = "select * from ".USERS." where email_address = '".trim($dataForm['email_address'])."'";

				$chkArr = $db->runQuery($sql);
				
				
				if(count($chkArr) == 0)
				{
					$myObj = new Users();
					$Result=$myObj->SaveUser($dataForm, 1);
					$mySession->sucessMsg = "Successfully registered";
					$varsuccess = 1;
				}
				else
				{
					
					$mySession->errorMsg = "This username or email address already exist";		
					
				}
									
			}
			else
			{
				
				
				$mySession->errorMsg = "Image not proper";	
				
			
			}
			
		}
		
		$this->view->varsuccess = $varsuccess;
		$this->view->myform=$myform;
		//$this->view->SignUpfor=$SignUpfor;
	}
	
	public function processAction()
	{
		global $mySession;
		$db=new Db();
		//$countryId='218';
		$dataForm = NVPToArray($_REQUEST['username']);
		$photo = $_REQUEST['photo'];
		$dataForm['photo'] = $photo;
		$myObj = new Users();
		$Result=$myObj->SaveUser($dataForm, 1);
		if($Result > 0)
		exit("s");
		else
		exit("f");
	}
	
	public function checkvaliduserAction()
	{
		global $mySession;
		$db = new Db();
		$sql = "select * from ".USERS." where username = '".trim($_REQUEST['username'])."' ";
		$chkArr = $db->runQuery($sql);
		if(count($chkArr) > 0)
		exit("f");
		else
		exit("s");
	}
	
	public function checkvaliduseremailAction()
	{
		global $mySession;
		$db = new Db();
		$sql = "select * from ".USERS." where username = '".$_REQUEST['username']."' and email_address = '".$_REQUEST['email']."'";
		
		$chkArr = $db->runQuery($sql);
		


		if(count($chkArr) > 0)
		{	
		exit("1");
		}
		else
		exit("false");
	}
		
	public function checkvalidemailAction()
	{
		global $mySession;
		$db = new Db();
		$sql = "select * from ".USERS." where email_address = '".trim($_REQUEST['email'])."' ";
		$chkArr = $db->runQuery($sql);
		if(count($chkArr) > 0)
		exit("f");
		else
		exit("s");
	}	
	
	
	public function checkvalidphotoAction()
	{
		global $mySession;
		$db = new Db();
		
		$img = explode(".",$_REQUEST['image'])	;
		
		$x = $img[count($img)-1];

		if(preg_match('/jpg/',$x)|| preg_match('/gif/',$x)|| preg_match('/jpeg/',$x)|| preg_match('/png/',$x) || preg_match('/bmp/',$x))
		exit("s");
		else
		exit("f");
		
		
		
	}
	
	
	public function getstatebycountryAction()
	{

		global $mySession;
		$db=new Db();
		$OptionState="";
		$DataState=$db->runQuery("select * from ".STATE." where country_id='".$_REQUEST['countryId']."' order by state_name");
		if($DataState!="" and count($DataState)>0)
		{

			foreach($DataState as $key=>$valueState)
			{
			$OptionState.=$valueState['state_id']."|||".$valueState['state_name']."***";
			}
			$OptionState=substr($OptionState,0,strlen($OptionState)-3);
		}
		echo $OptionState;
		exit();
	}
	
	public function getcitiesbystateAction()
	{
		global $mySession;
		$db=new Db();
		$OptionCities="";
		$DataCity=$db->runQuery("select * from ".CITIES." where state_id='".$_REQUEST['stateId']."' order by city_name");
		if($DataCity!="" and count($DataCity)>0)
		{
			foreach($DataCity as $key=>$valueCity)
			{
			$OptionCities.=$valueCity['city_id']."|||".$valueCity['city_name']."***";
			}
			$OptionCities=substr($OptionCities,0,strlen($OptionCities)-3);
		}
		echo $OptionCities;
		exit();
	}
	
	public function getsubareabycityAction()
	{
		global $mySession;
		$db=new Db();
		$OptionSub = "";
		$DataSub = $db->runQuery("select * from ".SUB_AREA." where city_id='".$_REQUEST['cityId']."' order by sub_area_name");

		if($DataSub != "" and count($DataSub)>0)
		{
			foreach($DataSub as $key=>$valueSub)
			{
				$OptionSub .= $valueSub['sub_area_id']."|||".$valueSub['sub_area_name']."***";
			}
			$OptionSubarea = substr($OptionSub, 0, strlen($OptionSubarea)-3);
		}
		
		echo $OptionSubarea;
		exit();
		
	}
	
	public function getlocalareabysubAction()
	{
		global $mySession;
		$db=new Db();
		$OptionLocal = "";
		$DataLocal = $db->runQuery("select * from ".LOCAL_AREA." where sub_area_id='".$_REQUEST['subId']."' order by local_area_name");

		if($DataLocal != "" and count($DataLocal)>0)
		{
			foreach($DataLocal as $key=>$valueLocal)
			{
				$OptionLocal .= $valueLocal['local_area_id']."|||".$valueLocal['local_area_name']."***";
			}
			$OptionLocalarea = substr($OptionLocal, 0, strlen($OptionLocalarea)-3);
		}
		
		echo $OptionLocalarea;
		exit();
		
	}
	
	public function activateAction()
	{
		global $mySession;
		$db = new Db();
		$this->view->pageTitle = "Welcome";
		$requestId = $this->getRequest()->getParam('cId');

		$chkData=$db->runQuery("select * from ".USERS." where password_reset='".$requestId."'");	
		if($chkData != "" and count($chkData)>0)
		{	
			$this->view->requestId = $requestId;
			
			$conditionUpdate = "password_reset='".$chkData[0]['password_reset']."'";
			$dataUpdate['user_status'] = '1';
			$db->modify(USERS,$dataUpdate,$conditionUpdate);
			
			$mySession->LoggedUserId = $chkData[0]['user_id'];
			$mySession->LoggedUserType = $chkData[0]['uType'];
			$mySession->LoggedUserName = $chkData[0]['email_address'];
			$mySession->LoggedUser  = $chkData[0];
			$this->_redirect("myaccount");
		}
		else
		{
			$this->_redirect("");
		}		
		
	}
	
	public function forgotpasswordAction()
	{
		global $mySession;
		$db=new Db();

		$this->view->pageTitle = "Forgot Password";
		
		$myform = new Form_Forgotpassword();
		
		$varsuccess = 0; 
		if ($this->getRequest()->isPost())
		{

			$request=$this->getRequest();
			$dataForm=$myform->getValues();
	
			 
			if ($myform->isValid($request->getPost()))
			{	
				
				$dataForm=$myform->getValues();
				//prd($dataForm);
				
				$sql = "select * from ".USERS." where email_address = '".$dataForm['email_address']."'";
				$chkArr = $db->runQuery($sql);
				if(count($chkArr) > 0)
				{
					$myObj=new Users();
					$dataForm['pswd_change_link'] = $_REQUEST['pswd_change_link'];
					$Result = $myObj->CheckForgotData($dataForm);
					$mySession->sucessMsg = "mail has been sent to your account";
					$varsuccess = 1;
				}
				else
				{
					$mySession->errorMsg = "Username or email_address is incorrect";
				
				}
		}
			else
			{
				$mySession->errorMsg = "Username or email_address is incorrect";
				
			
			}
			
		}
		$this->view->myform = $myform;	
		$this->view->varsuccess = $varsuccess;
		
		
	}
	public function processforgotpasswordAction()
	{
		global $mySession;
		$db=new Db();
		
		$sql = "select * from ".USERS." where username = '".$_REQUEST['username']."' and email_address = '".$_REQUEST['email']."'";
		
		$chkArr = $db->runQuery($sql);
		

		if(count($chkArr) > 0)
		{
			$myObj=new Users();
			$Result = $myObj->CheckForgotData($_REQUEST['email']);
			exit("s");
		}
		else
		exit("f");
	}

	public function resetpasswordAction()
	{
		global $mySession;
		$db=new Db();

		$this->view->pageTitle = "Reset Password";
		
		$requestId = $this->getRequest()->getParam("requestId");
		$this->view->requestId = $requestId;
		//$send = $this->getRequest()->getParam("send");
		//$this->view->send = $send;
		
		$varsuccess = 0;
		
		
		$myform=new Form_Reactivate();
		
		
		$chkData=$db->runQuery("select * from ".USERS." where password_reset='".$requestId."'");

		if($chkData!="" and count($chkData)>0)
		{	
			$this->view->requestId=$requestId;
			
			$this->view->myform=$myform;
		}
		else
		{
			$this->view->requestId='Expire';
		}	
		
		$this->view->uId = $chkData[0]['user_id'];
		


		if ($this->getRequest()->isPost())
		{
	
			$request=$this->getRequest();
			 $myform=new Form_Reactivate();
			if ($myform->isValid($request->getPost()))
			{	
				
		
					$dataForm=$myform->getValues();
					$chkQuery = $db->runQuery("select * from ".USERS." where user_id = '".$chkData[0]['user_id']."' ");
					
					if($chkQuery != "" && count($chkQuery) > 0)
					{
						$dataUpdate['password'] = md5($dataForm['new_password']);
						$conditionUpdate = " user_id = '".$chkData[0]['user_id']."'";
						$db->modify(USERS,$dataUpdate,$conditionUpdate);
						$varsuccess = 1;
						$mySession->sucessMsg = "Password successfully reset";		
					}
					else
					{
						$mySession->errorMsg = "Please enter correct current password";		
						
					}
					
			
			}
		}
		$this->view->varsuccess = $varsuccess;
		$this->view->myform=$myform;
	}
	
	public function processresetAction()
	{
		global $mySession;
		$db=new Db();
		$uId = $_REQUEST['userId'];
		$dataForm = NVPToArray($_REQUEST['username']);

		$dataUpdate['password'] = md5($dataForm['new_password']);
		$conditionUpdate = " user_id = '".$uId."'";
		$db->modify(USERS,$dataUpdate,$conditionUpdate);
		
		exit(1);
				
		
	}
	
	public function processcontactAction()
	{
		global $mySession;
		$db=new Db();
		$Data = $_REQUEST['Data'];
		$dataForm = NVPToArray($Data);
		$contact = new Users();
		$result = $contact->contactus($dataForm);
		exit;
	}
	
	public function profileAction()
	{
		global $mySession;
		$db=new Db();
		$this->view->pageTitle = "Edit Profile";
		$myform = new Form_User($mySession->LoggedUserId);
		$varsuccess = 0;
		if ($this->getRequest()->isPost())
		{

			$request=$this->getRequest();
			$dataForm=$myform->getValues();
			if($myform->isValid($request->getPost()))
			{	
				$dataForm=$myform->getValues();
				//prd($dataForm);
				$myObj = new Users();
				$Result = $myObj->UpdateUser($dataForm, 1);
				$mySession->sucessMsg = "Your Profile successfully updated";
				$varsuccess = 1;
			}
			else
			{
				$mySession->errorMsg = "Image not proper";	
			}
			
		}
		
		$this->view->varsuccess = $varsuccess;
		$this->view->myform=$myform;
		
	}
	
	public function processprofileAction()
	{
		global $mySession;
		$db=new Db();
		$dataForm = NVPToArray($_REQUEST['username']);
		$photo = $_REQUEST['photo'];
		$dataForm['photo'] = $photo;
		$myObj = new Users();
		$Result=$myObj->UpdateUser($dataForm, 1);
		
		exit("s");
		
	}
	
	public function changepasswordAction()
	{
		global $mySession;
		$db=new Db();
		$this->view->pageTitle = "Change Password";
		$myform = new Form_Resetpassword();
		$varsuccess = 0; 
		if ($this->getRequest()->isPost())
		{

			$request=$this->getRequest();
			$dataForm=$myform->getValues();
	
			 
			if ($myform->isValid($request->getPost()))
			{	
				
				$dataForm=$myform->getValues();
				//prd($dataForm);
				$myObj = new Users();
				$Result=$myObj->ChangePassword($dataForm);
				if($Result != '0')
				{
					$mySession->sucessMsg = "Your Password successfully changed";
					$varsuccess = 1;
				}
				else
				$mySession->errorMsg = "Please enter correct old password";
			
			}
			else
			{
				$mySession->errorMsg = "Password do not match";	
				
			
			}
			
		}
		$this->view->myform = $myform;	
		$this->view->varsuccess = $varsuccess;
	}
	
	public function processpasswordAction()
	{
		global $mySession;
		$db=new Db();
		$this->view->pageTitle = "Change Password";
		$dataForm = NVPToArray($_REQUEST['username']);
		$myObj = new Users();
		$Result=$myObj->ChangePassword($dataForm);
		exit(1);
			
	}
	
	public function ownerregistrationAction()
	{
		global $mySession;
		$db=new Db();
		$this->view->pageTitle="Property Owner Registration";
                $this->view->headMeta("Deal A Trip Customers and Property Owner Login and Registration", 'description');
		$myform=new Form_User();
		$varsuccess = 0;
		if ($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			if ($myform->isValid($request->getPost()))
			{	
				$dataForm=$myform->getValues();
				//prd($dataForm);
				$sql = "select * from ".USERS." where email_address = '".trim($dataForm['email_address'])."'";
				$chkArr = $db->runQuery($sql);
				if(count($chkArr) == 0)
				{
					$myObj = new Users();
					$Result=$myObj->SaveUser($dataForm, 2);
					$mySession->sucessMsg = "Successfully registered";
					$varsuccess = 1;
				}
				else
				{
					$mySession->errorMsg = "This username or email address already exist";		
				}
			}
			else
			{
				$mySession->errorMsg = "Image not proper";	
			}
			
		}
		$this->view->varsuccess = $varsuccess;
		$this->view->myform=$myform;
		$this->view->myform=$myform;		
		
	}
	
	public function processownerAction()
	{
		global $mySession;
		$db=new Db();
		$dataForm = NVPToArray($_REQUEST['username']);
		$photo = $_REQUEST['photo'];
		$dataForm['photo'] = $photo;
		$myObj = new Users();
		$Result=$myObj->SaveUser($dataForm, 2);
		if($Result > 0)
		exit("s");
		else
		exit("f");	
		
	}
	/*** Receive password that is auto generated password  ***/
	/*** User receives password via mail  ***/

	public function receivepasswordAction()
	{
		global $mySession;
		$db=new Db();		
		$requestId = $this->getRequest()->getParam("requestId");
		$this->view->requestId = $requestId;
		//$send = $this->getRequest()->getParam("send");
		//$this->view->send = $send;
		$varsuccess = 0;
		$chkData=$db->runQuery("select * from ".USERS." where password_reset='".$requestId."'");
		if($chkData!="" and count($chkData)>0)
		{	
			$myObj = new Users();

			$Result=$myObj->ReceivePassword($chkData[0]);
			$mySession->sucessMsg = "Password change information has been sent successfully";
			
		}
		else
		{
			$this->view->requestId='Expire';
			$mySession->sucessMsg = "The page you are looking for has been expired";
		}	
			
	}

}
?>