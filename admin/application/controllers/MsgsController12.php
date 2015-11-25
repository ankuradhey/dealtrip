<?php
__autoloadDB('Db');
class MsgsController extends AppController
{
	public function indexAction()
	{
	global $mySession;
	
	$this->view->pageHeading="View All Messages";
	}
	
	public function viewmsgsAction()
	{ //$db=new Db();
//	  $id=$this->getRequest()->getParam('msgsId');
//	  
//	  $qry="SELECT * , ( SELECT concat( first_name, ' ', last_name )
//		FROM ".USERS."
//		WHERE user_id = myMsg.sender_id
//		) AS sender_name, (
//		SELECT concat( first_name, ' ', last_name )
//		FROM ".USERS."
//		WHERE user_id = myMsg.receiver_id
//		) AS receiver_name
//		FROM ".MESSAGES." AS myMsg where myMsg.message_id= ".$id;
//		
//	  $result=$db->runQuery($qry);
//	  $this->view->result=$result;	  
//	  $this->view->pageheading="View Message";
	
	}
	public function generatemsgsgridAction()
	{
		global $mySession;
		$this->_helper->viewRenderer->setNoRender();
		$db=new Db();
//		$uType=$this->getRequest()->getParam('uType');
		$page=$this->getRequest()->page;
		$rp=$this->getRequest()->rp;
		$sortname=$this->getRequest()->sortname;
		$sortorder=$this->getRequest()->sortorder;
		if (!$sortname) $sortname = 'message_id';
		if (!$sortorder) $sortorder = 'asc';		
		$where="where 1=1 ";
		if(@$_POST['query']!='')
		{
			$where .= " and LOWER(".$_POST['qtype'].") LIKE '%".strtolower($_POST['query'])."%' ";					
		}
		$sort = "ORDER BY $sortname $sortorder";					
		if (!$page) $page = 1;
		if (!$rp) $rp = 10;		
		$start = (($page-1) * $rp);		
		$limit = "LIMIT $start, $rp";
		$qry="SELECT * , ( SELECT concat( first_name, ' ', last_name )
		FROM ".USERS."
		WHERE user_id = myMsg.sender_id
		) AS sender_name, (
		SELECT concat( first_name, ' ', last_name )
		FROM ".USERS."
		WHERE user_id = myMsg.receiver_id
		) AS receiver_name
		FROM ".MESSAGES." AS myMsg";
		
//		$qry="select * from ".MESSAGES;  
		$ResData=$db->runQuery("$qry $where $sort $limit");		

		$countQuery=$db->runQuery("$qry $where");
		$total=count($countQuery);		
		$json = "";
		$json .= "{\n";
		$json .= "page: $page,\n";
		$json .= "total: $total,\n";
		$json .= "rows: [";
		$rc = false;
		if(isset($ResData[0]) && $ResData[0]['sender_id']!="" &&  $ResData[0]['receiver_id']!="" )
		{
		$start=$start+1;
		$i=1;
		foreach($ResData as $row)
		{
			if ($rc) $json .= ",";
			$json .= "\n{";
			$json .= "id:'".$row['message_id']."',";
			$json .= "cell:['".$start."'";
			$json .= ",'<input name=\'check".$i."\' id=\'check".$i."\' value=\'".$row['message_id']."\' onchange=\'return check_check(\"bcdel\",\"deletebcchk\")\' type=\'checkbox\'>
			<script>$(\'#bcdel\').html(\'\');document.getElementById(\'deletebcchk\').checked = false;</script>'";
	
		$json .= ",'".$row['sender_name']."'";
			$json .= ",'".$row['receiver_name']."'";
			$json .= ",'".$row['date_message_sent']."'";			

	//		$json .= ",'<a href=\'".APPLICATION_URL_ADMIN."msgs/viewmsgs/msgsId/".$row['message_id']."\'><img src=\'".IMAGES_URL_ADMIN."view_review.png\' border=\'0\' title=\'View Msgs\' alt=\'View Msgs\'></a>'";

$json .= ",'<a href=\'".APPLICATION_URL_ADMIN."jobs/view/jobId/".$row['job_id']."\'><img src=\'".IMAGES_URL_ADMIN."view_review.png\' border=\'0\' title=\'View Jobs\' alt=\'View Jobs\'></a>'";

			//$json .= ",'<a  href=\'".APPLICATION_URL_ADMIN."msgs/viewmsgs/msgsId/".$row['message_id']."\'><img src=\'".IMAGES_URL_ADMIN."view_review.png\' border=\'0\' title=\'View Msgs\' alt=\'View Msgs\'></a>'";

			$json .= "]}";
			$rc = true;
			$start++;
			$i++;
		}
		}
		$json .= "]\n";
		$json .= "}"; 
		echo $json; 
		exit();
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
	/*public function edituserAction()
	{
	global $mySession;
	$db=new Db();
	$userId=$this->getRequest()->getParam('userId'); 
	$this->view->userId=$userId;
	$userData=$db->runQuery("select * from ".USERS." where user_id='".$userId."'");
	$this->view->SignUpfor=$userData[0]['user_type'];
	$this->view->profileImage=$userData[0]['profile_image'];
	$this->view->pageHeading="Edit User - ".$userData[0]['first_name']." ".$userData[0]['last_name'];
	$myform=new Form_User($userData[0]['user_type'],$userId);
	$this->view->myform=$myform;
	}
	public function updateuserAction()
	{
		global $mySession;
		$db=new Db();
		$userId=$this->getRequest()->getParam('userId'); 
		$this->view->userId=$userId;
		$userData=$db->runQuery("select * from ".USERS." where user_id='".$userId."'");
		$this->view->SignUpfor=$userData[0]['user_type'];
		$this->view->profileImage=$userData[0]['profile_image'];
		$this->view->pageHeading="Edit User - ".$userData[0]['first_name']." ".$userData[0]['last_name'];
		if ($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			$myform=new Form_User($userData[0]['user_type'],$userId);
			if ($myform->isValid($request->getPost()))
			{				
				$dataForm=$myform->getValues();
				$myObj=new Users();
				$Result=$myObj->UpdateUser($dataForm,$userData[0]['user_type'],$userId);
				if($Result==1)
				{
				$mySession->errorMsg ="User details updated successfully.";
				$this->_redirect('users/index/uType/'.$userData[0]['user_type']);
				}
				else
				{
				$mySession->errorMsg ="Email address you entered is already exists.";
				$this->view->myform = $myform;
				$this->render('edituser');
				}
			}
			else
			{	
				$this->view->myform = $myform;
				$this->render('edituser');
			}
		}
		else
		{			
			$this->_redirect('users/edituser/userId/'.$userId);
		}
	}
	public function deleteuserAction()
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
					$condition1="user_id='".$Id."'"; 
					$db->delete(USERS,$condition1);
				}
			}
		}		
		exit();
	}
	public function changeuserstatusAction()
	{  
	  global $mySession;
	  $db=new Db(); 
	  $BcID=$_REQUEST['Id']; 
	  $status=$_REQUEST['Status'];
	  if($status=='1')
	  { 
	  $status = '0';
	  }
	  else 
	  { 
	  $status = '1';
	  } 
	  if($_REQUEST['For']=='1'){
	  $data_update['user_status']=$status; 
	  }
	  else
	  {
	  $data_update['is_active']=$status; 
	  }
	  $condition="user_id='".$BcID."'";
	  $db->modify(USERS,$data_update,$condition);
	    
	  exit();
	}
	
	
	
	public function subscriptionsAction()
	{
		global $mySession;		
		$uType=$this->getRequest()->getParam('uType');
		$this->view->uType=$uType;
		$userId=$this->getRequest()->getParam('userId');
		$this->view->userId=$userId;
		$this->view->pageHeading="User Subscriptions";
	}
	public function generatesubscriptionsgridAction()
	{
		global $mySession;
		$this->_helper->viewRenderer->setNoRender();
		$db=new Db();
		$uType=$this->getRequest()->getParam('uType');
		$userId=$this->getRequest()->getParam('userId');
		$page=$this->getRequest()->page;
		$rp=$this->getRequest()->rp;
		$sortname=$this->getRequest()->sortname;
		$sortorder=$this->getRequest()->sortorder;
		if (!$sortname) $sortname = 'date_purchase';
		if (!$sortorder) $sortorder = 'desc';		
		$where="where 1=1 and ".USER_SUBSCRIPTIONS.".user_id='".$userId."'";
		if(@$_POST['query']!='')
		{
			$where .= " and LOWER(".$_POST['qtype'].") LIKE '%".strtolower($_POST['query'])."%' ";					
		}
		$sort = "ORDER BY $sortname $sortorder";					
		if (!$page) $page = 1;
		if (!$rp) $rp = 10;		
		$start = (($page-1) * $rp);		
		$limit = "LIMIT $start, $rp";
		$qry="select ".USER_SUBSCRIPTIONS.".*,first_name,last_name,".SUBSCRIPTIONS.".plan_name from ".USER_SUBSCRIPTIONS."
		join ".SUBSCRIPTIONS." on(".USER_SUBSCRIPTIONS.".plan_id=".SUBSCRIPTIONS.".plan_id)
		join ".USERS." on(".USER_SUBSCRIPTIONS.".user_id=".USERS.".user_id)";
		$ResData=$db->runQuery("$qry $where $sort $limit");		
		$countQuery=$db->runQuery("$qry $where");
		$total=count($countQuery);		
		$json = "";
		$json .= "{\n";
		$json .= "page: $page,\n";
		$json .= "total: $total,\n";
		$json .= "rows: [";
		$rc = false;
		if(isset($ResData[0]) && $ResData[0]['user_id']!="")
		{
		$start=$start+1;
		$i=1;
		foreach($ResData as $row)
		{
			if ($rc) $json .= ",";
			$json .= "\n{";
			$json .= "id:'".$row['user_id']."',";
			$json .= "cell:['".$start."'";
			$json .= ",'".$row['first_name']."'";
			$json .= ",'".$row['last_name']."'";
			$json .= ",'".$row['plan_name']."'";
			//$json .= ",'".$row['amount_paid']."'";
			if($row['amount_paid']==0)
			{
				$json .= ",'Free'";
			}
			
			//$json .= ",'".$row['expire_date']."'";
			if($row['expire_date']==0)
			{
				$json .= ",'No Date'";
			}
			if($row['purchase_status']=='1')
			{
			$json .= ",'Purchase'";
			}
			if($row['purchase_status']=='2')
			{
			$json .= ",'Renew'";
			}
			if($row['purchase_status']=='3')
			{
			$json .= ",'Upgrade'";
			}
			$json .= "]}";
			$rc = true;
			$start++;
			$i++;
		}
		}
		$json .= "]\n";
		$json .= "}"; 
		echo $json; 
		exit();
	}*/
}
?>