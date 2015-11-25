<?php
__autoloadDB('Db');
class SubadminController extends AppController
{
	public function indexAction()
	{   
		global $mySession; 
	    $db=new Db();
		$this->view->pageHeading="Sub Administrator";   		
	    $Obj=new Subadmin(); 
	    $sql=$db->runquery("select * from ".SUBADMIN." ");

	    $this->view->sql=$sql;	
		

	}
	public function addnewAction()
	{
		global $mySession; 
	    $db=new Db();
		$this->view->pageHeading="Add New Sub admin";   		
	
		$myform=new Form_Subadmin();
		$this->view->myform=$myform;
	}
	
	public function savenewAction()
	{
		global $mySession; 
	    $db=new Db();
		$this->view->pageTitle="Add Subadmin";
		if ($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			$myform = new Form_Subadmin();	
		
			if ($myform->isValid($request->getPost()))
			{
				$dataForm=$myform->getValues();				
				$myObj=new Subadmin();
				$Result=$myObj->SaveSubadmin($dataForm);
												
					if($Result==1)
					{
					$mySession->errorMsg ="New subadmin added successfully.";
					$this->_redirect('subadmin/index');
					}
					else
					{
					$mySession->errorMsg ="Subadmin email address you have entered already exists.";
					$this->view->myform = $myform;
					$this->render('addnew');
					}
			}
			else
			{	
				$this->view->myform = $myform;
				$this->render('addnew');
			}
		}
		else
		{			
			$this->_redirect('subadmin/addnew');
		}	  	
	}

	
	public function editAction()
	{
		global $mySession;
		$subsID=$this->getRequest()->getParam('subsID'); 
		$this->view->subsID=$subsID;
		$myform=new Form_Subadmin($subsID);

	$this->view->myform=$myform;
		$this->view->pageTitle="Edit Sub Admin Details";
	}
	
	public function updateAction()
	{
		global $mySession;
		$db=new Db();
		$subsID=$this->getRequest()->getParam('subsID'); 
		$this->view->subsID=$subsID;
		$this->view->pageTitle="Edit Sub Admin Details";
		
		if ($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			
			$myform=new Form_Subadmin($subsID);		
			
			
			if ($myform->isValid($request->getPost()))
			{					
				$dataForm=$myform->getValues();
				$myObj=new Subadmin();
				$Result=$myObj->UpdateSubadmin($dataForm,$subsID);

				if($Result==1)
				{
				$mySession->errorMsg ="Subadmin Details updated successfully.";
				$this->_redirect('subadmin/index');
	
				}
				else
				{
				$mySession->errorMsg ="email address you have entered already exists.";
				$this->view->myform = $myform;
				$this->render('edit');
				}
			}
			else
			{	
				$mySession->errorMsg ="Please enter valid entry.";
				echo "<script>parent.top.location='".APPLICATION_URL_ADMIN."subadmin/index';</script>";	
				exit();
			}
		}
		
	}


public function deletesubadminAction()
	{
		
		
		global $mySession;
		$db=new Db();
		
		
		
		if($_POST['checkbox']>0)
		{
		foreach(($_POST['checkbox']) as $key=>$valueArr)
			{
			$a=count($_POST['checkbox']);
			echo $condition1="subadmin_id='".$valueArr."'";
			$db->delete(SUBADMIN,$condition1);
			}
			$mySession->errorMsg ="Subadmin deleted successfully.";
			$this->_redirect('Subadmin/index');
		}
	}

	public function changestatusAction()
	{
	  global $mySession;
	  $db=new Db(); 
	  $subsID=$_REQUEST['AID']; 
	  $status=$_REQUEST['AStatus'];	  
	  if($status=='0')
	  { $status = '1';}
	  else if($status=='1'){ $status = '0';} 	 
	  $data_update['status']=$status; 	 
	  $condition="subadmin_id  ='".$subsID."'";
	  $db->modify(SUBADMIN,$data_update,$condition);
	  if($db)	  
	  $mySession->errorMsg ="Subadmin status updated successfully";
	  exit();		
	}	
}
?>