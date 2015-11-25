<?php
__autoloadDB('Db');
class SigninController extends AppController
{
	public function indexAction()
	{
		global $mySession;
		$db=new Db();
	
		$this->view->pageTitle="Customer Sign In";
                $this->view->headMeta("Deal A Trip Customers and Property Owner Login and Registration", 'description');
		$myform=new Form_Signin();
		if($mySession->LoggedUserType=="1")
		{
			$this->_redirect("myaccount");			
		}
		if ($this->getRequest()->isPost())
		{
	
			$request=$this->getRequest();
			 
			if ($myform->isValid($request->getPost()))
			{	
				$dataForm=$myform->getValues();
				$myObj = new Users();
				$result = $myObj->CheckLogin($dataForm);	
		
				if($result!="" and count($result)>0 && $result[0]['user_status']==1 && $result[0]['uType']==1)
				{
					$mySession->LoggedUserId = $result[0]['user_id'];
					$mySession->LoggedUserType = $result[0]['uType'];
					$mySession->LoggedUserName = $result[0]['email_address'];
					$mySession->LoggedUser  = $result[0];
					$this->_redirect("myaccount");
				}
				else
				{
					$mySession->errorMsg = "username or password incorrect";
					//$this->view->error = "username or password incorrect";
				}
				
				
									
			}
			
			
		}
		$this->view->myform=$myform;
		
	}
	public function checkusernameAction()
	{
		global $mySession;
		$db = new Db();
		//$username = $_REQUEST['username'];
		//$password = $_REQUEST['password'];
		$dataForm = NVPToArray($_REQUEST['Data']);
		$myObj = new Users();
		$result = $myObj->CheckLogin($dataForm);	
		
		if($result!="" and count($result)>0 && $result[0]['user_status']==1)
		{
				$mySession->LoggedUserId = $result[0]['user_id'];
				
				exit("1");
		}
		else
		{
				exit("0"); 	
		}
	
	
	}
        
        public function signoutAction()
	{
		global $mySession;
		$db = new Db();
		unsetSessionVars();
		$this->_redirect("")	;
		
	}
	
	public function loginAction()
	{
		global $mySession;
		$db=new Db();
	
		$this->view->pageTitle="Property Owner Sign In";
		$myform=new Form_Signin();
                $this->view->headMeta("Deal A Trip Customers and Property Owner Login and Registration", 'description');
		
		
		

		if ($this->getRequest()->isPost())
		{
	
			$request=$this->getRequest();
			 
			if ($myform->isValid($request->getPost()))
			{	
				$dataForm=$myform->getValues();
				//prd($dataForm);
				$myObj = new Users();
				$result = $myObj->CheckLogin($dataForm);	
		
				if($result!="" and count($result)>0 && $result[0]['user_status']==1 && $result[0]['uType']==2)
				{
						$mySession->LoggedUserId = $result[0]['user_id'];
						$mySession->LoggedUserType = $result[0]['uType'];
						$mySession->LoggedUserName = $result[0]['username'];
						$mySession->LoggedUser  = $result[0];
						$this->_redirect("myaccount");
						
						
				}
				else
				{
					$mySession->errorMsg = "username or password incorrect";
					//$this->view->error = "username or password incorrect";
				}
				
				
									
			}
			
			
		}
		$this->view->myform=$myform;
		
	}
	/*public function sendactivationAction()
	{
		global $mySession;
		$db=new Db();
		$UserId= $this->getRequest()->getParam('userid');
		$sql="select * from ".USERS." where user_id= ".$UserId;
		$result=$db->runQuery($sql);		
		$fullName=$result[0]['first_name'].' '.$result[0]['last_name'];
		$Url='<a href="'.APPLICATION_URL.'">'.APPLICATION_URL.'</a>';
		$ActivationLink='<a href="'.APPLICATION_URL.'signup/activate/cId/'.md5($UserId).':'.$SignUpfor.'">'.APPLICATION_URL.'signup/activate/cId/'.md5($UserId).'</a>';		
		$templateData=$db->runQuery("select * from ".EMAIL_TEMPLATES." where template_id='2'");
		$messageText=$templateData[0]['email_body'];
		$subject=$templateData[0]['email_subject'];				
		$messageText=str_replace("[NAME]",$fullName,$messageText);
		$messageText=str_replace("[SITENAME]",SITE_NAME,$messageText);
		$messageText=str_replace("[LOGINNAME]",$dataForm['email_address'],$messageText);
		$messageText=str_replace("[PASSWORD]",$dataForm['password_o'],$messageText);
		$messageText=str_replace("[SITEURL]",$Url,$messageText);
		$messageText=str_replace("[ACTIVATIONLINK]",$ActivationLink,$messageText);		
//				echo "hello"; exit();
		SendEmail($result[0]['email_address'],$subject,$messageText);	
		$mySession->errorMsg ="Activation link is sent to your email id";
		$this->_redirect('signin/index');

	}
	public function processAction()
	{
		global $mySession;
		$db=new Db();
		$this->view->pageTitle="Sign In";
		if ($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			$myform = new Form_Signin();			
			if ($myform->isValid($request->getPost()))
			{				
				$dataForm=$myform->getValues();
				$myObj=new Users();
				$result=$myObj->CheckLogin($dataForm);

				if($result!="" and count($result)>0)
				{
					if($result[0]['is_active']==1)
					{
						$mySession->LoggedUserId=$result[0]['user_id'];
						if($mySession->OneBackUrl=="")
						{
						 $this->_redirect('myaccount/index');
						}
						else
						{
						  $this->_redirect($mySession->OneBackUrl);	
						}
					}
					else
					{ 
					 	$mySession->errorMsg="Your account is not activated .Please click following link to get activation link .. <br><a href='".APPLICATION_URL."signin/sendactivation/userid/".$result[0]['user_id']."'> Click Here to Get activation Link</a>";
						 $this->_redirect('signin/index');
					}
				}
				else
				{
					$mySession->errorMsg ="Invalid email address or password.";
					$this->view->myform = $myform;
					$this->render('index');
				}
			}
			else
			{	
				$this->view->myform = $myform;
				$this->render('index');
			}
		}
		else
		{			
			$this->_redirect('signin/index');
		}
	}*/
	
	
	

}
?>