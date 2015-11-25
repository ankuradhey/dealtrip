<?PHP
class Form_Review extends Zend_Form
{  
	public function __construct($review_id = "")
	{
		global $mySession;
		$this->init($review_id);
	}
	public function init($review_id)
	{   
		global $mySession;
                $db = new Db();   
		
		$airport1_value = ""; $review_date_value = ""; $property_value = ""; $status_value = "";$review_value = "";$description_value="";
		$amenityArr = $db->runQuery("select * from ".OWNER_REVIEW." where review_status = '1' ");
		
		if($review_id != "")
		{
			$pptyData = $db->runQuery("select guest_name, property_title, review_date, location, review_status, check_in, review,  
									   ".OWNER_REVIEW.".rating as rating, headline, comment from ".OWNER_REVIEW." 
									   inner join ".PROPERTY." on ".PROPERTY.".id = ".OWNER_REVIEW.".property_id 
									   where review_id = '".$review_id."' ");	
				
			$user_value = $pptyData[0]['guest_name'];
			$property_value = $pptyData[0]['property_title'];
			$review_date_value = date('d-m-Y',strtotime($pptyData[0]['review_date']));
			$location_value = $pptyData[0]['location'];
			$status_value = $pptyData[0]['review_status'];
			$check_in_value = date('d-m-Y',strtotime($pptyData[0]['check_in']));
			$rating_value = $pptyData[0]['rating'];
			$review_value = $pptyData[0]['review'];
			$headline_value = $pptyData[0]['headline'];
			$comment_value = $pptyData[0]['comment'];

		}

		$i = 0;
		

		$full_name= new Zend_Form_Element_Text('full_name');
		$full_name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>"Please enter Full Name"))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")
		->setAttrib("maxlength","50")
		->setValue($user_value);	
		 
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
		->setAttrib("class","mws-textinput mws-datepicker required ")
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
		->addValidator('NotEmpty',true,array('messages' =>"Enter Rating"))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")		
		->setValue($rating_value);
			
		$headline = new Zend_Form_Element_Text('headline');
		$headline->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>"Enter Review Headline"))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required reviewHeadline")		
		->setAttrib("maxlength","100")		
		->setValue($headline_value);
		
		
		
		$comment= new Zend_Form_Element_Textarea('comment');
		$comment
                //->setRequired(true)
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","required")
		->setAttrib("rows","4")
		->setAttrib("cols","30")
		->setAttrib("maxlength","300")
		//->addValidator('NotEmpty',true,array('messages' =>"Enter Comment"))
		->setValue($comment_value);
		
		
		
		$review = new Zend_Form_Element_Textarea('review');
		$review->setRequired(true)
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","required")
		->setAttrib("rows","4")
		->setAttrib("cols","30")
		->setAttrib("maxlength","1000")
		->addValidator('NotEmpty',true,array('messages' =>"Enter Review"))
		->setValue($review_value);		
		
		$statusArr[0]['key'] = "";
		$statusArr[0]['value'] = "- - Select - ";
		
		$statusArr[1]['key'] = "0";
		$statusArr[1]['value'] = "Disable";

		$statusArr[2]['key'] = "1";
		$statusArr[2]['value'] = "Enable";
		
		$status = new Zend_Form_Element_Select('status');
		$status->setRequired(true)
		->addMultiOptions($statusArr)
		->setValue($status_value);
		
		
		$this->addElements(array($full_name,$location,$check_in,$rating,$headline,$comment,$review,$status));	

		
	}
}