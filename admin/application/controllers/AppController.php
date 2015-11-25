<?php 
class AppController extends Zend_Controller_Action 
{
	
    public function init()
    {
        
		global $mySession;
		$db=new Db();
		$myControllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();		
		$myActionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
		$defineData=$db->runQuery("select * from ".ADMINISTRATOR." where admin_id='1'");		
		define("ADMIN_EMAIL",$defineData[0]['admin_email']);
		define("SITE_NAME",$defineData[0]['site_title']);
		define("PRICE_SYMBOL",$defineData[0]['currency_symbol']);
		

		if($mySession->adminId=="" && $myControllerName!='index')
		{				
			$mySession->errorMsg ="Please login now to access administrator control panel.";
			$this->_redirect('index');
		}		
		if($mySession->adminId != "" && $myControllerName=='index' && $myActionName=='index')
		{	
			$this->_redirect('dashboard');
		}		
		
		if($mySession->adminId != ""){
                    
                    if($myActionName != 'addproperty' && $myActionName != 'upload' && $myActionName != 'uploaded' && $myActionName != 'deleteimg' && $myActionName != 'sortimages' && $myActionName != 'setsession' && $myActionName != 'savecalendarstat' && $myActionName != 'processpage' && $myActionName != 'setcaption' && $myActionName != 'getstatebycountry' && $myActionName != 'getcitiesbystate' && $myActionName != 'getsubareabycity' && $myActionName != 'getlocalareabysub' && $myActionName != 'processphoto' && $myActionName != 'processcal' && $myActionName != 'getrates' && $myActionName != 'setrates' && $myActionName != 'deleterentalrate' && $myActionName != 'getextras' && $myActionName != 'saveextras' && $myActionName != 'deleteextras' && $myActionName != 'saveoffers' && $myActionName != 'deactivateoffers' && $myActionName != 'ownerimageupload' && $myActionName != 'processrental' && $myActionName != 'savereview' && $myActionName != 'checkheadline' && $myActionName != 'savereviewreply' && $myActionName != 'editproperty'                     && $myActionName != 'floorplan' && $myActionName != 'setcaldefault' && $myActionName != "setcurrency" && $myActionName != "instuctionupload" && $myActionName != "deleteinstruction" && $myActionName != "updaterate" && $myActionName != "preview"
			&& $myActionName != 'deletefloorplan' && $myActionName != 'doajaxfileupload'
			)
			{	
				unset($mySession->activate);
				unset($mySession->admin_property_id); 
				unset($mySession->step);
				unset($mySession->ppty_no);
				unset($mySession->reviewImage);
				
				
			}
                    
                }
                
                $msgArr = $db->runQuery("select message_id,concat(first_name,' ',last_name) as sender_name, message_text, date_message_sent 
								from ".MESSAGES." 
							    inner join ".USERS." on ".USERS.".user_id = ".MESSAGES.".sender_id
								where ".MESSAGES.".del_receiver = '0' and ".MESSAGES.".read = '0' and receiver_id = '0'
								order by ".MESSAGES.".message_id desc
								 ");
		$this->view->latestMsg = $msgArr;
		
		$notifyArr = $db->runQuery('Select * from '.BOOKING.' 
									inner join '.PROPERTY.' on '.BOOKING.'.property_id = '.PROPERTY.'.id 
									inner join '.USERS.' on '.BOOKING.'.user_id = '.USERS.'.user_id 
									where '.BOOKING.'.booking_notify = "0"
									order by '.BOOKING.'.booking_id desc 
									');
									
		$this->view->notifyArr = $notifyArr;
    }
}
?>