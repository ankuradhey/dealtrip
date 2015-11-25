<?PHP
class Form_Login extends Zend_Form
{
	public function init()
	{
		
		global $mySession;
		$db=new Db();	
		
		$admin_username= new Zend_Form_Element_Text('admin_username');
		$admin_username->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Fill me please'))
		->addDecorator('Errors', array('class'=>'error1'))
		->setAttrib("class","span12 text ");
		
		
		
		$admin_password= new Zend_Form_Element_Password('admin_password');
		$admin_password->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Please provide a password'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","span12 password valid");
		
	
		$this->addElements(array($admin_username,$admin_password));
	}		
}

?>