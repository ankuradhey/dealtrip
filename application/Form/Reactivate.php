<?PHP
class Form_Reactivate extends Zend_Form
{  
	public function init()
	{   
		global $mySession;
        				
		
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
				
		$this->addElements(array($new_password,$confirm_new_password));		
	}
}
?>