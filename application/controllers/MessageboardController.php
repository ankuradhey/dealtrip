<?php
__autoloadDB('Db');
class MessageboardController extends AppController
{
	
	public function inboxAction()
	{
		global $mySession;
		$db=new Db();
		$this->view->pageTitle="Inbox";
		
		$qry="select *,admin_fullname as SenderName from ".MESSAGES." 
		join ".ADMINISTRATOR." ON(".MESSAGES.".sender_id= ".ADMINISTRATOR.".user_id)
		where receiver_id='".$mySession->LoggedUserId."' and ".MESSAGES.".del_receiver = '0' ORDER BY date_message_sent DESC";
		$countData=$db->runQuery($qry);
		
		
		
		$messagesData=$db->runQuery($qry);		
		
		$this->view->messagesData=$messagesData;
		$this->view->totalRecords=count($countData);
	}
	
	public function outboxAction()
	{
		global $mySession;
		$db=new Db();
		$this->view->pageTitle="Message Board - Outbox";

		$qry="select *,".ADMINISTRATOR.".admin_fullname as ReceiverName from ".MESSAGES." 
		join ".ADMINISTRATOR." ON(".MESSAGES.".receiver_id = ".ADMINISTRATOR.".user_id)
		where ".MESSAGES.".sender_id='".$mySession->LoggedUserId."' and ".MESSAGES.".del_sender = '0' 
		ORDER BY ".MESSAGES.".date_message_sent DESC";

		$countData = $db->runQuery($qry);
		
	
		$messagesData=$db->runQuery($qry);		
		

		
		$this->view->messagesData=$messagesData;
		$this->view->totalRecords=count($countData);
	}
	
	
	public function composeAction()
	{
		global $mySession;
		$db=new Db();
		$this->view->pageTitle="Compose Message";
	 
		$myform=new Form_Compose(1);

		if ($this->getRequest()->isPost())
		{
	
			$request=$this->getRequest();
			 
			if ($myform->isValid($request->getPost()))
			{	
				$dataForm=$myform->getValues();
				$dataForm['receiver_id']=$_REQUEST['receiver_id'];
				$dataMessage['sender_id']=$mySession->LoggedUserId;
				$dataMessage['receiver_id']=$dataForm['receiver_id'];
				$dataMessage['message_subject']=$_REQUEST['message_subject'];
				$dataMessage['message_text']=$dataForm['message_text'];
				
				$dataMessage['date_message_sent']=date('Y-m-d H:i:s');
				$db->save(MESSAGES,$dataMessage);
	
				$mySession->sucessMsg ="Message sent successfully.";
				$this->_redirect('messageboard/outbox');
				 
			}
			 
		}
		 
		$this->view->myform=$myform;
	}
	public function deletemessageAction()
	{
		global $mySession;
		$db=new Db();
		
		$this->_helper->layout()->disableLayout();
		$messageId=$this->getRequest()->getParam('messageId');
		$del=$this->getRequest()->getParam('del');

		
		if($messageId != "")
		{
			$messageData=$db->runQuery("select * from ".MESSAGES." where md5(message_id)='".$messageId."'");
			if($messageData!="" and count($messageData)>0)
			{
				//receiver_status
				if($del=='inbox')
				{
					if($messageData[0]['del_sender']=='1')
					{
						$delCondition="md5(message_id)='".$messageId."'";
						$db->delete(MESSAGES,$delCondition);
					}
					else
					{
						$dataDelete['del_receiver']='1';
						$updateCondition="md5(message_id)='".$messageId."'";
						$db->modify(MESSAGES,$dataDelete,$updateCondition);
					}
					
				}
				if($del=='outbox')
				{
					//sender_status
					if($messageData[0]['del_receiver']=='1')
					{
						$delCondition="md5(message_id)='".$messageId."'";
						$db->delete(MESSAGES,$delCondition);
					}
					else
					{
						$dataDelete['del_sender']='1';
						$updateCondition="md5(message_id)='".$messageId."'";
						$db->modify(MESSAGES,$dataDelete,$updateCondition);
					}
				}
			}
			$mySession->sucessMsg ="Message deleted successfully.";
			
			$this->_redirect('messageboard/'.$del);
		}
		else
		{
			
			if($_REQUEST['Id']!="")
			{
				$arrId=explode("|",$_REQUEST['Id']);
				if(count($arrId)>0)
				{
					
					foreach($arrId as $key=>$Id)
					{
							if($del=='inbox')
							{
								if($messageData[0]['del_sender']=='1')
								{
									$delCondition="md5(message_id)='".$Id."'";
									$db->delete(MESSAGES,$delCondition);
								}
								else
								{
									$dataDelete['del_receiver']='1';
									$updateCondition="md5(message_id)='".$Id."'";
									$db->modify(MESSAGES,$dataDelete,$updateCondition);
								}
								
							}//inbox ends
							if($del=='outbox')
							{
								//sender_status
								if($messageData[0]['del_receiver']=='1')
								{
									$delCondition="md5(message_id)='".$Id."'";
									$db->delete(MESSAGES,$delCondition);
								}
								else
								{
									$dataDelete['del_sender']='1';
									$updateCondition="md5(message_id)='".$Id."'";
									$db->modify(MESSAGES,$dataDelete,$updateCondition);
								}
							}//outbox ends	
						
					}//loop ends
				}//if count > 0
			}//if id is not empty			
			
			
		}// if multiple delete


		exit;
		//$this->_redirect('messageboard/'.$del);
	}
	public function viewmessageAction()
	{
		global $mySession;
		$db=new Db();
		$this->_helper->layout->setLayout('simplecontent');
		$messageId=$this->getRequest()->getParam('messageId');
		
		$messageData=$db->runQuery("select * from ".MESSAGES." 
									inner join ".ADMINISTRATOR." on ".ADMINISTRATOR.".user_id = ".MESSAGES.".sender_id 
									where md5(message_id)='".$messageId."'");
									

		$this->view->messageData=$messageData[0];
		$this->view->pageTitle=$messageData[0]['message_subject'];
		
		if($messageData[0]['read'] == '0')
		{
			$data_update['read'] = '1';
			$condition = "md5(message_id)='".$messageId."'";
			$db->modify(MESSAGES,$data_update,$condition);	
		}


	}
	
	public function viewsentmessageAction()
	{
		global $mySession;
		$db=new Db();
		$this->_helper->layout->setLayout('simplecontent');
		$messageId=$this->getRequest()->getParam('messageId');
		
		$messageData=$db->runQuery("select * from ".MESSAGES." 
								    left join ".USERS." on ".USERS.".user_id = ".MESSAGES.".receiver_id
									left join ".ADMINISTRATOR." on ".ADMINISTRATOR.".user_id = ".MESSAGES.".receiver_id
									 where md5(message_id)='".$messageId."'");

		$this->view->messageData=$messageData[0];
		$this->view->pageTitle=$messageData[0]['message_subject'];


	}
	
}
?>