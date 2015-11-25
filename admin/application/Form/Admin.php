<?PHP
class Form_Admin extends Zend_Form
{  
	public function __construct($adminId="")
	{
		$this->init($adminId);
	}
    public function init($adminId)
	{   
		global $mySession;
        $db=new Db();
		
		$first_name_value="";$last_name_value="";$email_address_value="";$username_value="";$admin_status_value="";$password_o_value="";$password_c_value="";
		if($adminId!="")
		{
		$adminData=$db->runQuery("select * from ".ADMINISTRATOR." where admin_id='".$adminId."'");
		$first_name_value=$adminData[0]['admin_first_name'];
		$last_name_value=$adminData[0]['admin_last_name'];
		$email_address_value=$adminData[0]['admin_email'];
		$username_value=$adminData[0]['admin_username'];
		$admin_status_value=$adminData[0]['admin_status'];
		$password_o_value=$adminData[0]['admin_password'];
		$password_c_value=$adminData[0]['admin_password'];
		}
		
		$first_name= new Zend_Form_Element_Text('first_name');
		$first_name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'First Name is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setValue($first_name_value);	
		
		$last_name= new Zend_Form_Element_Text('last_name');
		$last_name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Last Name is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setValue($last_name_value);
		
		$email_address= new Zend_Form_Element_Text('email_address');
		$email_address->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Email address is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setValue($email_address_value);
		
		$username= new Zend_Form_Element_Text('username');
		$username->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Username is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setValue($username_value);
		
		$StatusArr=array();
		$StatusArr[0]['key']="1";
		$StatusArr[0]['value']="Active";
		$StatusArr[1]['key']="0";
		$StatusArr[1]['value']="Inactive";
		
		$admin_status= new Zend_Form_Element_Select('admin_status');
		$admin_status->addMultiOptions($StatusArr)
		->setAttrib("class","textInput")
		->setValue($admin_status_value);
		
		$password_o= new Zend_Form_Element_Password('password_o');
		$password_o->setAttrib("class","textInput");
		
		$password_c= new Zend_Form_Element_Password('password_c');
		$password_c->setAttrib("class","textInput");
		
		if($adminId="" || (isset($_REQUEST['ChangePass']) && $adminId!=""))
		{
		$password_o->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Password is required.'))
		->addDecorator('Errors', array('class'=>'error'));
		
		$password_c->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Confirm password is required.'))
		->addDecorator('Errors', array('class'=>'error'));
		}
		
		$this->addElements(array($first_name,$last_name,$email_address,$username,$admin_status,$password_o,$password_c));		
	}
}	

?>