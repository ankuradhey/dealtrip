<?PHP
class Form_Ownproperty extends Zend_Form
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
		$country_id_value = ""; $state_id_value = "";$city_id_value="";$address_value = "";$zipcode_value = "";$telephone_value="";
		$website_value = "";$no_of_bedroom_value = "";$no_of_bathroom_value = "";$no_of_en_suite_bathroom_value = "";		
		$occupancy_value = "";$company_name_value = "";	$agent_name_value="";$agent_address_value="";$agent_telephone="";	
		$agent_email_value = "";$direction_property_value="";$key_instruction_value="";$late_instruction_value="";
		$arrival_instruction_value = "";$title_value = "";$introduction_value = "";
		$sub_area_value = "";$local_area_value = "";
		$accomodation_type_value = "";
		$rating_value = "";
		
		if($ppty_id != "")
		{
			$pptyData = $db->runQuery("select * from ".PROPERTY." where id = '".$ppty_id."' ");	
			$title_value = $pptyData[0]['property_title'];
			$introduction_value = $pptyData[0]['brief_desc'];
			$country_id_value = $pptyData[0]['country_id'];
			$state_id_value = $pptyData[0]['state_id'];
			$city_id_value = $pptyData[0]['city_id'];
			$sub_area_value = $pptyData[0]['sub_area_id'];
			$local_area_value = $pptyData[0]['local_area_id'];
			$zipcode_value = $pptyData[0]['zipcode'];
			$accomodation_type_value = $pptyData[0]['property_type'];
			$no_of_bedroom_value = $pptyData[0]['bedrooms'];
			$no_of_bathroom_value = $pptyData[0]['bathrooms'];
			$no_of_en_suite_bathroom_value = $pptyData[0]['en_bedrooms'];
			$occupancy_value = $pptyData[0]['maximum_occupancy'];
			//$rating_value = $pptyData[0]['star_rating'];

		}
		
		$title =  new Zend_Form_Element_Text('title');
		$title->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Title is required.'))
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setAttrib("class","mws-textinput required limitmatch")
		->setAttrib("placeholder","Up to 100 Characters")		
		->setAttrib("maxlength","100")
		->setValue($title_value);
		
		$introduction =  new Zend_Form_Element_Textarea('introduction');
		$introduction->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Title is required.'))
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setAttrib("class","mws-textinput required limitmatch1")
		->setAttrib("placeholder","Up to 300 Characters")		
		->setAttrib("maxlength","300")
		->setValue($introduction_value);
		
		$CountryArr=array();
		$CountryArr[0]['key']="";
		$CountryArr[0]['value']="- - Country - -";
		
		$CountryData=$db->runQuery("select * from ".COUNTRIES."  order by country_name");
		
		if($CountryData!="" and count($CountryData)>0)
		{
			$i=1;
			foreach($CountryData as $key=>$CountryValues)
			{
			$CountryArr[$i]['key']=$CountryValues['country_id'];
			$CountryArr[$i]['value']=$CountryValues['country_name'];
			$i++;
			}
		}
		
		
		
		$country_id= new Zend_Form_Element_Select('country_id');
		$country_id->setRequired(true)
		->addMultiOptions($CountryArr)
		->addValidator('NotEmpty',true,array('messages' =>'Country is required.'))
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setAttrib("class","mws-textinput required")
		->setAttrib("onchange","getCountryState(this.value);")	
		->setValue($country_id_value);
	
					
		
		$stateArr=array();
		$stateArr[0]['key']="";
		$stateArr[0]['value']="- - State - -";
		
		/*if($userId != "")
		{
			$stateData=$db->runQuery("select * from ".USERS." as u inner join ".STATE." as s on s.state_id = u.state_id
			 						  where u.user_id='".$userId."'");
			$stateArr[0]['key'] = $stateData[0]['state_id'];
			$stateArr[0]['value'] = $stateData[0]['state_name'];
			$state_id_value = $adminData[0]['state_id'];	
			
		}*/
		
//		echo $_REQUEST['country_id']; exit; 
		
		if($_REQUEST['country_id'] != "" || $ppty_id != "")
		{
			
			if($_REQUEST['country_id'] != "")
			{	
				$stateData=$db->runQuery("select * from ".STATE." as s inner join ".COUNTRIES." as c on s.country_id = c.country_id
			 						  where s.country_id='".$_REQUEST['country_id']."'");
				
				$state_id_value = $_REQUEST['state_id'];
			}
			else
			{
				$stateData=$db->runQuery("select * from ".STATE." as s inner join ".COUNTRIES." as c on s.country_id = c.country_id
	 						  where s.country_id='".$country_id_value."'");
							  
				  $state_id_value = $pptyData[0]['state_id'];
							  
			}
	
			$i = 1;
			foreach($stateData as $values)
			{	$stateArr[$i]['key'] = $values['state_id'];
				$stateArr[$i]['value'] = $values['state_name'];
					
				$i++;
			}
			
			
		}
		
		
		$state_id= new Zend_Form_Element_Select('state_id');
		$state_id->setRequired(true)
		->addMultiOptions($stateArr)
		->addValidator('NotEmpty',true,array('messages' =>'State is required.'))
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setRegisterInArrayValidator(false)
		->setAttrib("class","mws-textinput required")
		->setAttrib("onchange","getStateCity(this.value);")
		->setValue($state_id_value);
		
	
		
		$cityArr=array();
		$cityArr[0]['key']="";
		$cityArr[0]['value']="- - City - -";
		
		
		/*if($userId != "")
		{
			$cityData=$db->runQuery("select * from ".USERS." as u inner join ".CITIES." as c on c.city_id = u.city_id
			 						  where u.user_id='".$userId."'");
			$cityArr[0]['key'] = $cityData[0]['city_id'];
			$cityArr[0]['value'] = $cityData[0]['city_name'];
			$city_id_value = $cityData[0]['city_id'];	
			
		}*/
		
		if($_REQUEST['state_id'] != "" || $ppty_id != "")
		{
			if($_REQUEST['state_id'] != "")
			{
				$cityData=$db->runQuery("select * from ".CITIES." where state_id='".$_REQUEST['state_id']."'");
				$city_id_value = $_REQUEST['city_id'];	
			}
			else
			{
				$cityData=$db->runQuery("select * from ".CITIES." where state_id='".$state_id_value."'");
				
			}
			
			$i = 1;
			foreach($cityData as $values)
			{	$cityArr[$i]['key'] = $values['city_id'];
				$cityArr[$i]['value'] = $values['city_name'];
				
				$i++;
			}
			
			
		}
		
		
		$city_id= new Zend_Form_Element_Select('city_id');
		$city_id->setRequired(true)
		->addMultiOptions($cityArr)
		->setRegisterInArrayValidator(false)
		->addValidator('NotEmpty',true,array('messages' =>'City is required.'))
		->setAttrib("class","mws-textinput required")
		->setAttrib("onchange","getCitySub(this.value);")
		->setValue($city_id_value);


		/**** SUB AREA[OPTIONAL] *****/
		/*****************************/
		
		$subareaArr=array();
		$subareaArr[0]['key']="";
		$subareaArr[0]['value']="- - Sub Area - -";
		
		if($_REQUEST['city_id'] != "" || $ppty_id != "")
		{
			if($_REQUEST['city_id'] != "")
			{
				$subareaData=$db->runQuery("select * from ".SUB_AREA." where city_id ='".$_REQUEST['city_id']."'");
				$sub_area_value = $_REQUEST['sub_area'];		
			}
			else
			{
				$subareaData=$db->runQuery("select * from ".SUB_AREA." where city_id ='".$city_id_value."'");
			}
			
			$i = 1;
			foreach($subareaData as $values)
			{	
				$subareaArr[$i]['key'] = $values['sub_area_id'];
				$subareaArr[$i]['value'] = $values['sub_area_name'];
				$i++;
			}
		}
		
		
		$sub_area = new Zend_Form_Element_Select('sub_area');
		$sub_area->addMultiOptions($subareaArr)
		->setRegisterInArrayValidator(false)
		->setAttrib("class","mws-textinput")
		->setAttrib("onchange","getSubLocal(this.value);")		
		->setValue($sub_area_value);
		
		
				
		/**** LOCAL AREA[OPTIONAL] ***/
		/*****************************/
		
		$localareaArr=array();
		$localareaArr[0]['key']="";
		$localareaArr[0]['value']="- - Local Area - -";
		
		if($_REQUEST['sub_area'] != "" || $ppty_id != "")
		{
			if($_REQUEST['sub_area'] != "")
			{
				$localareaData = $db->runQuery("select * from ".LOCAL_AREA." where sub_area_id ='".$_REQUEST['sub_area']."'");
				$local_area_value = $_REQUEST['local_area'];	
			}
			else
			{
				$localareaData = $db->runQuery("select * from ".LOCAL_AREA." where sub_area_id ='".$sub_area_value."'");		
			}
			
			$i = 1;
			foreach($localareaData as $values)
			{	
				$localareaArr[$i]['key'] = $values['local_area_id'];
				$localareaArr[$i]['value'] = $values['local_area_name'];
				$i++;
			}
		}
		
		

		$local_area = new Zend_Form_Element_Select('local_area');
		$local_area->addMultiOptions($localareaArr)
		->setRegisterInArrayValidator(false)
		->setAttrib("class","mws-textinput")
		->setValue($local_area_value);
		
		
		
		
/*		$address= new Zend_Form_Element_Textarea('address');
		$address->setAttrib("class","mws-textinput required")
		->setAttrib("rows","4")
		->setAttrib("cols","30")
		->setValue($address_value);*/
	
			
		
		
		
		
		$zipcode= new Zend_Form_Element_Text('zipcode');
		$zipcode->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Zipcode is required.'))
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setAttrib("class","mws-textinput required")
		->setAttrib("maxlength","7")
		->setValue($zipcode_value);
		
		/*$telephone = new Zend_Form_Element_Text('telephone');
		$telephone->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Telephone number is required.'))
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setAttrib("class","mws-textinput required")
		->setAttrib("maxlength","15")
		->setValue($telephone_value);*/
	
		/*$website = new Zend_Form_Element_Text('website');
		$website->addDecorator('Errors', array('class'=>'errmsg'))
		->setAttrib("class","mws-textinput url")
    	->setValue($website_value);		*/
		
		$accomodationData = $db->runQuery("select * from ".PROPERTY_TYPE." order by ptyle_name");
		$accomodationArr[0]['key']= "";
		$accomodationArr[0]['value']= "- - select -- ";
			
		$i=1;
		foreach($accomodationData as $key=>$Data)
		{
			$accomodationArr[$i]['key']=$Data['ptyle_id'];
			$accomodationArr[$i]['value']=$Data['ptyle_name'];
			$i++;
		}
		
		
		$accomodation_type = new Zend_Form_Element_Select('accomodation_type');
		$accomodation_type->setRequired(true)
		->addMultiOptions($accomodationArr)
		->addValidator('NotEmpty',true,array('messages' =>'Country is required.'))
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setAttrib("class","mws-textinput required")
		->setValue($accomodation_type_value);
		
		
		
		/** number of bedrooms **/
		
		$no_of_bedroomArr[0]['key'] = "";
		$no_of_bedroomArr[0]['value'] = "- - Select - -";
		
		for($i = 1; $i<=10;$i++)
		{
			$no_of_bedroomArr[$i]['key'] = $i;
			$no_of_bedroomArr[$i]['value'] = $i;
	
		}
		
		
		
		$no_of_bedroom = new Zend_Form_Element_Select('no_of_bedroom');
		$no_of_bedroom->setRequired(true)
		->addMultiOptions($no_of_bedroomArr)
		->addValidator('NotEmpty',true,array('messages' =>'Country is required.'))
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setAttrib("class","mws-textinput required")
		->setValue($no_of_bedroom_value);
		
		/** number of bathrooms **/
		
		$no_of_bathroomArr[0]['key'] = "";
		$no_of_bathroomArr[0]['value'] = "- - Select - -";
		
		for($i = 1,$k=1; $i<=20;$k=$k+0.5,$i++)
		{
			$no_of_bathroomArr[$i]['key'] = $k;
			$no_of_bathroomArr[$i]['value'] = $k;
			
		}

		
		$no_of_bathroom = new Zend_Form_Element_Select('no_of_bathroom');
		$no_of_bathroom->setRequired(true)
		->addMultiOptions($no_of_bathroomArr)
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setAttrib("class","mws-textinput required")
		->setValue($no_of_bathroom_value);
		
		/** number of En-Suite Bathrooms **/
		
		
		$no_of_nbathroomArr[0]['key'] = "";
		$no_of_nbathroomArr[0]['value'] = "- - Select - -";
		
		for($i = 1,$k = 0; $k<=8;$i++,$k++)
		{
			$no_of_nbathroomArr[$i]['key'] = $k;
			$no_of_nbathroomArr[$i]['value'] = $k;

		}
		
		
		
		$no_of_en_suite_bathroom = new Zend_Form_Element_Select('no_of_en_suite_bathroom');
		$no_of_en_suite_bathroom->setRequired(true)
		->addMultiOptions($no_of_nbathroomArr)
		->addValidator('NotEmpty',true,array('messages' =>'Country is required.'))
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setAttrib("class","mws-textinput required")
		->setValue($no_of_en_suite_bathroom_value);
		
		
		/** maximum occupancy **/
		
		$occupancyArr[0]['key'] = "";
		$occupancyArr[0]['value'] = "- - Select - -";
		
		for($i = 1; $i<=20;$i++)
		{
			$occupancyArr[$i]['key'] = $i;
			$occupancyArr[$i]['value'] = $i;

		}
		
		$occupancy = new Zend_Form_Element_Select('occupancy');
		$occupancy->setRequired(true)
		->addMultiOptions($occupancyArr)
		->addValidator('NotEmpty',true,array('messages' =>'Country is required.'))
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setAttrib("class","mws-textinput required")
		->setValue($occupancy_value);
		
		
		
		/** Rating **/
		
/*		
		for($i = 1; $i<=5;$i++)
		{
			$ratingArr[$i]['key'] = $i;
		}
		
		

		$rating = new Zend_Form_Element_Radio('rating');
		$rating->setRequired(true)
		->addMultiOptions($ratingArr)
		->setRegisterInArrayValidator(false)
		->removeDecorator('label')
		->setAttrib("class","star")
		->setValue($rating_value);
			
	*/	
		

		/* hidden field for checking the step */
		
		$step = new Zend_Form_Element_Hidden('step');
		$step->setValue("1");
		
		$ppty_no = new Zend_Form_Element_Hidden('ppty_no');
		$ppty_no->setValue(generate_property_no($mySession->LoggedUserId));
		
		$this->addElements(array($title,$introduction,$country_id,$state_id,$city_id,$zipcode,$accomodation_type,$no_of_bedroom,$no_of_bathroom,$no_of_en_suite_bathroom,$occupancy,$step,$sub_area,$local_area,$ppty_no));

		
	}
}
?>