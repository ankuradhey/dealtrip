<?PHP
class Form_Arrival extends Zend_Form
{  
	public function __construct($ppty_id = "")
	{
		global $mySession;
		$this->init($ppty_id);
	}
	public function init($ppty_id)
	{   
		global $mySession;
      	$db = new Db();
		
		$address_value = "";$telephone_value = "";$website_value ="";$master_cal_value = "";$company_name_value = "";
		$agent_name_value = "";$agent_address_value = "";$agent_telephone_value = "";$agent_email_value = "";
		$agent_website_value = "";$direction_property_value = "";$key_instruction_value = "";
		$late_instruction_value = "";$property_name_value = "";$emergency_value = "";
		
		if($ppty_id != "")
		{
			$arrivalValue = $db->runQuery("select * from ".PROPERTY." where id = '".$ppty_id."' ");	
			$property_name_value = $arrivalValue['0']['property_name'];
			$address_value = $arrivalValue['0']['address1'];
			$telephone_value = $arrivalValue['0']['telephone'];
			$emergency_value = $arrivalValue['0']['emergency_no'];
			$website_value = $arrivalValue['0']['website'];
			$master_cal_value = $arrivalValue['0']['master_cal_url'];
			$company_name_value = $arrivalValue['0']['agent_name'];
			$agent_name_value = $arrivalValue['0']['agent_person'];
			$agent_address_value = $arrivalValue['0']['agent_address'];
			$agent_telephone_value = $arrivalValue['0']['agent_phone'];
			$agent_email_value = $arrivalValue['0']['agent_email'];
			$agent_website_value = $arrivalValue['0']['agent_website'];
			$direction_property_value = $arrivalValue['0']['directions_to_property'];			
			$key_instruction_value = $arrivalValue['0']['key_instructions'];
			$late_instruction_value = $arrivalValue['0']['late_arrival_instruction'];
			//$late_instruction_value = $arrivalValue['0']['late_arrival_instruction'];
			
		}

		$property_name = new Zend_Form_Element_Text('property_name');
		$property_name->setAttrib("class","mws-textinput")
		->setAttrib("maxlength","20")
		->setValue($property_name_value);
		
		
		
		$address= new Zend_Form_Element_Textarea('address');
		$address->setAttrib("class","mws-textinput")
		->setAttrib("rows","4")
		->setAttrib("cols","30")
		->setValue($address_value);
		
		$telephone = new Zend_Form_Element_Text('telephone');
		$telephone->setAttrib("class","mws-textinput number")
		->setAttrib("maxlength","15")
		->setValue($telephone_value);
		
		$emergency = new Zend_Form_Element_Text('emergency');
		$emergency->setAttrib("class","mws-textinput number")
		->setAttrib("maxlength","15")
		->setValue($emergency_value);
		
		
		$website = new Zend_Form_Element_Text('website');
		$website->setAttrib("class","mws-textinput validUrl")
    	->setValue($website_value);		
		
		$master_cal = new Zend_Form_Element_Text('master_cal');
		$master_cal->setAttrib("class","mws-textinput validUrl")
    	->setValue($master_cal_value);		
		
		
		
		////** Agent details **////
		
		
		/*company name*/
		
		$company_name = new Zend_Form_Element_Text('company_name');
		$company_name->setAttrib("class","mws-textinput")
		->setAttrib("maxlength","50")
		->setValue($company_name_value);	

		/*agent_name*/
		$agent_name = new Zend_Form_Element_Text('agent_name');
		$agent_name->setAttrib("class","mws-textinput")
		->setAttrib("maxlength","50")
		->setValue($agent_name_value);
		
		/*agent_address*/
		$agent_address = new Zend_Form_Element_Text('agent_address');
		$agent_address->setAttrib("class","mws-textinput")
		->setAttrib("maxlength","200")
		->setValue($agent_address_value);
		
	    /*agent_telephone*/
		$agent_telephone = new Zend_Form_Element_Text('agent_telephone');
		$agent_telephone->setAttrib("class","mws-textinput")
		->setAttrib("maxlength","200")
		->setValue($agent_telephone_value);	
		
		/*agent_email*/
		$agent_email = new Zend_Form_Element_Text('agent_email');
		$agent_email->setAttrib("class","mws-textinput")
		->setAttrib("maxlength","200")
		->setValue($agent_email_value);	
		
		/*agent_website*/
		$agent_website = new Zend_Form_Element_Text('agent_website');
		$agent_website->setAttrib("class","mws-textinput validUrl")
		->setAttrib("maxlength","200")
		->setValue($agent_website_value);
		
		
		////*** instructions**////	
		/* directions to the property*/
		
		
		$direction_property = new Zend_Form_Element_Textarea('direction_property');
		$direction_property->setAttrib("class","mws-textinput")
		->setAttrib("maxlength","250")
		->setValue($direction_property_value);
		
		/* Key Instructions to the property*/
		
		
		$key_instruction = new Zend_Form_Element_Textarea('key_instruction');
		$key_instruction->setAttrib("class","mws-textinput")
		->setAttrib("maxlength","250")
		->setValue($key_instruction_value);
		
		/* Late Instructions to the Property */
		
		$late_instruction = new Zend_Form_Element_Textarea('late_instruction');
		$late_instruction->setAttrib("class","mws-textinput")
		->setAttrib("maxlength","250")
		->setValue($late_instruction_value);
		
		
		/* Arrival Instructions to the Property */
		
		$arrival_instruction = new Zend_Form_Element_File('arrival_instruction');
		$arrival_instruction->setDestination(SITE_ROOT.'uploads/instructions/')
		->setAttrib("class","arrivalInstruction")

		//->addValidator('Extension', false, 'pdf,doc')
		;	
		
		$step = new Zend_Form_Element_Hidden('step');
		$step->setValue("9");
		
		$this->addElements(array($property_name,$address,$telephone,$emergency,$website,$master_cal,$agent_name,$agent_address,$agent_telephone,$agent_email,$agent_website,$direction_property,$key_instruction,$late_instruction,$arrival_instruction,$company_name,$step));			
		
	}
	
}