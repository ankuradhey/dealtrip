<?php
__autoloadDB('Db');
class ServicesController extends AppController
{
	public function categoriesAction()
	{
	global $mySession;
	$this->view->pageHeading="Manage Services Categories";
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
		if (!$sortname) $sortname = 'category_name';
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
		$qry="select * from ".SERVICE_CATEGORIES."";  
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
			if($row['cat_status']==1){ $image ='tick.gif';  }
			if($row['cat_status']==0){ $image ='cross.png';  } 
			if ($rc) $json .= ",";
			$json .= "\n{";
			$json .= "id:'".$row['cat_id']."',";
			$json .= "cell:['".$start."'";
			$json .= ",'<input name=\'check".$i."\' id=\'check".$i."\' value=\'".$row['cat_id']."\' onchange=\'return check_check(\"bcdel\",\"deletebcchk\")\' type=\'checkbox\'><script>$(\'#bcdel\').html(\'\');document.getElementById(\'deletebcchk\').checked = false;</script>'";
			$json .= ",'".addslashes($row['category_name'])."'";
			$json .= ",'<a href=\'".APPLICATION_URL_ADMIN."services/editcategory/categoryId/".$row['cat_id']."\'><img src=\'".IMAGES_URL_ADMIN."edit.png\' border=\'0\' title=\'Edit\' alt=\'Edit\'></a>'";
			$json .= ",'<a id=\'".$start."\' onclick=\'changestatus(".$row['cat_id'].",".$row['cat_status'].")\' ><img src=\'".IMAGES_URL_ADMIN."$image\' border=\'0\' title=\'Status\' alt=\'Status\'></a>'";
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
				$myObj=new Services();
				
				$Result=$myObj->SaveCategory($dataForm);
				if($Result==1)
				{
				$mySession->errorMsg ="Coupon Category added successfully.";
				$this->_redirect('services/categories');
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
			$this->_redirect('services/addcategory');
		}
	}
	public function editcategoryAction()
	{
	global $mySession;
	$db=new Db();
	$categoryId=$this->getRequest()->getParam('categoryId'); 
	$this->view->categoryId=$categoryId;
	$myform=new Form_Category($categoryId);
	$catData=$db->runQuery("select * from ".SERVICE_CATEGORIES." where cat_id='".$categoryId."'");
	$this->view->catImage=$catData[0]['cat_img_path'];
	$this->view->myform=$myform;
	$this->view->pageHeading="Edit Category";
	}
	public function updatecategoryAction()
	{
		global $mySession;
		$db=new Db();
		$categoryId=$this->getRequest()->getParam('categoryId'); 
		$this->view->categoryId=$categoryId;
		$catData=$db->runQuery("select * from ".SERVICE_CATEGORIES." where cat_id='".$categoryId."'");
		$this->view->catImage=$catData[0]['cat_img_path'];
		$this->view->pageHeading="Edit Category";
		if ($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			$myform = new Form_Category($categoryId);			
			if ($myform->isValid($request->getPost()))
			{				
				$dataForm=$myform->getValues();
				$myObj=new Services();
				$Result=$myObj->UpdateCategory($dataForm,$categoryId);
				if($Result==1)
				{
				$mySession->errorMsg ="Coupon Category Updated updated successfully.";
				$this->_redirect('services/categories');
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
			$this->_redirect('services/editcategory/categoryId/'.$categoryId);
		}
	}
	public function changecatstatusAction()
	{ // echo'hello';exit();
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
	  $data_update['cat_status']=$status; 
	  $condition="cat_id='".$BcID."'";
	  $db->modify(SERVICE_CATEGORIES,$data_update,$condition);
	    
	  exit();
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
					$db->delete(SERVICE_CATEGORIES,$condition);
				}
			}
		}		
		exit();
	}
//sub cate here

	public function subcateAction()
	{
	global $mySession;
	$this->view->pageHeading="Manage Services Sub Categories";
	}


	public function generategridsubcategoriesAction()
	{
		global $mySession;
		$this->_helper->viewRenderer->setNoRender();
		$db=new Db();
		$page=$this->getRequest()->page;
		$rp=$this->getRequest()->rp;
		$sortname=$this->getRequest()->sortname;
		$sortorder=$this->getRequest()->sortorder;
		if (!$sortname) $sortname = 'subcate_name';
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
		$qry="select * from ".SUBCATE." inner join ".SERVICE_CATEGORIES." on ".SERVICE_CATEGORIES.".cat_id= ".SUBCATE.".cat_id ";  
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
		if(isset($ResData[0]) && $ResData[0]['subcate_id']!="")
		{
		$start=$start+1;
		$i=1;
		foreach($ResData as $row)
		{
			if($row['subcate_status']==1){ $image ='tick.gif';  }
			if($row['subcate_status']==0){ $image ='cross.png';  } 
			if ($rc) $json .= ",";
			$json .= "\n{";
			$json .= "id:'".$row['cat_id']."',";
			$json .= "cell:['".$start."'";
			$json .= ",'<input name=\'check".$i."\' id=\'check".$i."\' value=\'".$row['subcate_id']."\' onchange=\'return check_check(\"bcdel\",\"deletebcchk\")\' type=\'checkbox\'><script>$(\'#bcdel\').html(\'\');document.getElementById(\'deletebcchk\').checked = false;</script>'";
			$json .= ",'".addslashes($row['subcate_name'])."'";
			$json .= ",'".addslashes($row['category_name'])."'";
			$json .= ",'<a href=\'".APPLICATION_URL_ADMIN."services/editsubcate/subcateid/".$row['subcate_id']."\'><img src=\'".IMAGES_URL_ADMIN."edit.png\' border=\'0\' title=\'Edit\' alt=\'Edit\'></a>'";
			$json .= ",'<a id=\'".$start."\' onclick=\'changesubcatestatus(".$row['subcate_id'].",".$row['subcate_status'].")\' ><img src=\'".IMAGES_URL_ADMIN."$image\' border=\'0\' title=\'Status\' alt=\'Status\'></a>'";
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
	
	public function addsubcateAction()
	{
		global $mySession;
		$myform=new Form_Subcate();
		$this->view->myform=$myform;
		$this->view->pageHeading="Add New Sub Category";

	}
	public function savesubcateAction()
	{
		global $mySession;
		$db=new Db();
		
		$this->view->pageHeading="Add New Sub Category";
		if ($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			$myform = new Form_Subcate();	
					
			if ($myform->isValid($request->getPost()))
			{				
				$dataForm=$myform->getValues();
//				prd($dataForm);

				$myObj=new Services();
				$Result=$myObj->Savesubcategory($dataForm);
				if($Result==1)
				{
				$mySession->errorMsg ="Sub Category added successfully.";
				$this->_redirect('services/subcate');
				}
				else
				{
				$mySession->errorMsg ="Sub category name you entered is already exists.";
				$this->view->myform = $myform;
				$this->render('addsubcate');
				}
			}
			else
			{	
				$this->view->myform = $myform;
				$this->render('addsubcate');
			}
		}
		else
		{			
			$this->_redirect('services/addsubcate');
		}
	}
	
	public function editsubcateAction()
	{
		global $mySession;
		$db=new Db();
		$subcateid=$this->getRequest()->getParam('subcateid'); 
		$this->view->subcateid=$subcateid;
		$myform=new Form_Subcate($subcateid);
		$subcateData=$db->runQuery("select * from ".SUBCATE." where subcate_id='".$subcateid."'");
		$this->view->catImage=$subcateData[0]['subcate_img_path'];
		$this->view->myform=$myform;
		$this->view->pageHeading="Edit Sub Category";
	}
	public function updatesubcateAction()
	{
		global $mySession;
		$db=new Db();
		$subcateid=$this->getRequest()->getParam('subcateid'); 
		$this->view->subcateid=$subcateid;

		$catData=$db->runQuery("select * from ".SUBCATE." where subcate_id='".$subcateid."'");
		$this->view->catImage=$catData[0]['subcate_img_path'];
		$this->view->pageHeading="Edit Sub Category";

		if ($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			$myform = new Form_Subcate($subcateid);			
			if ($myform->isValid($request->getPost()))
			{				
				$dataForm=$myform->getValues();
				$myObj=new Services();

				$Result=$myObj->Updatesubcate($dataForm,$subcateid);
				if($Result==1)
				{
				$mySession->errorMsg ="Sub Category updated successfully.";
				$this->_redirect('services/subcate');
				}
				else
				{
				$mySession->errorMsg ="Sub Category name you entered is already exists.";
				$this->view->myform = $myform;
				$this->render('editsubcate');
				}
			}
			else
			{	
				$this->view->myform = $myform;
				$this->render('editsubcate');
			}
		}
		else
		{			
			$this->_redirect('services/editsubcate/subcateid/'.$subcateid);
		}

	}
	public function changesubcatestatusAction()
	{  //echo 'hello'; exit();
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
	  $data_update['subcate_status']=$status; 
	  $condition="subcate_id='".$BcID."'";
	  $db->modify(SUBCATE,$data_update,$condition);    
	  exit();
	}

	public function deletesubcateAction()
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
					$condition="subcate_id='".$Id."'"; 
					$db->delete(SUBCATE,$condition);
				}
			}
		}		
		exit();
	}

//end of sub cate


	public function businessesAction()
	{
	global $mySession;
	$this->view->pageHeading="Manage Business/Service Providers";
	}
	public function generategridbusinessesAction()
	{
		global $mySession;
		$this->_helper->viewRenderer->setNoRender();
		$db=new Db();
		$page=$this->getRequest()->page;
		$rp=$this->getRequest()->rp;
		$sortname=$this->getRequest()->sortname;
		$sortorder=$this->getRequest()->sortorder;
		if (!$sortname) $sortname = 'business_title';
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
		$qry="select *,concat(first_name,' ',last_name) as provider_name from ".SERVICE_BUSINESS."
		left join ".USERS." on(".SERVICE_BUSINESS.".user_id=".USERS.".user_id)";
		$ResData=$db->runQuery("$qry $where $sort $limit");		
		$countQuery=$db->runQuery("$qry $where");
		$total=count($countQuery);		
		$json = "";
		$json .= "{\n";
		$json .= "page: $page,\n";
		$json .= "total: $total,\n";
		$json .= "rows: [";
		$rc = false;
		if(isset($ResData[0]) && $ResData[0]['business_id']!="")
		{
		$start=$start+1;
		$i=1;
		foreach($ResData as $row)
		{
			if($row['business_status']==1){ $image ='tick.gif';  }
			if($row['business_status']==0){ $image ='cross.png';  } 
			if ($rc) $json .= ",";
			$json .= "\n{";
			$json .= "id:'".$row['business_id']."',";
			$json .= "cell:['".$start."'";
			$json .= ",'<input name=\'check".$i."\' id=\'check".$i."\' value=\'".$row['business_id']."\' onchange=\'return check_check(\"bcdel\",\"deletebcchk\")\' type=\'checkbox\'><script>$(\'#bcdel\').html(\'\');document.getElementById(\'deletebcchk\').checked = false;</script>'";
			$json .= ",'".addslashes($row['business_title'])."'";
			$json .= ",'".addslashes($row['provider_name'])."'";
			$json .= ",'".date(DATEFORMAT,strtotime($row['date_business_added']))."'";
			$json .= ",'<a href=\'".APPLICATION_URL_ADMIN."services/editbusiness/businessId/".$row['business_id']."\'><img src=\'".IMAGES_URL_ADMIN."edit.png\' border=\'0\' title=\'Edit\' alt=\'Edit\'></a>'";
			$json .= ",'<a id=\'".$start."\' onclick=\'changestatus(".$row['business_id'].",".$row['business_status'].")\' ><img src=\'".IMAGES_URL_ADMIN."$image\' border=\'0\' title=\'Status\' alt=\'Status\'></a>'";
			$json .= ",'<a href=\'".APPLICATION_URL_ADMIN."services/reviews/businessId/".$row['business_id']."\'><img src=\'".IMAGES_URL_ADMIN."view_review.png\' border=\'0\' title=\'Reviews\' alt=\'Reviews\'></a>'";
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
	public function editbusinessAction()
	{
		global $mySession;
		$db=new Db();
		$businessId= $this->getRequest()->getParam('businessId');
		$this->view->businessId=$businessId;
		$this->view->pageHeading="Edit Service Business";
		$countryId='218';
		if(isset($_REQUEST['country_id']))
		{
			$countryId=$_REQUEST['country_id'];
		}
		$CountryData=$db->runQuery("select * from ".COUNTRIES." ".$whereCountry." where country_id='".$countryId."'");		
		$this->view->CountryName=$CountryData[0]['country_name'];
		$businessImageData=$db->runQuery("select business_image from ".SERVICE_BUSINESS." where business_id='".$businessId."'");
		$this->view->businessImage=$businessImageData[0]['business_image'];
		$myform=new Form_Business($businessId);
		$this->view->myform=$myform;
		
		$SubscriptionLimitData=$db->runQuery("select nof_images from ".USER_SUBSCRIPTIONS." 
		join ".SUBSCRIPTIONS." on(".USER_SUBSCRIPTIONS.".plan_id=".SUBSCRIPTIONS.".plan_id)
		where user_id='".$mySession->LoggedUserId."' and is_active='1'");
		$this->view->totImage=$SubscriptionLimitData[0]['nof_images'];
	}
	public function updatebusinessAction()
	{
		global $mySession;
		$db=new Db();
		$businessId= $this->getRequest()->getParam('businessId');
		$this->view->businessId=$businessId;
		$this->view->pageHeading="Edit Service Business";
		$countryId='218';
		if(isset($_REQUEST['country_id']))
		{
			$countryId=$_REQUEST['country_id'];
		}
		$CountryData=$db->runQuery("select * from ".COUNTRIES." ".$whereCountry." where country_id='".$countryId."'");		
		$this->view->CountryName=$CountryData[0]['country_name'];
		$businessImageData=$db->runQuery("select business_image from ".SERVICE_BUSINESS." where business_id='".$businessId."'");
		$this->view->businessImage=$businessImageData[0]['business_image'];
		
		$SubscriptionLimitData=$db->runQuery("select nof_images from ".USER_SUBSCRIPTIONS." 
		join ".SUBSCRIPTIONS." on(".USER_SUBSCRIPTIONS.".plan_id=".SUBSCRIPTIONS.".plan_id)
		where user_id='".$mySession->LoggedUserId."' and is_active='1'");
		$this->view->totImage=$SubscriptionLimitData[0]['nof_images'];
		
		if ($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			$myform=new Form_Business($businessId);
			if ($myform->isValid($request->getPost()))
			{				
				$dataForm=$myform->getValues();
				$myObj=new Services();
				$Result=$myObj->UpdateBusiness($dataForm,$businessId);
				if($Result>0)
				{
				$mySession->errorMsg ="Service Business updated successfully.";
				$this->_redirect('services/businesses');
				}
				else
				{
				$mySession->errorMsg ="Business title you entered is already exists.";
				$this->view->myform = $myform;
				$this->render('editbusiness');
				}
			}
			else
			{	
				$this->view->myform = $myform;
				$this->render('editbusiness');
			}
		}
		else
		{			
		$this->_redirect('services/editbusiness/businessId/'.$businessId);
		}
	}
	public function changebusinessstatusAction()
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
	  $data_update['business_status']=$status; 
	  $condition="business_id='".$BcID."'";
	  $db->modify(SERVICE_BUSINESS,$data_update,$condition);
	    
	  exit();
	}
	public function deletebusinessAction()
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
					$condition="business_id='".$Id."'"; 
					$db->delete(SERVICE_BUSINESS,$condition);
				}
			}
		}		
		exit();
	}
	public function reviewsAction()
	{
		global $mySession;
		$db=new Db();
		$businessId= $this->getRequest()->getParam('businessId');
		$this->view->businessId=$businessId;
		$WhereCondition="";
		$this->view->AllReview=1;
		if($businessId!="")
		{
		$WhereCondition="where service_business_id='".$businessId."'";
		$this->view->AllReview=0;
		}
		$this->view->businessId=$businessId;
		
		
		$start=$this->getRequest()->getParam('start');
		if($start=="")
		{
		$start=1;	
		}
		$limit=10;
		$current = ($start-1)*$limit;
		$this->view->Start=$start;
		$this->view->Limit=$limit;
		$qry="select *,concat(first_name,' ',last_name) as fullName from ".REVIEWS." 
		join ".USERS." on(".REVIEWS.".user_id=".USERS.".user_id)
		".$WhereCondition."
		order by date_reviewed desc";
		$countData=$db->runQuery($qry);
		$reviewsData=$db->runQuery($qry." Limit $current, $limit");		
		$this->view->reviewsData=$reviewsData;
		$this->view->totalRecords=count($countData);
		
		$businessData=$db->runQuery("select * from ".SERVICE_BUSINESS." where business_id='".$businessId."'");
				
		if($businessId!="")
		{
		$this->view->pageHeading="Reviews for ".$businessData[0]['business_title'];
		}
		else
		{
		$this->view->pageHeading="All Business Reviews";		
		}
	}
	public function deletereviewAction()
	{
		global $mySession;
		$db=new Db();
		$businessId= $this->getRequest()->getParam('businessId');
		$reviewId= $this->getRequest()->getParam('reviewId');
		if($businessId!="" and $reviewId!="")
		{
		$conditionDel="service_business_id='".$businessId."' and md5(review_id)='".$reviewId."'";
		$db->delete(REVIEWS,$conditionDel);
		}
		$mySession->errorMsg ="Review deleted successfully.";
		$this->_redirect('services/reviews/businessId/'.$businessId);
	}
	public function changereviewstatusAction()
	{
		global $mySession;
		$db=new Db();
		$businessId= $this->getRequest()->getParam('businessId');
		$reviewId= $this->getRequest()->getParam('reviewId');
		$Change= $this->getRequest()->getParam('Change');
		$AllReview=$this->getRequest()->getParam('AllReview');
		
		if($businessId!="" and $reviewId!="")
		{
		$dataUpdate['review_status']=$Change;
		$conditionDel="service_business_id='".$businessId."' and md5(review_id)='".$reviewId."'";
		$db->modify(REVIEWS,$dataUpdate,$conditionDel);
		}
		$mySession->errorMsg ="Status Changed successfully.";
		if($AllReview=='1')
		{
		$this->_redirect('services/reviews/');
		}
		else
		{
		$this->_redirect('services/reviews/businessId/'.$businessId);
		}
	}
}
?>