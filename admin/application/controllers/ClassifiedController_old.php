<?php
__autoloadDB('Db');
class ClassifiedController extends AppController
{
	public function indexAction()
	{
	global $mySession;
	$this->view->pageHeading="Manage Classified Categories";
	}
	public function generategridcategoriesAction()
	{
		global $mySession;
		$this->_helper->viewRenderer->setNoRender();
		$db=new Db();
		$page=$this->getRequest()->page;
		$rp=$this->getRequest()->rp;
		$sortname=$this->getRequest()->sortname;
		$sortorder=$this->getRequest()->sortorder;
		if (!$sortname) $sortname = 'cat_name';
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
		$qry="select * from ".CATEGORY."";  
		$ResData=$db->runQuery("$qry $where $sort $limit");		
		$countQuery=$db->runQuery("$qry $where");
		$total=count($countQuery);		
		$json = "";
		$json .= "{\n";
		$json .= "page: $page,\n";
		$json .= "total: $total,\n";
		$json .= "rows: [";
		$rc = false;
		if(isset($ResData[0]) && $ResData[0]['cat_id']!="")
		{
		$start=$start+1;
		$i=1;
		foreach($ResData as $row)
		{
			
			if ($rc) $json .= ",";
			$json .= "\n{";
			$json .= "id:'".$row['cat_id']."',";
			$json .= "cell:['".$start."'";
			$json .= ",'<input name=\'check".$i."\' id=\'check".$i."\' value=\'".$row['cat_id']."\' onchange=\'return check_check(\"bcdel\",\"deletebcchk\")\' type=\'checkbox\'><script>$(\'#bcdel\').html(\'\');document.getElementById(\'deletebcchk\').checked = false;</script>'";
			$json .= ",'".addslashes($row['cat_name'])."'";
			$json .= ",'<a href=\'".APPLICATION_URL_ADMIN."classified/editcategory/categoryId/".$row['cat_id']."\'><img src=\'".IMAGES_URL_ADMIN."edit.png\' border=\'0\' title=\'Edit\' alt=\'Edit\'></a>'";
			
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
	
	public function addcategoryAction()
	{
	global $mySession;
	$myform=new Form_Category();
	$this->view->myform=$myform;
	$this->view->pageHeading="Add New Category";
	}
	public function savecategoryAction()
	{
		global $mySession;
		$db=new Db();
	$this->view->pageHeading="Add New Category";
		if ($this->getRequest()->isPost())
		{
			
			$request=$this->getRequest();
			$myform = new Form_Category();	
			
			if ($myform->isValid($request->getPost()))
			{	
								
				$dataForm=$myform->getValues();
				$myObj=new classified();
		
				$Result=$myObj->SaveCategory($dataForm);
				if($Result==1)
				{
				$mySession->errorMsg ="Classified Category added successfully.";
				$this->_redirect('classified/index');
				}
				else
				{
				$mySession->errorMsg ="Category name you entered is already exists.";
				$this->view->myform = $myform;
				$this->render('addcategory');
				}
			}
			else
			{	
				$this->view->myform = $myform;
				$this->render('addcategory');
			}
		}
		else
		{			
			$this->_redirect('classified/addcategory');
		}
	}
	public function editcategoryAction()
	{
	global $mySession;
	$db=new Db();
	$categoryId=$this->getRequest()->getParam('categoryId'); 
	$this->view->categoryId=$categoryId;
	$myform=new Form_Classified($categoryId);
	$catData=$db->runQuery("select * from ".CATEGORY." where cat_id='".$categoryId."'");
	$this->view->catImage=$catData[0]['cat_image'];
	$this->view->myform=$myform;
	$this->view->pageHeading="Edit Category";
	}
	public function updatecategoryAction()
	{
		global $mySession;
		$db=new Db();
		$categoryId=$this->getRequest()->getParam('categoryId'); 
		$this->view->categoryId=$categoryId;
		$catData=$db->runQuery("select * from ".CATEGORY." where cat_id='".$categoryId."'");
		$this->view->catImage=$catData[0]['cat_image'];
		$this->view->pageHeading="Edit Category";
		if ($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			$myform = new Form_Classified($categoryId);			
			if ($myform->isValid($request->getPost()))
			{				
				$dataForm=$myform->getValues();
				$myObj=new Classified();
				$Result=$myObj->UpdateCategory($dataForm,$categoryId);
				if($Result==1)
				{
				$mySession->errorMsg ="Classified Category Updated  successfully.";
				$this->_redirect('classified/index');
				}
				else
				{
				$mySession->errorMsg ="Category name you entered is already exists.";
				$this->view->myform = $myform;
				$this->render('editcategory');
				}
			}
			else
			{	
				$this->view->myform = $myform;
				$this->render('editcategory');
			}
		}
		else
		{			
			$this->_redirect('classified/editcategory/categoryId/'.$categoryId);
		}
	}
	
	public function deletecategoryAction()
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
					$condition="cat_id='".$Id."'"; 
					$db->delete(CATEGORY,$condition);
					$db1->delete(Classified,$condition);
				}
			}
		}		
		exit();
	}
//classified

public function classifiedcategoryAction()
	{
	global $mySession;
	$this->view->pageHeading="Manage Classifieds";
	}


	public function generategridclassifiedAction()
	{
		global $mySession;
		$this->_helper->viewRenderer->setNoRender();
		$db=new Db();
		$page=$this->getRequest()->page;
		$rp=$this->getRequest()->rp;
		$sortname=$this->getRequest()->sortname;
		$sortorder=$this->getRequest()->sortorder;
		if (!$sortname) $sortname = 'c_name';
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
		$limit = " LIMIT $start, $rp";
		$qry="select * from ". classified." inner join ".CATEGORY." on ".CATEGORY.".cat_id= ". classified.".cat_id ";  
//		echo $qry.$where.$sort.$limit; exit();
		$ResData=$db->runQuery("$qry $where $sort $limit");		
		$countQuery=$db->runQuery("$qry $where");
		$total=count($countQuery);		
		$json = "";
		$json .= "{\n";
		$json .= "page: $page,\n";
		$json .= "total: $total,\n";
		$json .= "rows: [";
		$rc = false;
		if(isset($ResData[0]) && $ResData[0]['c_id']!="")
		{
		$start=$start+1;
		$i=1;
		foreach($ResData as $row)
		{
		if($row['status']==1){ $image ='tick.gif';  }
			if($row['status']==0){ $image ='cross.png';  } 
			if ($rc) $json .= ",";
			$json .= "\n{";
			$json .= "id:'".$row['cat_id']."',";
			$json .= "cell:['".$start."'";
			$json .= ",'<input name=\'check".$i."\' id=\'check".$i."\' value=\'".$row['c_id']."\' onchange=\'return check_check(\"bcdel\",\"deletebcchk\")\' type=\'checkbox\'><script>$(\'#bcdel\').html(\'\');document.getElementById(\'deletebcchk\').checked = false;</script>'";
			$json .= ",'".addslashes($row['c_name'])."'";
			$json .= ",'".addslashes($row['cat_name'])."'";
			$json .= ",'<a href=\'".APPLICATION_URL_ADMIN."classified/editclassified/c_id/".$row['c_id']."\'><img src=\'".IMAGES_URL_ADMIN."edit.png\' border=\'0\' title=\'Edit\' alt=\'Edit\'></a>'";
			$json .= ",'<a id=\'".$start."\' onclick=\'changeclassifiedstatus(".$row['c_id'].",".$row['status'].")\' ><img src=\'".IMAGES_URL_ADMIN."$image\' border=\'0\' title=\'Status\' alt=\'Status\'></a>'";
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
	
	public function addclassifiedAction()
	{
	global $mySession;
	$myform=new Form_Classifiedcategory ();
	$this->view->myform=$myform;
		$this->view->pageHeading="Add New Classified";
	}
	public function saveclassifiedAction()
	{
		global $mySession;
		$db=new Db();
		
		$this->view->pageHeading="Add New Classified";
		if ($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			$myform = new Form_Classifiedcategory();	
					
			if ($myform->isValid($request->getPost()))
			{				
				$dataForm=$myform->getValues();

				$myObj=new Classified();
				$Result=$myObj->Saveclassified($dataForm);
				if($Result==1)
				{
				$mySession->errorMsg ="Classified added successfully.";
				$this->_redirect('classified/classifiedcategory');
				}
				else
				{
				$mySession->errorMsg ="Classified name you entered is already exists.";
				$this->view->myform = $myform;
				$this->render('addclassified');
				}
			}
			else
			{	
				$this->view->myform = $myform;
				$this->render('addclassified');
			}
		}
		else
		{			
			$this->_redirect('classified/addclassified');
		}
	}
	
	public function editclassifiedAction()
	{
		global $mySession;
		$db=new Db();
		$c_id=$this->getRequest()->getParam('c_id'); 
		$this->view->c_id=$c_id;
		$myform=new Form_Classifiedcategory($c_id);
		
		
		$this->view->myform=$myform;
		$this->view->pageHeading="Edit Classified";
	}
	public function updateclassifiedAction()
	{
		global $mySession;
		$db=new Db();
		$c_id=$this->getRequest()->getParam('c_id'); 
		$this->view->c_id=$c_id;

		$catData=$db->runQuery("select * from ".Classified." where c_id='".$c_id."'");

		$this->view->pageHeading="Edit Classified";

		if ($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			$myform = new Form_Classifiedcategory($c_id);			
			if ($myform->isValid($request->getPost()))
			{				
				$dataForm=$myform->getValues();
				$myObj=new Classified();

				$Result=$myObj->Updateclassified($dataForm,$c_id);
				if($Result==1)
				{
				$mySession->errorMsg ="Classified updated successfully.";
				$this->_redirect('classified/classifiedcategory');
				}
				else
				{
				$mySession->errorMsg ="Classified name you entered is already exists.";
				$this->view->myform = $myform;
				$this->render('editclassified');
				}
			}
			else
			{	
				$this->view->myform = $myform;
				$this->render('editclassified');
			}
		}
		else
		{			
			$this->_redirect('classified/editclassified/c_id/'.$c_id);
		}

	}
	public function changeclassifiedstatusAction()
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
	  $data_update['status']=$status; 
	  $condition="c_id='".$BcID."'";
	  $db->modify(Classified,$data_update,$condition);    
	  exit();
	}

	public function deleteclassifiedAction()
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
					$condition="c_id='".$Id."'"; 
					$db->delete(Classified,$condition);
				}
			}
		}		
		exit();
	}
	
}