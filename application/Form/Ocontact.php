<?PHP
class Form_Ocontact extends Zend_Form
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
			
		
		$full_name_value="";$phone_value="";$email_value="";$comment_value="";$subject_value = "";$property_no_value = "";
		
		if($cid != "")
		{
			
			$ppty = $db->runQuery("select * from ".PROPERTY." where id = '".$cid."' ");
			$property_no_value = $cid;
			
			$subject_value = "enquiry about property number - ".$ppty[0]['propertycode'];
				
		}
		
	
		
		
		$full_name= new Zend_Form_Element_Text('full_name');
		$full_name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>"Please enter Full Name"))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")
		->setValue($full_name_value);	
		 
		$email_address= new Zend_Form_Element_Text('email_address');
		$email_address->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>"enter email address"))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required email")		
		->setValue($email_value);
		
		
		$phone = new Zend_Form_Element_Text('phone');
		$phone->setAttrib("class","mws-textinput")
		->setValue($phone_value);
		
		
	/*	$property_no = new Zend_Form_Element_Text('property_no');
		$property_no->setAttrib("class","mws-textinput")		
		->setAttrib("maxlength",25)
		->setValue($property_no_value);*/
		
			
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
		
		$question = new Zend_Form_Element_Textarea('question');
		$question->setRequired(true)
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","required")
		->setAttrib("rows","4")
		->setAttrib("cols","30")
		->setAttrib("maxlength","300")
		->addValidator('NotEmpty',true,array('messages' =>"Enter message"));
		

		/*$check = new Zend_Form_Element_Hidden('check');
		$check->setRequired(true)
		->setValue($text);*/
		
		$this->addElements(array($full_name,$email_address,$phone,$subject,$question));	
	}
}	

?>