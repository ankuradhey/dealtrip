<?PHP
class Form_Subadmin extends Zend_Form
{  
	public function __construct($subadmin_id="")
	{
		$this->init($subadmin_id);
		
	}
	public function init($subadmin_id)
	{   
		global $mySession;
		$db=new Db();

		
		$first_name_value="";
		$last_name_value="";
		$emailID_value="";
		$username_value="";
		$password_value="";
		$status_value='1';

		if($subadmin_id!="")
		{
			
		$SubsData=$db->runQuery("select * from ".SUBADMIN." where subadmin_id='".$subadmin_id."'");		
		$first_name_value=$SubsData[0]['first_name'];
		$last_name_value=$SubsData[0]['last_name'];
		$emailID_value=$SubsData[0]['emailID'];
		$username_value=$SubsData[0]['username'];
		$password_value=$SubsData[0]['password'];
		$status_value=$SubsData[0]['status'];
		$priv=$SubsData[0]['priviledges'];
		$priv=explode(",",$priv);
		}


		$first_name=new Zend_Form_Element_Text('first_name');
		$first_name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Subadmin first name is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("style","text-input medium-input")
		->setValue($first_name_value);	
		
		
		$last_name= new Zend_Form_Element_Text('last_name');
		$last_name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Subadmin last name is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","text-input medium-input")
		->setValue($last_name_value);
		
		$emailID= new Zend_Form_Element_Text('emailID');
		$emailID->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Subadmin email address is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","text-input medium-input")
		->setValue($emailID_value);

		$username= new Zend_Form_Element_Text('username');
		$username->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Subadmin username is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","text-input medium-input")
		->setValue($username_value);


		$password= new Zend_Form_Element_Password('password');
		$password->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Subadmin password is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","text-input medium-input")
		->setValue($password_value);

		$StatusArr=array();
		$StatusArr[0]['key']="0";
		$StatusArr[0]['value']="Disable";
		$StatusArr[1]['key']="1";
		$StatusArr[1]['value']="Enable";
		
		$status= new Zend_Form_Element_Select('status');
		$status->addMultiOptions($StatusArr)
		->setAttrib("class","textInput")
		->setValue($status_value);
		
		
		
		$priviledges= new Zend_Form_Element_MultiCheckbox('priviledges');
		$priviledges->setRequired(true)
		->addMultiOption('configuration','Configuration')
		->addMultiOption('emailtemplates','Email Templates')
		->addMultiOption('generalsettings','General Settings')
		->addMultiOption('cmspages','CMS Pages')
		->addMultiOption('subscription','Subscription')
		->addMultiOption('calender','Calender')
		->addMultiOption('users','Users')
		->addMultiOption('blogs','Blogs')
		->addMultiOption('documents','Documents')
		->addMultiOption('action','Action Management')
		->addMultiOption('products','Products Management')
		->addMultiOption('messages','Messages')
		->addMultiOption('changepass','Change Password')
		->setValue($priv);
		
		$this->addElements(array($first_name,$last_name,$emailID,$username,$password,$status,$priviledges));		
	}
}
?>