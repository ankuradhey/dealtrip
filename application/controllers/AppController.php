<?php 
class AppController extends Zend_Controller_Action 
{	
    public function init()
    {
		global $mySession;
		$db=new Db();
		$myControllerName=Zend_Controller_Front::getInstance()->getRequest()->getControllerName();		
		$myActionName=Zend_Controller_Front::getInstance()->getRequest()->getActionName();
		$paramName = Zend_Controller_Front::getInstance()->getRequest()->getParam("ppty");
		$paramStep = Zend_Controller_Front::getInstance()->getRequest()->getParam("step");
		
                //pr($this->view->url());
                //canonicalization
                //$this->view->headLink()->headLink(array('rel' => 'canonical', 'href' => canonicalUrl()), 'PREPEND');
                
		/*if($myControllerName != "booking" && ($myActionName != "index" || $myActionName != "process" || $myActionName != "processbook" || $myActionName != "checkvaliduser" || $myActionName != "getvoucher" ) && ($myControllerName != "search" && $myActionName != "searchdetail"))
		{
			unset($mySession->noOfNights);
			unset($mySession->arrivalDate);
			unset($mySession->partySize);
			unset($mySession->totalCost);
			unset($mySession->steps);
			unset($mySession->bookingUser);			
			
			
		}*/
		
	if($myControllerName == "booking" && $myActionName == "index" && $paramName != "")
	{
			if($mySession->step != '1' && $paramStep != "1" && !isset($mySession->noOfNights))	
			{
					$mySession->step = '1';
					$this->_redirect(APPLICATION_URL."booking/index/ppty/".$paramName."/step/1");
					
			}
			
	}
		
		
		
		if(isset($mySession->LoggedUserId) and @$mySession->LoggedUserId!="" )
		{
			$loggedUserData=$db->runQuery("select * from ".USERS." where user_id='".$mySession->LoggedUserId."'");
			$this->view->loggedUserData=$loggedUserData[0];
			/** unsetting session for adding property **/
			
			if($myActionName != 'addproperty' && $myActionName != 'upload' && 
                                $myActionName != 'uploaded' && $myActionName != 'deleteimg' && 
                                $myActionName != 'sortimages' && $myActionName != 'setsession' && 
                                $myActionName != 'savecalendarstat' && $myActionName != 'processpage' && 
                                $myActionName != 'setcaption' && $myActionName != 'getstatebycountry' && 
                                $myActionName != 'getcitiesbystate' && $myActionName != 'getsubareabycity' && 
                                $myActionName != 'getlocalareabysub' && $myActionName != 'processphoto' && 
                                $myActionName != 'processcal' && $myActionName != 'getrates' && 
                                $myActionName != 'setrates' && $myActionName != 'deleterentalrate' && 
                                $myActionName != 'getextras' && $myActionName != 'saveextras' && 
                                $myActionName != 'deleteextras' && $myActionName != 'saveoffers' && 
                                $myActionName != 'deactivateoffers' && $myActionName != 'ownerimageupload' && 
                                $myActionName != 'processrental' && $myActionName != 'savereview' && 
                                $myActionName != 'checkheadline' && $myActionName != 'savereviewreply' && 
                                $myActionName != 'editproperty' && $myActionName != 'floorplan' && 
                                $myActionName != 'setcaldefault' && $myActionName != "setcurrency" && 
                                $myActionName != "instuctionupload" && $myActionName != "deleteinstruction" && 
                                $myActionName != "updaterate" && $myActionName != "preview" && 
                                $myActionName != 'deletefloorplan' && $myActionName != 'doajaxfileupload' &&
                                $myActionName != 'uptodate')
			{
                            
                            //prd($myActionName);
                                
                                //$this->_redirect(APPLICATION_URL.$myActionName);
                            
				unset($mySession->activate);
				unset($mySession->property_id); 
				unset($mySession->step);
				unset($mySession->ppty_no);
				unset($mySession->reviewImage);
				
			}
			
			
			
			/*if($myControllerName == "myaccount" && ($myActionName == "addproperty" || $myActionName == "editproperty") && $mySession->property_id == "" && $paramStep != "")
			{
					$this->_redirect(APPLICATION_URL."myaccount");
					
			}*/
			
			

			
		}
		else
		{
			


			
			if(  $myControllerName != "signin" && $myControllerName!="signup"  && $myControllerName!="index" && $myControllerName!="contents" && $myControllerName!="search" && $myControllerName!="booking"  && $myControllerName != "location"  && !isset($mySession->adminId))	
			{				
				$mySession->OneBackUrl="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				echo "<script>parent.top.location='".APPLICATION_URL."signin/index';</script>";
			}
		
                        
                        if(!isset($mySession->adminId))
			if( $myActionName == "changepassword" || $myActionName == "profile" || $myActionName == "booking" || $myControllerName == "myaccount")
			$this->_redirect();
		
		}

		$defineData=$db->runQuery("select * from ".ADMINISTRATOR." where admin_id='1'");
		define("ADMIN_EMAIL",$defineData[0]['admin_email']);
		define("SITE_NAME",$defineData[0]['site_title']);
		define("PAYPAL_EMAIL",$defineData[0]['paypal_email']);
	}
}
?>


