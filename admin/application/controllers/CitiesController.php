<?php
__autoloadDB('Db');
class CitiesController extends AppController
{
	public function indexAction()
	{
		global $mySession;
		$this->view->pageHeading="Manage Cities";
	}
	public function generategridcitiesAction()
	{//echo "hello"; exit();
		global $mySession;
		$this->_helper->viewRenderer->setNoRender();
		$db=new Db();
		$page=$this->getRequest()->page;
		$rp=$this->getRequest()->rp;
		$sortname=$this->getRequest()->sortname;
		$sortorder=$this->getRequest()->sortorder;
		if (!$sortname) $sortname = 'name';
		if (!$sortorder) $sortorder =  'asc ';		
		$where="where 1=1 ";
		if(@$_POST['query']!='')
		{
			$where .= " and LOWER(".$_POST['qtype'].") LIKE '%".strtolower($_POST['query'])."%' ";			
		}
		$sort = " ORDER BY $sortname $sortorder ";					
		if (!$page) $page = 1;
		if (!$rp) $rp = 10;		
		$start = (($page-1) * $rp);		
		$limit = "LIMIT $start, $rp";
		$qry="select * from ".ADMIN_CITIES." ";  
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
		if(isset($ResData[0]) && $ResData[0]['id']!="")
		{
		$start=$start+1;
		$i=1;
		foreach($ResData as $row)
		{
			if ($rc) $json .= ",";
			$json .= "\n{";
			$json .= "id:'".$row['id']."',";
			$json .= "cell:['".$start."'";
			$json .= ",'<input name=\'check".$i."\' id=\'check".$i."\' value=\'".$row['id']."\' onchange=\'return check_check(\"bcdel\",\"deletebcchk\")\' type=\'checkbox\'><script>$(\'#bcdel\').html(\'\');document.getElementById(\'deletebcchk\').checked = false;</script>'";
			$json .= ",'".addslashes(strip_tags($row['name']))."'";
			$json .= ",'".addslashes(strip_tags($row['zipcode']))."'";
			$json .= ",'<a href=\'".APPLICATION_URL_ADMIN."cities/editcities/Id/".$row['id']."\'><img src=\'".IMAGES_URL_ADMIN."edit.png\' border=\'0\' title=\'Edit\' alt=\'Edit\'></a>'";
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
	
	public function addcitiesAction()
	{

	global $mySession;
	$myform=new Form_Admincities();
	$this->view->myform=$myform;

	$this->view->pageHeading="Add New City";
	}
	public function savecitiesAction()
	{
		global $mySession;
		$db=new Db();
		$this->view->pageHeading="Add New City";
		if ($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			$myform = new Form_Admincities();			
			if ($myform->isValid($request->getPost()))
			{				
				$dataForm=$myform->getValues();
				$chkQry=$db->runQuery("select * from ".ADMIN_CITIES." where name='".$dataForm['name']."'");
				if($chkQry!="" and count($chkQry)>0)
				{
					$mySession->errorMsg ="City you entered is already exists.";
					$this->view->myform = $myform;
					$this->render('addcities');		
				}
				else
				{
					$dataInsert['name']=$dataForm['name'];
					$dataInsert['zipcode']=$dataForm['zipcode'];
					$db->save(ADMIN_CITIES,$dataInsert);
					$mySession->errorMsg ="City added successfully.";
					$this->_redirect('cities/index');
				}
			}
			else
			{	
				$this->view->myform = $myform;
				$this->render('addcities');
			}
		}
		else
		{			
			$this->_redirect('cities/addcities');
		}
	}
	public function editcitiesAction()
	{
	global $mySession;
	$Id=$this->getRequest()->getParam('Id'); 
	$this->view->Id=$Id;
	$myform=new Form_Admincities($Id);
	$this->view->myform=$myform;
	$this->view->pageHeading="Edit City";
	}
	public function updatecitiesAction()
	{
		global $mySession;
		$db=new Db();
		$Id=$this->getRequest()->getParam('Id'); 
		$this->view->Id=$Id;
		$this->view->pageHeading="Edit City";
		if ($this->getRequest()->isPost())
		{ 
			$request=$this->getRequest();
			$myform = new Form_Admincities($Id);			
			if ($myform->isValid($request->getPost()))
			{
				$dataForm=$myform->getValues();
				$dataInsert['name']=$dataForm['name'];
				$dataInsert['zipcode']=$dataForm['zipcode'];
				$condition=" id=".$Id; 
				$db->modify(ADMIN_CITIES,$dataInsert,$condition);
				$mySession->errorMsg ="City updated Successfully.";
				$this->_redirect('cities/index');
			}
			else
			{	
				$this->view->myform = $myform;
				$this->render('editcities');
			}
		}
		else
		{			
			$this->_redirect('cities/editcities/Id/'.$Id);
		}
	}
	
	public function deletecitiesAction()
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
					$condition="id='".$Id."'"; 
					$db->delete(ADMIN_CITIES,$condition);
				}
			}
		}		
		exit();
	}

	
}
?>