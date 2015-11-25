<?PHP
class Form_Oreview extends Zend_Form
{  
	public function __construct($userId)
	{
	
		global $mySession;
		$this->init($userId);
	}
    public function init($userId)
	{   
		global $mySession;
        $db=new Db();
			
		
		$full_name_value="";$location_value="";$check_in_value="";$rating_value="";$headline_value = "";$comment_value = "";$review_value = "";
		/*if($userId != "")
		{
			$userData = $db->runQuery("select * from ".OWNER_REVIEW." where property_id = '".$userId."' ");	
			
			if($userData != "" && count($userData) > 0)
			{
				$full_name_value = $userData[0]['owner_name'];
				$location_value = $userData[0]['location'];
				$check_in_value = $userData[0]['check_in'];
				$rating_value = $userData[0]['rating'];
				$headline_value = $userData[0]['headline'];
				$comment_value = $userData[0]['comment'];
				$review_value = $userData[0]['review'];
			}
			
		}*/
		
		
		$full_name= new Zend_Form_Element_Text('full_name');
		$full_name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>"Please enter Full Name"))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")
		->setAttrib("maxlength","50")
		->setValue($full_name_value);	
		 
		$location = new Zend_Form_Element_Text('location');
		$location->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>"enter email address"))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")		
		->setAttrib("maxlength","50")		
		->setValue($location_value);
		
		
		$check_in = new Zend_Form_Element_Text('check_in');
		$check_in->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>"Please enter phone number"))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")
		->setValue($check_in_value);
		
		$ratingArr = array();
		$ratingArr[0]['key'] = "";
		$ratingArr[0]['value'] = "- - Select - -";
		
		for($i=1;$i<=10;$i++)
		{
			$ratingArr[$i]['key'] = $i;
			$ratingArr[$i]['value'] = $i;
		
		}
		
		$rating = new Zend_Form_Element_Select('rating');
		$rating->setRequired(true)
		->addMultiOptions($ratingArr)
		->addValidator('NotEmpty',true,array('messages' =>"enter email address"))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")		
		->setValue($rating_value);
			
		$headline= new Zend_Form_Element_Text('headline');
		$headline->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>"enter email address"))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required reviewHeadline")		
		->setAttrib("maxlength","100")		
		->setValue($headline_value);
		
		
		
		$comment= new Zend_Form_Element_Textarea('comment');
		$comment
		->addDecorator('Errors', array('class'=>'error'))
		//->setAttrib("class","required")
		->setAttrib("rows","4")
		->setAttrib("cols","30")
		->setAttrib("maxlength","300")
		//->addValidator('NotEmpty',true,array('messages' =>"Enter message"))
		->setValue($comment_value);
		
		
		
		$review = new Zend_Form_Element_Textarea('review');
		$review->setRequired(true)
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","required")
		->setAttrib("rows","4")
		->setAttrib("cols","30")
		->setAttrib("maxlength","1000")
		->addValidator('NotEmpty',true,array('messages' =>"Enter message"))
		->setValue($review_value);		
		

		$step = new Zend_Form_Element_Hidden('step');
		$step->setRequired(true)
		->setValue("8");
		/*$check = new Zend_Form_Element_Hidden('check');
		$check->setRequired(true)
		->setValue($text);*/
		
		$this->addElements(array($full_name,$location,$check_in,$rating,$headline,$comment,$review,$step));	
	}
}	

?>