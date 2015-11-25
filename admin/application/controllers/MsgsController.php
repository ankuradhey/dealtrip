<?php
__autoloadDB('Db');
class MsgsController extends AppController
{
	public function indexAction()
	{
	global $mySession;
	$db=new Db();
	$this->view->pageHeading="Messages";
	
	$qry="SELECT * , ( SELECT concat( first_name, ' ', last_name )
		FROM ".USERS."
		WHERE user_id = myMsg.sender_id
		) AS sender_name, (
		SELECT admin_username
		FROM ".ADMINISTRATOR."
		WHERE admin_id = '1' 
		) AS receiver_name
		FROM ".MESSAGES." AS  myMsg
		where myMsg.receiver_id = '0'
		order by myMsg.message_id desc
		";

		$ResData=$db->runQuery("$qry");
		$this->view->ResData=$ResData;	
	}
	
	public function viewmsgsAction()
	{ 
	   $db=new Db();
	  $id=$this->getRequest()->getParam('msgsId');
	  
	  $qry = "SELECT * , ( SELECT concat( first_name, ' ', last_name )
		FROM ".USERS."
		WHERE user_id = myMsg.sender_id
		) AS sender_name, (
		SELECT admin_username
		FROM ".ADMINISTRATOR."
		WHERE user_id = myMsg.receiver_id
		) AS receiver_name
		FROM ".MESSAGES." AS myMsg where myMsg.message_id= ".$id;


	  $result = $db->runQuery("$qry");
	  
	  if($result[0]['read'] == '0')
	  {
			$data_update['read'] = '1';
			$condition = "message_id ='".$id."'";
			$db->modify(MESSAGES,$data_update,$condition);	
	  }
	 
	  $this->view->result=$result;	  
	  $this->view->pageHeading="View Message";
	
	}
	
	
	public function viewsentmsgsAction()
	{ 
	   $db=new Db();
	  $id=$this->getRequest()->getParam('msgsId');
	  
	  $qry = "SELECT * , ( SELECT concat( first_name, ' ', last_name )
		FROM ".USERS."
		WHERE user_id = myMsg.receiver_id
		) AS receiver_name
		FROM ".MESSAGES." AS myMsg where myMsg.message_id= ".$id;


	  $result=$db->runQuery("$qry");
	 
	  $this->view->result=$result;	  
	  $this->view->pageHeading="View Message";
	
	}
	
	public function sentAction()
	{
		$db = new Db();
		global $mySession;
		$this->view->pageHeading = "Sent Message";

		$qry = "SELECT * , (select admin_username
		FROM ".ADMINISTRATOR."
		WHERE user_id = myMsg.sender_id)
		 AS sender_name, (
		SELECT concat(first_name, ' ', last_name)
		FROM ".USERS."
		WHERE user_id = myMsg.receiver_id
		) AS receiver_name
		FROM ".MESSAGES." AS myMsg
		where myMsg.sender_id = '0' 
		and myMsg.del_sender = '0'
		order by myMsg.message_id desc 
		"; 
		
		
		$sentArr = $db->runQuery($qry);	
		
		$this->view->result = $sentArr;
		
	}
	
	public function deletemsgsAction()
	{
		global $mySession;
		$db=new Db();
		if($_REQUEST['Id']!="")
		{
			$arrId=explode("|",$_REQUEST['Id']);
			if(count($arrId)>0)
			{
				foreach($arrId as $key=>$Id)
				{
					$condition1="message_id='".$Id."'"; 
					$db->delete(MESSAGES,$condition1);
				}
			}
		}		
		exit();
	}
	
	public function composeAction()
	{
		global $mySession;
		$db = new Db();
		$userId = $this->getRequest()->getParam("rId");
		
		
		$myform = new Form_Compose(0,$userId!=""?$userId:"");
		$this->view->myform = $myform;
		$this->view->pageHeading="Compose Message";
		
		
		if($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			if ($myform->isValid($request->getPost()))
			{				
				$dataForm = $myform->getValues();
				$myObj = new Message();		
				$Result = $myObj->sendMessage($dataForm);
				if($Result == 1)
				{
					$mySession->sucessMsg = "Message Sucessfully sent";
					$this->_redirect("msgs/sent");
				}
				else
				$mySession->errorMsg = "Error in sending message";
			}
			
		
		}

	}
	
}
?>