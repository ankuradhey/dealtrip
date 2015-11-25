<?PHP

class Form_Contact extends Zend_Form
{  
	public function __construct($cid)
	{
	
		global $mySession;
		$this->init($cid);
	}
    public function init($cid)
	{   
		global $mySession;
        $db=new Db();
			
		
		$full_name_value="";$phone_value="";$email_value="";$comment_value="";$captcha_value = "";$subject_value = "";
		
		if($cid!="")
		{
		$adminData=$db->runQuery("select * from ".user." where id='".$cid."'");
		$full_name_value=$adminData[0]['fname']." ".$adminData[0]['lname'];
		$phone_value=$adminData[0]['phone'];
		$email_value=$adminData[0]['email'];
		$comment_value=$adminData[0]['comment'];
		}
		
		$full_name= new Zend_Form_Element_Text('full_name');
		$full_name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>"Please enter Full Name"))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")
		->setValue($full_name_value);	
		 
		/*$phone = new Zend_Form_Element_Text('phone');
		$phone->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>"Please enter phone number"))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("onkeypress","return checknummsp(event);")
		->setAttrib("class","mws-textinput required")
		->addValidator('Digits',true,array('messages' =>"enter proper phone number"))
		->setValue($phone_value);*/
		
		if(isset($_REQUEST['phone']))
		{
		  $stringLength = new Zend_Validate_StringLength($_POST['phone']);
		  $stringLength->setMin(10);
		  $stringLength->setMax(10);
		  $phone->addValidator($stringLength);
		}
		
		
		
		$email_address= new Zend_Form_Element_Text('email_address');
		$email_address->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>"enter email address"))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required email")		
		->setValue($email_value);
		
		$cemail_address= new Zend_Form_Element_Text('cemail_address');
		$cemail_address->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>"enter email address"))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required email")		
		->setAttrib("equalTo","#email_address")
		->setValue($email_value);
		
	
		$subject= new Zend_Form_Element_Text('subject');
		$subject->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>"enter email address"))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")		
		->setValue($subject_value);
		
/*		$comment= new Zend_Form_Element_Textarea('comment');
		$comment->setRequired(true)
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","required")
		->setAttrib("rows","4")
		->setAttrib("cols","30")
		->addValidator('NotEmpty',true,array('messages' =>"Enter message"));*/
		
		$enquiry  = new Zend_Form_Element_Textarea('enquiry');
		$enquiry->setRequired(true)
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","required")
		->setAttrib("rows","4")
		->setAttrib("cols","30")
		->addValidator('NotEmpty',true,array('messages' =>"Enter message"));
		
		/*$captcha  = new Zend_Form_Element_Text('captcha');
		$captcha->setRequired(true)
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")

		->addValidator('NotEmpty',true,array('messages' =>"Enter captcha"));*/
		
		/*$check = new Zend_Form_Element_Hidden('check');
		$check->setRequired(true)
		->setValue($text);*/
		
		$this->addElements(array($full_name,$email_address,$cemail_address,$subject,$enquiry));	
	}
}	

?>