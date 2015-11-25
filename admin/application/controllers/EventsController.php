<?php
__autoloadDB('Db');
class EventsController extends AppController
{
	public function indexAction()
	{
	global $mySession;
	$this->view->pageHeading="View All Events";
	}
	public function generategridforeventsAction()
	{
		global $mySession;
		$this->_helper->viewRenderer->setNoRender();
		$db=new Db();
		$page=$this->getRequest()->page;
		$rp=$this->getRequest()->rp;
		$sortname=$this->getRequest()->sortname;
		$sortorder=$this->getRequest()->sortorder;
		if (!$sortname) $sortname = 'date_event_added';
		if (!$sortorder) $sortorder = 'desc';		
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
		$qry="select * from ".EVENTS." 
		left join ".USERS." on (".EVENTS.".user_id=".USERS.".user_id)";
		$ResData=$db->runQuery("$qry $where $sort $limit");		
		$countQuery=$db->runQuery("$qry $where");
		$total=count($countQuery);		
		$json = "";
		$json .= "{\n";
		$json .= "page: $page,\n";
		$json .= "total: $total,\n";
		$json .= "rows: [";
		$rc = false;
		if(isset($ResData[0]) && $ResData[0]['event_id']!="")
		{
		$start=$start+1;
		$i=1;
		foreach($ResData as $row)
		{
			if($row['event_status']==1){ $image ='tick.gif';  }
			if($row['event_status']==0){ $image ='cross.png';  } 
			if ($rc) $json .= ",";
			$json .= "\n{";
			$json .= "id:'".$row['event_id']."',";
			$json .= "cell:['".$start."'";
			$json .= ",'<input name=\'check".$i."\' id=\'check".$i."\' value=\'".$row['event_id']."\' onchange=\'return check_check(\"bcdel\",\"deletebcchk\")\' type=\'checkbox\'><script>$(\'#bcdel\').html(\'\');document.getElementById(\'deletebcchk\').checked = false;</script>'";
			if($row['first_name']!="")
			{
				$fname=$row['first_name'];
			}
			else
			{
				  $fname='Admin';
			}
			$json .= ",'".ucfirst(addslashes($fname))."'";
			$json .= ",'".addslashes($row['event_title'])."'";
			$json .= ",'".addslashes($row['event_venue'])."'";
			$json .= ",'".date(DATEFORMAT,strtotime($row['event_date']))."'";
			$json .= ",'<a href=\'".APPLICATION_URL_ADMIN."events/edit/eventId/".$row['event_id']."\'><img src=\'".IMAGES_URL_ADMIN."edit.png\' border=\'0\' title=\'Edit Event\' alt=\'Edit Event\'></a>'";
			$json .= ",'<a id=\'".$start."\' onclick=\'changestatus(".$row['event_id'].",".$row['event_status'].")\' ><img src=\'".IMAGES_URL_ADMIN."$image\' border=\'0\' title=\'Status\' alt=\'Status\'></a>'";
			$json .= ",'<a href=\'".APPLICATION_URL_ADMIN."events/view/eventId/".$row['event_id']."\'><img src=\'".IMAGES_URL_ADMIN."view_review.png\' border=\'0\' title=\'View Event\' alt=\'View Event\'></a>'";
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
	public function changeeventstatusAction()
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
	  $data_update['event_status']=$status; 
	  $condition="event_id='".$BcID."'";
	  $db->modify(EVENTS,$data_update,$condition);
	    
	  exit();
	}
	public function deleteeventAction()
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
					$eventData=$db->runQuery("select event_image from ".EVENTS." where event_id='".$Id."'");
					if($eventData[0]['event_image']!="")
					{
					@unlink(SITE_ROOT.'images/events/'.$eventData[0]['event_image']);	
					}
					$condition="event_id='".$Id."'"; 
					$db->delete(EVENTS,$condition);
				}
			}
		}		
		exit();
	}
	public function viewAction()
	{
		global $mySession;
		$db=new Db();
		$eventId= $this->getRequest()->getParam('eventId');
		
		$eventData=$db->runQuery(" select *, sum(".EVENTSIGNUP.".NosAttendee)  AS 'totalattendee' from ".EVENTS." 
		join ".USERS." on (".EVENTS.".user_id=".USERS.".user_id) inner join ".EVENTSIGNUP." on ".EVENTSIGNUP.".event_id=".EVENTS.".event_id
		where ".EVENTS.".event_id='".$eventId."'");
		$this->view->eventData=$eventData[0];
		$this->view->pageHeading="View Events - ".$eventData[0]['event_title'];
	}
	public function addAction()
	{
		global $mySession;
		$db=new Db();
		$this->view->pageHeading="Add Event";
		$myform=new Form_Event();
		$this->view->myform=$myform;
	}
	public function saveneweventAction()
	{
		global $mySession;
		$db=new Db();
		$this->view->pageHeading="Add Event";
		if ($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			$myform=new Form_Event();
			if ($myform->isValid($request->getPost()))
			{				
				$dataForm=$myform->getValues();
				$myObj=new Event();
				$Result=$myObj->SaveEvent($dataForm);
				if($Result>0)
				{
				$mySession->errorMsg ="New event added successfully.";
				$this->_redirect('events/index');
				}
				else
				{
				$mySession->errorMsg ="Event title you entered is already exists.";
				$this->view->myform = $myform;
				$this->render('add');	
				}
			}
			else
			{	
				$this->view->myform = $myform;
				$this->render('add');
			}
		}
		else
		{			
		$this->_redirect('events/add');
		}
	}
	public function editAction()
	{
		global $mySession;
		$db=new Db();
		$eventId=$this->getRequest()->getParam('eventId');
		$this->view->eventId=$eventId;
		$eventData=$db->runQuery("select event_image from ".EVENTS." where event_id='".$eventId."' and user_id='".$mySession->LoggedUserId."'");
		$this->view->EventImage=$eventData[0]['event_image'];
		$this->view->pageHeading="Edit Event";
		$myform=new Form_Event($eventId);
		$this->view->myform=$myform;
	}
	public function updateeventAction()
	{
		global $mySession;
		$db=new Db();
		$eventId=$this->getRequest()->getParam('eventId');
		$this->view->eventId=$eventId;
		$eventData=$db->runQuery("select event_image from ".EVENTS." where event_id='".$eventId."' and user_id='".$mySession->LoggedUserId."'");
		$this->view->EventImage=$eventData[0]['event_image'];
		$this->view->pageHeading="Edit Event";
		if ($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			$myform=new Form_Event($eventId);
			if ($myform->isValid($request->getPost()))
			{				
				$dataForm=$myform->getValues();
				$myObj=new Event();
				$Result=$myObj->UpdateEvent($dataForm,$eventId);
				if($Result>0)
				{
				$mySession->errorMsg ="Event Information updated successfully.";
				$this->_redirect('events/index');
				}
				else
				{
				$mySession->errorMsg ="Event title you entered is already exists.";
				$this->view->myform = $myform;
				$this->render('edit');	
				}
			}
			else
			{	
				$this->view->myform = $myform;
				$this->render('edit');
			}
		}
		else
		{			
		$this->_redirect('events/edit/eventId/'.$eventId);
		}
	}
}
?>