<?PHP
class Form_Changepassword extends Zend_Form
{
	public function init()
	{
		global $mySession;
		$db=new Db();		
		$new_password= new Zend_Form_Element_Password('new_password');
		$new_password->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Please enter you new password.'))
		->addDecorator('Errors', array('class'=>'errormsg'))
		->setAttrib("class","mws-textinput required");
		
		$confirm_new_password= new Zend_Form_Element_Password('confirm_new_password');
		$confirm_new_password->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Please confirm your new password.'))
		->addDecorator('Errors', array('class'=>'errormsg'))
		->setAttrib("class","mws-textinput required");
				
		$this->addElements(array($new_password,$confirm_new_password));
	}		
}

?>