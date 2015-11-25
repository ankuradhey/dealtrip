<?PHP
class Form_Resetpassword extends Zend_Form
{  
	public function init()
	{   
		global $mySession;
        				
		$o_password= new Zend_Form_Element_Password('o_password');
		$o_password->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Current password is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required");
	
		
		$new_password= new Zend_Form_Element_Password('new_password');
		$new_password->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'New password is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")
		->setAttrib("id","new_password");	
		
		$confirm_new_password= new Zend_Form_Element_Password('confirm_new_password');
		$confirm_new_password->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Confirm new password is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput equals required")
		->setAttrib("id","confirm_new_password");
						
				
				
		$this->addElements(array($o_password,$new_password,$confirm_new_password));		
	}
}
?>