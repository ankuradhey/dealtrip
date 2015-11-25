<?PHP
class Form_Forgotpassword extends Zend_Form
{  
	public function init()
	{   
		global $mySession;
        				
		$username= new Zend_Form_Element_Text('username');
		$username->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Username is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required checkvaliduser");	
		
		$email_address= new Zend_Form_Element_Text('email_address');
		$email_address->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Email address is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required email checkemail");
		
		if(@$_REQUEST['email_address']!="")
		{
		$email_address-> addValidator('EmailAddress', true)
		->addErrorMessage('Please enter a valid email address');
		}
			
			
		
				
		$this->addElements(array($email_address));		
	}
}
?>