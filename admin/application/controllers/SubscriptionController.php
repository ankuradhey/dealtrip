<?php
__autoloadDB('Db');

class SubscriptionController extends AppController
{
	
		public function indexAction()
	{
		global $mySession;
		$db=new Db();
		$this->view->pageHeading="Manage Subscriptions Plans";
		$qry=$db->runquery("select * from ".SUBSCRIPTIONS."");
		$this->view->sql=$qry;  
	}
	
	public function addplanAction()
	{
	global $mySession;
	$myform=new Form_Subscription();
	$this->view->myform=$myform;
	$this->view->pageHeading="Add Subscription Plan";
	}
	public function saveplanAction()
	{
		global $mySession;
		$db=new Db();
		
		$this->view->pageHeading="Add Subscription Plan";
		if ($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			$myform = new Form_Subscription();	
					
			if ($myform->isValid($request->getPost()))
			{				
				$dataForm=$myform->getValues();
				$myObj=new System();
				
				$Result=$myObj->SavePlan($dataForm);
				if($Result==1)
				{
				$mySession->errorMsg ="Subscription plan added successfully.";
				$this->_redirect('subscription/index');
				}
				else
				{
				$mySession->errorMsg ="Subscription plan name you entered is already exists.";
				$this->view->myform = $myform;
				$this->render('addplan');
				}
			}
			else
			{	
				$this->view->myform = $myform;
				$this->render('addplan');
			}
		}
		else
		{			
			$this->_redirect('system/addplan');
		}
	}
	public function editplanAction()
	{
	global $mySession;
	$db=new Db();
	$planId=$this->getRequest()->getParam('planId'); 
	$this->view->planId=$planId;
	$planData=$db->runQuery("select * from ".SUBSCRIPTIONS." where plan_id='".$planId."'");
	$this->view->planImage=$planData[0]['plan_image'];
	$myform=new Form_Subscription($planId);
	$this->view->myform=$myform;
	$this->view->pageHeading="Edit Subscription Plan";
	}
	public function updateplanAction()
	{
		
		global $mySession;
		$db=new Db();
		$planId=$this->getRequest()->getParam('planId'); 
		$this->view->planId=$planId;
		$planData=$db->runQuery("select * from ".SUBSCRIPTIONS." where plan_id='".$planId."'");
		$this->view->planImage=$planData[0]['plan_image'];
		$this->view->pageHeading="Edit Subscription Plan";
		if ($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			$myform = new Form_Subscription($planId);	
					
			if ($myform->isValid($request->getPost()))
			{				
				$dataForm=$myform->getValues();
				$myObj=new System();
				
				$Result=$myObj->UpdatePlan($dataForm,$planId);
				if($Result==1)
				{
				$mySession->errorMsg ="Subscription plan updated successfully.";
				$this->_redirect('subscription/index');
				}
				else
				{
				$mySession->errorMsg ="Subscription plan name you entered is already exists.";
				$this->view->myform = $myform;
				$this->render('editplan');
				}
			}
			else
			{	
				$this->view->myform = $myform;
				$this->render('editplan');
			}
		}
		else
		{			
			$this->_redirect('subscription/editplan/planId/'.$planId);
		}
	}
	public function changeplanstatusAction()
		{
			 global $mySession;
	  $db=new Db(); 
	  
	  $BcID=$this->getRequest()->getParam('planId');
	  $status=$this->getRequest()->getParam('Status');
	  
	  	
	  if($status=='1')
	  { 
	   
	  $status = '0';
	  }
	  else 
	  { 
	 
	  $status = '1';
	  } 
	 $data_update['plan_status']=$status; 
	 $condition="plan_id='".$BcID."' ";
	  $db->modify(SUBSCRIPTIONS,$data_update,$condition);
	  
	  if($db)
	  {
		  $mySession->errorMsg ="Status Changed successfully.";
			$this->_redirect('subscription/index');
			
	  }
	  
	    
	  exit();
		
			
		}
	public function deleteplanAction()
	{
		
		
		global $mySession;
		$db=new Db();
		
		
		
		if($_POST['checkbox']>0)
		{
		foreach(($_POST['checkbox']) as $key=>$valueArr)
			{
			$a=count($_POST['checkbox']);
			echo $condition1="plan_id='".$valueArr."'";
			$db->delete(SUBSCRIPTIONS,$condition1);
			}
			$mySession->errorMsg ="Subscriptions deleted successfully.";
			$this->_redirect('subscription/index');
		}
	}
		
		

	
}