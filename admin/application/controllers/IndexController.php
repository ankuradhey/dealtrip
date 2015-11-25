<?php
__autoloadDB('Db');
class IndexController extends AppController
{
	public function indexAction()
	{
/*		global $mySession;
		$myform = new Form_Login();
		$this->view->myform=$myform;
		$this->_helper->layout->setLayout('login');
		$this->view->pageHeading="Login";*/	
	
		global $mySession;
		$db=new Db();
		$this->view->pageHeading="Login";
		$this->_helper->layout->setLayout('login');
		$myform = new Form_Login();			
		if ($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			if ($myform->isValid($request->getPost()))
			{				
				$dataForm=$myform->getValues();
				$chkLogin=$db->runQuery("select * from ".ADMINISTRATOR." where admin_username='".mysql_escape_string($dataForm['admin_username'])."' and admin_password='".mysql_escape_string(md5($dataForm['admin_password']))."'");
				if($chkLogin!="" and count($chkLogin)>0)
				{
					$mySession->adminId=$chkLogin[0]['admin_id'];
					$this->_redirect('dashboard');
				}
				else
				{
					$mySession->errorMsg ="Invalid username or password."; 
				}
			}
			 
		}
		$this->view->myform = $myform; 
	
	}
	 
	public function logoutAction()
	{ 	
		global $mySession;					
		$mySession->adminId="";		
		unset($mySession->adminId);
		$mySession->errorMsg ="You are successfully logged out.";
		$this->_redirect('index');
	}
}
?>