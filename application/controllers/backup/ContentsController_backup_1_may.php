<?php
__autoloadDB('Db');
class ContentsController extends AppController
{
	public function pagesAction()
	{
		global $mySession;
		$db=new Db();
		$slug = $this->getRequest()->getParam("slug");
		
		$userArr = $db->runQuery("select * from ".USERS." where user_id = '".$mySession->LoggedUserId."' ");
		$sql = "select * from ".PAGES1." where synonyms = '$slug'";
		
		
		$staticArr = $db->runQuery($sql);
		
		

		$strContent=str_replace("[SITEURL]",APPLICATION_URL,$staticArr[0]['page_content']);
		$strContent=str_replace("[FULLNAME]",$userArr[0]['email_address'],$strContent); 
		
		if(!isLogged()): 
		$strContent= str_replace("[LOGINMSG]","<a href='".APPLICATION_URL."/signin'>click here</a> to Login",$strContent); 
		else:
		$strContent= str_replace("[LOGINMSG]","",$strContent); 
		endif;

		$this->view->pageContent = $strContent;
		$this->view->pageTitle = $staticArr[0]['page_title'];
		
		$this->view->page_id = $staticArr[0]['page_id'];
		
		if($staticArr[0]['page_id'] == '12' || $staticArr[0]['page_id'] == '63')
		{
			$myform = new Form_Contact();
			$this->view->myform = $myform;	
		}
		
		if($staticArr[0]['page_id'] == '80' )
		{
			$myform = new Form_Ocontact();
			$this->view->myform = $myform;	
		}
		
		$varsuccess = 0; 
		if ($this->getRequest()->isPost())
		{

			$request=$this->getRequest();
			$dataForm=$myform->getValues();
			if ($myform->isValid($request->getPost()))
			{	
				$dataForm=$myform->getValues();
				//prd($dataForm);
				if($dataForm['captcha'] == $_SESSION['captcha'])
				{
					$myObj = new Users();
					if($staticArr[0]['page_id'] == '80')
					$Result=$myObj->ownercontactus($dataForm);
					else
					$Result=$myObj->contactus($dataForm);
					$mySession->sucessMsg = "Thank you, You will soon be contacted";
					$varsuccess = 1;
				}
				else
				$mySession->errorMsg = "Human Verification code is wrong";
			}
			
			
		}
		$this->view->myform = $myform;	
		$this->view->varsuccess = $varsuccess;

	}
	

}
?>