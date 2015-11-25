<?PHP
class Form_Compose extends Zend_Form
{  
	public function __construct($userType="1",$userId = "") //0 => admin,  1 => users 
	{
		global $mySession;
		$this->init($userType,$userId);
	}
    public function init($userType,$userId)
	{   
	

		global $mySession;
		$db=new Db();
		$receiver_id_value="";
		$message_subject_value="";
		$message_text_value="";

		if($userType == '1'):
			$ReceiverArr=array();
			$ReceiverArr[0]['key']="0";
			$ReceiverArr[0]['value']="ADMIN";
		else:
			$ReceiverArr=array();
			if($userId != "")
			$ReceiverData=$db->runQuery("select ".USERS.".user_id,concat(first_name,' ',last_name) as ReceiverName,
										 email_address, uType from ".USERS." where ".USERS.".user_id != '".$userId."'  
										 and ".USERS.".user_status = '1'
										 ");
			else
			$ReceiverData=$db->runQuery("select ".USERS.".user_id,concat(first_name,' ',last_name) as ReceiverName,
										 email_address, uType from ".USERS." where ".USERS.".user_status = '1' ");
		endif;				

		

		if($ReceiverData!="" and count($ReceiverData)>0)
		{
			$i=1;
			foreach($ReceiverData as $key=>$ReceiverValues)
			{
				$usertype = ($ReceiverValues['uType'] == '2')?"Owner":"Customer";
				$ReceiverArr[$i]['key'] = $ReceiverValues['user_id'];
				$ReceiverArr[$i]['value']=  $ReceiverValues['email_address']." [".$usertype."]";
				$i++;
			}
		}
		
		
		$box1View= new Zend_Form_Element_Select('box1View');
		$box1View->addMultiOptions($ReceiverArr)
		->setAttrib("multiple","multiple")
		->setRegisterInArrayValidator(false)
		->setAttrib("style","width:100%;height:250px;")
		->setValue($receiver_id_value);
		$this->addElement($box1View);	
		
		$box2View= new Zend_Form_Element_Select('box2View');
		$box2View->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Reciever is required'))
		->setRegisterInArrayValidator(false)		
		->setAttrib("multiple","multiple")
		->setAttrib("style","width:100%;height:250px;");
		$this->addElement($box2View);	

		if($userId != ""):
			$ReceiverData=$db->runQuery("select ".USERS.".user_id,concat(first_name,' ',last_name) as ReceiverName,
											 email_address, uType from ".USERS." where ".USERS.".user_id = '".$userId."'  ");		
			$ReceiverArr = array();								 
			$usertype = ($ReceiverValues['uType'] == '2')?"Owner":"Customer";
			$ReceiverArr[0]['key'] = $ReceiverData[0]['user_id'];
			$ReceiverArr[0]['value'] = $ReceiverData[0]['email_address']." [".$usertype."]";;
			$box2View->addMultiOptions($ReceiverArr);
		endif;
	
		
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