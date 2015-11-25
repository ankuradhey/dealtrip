<?PHP
class Form_Configuration extends Zend_Form
{  
    public function init()
	{   
		global $mySession;
        $db=new Db();
		
		$ConfigData=$db->runQuery("select * from ".ADMINISTRATOR." where admin_id='1'");	
		
		$SiteTitle=$ConfigData[0]['site_title'];
		$MetaDescription=$ConfigData[0]['site_description'];
		$MetaKeyword=$ConfigData[0]['site_keyword'];
		$AdminEmail=$ConfigData[0]['admin_email'];
		$PaypalEmail=$ConfigData[0]['paypal_email'];
		$welcomemessageC=$ConfigData[0]['welcomemessage'];
		$admin_name_value = 		$ConfigData[0]['admin_fullname'];
		
		
		$site_title= new Zend_Form_Element_Text('site_title');
		$site_title->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Site title is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")
		->setValue($SiteTitle);
		
		
		$admin_name = new Zend_Form_Element_Text('admin_name');
		$admin_name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Admin name is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")
		->setValue($admin_name_value);
		
		$site_description= new Zend_Form_Element_Text('site_description');
		$site_description->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Site description is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")
		->setValue($MetaDescription);
		
		$site_keyword= new Zend_Form_Element_Text('site_keyword');
		$site_keyword->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Site keyword is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")
		->setValue($MetaKeyword);
		
		$admin_email= new Zend_Form_Element_Text('admin_email');
		$admin_email->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Administrator email is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required email")
		->setValue($AdminEmail);
		
		$paypal_email= new Zend_Form_Element_Text('paypal_email');
		$paypal_email->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Paypal email address is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required email")
		->setValue($PaypalEmail);
		
		/*$currency_symbol= new Zend_Form_Element_Text('currency_symbol');
		$currency_symbol->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Currency symbol is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","required")
		->setValue($CurrencySymbol);*/
		
		
		$welcomemessage= new Zend_Form_Element_Textarea('welcomemessage');
		$welcomemessage->setAttrib("id","elrte")->setAttrib("cols","auto")
		->setAttrib("rows","auto")
		->setValue($welcomemessageC);
		
		$this->addElements(array($site_title,$admin_name,$site_description,$site_keyword,$admin_email,$paypal_email,$welcomemessage));
		
	}
}	

?>