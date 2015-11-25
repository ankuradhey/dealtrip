<?php
__autoloadDB('Db');
class ForumController extends AppController
{
	public function indexAction()
	{
		global $mySession;
		$this->view->pageHeading="Manage Forum Topics";
	}
	 
	public function generategridAction()
	{
		global $mySession;
		$this->_helper->viewRenderer->setNoRender();
		$db=new Db();
		$page=$this->getRequest()->page;
		$rp=$this->getRequest()->rp;
		$sortname=$this->getRequest()->sortname;
		$sortorder=$this->getRequest()->sortorder;
		if (!$sortname) $sortname = 'topic_title';
		if (!$sortorder) $sortorder = 'asc';		
		$where=" where 1=1 ";
		if(@$_POST['query']!='')
		{
			$where .= " and LOWER(".$_POST['qtype'].") LIKE '%".strtolower($_POST['query'])."%' ";
		}
		$sort = "ORDER BY $sortname $sortorder";					
		if (!$page) $page = 1;
		if (!$rp) $rp = 10;		
		$start = (($page-1) * $rp);		
		$limit = " LIMIT $start, $rp";
		//$qry="select * from ".FORUM_TOPICS.""; 
//		$qry="SELECT *,(SELECT count(`thread_id`) s FROM ".FORUM_THREADS."
//		INNER JOIN ".USERS." ON ".USERS.".user_id = ".FORUM_THREADS.".user_id
//		 WHERE ".FORUM_THREADS.".topic_id= ".FORUM_TOPICS.".topic_id  ) as topiccount FROM ".FORUM_TOPICS." WHERE topic_status='1'  ";

		$qry="SELECT *,(SELECT count(`thread_id`) s FROM ".FORUM_THREADS."
		INNER JOIN ".USERS." ON ".USERS.".user_id = ".FORUM_THREADS.".user_id
		 WHERE ".FORUM_THREADS.".topic_id= ".FORUM_TOPICS.".topic_id) as topiccount FROM ".FORUM_TOPICS."";
//		 echo $qry.$where.$sort.$limit; exit();
		$ResData=$db->runQuery("$qry $where $sort $limit");		

		$countQuery=$db->runQuery("$qry $where");
		$total=count($countQuery);		
		$json = "";
		$json .= "{\n";
		$json .= "page: $page,\n";
		$json .= "total: $total,\n";
		$json .= "rows: [";
		$rc = false;
		if(isset($ResData[0]) && $ResData[0]['topic_id']!="")
		{
		$start=$start+1;
		$i=1;
		foreach($ResData as $row)
		{
			if($row['topic_status']==1){ $image ='tick.gif';  }
			if($row['topic_status']==0){ $image ='cross.png';  } 
			if ($rc) $json .= ",";
			$json .= "\n{";
			$json .= "id:'".$row['topic_id']."',";
			$json .= "cell:['".$start."'";
			$json .= ",'<input name=\'check".$i."\' id=\'check".$i."\' value=\'".$row['topic_id']."\' onchange=\'return check_check(\"bcdel\",\"deletebcchk\")\' type=\'checkbox\'><script>$(\'#bcdel\').html(\'\');document.getElementById(\'deletebcchk\').checked = false;</script>'";
			$json .= ",'".$row['topic_title']."'";
			$json .= ",'".$row['topic_description']."'";
			$json .= ",'<a href=\'".APPLICATION_URL_ADMIN."forum/edittopic/topicId/".$row['topic_id']."\'><img src=\'".IMAGES_URL_ADMIN."edit.png\' border=\'0\' title=\'Edit\' alt=\'Edit\'></a>'";
			$json .= ",'<a href=\'".APPLICATION_URL_ADMIN."forum/thread/topicId/".$row['topic_id']."\'>".$row['topiccount']."</a>'";
			$json .= ",'<a id=\'".$start."\' onclick=\'changestatus(".$row['topic_id'].",".$row['topic_status'].")\' ><img src=\'".IMAGES_URL_ADMIN."$image\' border=\'0\' title=\'Status\' alt=\'Status\'></a>'";
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
	public function addtopicAction()
	{
	global $mySession;
	$myform=new Form_Topic();
	$this->view->myform=$myform;
	$this->view->pageHeading="Add New Topic";
	}
	public function savetopicAction()
	{
		global $mySession;
		$db=new Db();
		$this->view->pageHeading="Add New Topic";
		if ($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			$myform = new Form_Topic();			
			if ($myform->isValid($request->getPost()))
			{
				
				$dataForm=$myform->getValues();
				$myObj=new Forums();
				$Result=$myObj->SaveTopic($dataForm);
				if($Result==1)
				{
				$mySession->errorMsg ="New topic added successfully.";
				$this->_redirect('forum/index');
				}
				else
				{
				$mySession->errorMsg ="Topic name you entered is already exists.";
				$this->view->myform = $myform;
				$this->render('addtopic');
				}
			}
			else
			{	
				$this->view->myform = $myform;
				$this->render('addtopic');
			}
		}
		else
		{			
			$this->_redirect('forum/addtopic');
		}
	}
	public function edittopicAction()
	{
	global $mySession;
	$topicId=$this->getRequest()->getParam('topicId');
	$this->view->topicId=$topicId;
	$myform=new Form_Topic($topicId);
	$this->view->myform=$myform;
	$this->view->pageHeading="Edit Topic";
	}
	public function updatetopicAction()
	{
		global $mySession;
		$db=new Db();
		$topicId=$this->getRequest()->getParam('topicId');
		$this->view->topicId=$topicId;
		$this->view->pageHeading="Edit Topic";
		if ($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			$myform=new Form_Topic($topicId);
			if ($myform->isValid($request->getPost()))
			{				
				$dataForm=$myform->getValues();
				$myObj=new Forums();
				$Result=$myObj->UpdateTopic($dataForm,$topicId);
				if($Result==1)
				{
				$mySession->errorMsg ="Topic updated successfully.";
				$this->_redirect('forum/index');
				}
				else
				{
				$mySession->errorMsg ="Topic name you entered is already exists.";
				$this->view->myform = $myform;
				$this->render('edittopic');
				}
			}
			else
			{	
				$this->view->myform = $myform;
				$this->render('edittopic');
			}
		}
		else
		{			
			$this->_redirect('forum/edittopic/topicId/'.$topicId);
		}
	}
	public function changetopicstatusAction()
	{  
	  global $mySession;
	  $db=new Db(); 
	  $Id=$_REQUEST['Id']; 
	  if($_REQUEST['Status']=='1')
	  { 
	  $Status = '0';
	  }
	  else 
	  { 
	  $Status = '1';
	  } 
	  $data_update['topic_status']=$Status; 
	  $condition="topic_id='".$Id."'";
	  $db->modify(FORUM_TOPICS,$data_update,$condition);	    
	  exit();
	}
	
	
	public function deletetopicAction()
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
					$condition="topic_id='".$Id."'"; 
					$db->delete(FORUM_TOPICS,$condition);
				}
			}
		}		
		exit();
	}
	public function threadAction( )
	{
		global $mySession;
		$db=new Db();
		$topicId= $this->getRequest()->getParam('topicId');
		$this->view->topicId=$topicId;
		
		$forumTopics=$db->runQuery("select topic_title from ".FORUM_TOPICS." where topic_id=".$topicId);

		$this->view->pageHeading="Forum - ".$forumTopics[0]['topic_title'];
	}
	public function generategridthreadAction()
	{
		global $mySession;
		$this->_helper->viewRenderer->setNoRender();
		$db=new Db();
		$topicId= $this->getRequest()->getParam('topicId');
		
		$page=$this->getRequest()->page;
		$rp=$this->getRequest()->rp;
		$sortname=$this->getRequest()->sortname;
		$sortorder=$this->getRequest()->sortorder;
		if (!$sortname) $sortname = 'thread_text';
		if (!$sortorder) $sortorder = 'asc';		
// 	$where=" where topic_status='1' and ".FORUM_TOPICS.".topic_id = ".$topicId;
		$where =" where ".FORUM_TOPICS.".topic_id = '".$topicId."'";
		// and thread_id!='49' 
		if(@$_POST['query']!='')
		{
			$where .= " and LOWER(".$_POST['qtype'].") LIKE '%".strtolower($_POST['query'])."%' ";
		}
		$sort = " ORDER BY $sortname $sortorder ";					
		if (!$page) $page = 1;
		if (!$rp) $rp = 10;		
		$start = (($page-1) * $rp);		
		$limit = " LIMIT $start, $rp ";
		
		$qry="select *,CONCAT(users.first_name,' ',users.last_name) as username ,
		(SELECT count(`post_id`) s FROM ".FORUM_POSTS.
		" INNER JOIN ".USERS." ON ".USERS.".user_id = ".FORUM_POSTS.".user_id
		 WHERE ".FORUM_POSTS.".replyof=0 and ".FORUM_POSTS.".thread_id= ".FORUM_THREADS.".thread_id) as postcount 
		 from 
			".FORUM_THREADS." inner join ".FORUM_TOPICS." on ".FORUM_TOPICS.".topic_id = ".FORUM_THREADS.".topic_id  
				inner join ".USERS." on ".USERS.".user_id = ".FORUM_THREADS.".user_id ";

		 
//		$qry="select *,
//		(SELECT count(`post_id`) s FROM ".FORUM_POSTS." 
//		INNER JOIN ".USERS." ON ".USERS.".user_id = ".FORUM_POSTS.".user_id
//		WHERE 
//		".FORUM_POSTS.".replyof=0 and ".FORUM_POSTS.".thread_id= ".FORUM_THREADS.".thread_id 
//
//		) as postcount 
//		 from 
//			".FORUM_THREADS." inner join ".FORUM_TOPICS." on ".FORUM_TOPICS.".topic_id = ".FORUM_THREADS.".topic_id  
//				inner join ".USERS." on ".USERS.".user_id = ".FORUM_THREADS.".user_id
//			 where topic_status='1' and ".FORUM_TOPICS.".topic_id = ".$topicId." order by ".FORUM_THREADS.".thread_id desc ";
//echo $qry.$where.$sort.$limit; exit();
		$ResData=$db->runQuery("$qry $where $sort $limit");		
		$countQuery=$db->runQuery("$qry $where");
		//pr($ResData);
		$total=count($countQuery);		
		$json = "";
		$json .= "{\n";
		$json .= "page: $page,\n";
		$json .= "total: $total,\n";
		$json .= "rows: [";
		$rc = false;
		if(isset($ResData[0]) && $ResData[0]['topic_id']!="")
		{
		$start=$start+1;
		$i=1;
		foreach($ResData as $row)
		{ 

			if($row['topic_status']==1){ $image ='tick.gif';  }
			if($row['topic_status']==0){ $image ='cross.png';  } 
			if ($rc) $json .= ",";
			$json .= "\n{";
			$json .= "id:'".$row['thread_id']."',";
			$json .= "cell:['".$start."'";
			 
			$json .= ",'<input name=\'check".$i."\' id=\'check".$i."\' value=\'".$row['thread_id']."\' onchange=\'return check_check(\"bcdel\",\"deletebcchk\")\' type=\'checkbox\'><script>$(\'#bcdel\').html(\'\');document.getElementById(\'deletebcchk\').checked = false;</script>'";
			$json .= ",'".CheckAndFilter($row['thread_text'])."'";
			$json .= ",'".$row['username']."'";
			$json .= ",'<a href=\'".APPLICATION_URL_ADMIN."forum/post/thread_id/".$row['thread_id']."\'>".$row['postcount']."</a>'";
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
	public function deletethreadAction()
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
					$condition="thread_id='".$Id."'"; 
					$db->delete(FORUM_THREADS,$condition);
				}
			}
		}		
		exit();
	}
	
	function postAction()
	{
		global $mySession;
		$db=new Db();
		$thread_id= $this->getRequest()->getParam('thread_id');
		$forumTopics=$db->runQuery("select * 
		 from 
			".FORUM_THREADS." inner join ".FORUM_TOPICS." on ".FORUM_TOPICS.".topic_id = ".FORUM_THREADS.".topic_id  
			 
			 where topic_status='1' and ".FORUM_THREADS.".thread_id=".$thread_id );

		 
		$this->view->pageHeading="Forums - ".$forumTopics[0]['topic_title']." - ".$forumTopics[0]['thread_text'];
		$this->view->thread_id=$thread_id;
	}
	function generategridpostAction()
	{
		global $mySession;
		$this->_helper->viewRenderer->setNoRender();
		$db=new Db();
		$thread_id= $this->getRequest()->getParam('thread_id');
		
		$page=$this->getRequest()->page;
		$rp=$this->getRequest()->rp;
		$sortname=$this->getRequest()->sortname;
		$sortorder=$this->getRequest()->sortorder;
		if (!$sortname) $sortname = ' username ';
		if (!$sortorder) $sortorder = ' asc ';		
		$where=" where topic_status='1' and ".FORUM_POSTS.".replyof=0 and ".FORUM_THREADS.".thread_id = ".$thread_id;
		
		if(@$_POST['query']!='')
		{
			$where .= " and LOWER(".$_POST['qtype'].") LIKE '%".strtolower($_POST['query'])."%' ";
		}
		$sort = " ORDER BY $sortname $sortorder";					
		if (!$page) $page = 1;
		if (!$rp) $rp = 10;		
		$start = (($page-1) * $rp);		
		$limit = "  LIMIT $start, $rp";
		
		 $qry="select *,CONCAT(".USERS.".first_name,' ',".USERS.".last_name) as username from 
			".FORUM_POSTS." inner join ".FORUM_THREADS." on ".FORUM_POSTS.".thread_id=".FORUM_THREADS.".thread_id 
			inner join ".FORUM_TOPICS." on ".FORUM_TOPICS.".topic_id = ".FORUM_THREADS.".topic_id  
				inner join ".USERS." on ".USERS.".user_id = ".FORUM_POSTS.".user_id";
		 
			 
		 
		//echo $qry.$where.$sort.$limit; exit(); 
		$ResData=$db->runQuery("$qry $where $sort $limit");
		//prd($ResData);		
		$countQuery=$db->runQuery("$qry $where");
		$total=count($countQuery);		
		$json = "";
		$json .= "{\n";
		$json .= "page: $page,\n";
		$json .= "total: $total,\n";
		$json .= "rows: [";
		$rc = false;
		if(isset($ResData[0]) && $ResData[0]['topic_id']!="")
		{
		$start=$start+1;
		$i=1;
		foreach($ResData as $row)
		{

			if ($rc) $json .= ",";
			$json .= "\n{";
			$json .= "id:'".$row['post_id']."',";
			$json .= "cell:['".$start."'";
			 
			$json .= ",'<input name=\'check".$i."\' id=\'check".$i."\' value=\'".$row['post_id']."\' onchange=\'return check_check(\"bcdel\",\"deletebcchk\")\' type=\'checkbox\'><script>$(\'#bcdel\').html(\'\');document.getElementById(\'deletebcchk\').checked = false;</script>'";
			 
			$json .= ",'".$row['username']."'";

			$json .= ",'".$row['post_text']."'";

			$json .= ",'".$row['date_posted']."'";
					//		echo 'hello'; exit();			 
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
	public function deletepostAction()
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
					 $condition="post_id='".$Id."'"; 
					 //echo FORUM_POSTS; exit();
					$db->delete(FORUM_POSTS,$condition);
				}
			}
		}		
		exit();
	}
}
?>