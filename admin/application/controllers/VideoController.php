<?php
__autoloadDB('Db');
class VideoController extends AppController
{
	public function indexAction()
	{
		global $mySession;
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
		if (!$sortname) $sortname = 'video_title';
		if (!$sortorder) $sortorder = 'asc';		
		$where="where 1=1 ";
		if(@$_POST['query']!='')
		{
			$where .= " and ".$_POST['qtype']." LIKE '%".$_POST['query']."%' ";			
		}
		$sort = "ORDER BY $sortname $sortorder";					
		if (!$page) $page = 1;
		if (!$rp) $rp = 10;		
		$start = (($page-1) * $rp);		
		$limit = "LIMIT $start, $rp";
		$qry="select * from ".VIDEOGALLERY.""; 
		$ResData=$db->runQuery("$qry $where $sort $limit");		
		$countQuery=$db->runQuery("$qry $where");
		$total=count($countQuery);		
		$json = "";
		$json .= "{\n";
		$json .= "page: $page,\n";
		$json .= "total: $total,\n";
		$json .= "rows: [";
		$rc = false;
		if(isset($ResData[0]) && $ResData[0]['video_id']!="")
		{
		$start=$start+1;
		$i=1;
		foreach($ResData as $row)
		{   
		    if($row['video_status']==1){ $image ='tick.gif';  }
			if($row['video_status']==0){ $image ='cross.png';  } 
			if($row['featured']==0){$featured='cross.png';}else{$featured='tick.gif';}
			
			/*if($row['featured_status']==1){ $image1 ='tick.gif';  }
			if($row['featured_status']==2){ $image1 ='cross.png';  }  */
			$date = date('F d-Y, h:i:s a',strtotime($row['date_uploaded']));
			if ($rc) $json .= ",";
			$json .= "\n{";
			$json .= "id:'".$row['video_id']."',";
			$json .= "cell:['".$start."'";
			$json .= ",'<input name=\'check".$i."\' id=\'check".$i."\' value=\'".$row['video_id']."\' onchange=\'return check_check(\"vdel\",\"deletevchk\")\' type=\'checkbox\'><script>$(\'#vdel\').html(\'\');document.getElementById(\'deletevchk\').checked = false;</script>'";
			$json .= ",'".$row['video_title']."'";
			$json .= ",'".$date."'";
			$json .= ",'<a href=\'".APPLICATION_URL_ADMIN."video/view/videoId/".$row['video_id']."\' ><img src=\'".IMAGES_URL_ADMIN."edit.png\' border=\'0\' title=\'View\' alt=\'View\'></a>'";
			$json .= ",'<a id=\'".$start."\' onclick=\'changestatus(".$start.",".$row['video_id'].",".$row['video_status'].")\' ><img src=\'".IMAGES_URL_ADMIN."$image\' border=\'0\' title=\'Status\' alt=\'Status\'></a>'";
			$json .= ",'<a id=\'".$start."\' onclick=\'changefeaturedstatus(".$row['video_id'].")\' ><img src=\'".IMAGES_URL_ADMIN."$featured\' border=\'0\' title=\'Status\' alt=\'Status\'></a>'";
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
	
	public function statusAction()
	{  
	  global $mySession;
	  $db=new Db(); 
	  $videoId=$_REQUEST['VID']; 
	  $status=$_REQUEST['VStatus'];
	  if($status=='1'){ $status = '0';}
	  else if($status=='0'){ $status = '1';} 
	  $data_update['video_status']=$status; 
	  $condition="video_id='".$videoId."'";
	  $db->modify(VIDEOGALLERY,$data_update,$condition);
	    
	  exit();
	}	
    
	public function featuredstatusAction()
	{  
	  global $mySession;
	  $db=new Db(); 
	  $videoId=$_REQUEST['FID']; 
	  //$status=$_REQUEST['FStatus'];
	  
	  $data_update['featured']='0'; 
	  $condition="1=1";
	  $db->modify(VIDEOGALLERY,$data_update,$condition);

	  $data_update['featured']='1'; 
	  $condition="video_id='".$videoId."'";
	  $db->modify(VIDEOGALLERY,$data_update,$condition);
	  exit();
	}
	
	
	public function deleteAction()
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
				    $videoData=$db->runQuery("select * from ".VIDEOGALLERY." where video_id='".$Id."'");
					$condition1="video_id='".$Id."'"; 
					$db->delete(VIDEOGALLERY,$condition1);
					if($videoData[0]['video_path']==2) 
					{ $r = @unlink(SITE_ROOT.'video/'.$videoData[0]['video_path']);  }
				}
			}
		}		
		exit();
	}
	
		
	public function updateAction()
	{ 
		global $mySession;
		$db=new Db(); 
		$videoId= $this->getRequest()->getParam('videoId');
		if($videoId!="")
		{
			$videoData=$db->runQuery("select * from ".VIDEOGALLERY." where video_id='".$videoId."'");
			$this->view->videoData=$videoData[0];
			$this->view->pageTitle=$videoData[0]['video_title'];
			$myform=new Form_Video($videoId); 
		}
		else 
		{
			$this->view->pageTitle='Add new Video';
			$myform=new Form_Video();
		}
		$this->view->myform=$myform;
	}
	public function viewAction()
	{
		global $mySession;
		$db=new Db(); 
		//$this->_helper->layout()->disableLayout();
		$videoId= $this->getRequest()->getParam('videoId');
		if($videoId!="")
		{
			$videoData=$db->runQuery("select * from ".VIDEOGALLERY." where video_id='".$videoId."'");
			$this->view->videoData=$videoData; 
		}
		  
	} 
	
	
	/*public function saveAction()
	{ 
		global $mySession;
		$db=new Db();
		$videoId='';
		$videoId= $this->getRequest()->getParam('videoId');
	    if($videoId!="")
		{ 
	    $videoData=$db->runQuery("select * from ".VIDEO." where video_id='".$videoId."'");
	    $this->view->videoData=$videoData[0];
		}
		if ($this->getRequest()->isPost())
		{  
			$request=$this->getRequest(); 
			$myform = new Form_Video($videoId);
			if ($myform->isValid($request->getPost()))
			{    
			    $dataForm=$myform->getValues();
				$vobj=new Video();
				$result=$vobj->Addvideo($dataForm,$videoId);
			//die;
				if($result=='1')
				{
				 if($videoId!="")
				 { $mySession->errorMsg ="Video successfully Updated."; }
				 else { $mySession->errorMsg ="Video successfully Added."; }
				 if($videoData[0]['video_user_id']!=0)
				 {
				  $this->_redirect('video/uservideo');
				 }
				 else { $this->_redirect('video/index'); }
				}
				else
				{
				 if($videoId!="")
				 { $mySession->errorMsg ="Video not Updated."; }
				 else { $mySession->errorMsg ="Video not Added."; }

				 $this->view->myform = $myform;
				 $this->render('update');
				}
			}
			else 
			{ 
			  $this->view->myform = $myform;
			  $this->render('update');
			}	
		}
		else
		{			
			if($videoData[0]['video_user_id']!='0')
		    {
			  $this->_redirect('video/uservideo');
			}
			else { $this->_redirect('video/index'); }
		}	
	}*/
	
	public function uservideoAction()
	{
	 global $mySession;
	}
	
	/*public function generateusersgridAction()
	{		
		global $mySession;
		$this->_helper->viewRenderer->setNoRender();
		$db=new Db();
		$page=$this->getRequest()->page;
		$rp=$this->getRequest()->rp;
		$sortname=$this->getRequest()->sortname;
		$sortorder=$this->getRequest()->sortorder;
		if (!$sortname) $sortname = 'video_title';
		if (!$sortorder) $sortorder = 'asc';		
		$where="where video_user_id!='0'";
		if(@$_POST['query']!='')
		{
			$where .= " and ".$_POST['qtype']." LIKE '%".$_POST['query']."%' ";			
		}
		$sort = "ORDER BY $sortname $sortorder";					
		if (!$page) $page = 1;
		if (!$rp) $rp = 10;		
		$start = (($page-1) * $rp);		
		$limit = "LIMIT $start, $rp";
		$qry="select * from ".VIDEO."";
		$ResData=$db->runQuery("$qry $where $sort $limit");		
		$countQuery=$db->runQuery("$qry $where");
		$total=count($countQuery);		
		$json = "";
		$json .= "{\n";
		$json .= "page: $page,\n";
		$json .= "total: $total,\n";
		$json .= "rows: [";
		$rc = false;
		if(isset($ResData[0]) && $ResData[0]['video_id']!="")
		{
		$start=$start+1;
		$i=1;
		foreach($ResData as $row)
		{   
		    if($row['video_status']==1){ $image ='tick.gif';  }
			if($row['video_status']==0){ $image ='cross.png';  } 
			$date = date('F d-Y, h:i:s a',strtotime($row['video_date']));
			if ($rc) $json .= ",";
			$json .= "\n{";
			$json .= "id:'".$row['video_id']."',";
			$json .= "cell:['".$start."'";
			$json .= ",'<input name=\'check".$i."\' id=\'check".$i."\' value=\'".$row['video_id']."\' onchange=\'return check_check(\"vdel\",\"deletevchk\")\' type=\'checkbox\'><script>$(\'#vdel\').html(\'\');document.getElementById(\'deletevchk\').checked = false;</script>'";
			$json .= ",'".$row['video_title']."'";
			$json .= ",'".$date."'";
			$json .= ",'<a href=\'".APPLICATION_URL_ADMIN."video/update/videoId/".$row['video_id']."\'><img src=\'".IMAGES_URL_ADMIN."edit.png\' border=\'0\' title=\'Edit\' alt=\'Edit\'></a>'";
			$json .= ",'<a id=\'".$start."\' onclick=\'changestatus(".$start.",".$row['video_id'].",".$row['video_status'].")\' ><img src=\'".IMAGES_URL_ADMIN."$image\' border=\'0\' title=\'Status\' alt=\'Status\'></a>'";
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