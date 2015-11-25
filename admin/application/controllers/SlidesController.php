<?php
__autoloadDB('Db');
class SlidesController extends AppController
{
	/*protected  $mySession;
	protected $db;
	protected $Action_name;
	protected $actionArray = array( '0' => 'index', '1' => 'latestppty');
	
	function __construct()
	{
		
		global $mySession;
		$this->db =  new Db();	
		$this->mySession = &$mySession;
		
		parent::init();
		$action_name = $this->myActionName;
		$router = array_keys($this->actionArray,$action_name);

		switch($router[0])
		{
			case '0': $this->indexAction(); break;
			case '1': $this->latestpptyAction(); break;
			default : $this->_redirect("error/error");
		}
		
		
		
	}*/
	
	
	public function indexAction()
	{
		global $mySession;
		$db=new Db();

	}
	
	public function latestpptyAction()
	{
			global $mySession;
			$db=new Db();
			$this->view->pageHeading="List Of Latest Properties";
			$latestpptyArr =  $db->runQuery("select * from  ".SLIDES_PROPERTY." 
                                                        inner join ".PROPERTY." on ".PROPERTY.".id = ".SLIDES_PROPERTY.".lppty_property_id
                                                        inner join ".USERS." on ".USERS.".user_id = ".PROPERTY.".user_id
                                                        left join ".GALLERY." on ".GALLERY.".property_id = ".PROPERTY.".id
                                                        where lppty_type = '0' 
                                                        group by lppty_id
                                                        order by lppty_order asc
							");
			$this->view->latestpptyArr = $latestpptyArr;
			
	}
	
	public function addlatestpptyAction()
	{
		global $mySession;
		$db=new Db();
		$this->view->pageHeading="Add New Latest Property";
		$myform = new Form_Slides('0');
		$this->view->myform = $myform;
		
		if($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			if ($myform->isValid($request->getPost()))
			{				
				$dataForm=$myform->getValues();
				$myObj=new Slides();
				$Result=$myObj->saveSlides($dataForm,0);
				if($Result > 0)
				{
					$mySession->sucessMsg ="Latest Property Added successfully.";
					$this->_redirect('slides/latestppty');
				}
				else
				{
					$mySession->errorMsg ="An Error Occurred while trying to save the data";
				}
			}
		}
		
	}
	
	public function orderslidesAction()
	{
		global $mySession;
		$db = new Db();
		$this->_helper->layout()->disableLayout();	

		$current_element_id = $_REQUEST['id'];
		$fromPosition = $_REQUEST['fromPosition'];
		$toPosition = $_REQUEST['toPosition'];
		$direction = $_REQUEST['direction'];
		$group = $_REQUEST['group'];

		$lppty_type = $this->getRequest()->getParam("lppty_type");
		
		
		switch($lppty_type)
		{
			case '1': $where = " where lppty_type = '1' ";	break;
			case '0': $where = " where lppty_type = '0' "; break;
			case '2':  $where = " where lppty_type = '2' "; break;			  	
			default: $where = " where lppty_type = '0' "; break;
			
		}
		$chkposArr = $db->runQuery("select * from  ".SLIDES_PROPERTY."  $where 
									order by lppty_order asc  ");
		
		
			
			/*prd($chkposArr);*/
		$dataUpdate['lppty_order'] = $chkposArr[$toPosition-1]['lppty_order'];
		$condition = "lppty_id = ".$current_element_id;
		$db->modify(SLIDES_PROPERTY,$dataUpdate,$condition);
		
		$dataUpdate['lppty_order'] = $chkposArr[$fromPosition-1]['lppty_order'];
		$condition = "lppty_id = ".$chkposArr[$toPosition-1]['lppty_id'];
		$db->modify(SLIDES_PROPERTY,$dataUpdate,$condition);
		exit;
	}
	
	public function editlatestpptyAction()
	{
		global $mySession;
		$db = new Db();
		
		$this->view->pageHeading="Edit Latest Property";
		$lId = $this->getRequest()->getParam("lId");
		
		$myform = new Form_Slides('0',$lId);
		$this->view->myform = $myform;
		
		if($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			if ($myform->isValid($request->getPost()))
			{				
				$dataForm=$myform->getValues();
				$myObj=new Slides();
				$Result=$myObj->saveSlides($dataForm,0,$lId);
				if($Result > 0)
				{
					$mySession->sucessMsg ="Latest Property Modified successfully.";
					$this->_redirect('slides/latestppty');
				}
				else
				{
					$mySession->errorMsg ="An Error Occurred while trying to save the data";
				}
			}
		}
		
			
	}
	
	/****************************************************************/
	// Function for changing the status of the property slides      //
	/****************************************************************/
	
	public function changestatusAction()
	{
		global $mySession;
		$db = new Db();
		$status = $_REQUEST['Status'];
		$lId =  $_REQUEST['Id'];
		$lppty_type = $this->getRequest()->getParam("lppty_type");
		
		$myObj=new Slides();
		$Result=$myObj->changeStatus($lId,$status);
		
		if($Result > 0)
		{
			$mySession->sucessMsg ="Latest Property Modified successfully.";
		}
		else
		{
			$mySession->errorMsg ="An Error Occurred while trying to modify the data";
		}
		exit;
	}
	
	/****************************************************************/
	// Function for deleting of the property slides                 //
	/****************************************************************/
	
	public function deleteslidesAction()
	{
		global $mySession;
		$db = new Db();
		
		$lId =  $_REQUEST['Id'];
		$lppty_type = $this->getRequest()->getParam("lppty_type");

		$myObj=new Slides();
		$Result=$myObj->deleteSlides($lId,$lppty_type);
		
		if($Result > 0)
		{
			$mySession->sucessMsg ="Latest Property Deleted successfully.";
		}
		else
		{
			$mySession->errorMsg ="An Error Occurred while trying to delete the data";
		}
		exit;
	}
	
	
	
	/****************************************************************/
	// Function for diplaying list  of the  popular property        //
	/****************************************************************/
	
	public function popularpptyAction()
	{	
		global $mySession;
		$db = new Db();
		
		$this->view->pageHeading="List Of Popular Properties";
		$popularpptyArr =  $db->runQuery("select * from  ".SLIDES_PROPERTY." 
										 inner join ".PROPERTY." on ".PROPERTY.".id = ".SLIDES_PROPERTY.".lppty_property_id
										 inner join ".USERS." on ".USERS.".user_id = ".PROPERTY.".user_id
										 left join ".GALLERY." on ".GALLERY.".property_id = ".PROPERTY.".id
										 where lppty_type = '1' 
										 group by lppty_id
										 order by lppty_order asc
										 ");
		$this->view->popularpptyArr = $popularpptyArr;	
		
	}
	
	/****************************************************************/
	// Function for adding  popular property                        //
	/****************************************************************/
	
	
	public function addpopularpptyAction()
	{
		global $mySession;
		$db=new Db();
		$this->view->pageHeading="Add New Popular Property";
		$myform = new Form_Slides('1');
		$this->view->myform = $myform;
		
		if($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			if ($myform->isValid($request->getPost()))
			{				
				$dataForm=$myform->getValues();
				$myObj=new Slides();
				$Result=$myObj->saveSlides($dataForm,1);
				if($Result > 0)
				{
					$mySession->sucessMsg ="Popular Property Added successfully.";
					$this->_redirect('slides/popularppty');
				}
				else
				{
					$mySession->errorMsg ="An Error Occurred while trying to save the data";
				}
			}
		}
		
	}
	
	/****************************************************************/
	// Function for adding  popular property                        //
	/****************************************************************/
	
	
	public function editpopularpptyAction()
	{
		global $mySession;
		$db = new Db();
		
		$this->view->pageHeading="Edit Popular Property";
		$lId = $this->getRequest()->getParam("lId");
		
		$myform = new Form_Slides('1',$lId);
		$this->view->myform = $myform;
		
		if($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			if ($myform->isValid($request->getPost()))
			{				
				$dataForm=$myform->getValues();
				$myObj=new Slides();
				$Result=$myObj->saveSlides($dataForm,1,$lId);
				if($Result > 0)
				{
					$mySession->sucessMsg ="Latest Property Modified successfully.";
					$this->_redirect('slides/popularppty');
				}
				else
				{
					$mySession->errorMsg ="An Error Occurred while trying to save the data";
				}
			}
		}
	}
	
	/****************************************************************/
	// Function for diplaying list  of the  special property        //
	/****************************************************************/
	
	public function spclpptyAction()
	{
		global $mySession;
		$db=new Db();
		$this->view->pageHeading="List Of Special Properties";
		
		$spclpptyArr =  $db->runQuery("select * from  ".SLIDES_PROPERTY." 
										 inner join ".PROPERTY." on ".PROPERTY.".id = ".SLIDES_PROPERTY.".lppty_property_id
										 inner join ".USERS." on ".USERS.".user_id = ".PROPERTY.".user_id
										 left join ".GALLERY." on ".GALLERY.".property_id = ".PROPERTY.".id
										 where lppty_type = '2' 
										 group by lppty_id
										 order by lppty_order asc
										 ");
										 
		$this->view->spclpptyArr = $spclpptyArr;	
		
	}
	
	/****************************************************************/
	// Function for adding  popular property                        //
	/****************************************************************/
	
	
	public function addspclpptyAction()
	{
		global $mySession;
		$db=new Db();
		$this->view->pageHeading="Add New Special Property";
		$myform = new Form_Slides('2');
		$this->view->myform = $myform;
		
		if($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			if ($myform->isValid($request->getPost()))
			{				
				$dataForm=$myform->getValues();
				$myObj=new Slides();
				$Result=$myObj->saveSlides($dataForm,2);
				if($Result > 0)
				{
					$mySession->sucessMsg ="Special Property Added successfully.";
					$this->_redirect('slides/spclppty');
				}
				else
				{
					$mySession->errorMsg ="An Error Occurred while trying to save the data";
				}
			}
		}
		
	}
	
	/****************************************************************/
	// Function for editing  special property                       //
	/****************************************************************/
	
	
	public function editspclpptyAction()
	{
		global $mySession;
		$db = new Db();
		
		$this->view->pageHeading="Edit Special Property";
		$lId = $this->getRequest()->getParam("lId");

		$myform = new Form_Slides('2',$lId);
		$this->view->myform = $myform;
		
		if($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			if ($myform->isValid($request->getPost()))
			{				
				$dataForm=$myform->getValues();
				$myObj=new Slides();
				$Result=$myObj->saveSlides($dataForm,2,$lId);
				if($Result > 0)
				{
					$mySession->sucessMsg ="Speical Property Modified successfully.";
					$this->_redirect('slides/spclppty');
				}
				else
				{
					$mySession->errorMsg ="An Error Occurred while trying to save the data";
				}
			}
		}
	}
	
	/****************************************************************/
	// Function for diplaying list  of the  latest review           //
	/****************************************************************/
	
	public function latestreviewAction()
	{
		$db=new Db();
		$this->view->pageHeading="List Of Latest Reviews";
		
	
		$spclpptyArr =  $db->runQuery("select ".LATEST_REVIEW.".*, ".PROPERTY.".propertycode, ".GALLERY.".image_name from  ".LATEST_REVIEW." 
                                               inner join ".PROPERTY." on ".PROPERTY.".id = ".LATEST_REVIEW.".r_property_id 
                                                left join ".GALLERY." on ".GALLERY.".property_id = ".LATEST_REVIEW.".r_property_id
                                                group by r_id        
                                                order by r_order asc
										 ");
										 
		$this->view->latestpptyArr = $spclpptyArr;	

	}
	
	/****************************************************************/
	//  ADD latest review           								//
	/****************************************************************/
	public function addlatestreviewAction()
	{
		$db = new Db();
		$this->view->pageHeading="List Of Latest Reviews";	
		$myform = new Form_Latestreview();
		$this->view->myform = $myform;
		
		if($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			if ($myform->isValid($request->getPost()))
			{				
				$dataForm=$myform->getValues();
				$myObj = new Slides();
				$Result = $myObj->saveLatestReview($dataForm,$lId);
			
				if($Result > 0)
				{
					$mySession->sucessMsg ="Latest Review Added successfully.";
					$this->_redirect('slides/latestreview');
				}
				else
				{
					$mySession->errorMsg ="An Error Occurred while trying to save the data";
				}
			}
		}

	}
	
	/****************************************************************/
	//  EDIT latest review           								//
	/****************************************************************/
	
	public function editlatestreviewAction()
	{
		global $mySession;
		$db = new Db();
		
		$this->view->pageHeading="Edit Latest  Review";
		$lId = $this->getRequest()->getParam("lId");

		$myform = new Form_Latestreview($lId);
		$this->view->myform = $myform;
		
		if($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			if($myform->isValid($request->getPost()))
			{				
				$dataForm=$myform->getValues();
				$myObj = new Slides();
				$Result = $myObj->saveLatestReview($dataForm,$lId);
				if($Result > 0)
				{
					$mySession->sucessMsg ="Latest Review Modified successfully.";
					$this->_redirect('slides/latestreview');
				}
				else
				{
					$mySession->errorMsg ="An Error Occurred while trying to save the data";
				}
			}
		}
	}
	
	/****************************************************************/
	// Function for changing the status of the property slides      //
	/****************************************************************/
	
	public function changereviewstatusAction()
	{
		global $mySession;
		$db = new Db();
		$status = $_REQUEST['Status'];
		$lId =  $_REQUEST['Id'];
		$lppty_type = $this->getRequest()->getParam("lppty_type");
		$myObj = new Slides();
		$Result = $myObj->changeReviewStatus($lId,$status);
		if($Result > 0)
		{
			$mySession->sucessMsg ="Latest Property Modified successfully.";
		}
		else
		{
			$mySession->errorMsg ="An Error Occurred while trying to modify the data";
		}
		exit;
	}
	
	/****************************************************************/
	// Function for deleting of the review property slides           //
	/****************************************************************/
	
	public function deletelatestreviewsAction()
	{
		global $mySession;
		$db = new Db();
		$lId =  $_REQUEST['Id'];
		$lppty_type = $this->getRequest()->getParam("lppty_type");
		$myObj=new Slides();
		$Result=$myObj->deleteLatestReview($lId,$lppty_type);
		
		if($Result > 0)
		{
			$mySession->sucessMsg ="Latest Property Deleted successfully.";
		}
		else
		{
			$mySession->errorMsg ="An Error Occurred while trying to delete the data";
		}
		exit;
	}


	public function orderreviewslidesAction()
	{
		global $mySession;
		$db = new Db();
		$this->_helper->layout()->disableLayout();	

		$current_element_id = $_REQUEST['id'];
		$fromPosition = $_REQUEST['fromPosition'];
		$toPosition = $_REQUEST['toPosition'];
		$direction = $_REQUEST['direction'];
		$group = $_REQUEST['group'];

		$chkposArr = $db->runQuery("select * from  ".LATEST_REVIEW."   
									order by r_order asc  ");
		
		$dataUpdate['r_order'] = $chkposArr[$toPosition-1]['r_order'];
		$condition = "r_id = ".$current_element_id;
		$db->modify(LATEST_REVIEW,$dataUpdate,$condition);
		
		$dataUpdate['r_order'] = $chkposArr[$fromPosition-1]['r_order'];
		$condition = "r_id = ".$chkposArr[$toPosition-1]['r_id'];
		$db->modify(LATEST_REVIEW,$dataUpdate,$condition);
		exit;
	}
	
}
?>