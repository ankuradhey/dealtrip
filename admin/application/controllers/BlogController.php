<?php
__autoloadDB('Db');
class BlogController extends AppController
{
	public function indexAction()
	{
		global $mySession;
		$this->view->pageHeading="Manage Blogs";
	}
	 
	public function addblogAction()
	{//echo "hello"; exit();
		global $mySession;
		$db=new Db();	
		$this->view->pageHeading="Add New Blog";
		$myform=new Form_Blog();
		$this->view->myform=$myform;	

	} 

	public function editblogAction()
	{//echo "hello"; exit();
		global $mySession;
		$db=new Db();	
		$this->view->pageHeading="Edit New Blog";
		$blogId= $this->getRequest()->getParam('blogId');
		$this->view->blogId=$blogId;
		$myform=new Form_Blog($blogId);
		$this->view->myform=$myform;	

	} 
	public function updateblogAction()
	{

		global $mySession;
		$db=new Db();	
		$blogId= $this->getRequest()->getParam('blogId');
		if ($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			$myform=new Form_Blog();
			if ($myform->isValid($request->getPost()))
			{
			  $dataform=$myform->getValues();			  
		      $blogdata['title']=$dataform['title'];
			  $blogdata['description']=$dataform['description'];
			  $blogdata['status']=$dataform['status'];
			  $blogdata['cr_date']=date("Y-m-d H:i:s");
			  $blogdata['activeBlog']=0;
//			  prd($blogdata);
			  $condition="blog_post_id= ".$blogId;
			  $db->modify(BLOG_POST,$blogdata,$condition);
			  $this->_redirect('blog/index');
			}
			else
			{	
				$this->view->myform = $myform;
				$this->render('addblog');
			}		
		}
		else
		{			
			$this->_redirect('blog/index');
		}

	
	}


	public function saveblogAction()
	{ 
		global $mySession;
		$db=new Db();	
		if ($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			$myform=new Form_Blog();
			if ($myform->isValid($request->getPost()))
			{
			  $dataform=$myform->getValues();
			  
		      $blogdata['title']=$dataform['title'];
			  $blogdata['description']=$dataform['description'];
			  $blogdata['status']=1;
			  $blogdata['cr_date']=date("Y-m-d H:i:s");
			  $blogdata['activeBlog']=1;

			  $db->save(BLOG_POST,$blogdata);
			  $this->_redirect('blog/index');
			}
			else
			{	
				$this->view->myform = $myform;
				$this->render('addblog');
			}		
		}
		else
		{			
			$this->_redirect('blog/index');
		}

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
		if (!$sortname) $sortname = 'title';
		if (!$sortorder) $sortorder = 'asc';		
		$where="where status='1' ";
		if(@$_POST['query']!='')
		{
			$where .= " and LOWER(".$_POST['qtype'].") LIKE '%".strtolower($_POST['query'])."%' ";
		}
		$sort = "ORDER BY $sortname $sortorder";					
		if (!$page) $page = 1;
		if (!$rp) $rp = 10;		
		$start = (($page-1) * $rp);		
		$limit = "LIMIT $start, $rp";
		
		$qry="SELECT *,(SELECT count(`comment_id`) s FROM ".BLOG_COMMENTS." WHERE ".BLOG_COMMENTS.".blog_post_id= ".BLOG_POST.".blog_post_id) as topiccount FROM ".BLOG_POST." left join ".USERS." on ".USERS.".user_id = ".BLOG_POST.".user_id ";
		$ResData=$db->runQuery("$qry $where $sort $limit");		
		$countQuery=$db->runQuery("$qry $where");
		$total=count($countQuery);		
		$json = "";
		$json .= "{\n";
		$json .= "page: $page,\n";
		$json .= "total: $total,\n";
		$json .= "rows: [";
		$rc = false;
		if(isset($ResData[0]) && $ResData[0]['blog_post_id']!="")
		{
		$start=$start+1;
		$i=1;
		foreach($ResData as $row)
		{
		
			if($row['activeBlog']==1){ $image ='tick.gif';  }
			if($row['activeBlog']==0){ $image ='cross.png';  } 
			
			if($row['user_type']==1) { $usertype="Visitor/Parent";}
			if($row['user_type']==2) { $usertype="BabySitter"; }
			if($row['user_type']==3) { $usertype="Service Provider"; }
			
			
			if ($rc) $json .= ",";
			$json .= "\n{";
			$json .= "id:'".$row['blog_post_id']."',";
			$json .= "cell:['".$start."'";
			$json .= ",'<input name=\'check".$i."\' id=\'check".$i."\' value=\'".$row['blog_post_id']."\' onchange=\'return check_check(\"bcdel\",\"deletebcchk\")\' type=\'checkbox\'><script>$(\'#bcdel\').html(\'\');document.getElementById(\'deletebcchk\').checked = false;</script>'";
			$json .= ",'".$row['title']."'";
			$json .= ",'".$row['first_name']."'";
			$json .= ",'".$usertype."'";
			$json .= ",'".$row['cr_date']."'";
			$json .= ",'<a href=\'".APPLICATION_URL_ADMIN."blog/comment/blog_post_id/".$row['blog_post_id']."\'>".$row['topiccount']."</a>'";
			$json .= ",'<a id=\'".$start."\' onclick=\'changestatus(".$row['blog_post_id'].",".$row['activeBlog'].")\' ><img src=\'".IMAGES_URL_ADMIN."$image\' border=\'0\' title=\'Status\' alt=\'Status\'></a>'";
			
			$json .= ",'<a href=\'".APPLICATION_URL_ADMIN."blog/editblog/blogId/".$row['blog_post_id']."\'><img src=\'".IMAGES_URL_ADMIN."edit.png\' border=\'0\' title=\'Edit Blog\' alt=\'Edit Blog\'></a>'";

			

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
	 
	public function changestatusAction()
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
	  $data_update['activeBlog']=$Status; 
	  $condition="blog_post_id='".$Id."'";
	  $db->modify(BLOG_POST,$data_update,$condition);	    
	  exit($data_update);
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
					$condition="blog_post_id='".$Id."'"; 
					$db->delete(BLOG_POST,$condition);
				}
			}
		}		
		exit();
	}
	public function commentAction( )
	{
		global $mySession;
		$db=new Db();
		$blog_post_id= $this->getRequest()->getParam('blog_post_id');
		$this->view->blog_post_id=$blog_post_id;
		
		$forumTopics=$db->runQuery("select title from ".BLOG_POST." where blog_post_id=".$blog_post_id);
		$this->view->pageHeading="Blog - ".$forumTopics[0]['title'];
	}
	public function generategridcommentAction()
	{
		global $mySession;
		$this->_helper->viewRenderer->setNoRender();
		$db=new Db();
		$blog_post_id= $this->getRequest()->getParam('blog_post_id');
		
		$page=$this->getRequest()->page;
		$rp=$this->getRequest()->rp;
		$sortname=$this->getRequest()->sortname;
		$sortorder=$this->getRequest()->sortorder;
		if (!$sortname) $sortname = 'username';
		if (!$sortorder) $sortorder = 'asc';		
		$where="where  ".BLOG_POST.".blog_post_id = ".$blog_post_id;
		
		if(@$_POST['query']!='')
		{
			$where .= " and LOWER(".$_POST['qtype'].") LIKE '%".strtolower($_POST['query'])."%' ";
		}
		$sort = "ORDER BY $sortname $sortorder";					
		if (!$page) $page = 1;
		if (!$rp) $rp = 10;		
		$start = (($page-1) * $rp);		
		$limit = "LIMIT $start, $rp";
		
		$qry="select * 
		 from 
			".BLOG_POST." inner join ".BLOG_COMMENTS." on ".BLOG_POST.".blog_post_id = ".BLOG_COMMENTS.".blog_post_id  
				inner join ".USERS." on ".USERS.".user_id = ".BLOG_COMMENTS.".post_by ";
			 
		 
		//$qry="SELECT *,(SELECT count(`thread_id`) s FROM ".FORUM_THREADS." WHERE ".FORUM_THREADS.".topic_id= ".FORUM_TOPICS.".topic_id) as topiccount FROM ".FORUM_TOPICS."";
		 
		$ResData=$db->runQuery("$qry $where $sort $limit");		
		 
		$countQuery=$db->runQuery("$qry $where");
		$total=count($countQuery);		
		$json = "";
		$json .= "{\n";
		$json .= "page: $page,\n";
		$json .= "total: $total,\n";
		$json .= "rows: [";
		$rc = false;
		if(isset($ResData[0]) && $ResData[0]['comment_id']!="")
		{
		$start=$start+1;
		$i=1;
		foreach($ResData as $row)
		{
		 
			if ($rc) $json .= ",";
			$json .= "\n{";
			$json .= "id:'".$row['comment_id']."',";
			$json .= "cell:['".$start."'";
			 
			$json .= ",'<input name=\'check".$i."\' id=\'check".$i."\' value=\'".$row['comment_id']."\' onchange=\'return check_check(\"bcdel\",\"deletebcchk\")\' type=\'checkbox\'><script>$(\'#bcdel\').html(\'\');document.getElementById(\'deletebcchk\').checked = false;</script>'";
			$json .= ",'".$row['first_name']."'";
			$json .= ",'".$row['postedon']."'";
			$json .= ",'<a href=\'".APPLICATION_URL_ADMIN."blog/viewcomment/comment_id/".$row['comment_id']."\' class=\"thickbox\">View</a>'";
			$json .= "]}";
			$rc = true;
			$start++;
			$i++;
			//truncate
		}
		}
		$json .= "]\n";
		$json .= "}"; 
		echo $json; 
		exit();
	}
	
	public function viewcommentAction()
	{
		global $mySession;
		$db=new Db();
		$comment_id= $this->getRequest()->getParam('comment_id');
		$this->view->comment_id=$comment_id;
		
		$qry="select * 
		 from 
			".BLOG_POST." inner join ".BLOG_COMMENTS." on ".BLOG_POST.".blog_post_id = ".BLOG_COMMENTS.".blog_post_id  
				inner join ".USERS." on ".USERS.".user_id = ".BLOG_COMMENTS.".post_by where comment_id=".$comment_id;
				
		$forumTopics=$db->runQuery($qry);
		$this->view->pageHeading="Blog - ".$forumTopics[0]['title'];
		$this->view->forumTopics=$forumTopics;
		
	}
	public function deletecommentAction()
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
					$condition="comment_id='".$Id."'"; 
					$db->delete(BLOG_COMMENTS,$condition);
				}
			}
		}		
		exit();
	}
	
	 
}
?>