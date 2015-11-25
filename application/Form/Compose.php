<?PHP
class Form_Compose extends Zend_Form
{  
	public function __construct($userType="1") //0 => admin,  1 => users 
	{
		global $mySession;
		$this->init($userType);
	}
    public function init($userType)
	{   
	

		global $mySession;
		$db=new Db();
		$receiver_id_value="";
		$message_subject_value="";
		$message_text_value="";

		
		if($userType == '1'):
		
			$ReceiverData=$db->runQuery("select ".ADMINISTRATOR.".user_id,".ADMINISTRATOR.".admin_fullname as ReceiverName,
										 admin_email from ".ADMINISTRATOR." where ".ADMINISTRATOR.".user_id = '0' ");
										 
			
			$ReceiverArr=array();
			$ReceiverArr[0]['key']="0";
			$ReceiverArr[0]['value']=$ReceiverData['ReceiverName'];
		else:
			$ReceiverArr=array();
			$ReceiverArr[0]['key']="";
			$ReceiverArr[0]['value']="- - Select User - -";
			
			
			$ReceiverData=$db->runQuery("select ".USERS.".user_id,concat(first_name,' ',last_name) as ReceiverName,
										 email_address from ".USERS." where ".USERS.".user_status = '1' ");
		endif;				

		

		if($ReceiverData!="" and count($ReceiverData)>0)
		{
			$i=1;
			foreach($ReceiverData as $key=>$ReceiverValues)
			{
				$ReceiverArr[$i]['key']= $ReceiverValues['user_id'];
				$ReceiverArr[$i]['value']=     $ReceiverValues['ReceiverName'];//"<".$ReceiverValues['email_address'].">".$ReceiverValues['ReceiverName'];
				$i++;
			}
		}
		
		$receiver_id= new Zend_Form_Element_Select('receiver_id');
		$receiver_id->setRequired(true)
		->addMultiOptions($ReceiverArr)
		->addValidator('NotEmpty',true,array('messages' =>'Receiver is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")
		->setValue($receiver_id_value);
		$this->addElement($receiver_id);	
		
	
		
		$message_subject= new Zend_Form_Element_Text('message_subject');
		$message_subject->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Message subject is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")
		->setValue($message_subject_value);
		$this->addElement($message_subject);
			
				
		$message_text= new Zend_Form_Element_Textarea('message_text');
		$message_text->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Message is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")
		->setValue($message_text_value);
		$this->addElement($message_text);	
	}
}
?>