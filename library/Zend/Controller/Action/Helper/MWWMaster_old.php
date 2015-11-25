<?php

///this class is no longer used and is now kept in application/helpers folder
//to avoid problems please do not use this class again
class Zend_Controller_Action_Helper_MWWMaster_old extends Zend_Controller_Action_Helper_Abstract
{
	
/*	public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
        return $this;
    }
	
	public function preDispatch() 
	{
		global $mySession, $_CONFIG;
		
				
		$redirector =    Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
		$controllerName = $this->getRequest()->getControllerName(); 
		
		$siteObj = new SiteInformation();
		$sitewhere['Id'] = 1;
		$siteinfo = $siteObj->getSiteInformationDetail($sitewhere);
		$this->view->siteinfo = $siteinfo;
		
		
		if(isset($mySession->user['Id']) && $mySession->user['Id']!=""  && $controllerName=='register')
		{			
			$redirector->gotoUrl($mySession->user['profileHome']);	
		}
		// Add all the controller which are allowed without user login
		$allowed_controllers = array("cms", 'login', 'verify', 'index', 'register', 'subscription');

		if( !isset($mySession->user['LiscenceId']) && !in_array($controllerName, $allowed_controllers, true) ) {

			//Redirect to login page
			$redirector->gotoUrl('/login/index/');	

		} else {
			
			if( isset($mySession->user['LiscenceId']) && isset($mySession->user['FreeUser']) && $mySession->user['FreeUser'] != 'Y' && !in_array($controllerName, $allowed_controllers, true) ) {
				//if( $mySession->user['LiscenceId'] )
				$liscenceEndTime = strtotime($mySession->user['LiscenceEndDate']);
				$currentTime = time();
				if( $liscenceEndTime <= $currentTime ) {
					$redirector->gotoUrl('/subscription/subscriptionexpired/');	
				}				
			}				
		}
		//pr($_SESSION['default']['user']);
	}
	
	private function _isBackendPlan() 
	{
		global $mySession, $_CONFIG;
		//$_CONFIG['AdminManagedRole']
	}	
	
	public function liscenceInfo()
	{		
		global $mySession, $_CONFIG;
	    echo "<pre>";	
	    	
			$Liscence = new LiscenceBearer();
			
			$whereL['Id'] = $mySession->user['LiscenceId'];
			$whereL['UserId'] = $mySession->user['UserId'];
			$whereL['LiscenceStatus'] = 'A';
			$LiscenceResult = $Liscence->getLiscenceBearerDetail($whereL);
						
			$studentcount = 0;
			if(is_array($LiscenceResult))
			{
				//get the count of the students
				$studentObj = new Student();
				$wherest['LiscenceId'] = $mySession->user['LiscenceId'];
				$wherest['LiscenceType'] = $mySession->user['Role'];
				
				if(in_array($mySession->user['Role'],array('ESL','MSL','HSL')))
				{
					$wherest['LiscenceType'] = 'SL';
				}
				$studentresult = $studentObj->getStudentList('',$wherest);
				if(is_array($studentresult) && (!empty($studentresult)))
				{
					$studentcount = count($studentresult);
				}
				$LiscenceResult['PresentStudentCount'] = $studentcount;
				//echo ($studentcount); die;
				
				$SubscriptionPlan = new SubscriptionPlan();
				$whereS['Id'] = $LiscenceResult['SubscriptionPlanId'];
				//$whereS['PlanType'] = $LiscenceResult['SubscriptionPlanType'];  	//commented to get the value in the liscence profiles
				
				$SubscriptionPlanResult =  $SubscriptionPlan->getSubscriptionPlanDetail($whereS);
				if(is_array($SubscriptionPlanResult))
				{
					$LiscenceResult['PlanTitle'] = $SubscriptionPlanResult['PlanTitle'];
					
					// Reminder....
					$dayDiff = dateDiff(date('Y-m-d H:i:s'), $LiscenceResult['LiscenceEndDate'] );
					
					$LiscenceResult['Reminder'] = $dayDiff;
					
					if(in_array($mySession->user['Role'],$_CONFIG['AdminManagedRole']) || $mySession->user['Role']=='CRF')
					{
						//$this->view->displayEditBillingLink = 'N';
						$LiscenceResult['displayEditBillingLink'] = 'N';
					}
					else
					{
						//$this->view->displayEditBillingLink = 'Y';
						$LiscenceResult['displayEditBillingLink'] = 'Y';
					}
					//echo $this->view->displayEditBillingLink; die;
					//echo "<pre>";
					//print_r($LiscenceResult); die;
					return $LiscenceResult;	
				}				
			}
			else 
			{
				return false;
			}    	
	}
		*/
}
