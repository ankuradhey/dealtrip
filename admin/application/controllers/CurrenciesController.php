<?php
__autoloadDB('Db');
class CurrenciesController extends AppController
{
	
	
	#-----------------------------------------------------------#
	# Currency Index Action Function
	#-----------------------------------------------------------#
	
	public function indexAction()
	{
		global $mySession;
		$this->view->pageHeading="Manage Currency";
	 	$db=new Db();
		$qry="select * from ".CURRENCY;  
		$this->view->ResData=$db->runQuery("$qry");
		
		
	}
	
	
	#-----------------------------------------------------------#
	# Currency Add Action Function
	#-----------------------------------------------------------#
	
	public function addcurrencyAction()
	{
		global $mySession;
		$db=new Db();
		$myform=new Form_Currency();
		
		$this->view->pageHeading="Add New Currency";
		
	 
	
		  
		if($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			if($myform->isValid($request->getPost()))
			{
				$dataForm=$myform->getValues();
				$myObj=new Currencies();
				$result=$myObj->saveCurrency($dataForm);
				if($result==1)
				{
					$mySession->sucessMsg="New Currency Added Sucessfully";
					$this->_redirect('currencies/index');
				}
				else
				if($result==2)
				{
					$mySession->errorMsg ="This Currency Name Already Exists.";
					
					 
				}
				else
				{
					$mySession->errorMsg ="This Currency Code Already Exists.";
					 
				}
			}
			 
		}
		$this->view->myform=$myform;
	
	}
	
	#-----------------------------------------------------------#
	# Currency Edit Action Function
	#-----------------------------------------------------------#
	
	public function editcurrencyAction()
	{
		global $mySession;
		$db=new Db();
		
		$currencyId=$this->getRequest()->getParam('currencyId'); 
		$this->view->currencyId=$currencyId;
		$myform=new Form_Currency($currencyId);
		$this->view->pageHeading="Edit Currency";
		
		 
		
		if ($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			if ($myform->isValid($request->getPost()))
			{				
				$dataForm=$myform->getValues();
				$myObj=new Currencies();
				$result=$myObj->saveCurrency($dataForm,$currencyId);
				if($result==1)
				{
					$mySession->sucessMsg="Currency Updated Sucessfully";
					$this->_redirect('currencies/index');
				}
				else
				{
					$mySession->errorMsg ="This Currency Name Already Exists.";
					
				}
				
			}
			
		}
		 $this->view->myform=$myform;
		
	}
	
	
	
	#-----------------------------------------------------------#
	# Currency Delete Action Function
	#-----------------------------------------------------------#
	
	public function deletecurrencyAction()
	{
		global $mySession;
		$db=new Db();
		if($_REQUEST['Id']!="")
		{
			$arrId=explode("|",$_REQUEST['Id']);
			if(count($arrId)>0)
			{
				if($Id>1)
				{
					$myObj=new Currencies();
					$Result=$myObj->deleteCurrency($Id);
			    }
			}
		}		
		exit();
	}
	
	
}
?>