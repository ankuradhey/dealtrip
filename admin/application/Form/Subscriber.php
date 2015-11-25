<?php
class Form_Subscriber extends Zend_Form
{  
	public function __construct($subscriberId="")
	{
		$this->init($subscriberId);
	}
        
        public function init($subscriberId)
	{   
		global $mySession;
                $db=new Db();
		
		$CountryId="";$StateId="";$CityName="";
                
                
                if($subscriberId!="")
		{
			$subscriberData=$db->runQuery("select * from subscriber where subscriber_id='".$subscriberId."'");		
			$subscriber_name_value = $subscriberData[0]['subscriber_name'];
			$subscriber_url_value = $subscriberData[0]['subscriber_url'];
			$subscriber_api_username_value = $subscriberData[0]['subscriber_api_username'];
			$subscriber_api_password_value = $subscriberData[0]['subscriber_api_password'];
		}
		
		
		$subscriber_name = new Zend_Form_Element_Text('subscriber_name');
		$subscriber_name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Subscriber Name is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required") 	
		->setValue($subscriber_name_value);	
		
                $subscriber_url = new Zend_Form_Element_Text('subscriber_url');
		$subscriber_url->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Subscriber url is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required") 	
		->setValue($subscriber_url_value);	
		
                $subscriber_api_username = new Zend_Form_Element_Text('subscriber_api_username');
		$subscriber_api_username->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Subscriber API username is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required") 	
		->setValue($subscriber_api_username_value);	
		
                $subscriber_api_password = new Zend_Form_Element_Text('subscriber_api_password');
		$subscriber_api_password->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Subscriber API password is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required") 	
		->setValue($subscriber_api_password_value);	
		
		$this->addElements(array($subscriber_name, $subscriber_url, $subscriber_api_username,$subscriber_api_password));
	}
}	

?>