<?PHP
class Form_Signin extends Zend_Form
{  
	public function init()
	{   
		global $mySession;
        				
		$email_address= new Zend_Form_Element_Text('email_address');
		$email_address->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Email address is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("id","email_address")		
		->setAttrib("class","mws-textinput required noSpace");
		
		$password= new Zend_Form_Element_Password('password');
		$password->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Password is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("id","password")
		->setAttrib("class","mws-textinput required");
				
		$this->addElements(array($email_address,$password));		
	}
}
?>