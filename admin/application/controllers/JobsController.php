<?php
__autoloadDB('Db');
class JobsController extends AppController
{
	public function indexAction()
	{
	global $mySession;
	$this->view->pageHeading="Manage Jobs";	
	}
	public function viewAction()
	{
		global $mySession;
		$db=new Db();
//		$eventId= $this->getRequest()->getParam('eventId');
		$jobId= $this->getRequest()->getParam('jobId');
		$jobData=$db->runQuery("select * from ".JOBS." join ".USERS." on (".JOBS.".job_parent_id=".USERS.".user_id) where ".JOBS.".job_id='".$jobId."'");
		$this->view->jobData=$jobData[0];
		$this->view->pageHeading="View Jobs - ".$jobData[0]['job_title'];

	}

	public function generatejobsgridAction()
	{ 		//echo "hello <br>";
		global $mySession;
		$this->_helper->viewRenderer->setNoRender();
		$db=new Db();
		$page=$this->getRequest()->page;
		$rp=$this->getRequest()->rp;
		$sortname=$this->getRequest()->sortname;
		$sortorder=$this->getRequest()->sortorder;
		if (!$sortname) $sortname = 'job_title';
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
		$qry="select TJ.*,TU.* from ".JOBS." as TJ inner join ".USERS." as TU on TU.user_id=TJ.job_parent_id";  

		$ResData=$db->runQuery("$qry $where $sort $limit");		
//		prd($ResData);
		$countQuery=$db->runQuery("$qry $where");
		$total=count($countQuery);		
		$json = "";
		$json .= "{\n";
		$json .= "page: $page,\n";
		$json .= "total: $total,\n";
		$json .= "rows: [";
		$rc = false;
		if(isset($ResData[0]) && $ResData[0]['job_id']!="")
		{
		$start=$start+1;
		$i=1;
		foreach($ResData as $row)
		{
			if($row['job_status']==1){ $image ='tick.gif';  }
			if($row['job_status']==0){ $image ='cross.png';  } 
			if ($rc) $json .= ",";
			$json .= "\n{";
			$json .= "id:'".$row['job_id']."',";
			$json .= "cell:['".$start."'";
			$json .= ",'<input name=\'check".$i."\' id=\'check".$i."\' value=\'".$row['job_id']."\' onchange=\'return check_check(\"bcdel\",\"deletebcchk\")\' type=\'checkbox\'><script>$(\'#bcdel\').html(\'\');document.getElementById(\'deletebcchk\').checked = false;</script>'";
			$json .= ",'".$row['job_title']."'";
		//	$json .= ",'".$row['job_type']."'";
			if ($row['job_type']==1)
			{
			  $jobtype="Regularly Scheduled";
			}
			else if($row['job_type']==2)
			{
			  $jobtype="Occasional";
			}
			else if($row['job_type']==2)
			{
			  $jobtype="One-time";
			}
			$json .= ",'".$jobtype."'";
			$json .= ",'".$row['job_location']."'";
			$username=$row['first_name']." ".$row['last_name'];
			$json .= ",'".$username."'";
			
//			$json .= ",'<a href=\'".APPLICATION_URL_ADMIN."users/edituser/userId/".$row['user_id']."\'><img src=\'".IMAGES_URL_ADMIN."edit.png\' border=\'0\' title=\'Edit\' alt=\'Edit\'></a>'";
			$json .= ",'<a id=\'".$start."\' onclick=\'changestatus(".$row['job_id'].",".$row['job_status'].",1)\' ><img src=\'".IMAGES_URL_ADMIN."$image\' border=\'0\' title=\'Status\' alt=\'Status\'></a>'";
//			$json .= ",'<a id=\'".$start."\' onclick=\'changestatus(".$row['user_id'].",".$row['is_active'].",2)\' ><img src=\'".IMAGES_URL_ADMIN."$image1\' border=\'0\' title=\'Activation Status\' alt=\'Activation Status\'></a>'";
/*			if($uType!='1' && $uType!='2')
			{
			$json .= ",'<a href=\'".APPLICATION_URL_ADMIN."users/subscriptions/uType/".$uType."/userId/".$row['user_id']."\'><img src=\'".IMAGES_URL_ADMIN."view_review.png\' border=\'0\' title=\'View Subscription\' alt=\'View Subscription\'></a>'";
			}*/
			$json .= ",'<a href=\'".APPLICATION_URL_ADMIN."jobs/view/jobId/".$row['job_id']."\'><img src=\'".IMAGES_URL_ADMIN."view_review.png\' border=\'0\' title=\'View Jobs\' alt=\'View Jobs\'></a>'";

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
	public function editjobsAction()
	{/*
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
	*/}
	public function updatejobsAction()
	{/*
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
	*/}
	public function deletejobsAction()
	{
		global $mySession;
		$db=new Db();
		if($_REQUEST['Id']!="")
		{
			$arrId=explode("|",$_REQUEST['Id']);
			//prd($arrId);
			if(count($arrId)>0)
			{
				foreach($arrId as $key=>$Id)
				{
					$condition1="job_id='".$Id."'"; 
					$db->delete(JOBS,$condition1);
				}
			}
		}		
		exit();
	}
	
	public function changejobsstatusAction()
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
	  $data_update['job_status']=$status; 
	  $condition="job_id='".$BcID."'";
	  $db->modify(JOBS,$data_update,$condition);
	  exit();
	 }
	
	
	
	public function subscriptionsAction()
	{/*
		global $mySession;		
		$uType=$this->getRequest()->getParam('uType');
		$this->view->uType=$uType;
		$userId=$this->getRequest()->getParam('userId');
		$this->view->userId=$userId;
		$this->view->pageHeading="User Subscriptions";
	*/}
	public function generatesubscriptionsgridAction()
	{/*
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
	*/}
}
?>