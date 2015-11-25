<?PHP
class Form_Localarea extends Zend_Form
{  
	public function __construct($lId="")
	{
		$this->init($lId);
	}
    public function init($lId)
	{   
		global $mySession;
        $db=new Db();

		$CountryId="";$StateId="";$CityName="";$local_area_value = "";$sub_area_value = "";
		if($lId != "")
		{
			$PageData=$db->runQuery("select * from ".LOCAL_AREA."
		join ".SUB_AREA." on(".SUB_AREA.".sub_area_id=".LOCAL_AREA.".sub_area_id)
		join ".CITIES." on(".CITIES.".city_id=".SUB_AREA.".city_id)
		join ".STATE." on(".CITIES.".state_id=".STATE.".state_id)
		join ".COUNTRIES." on(".STATE.".country_id=".COUNTRIES.".country_id) 
			where ".LOCAL_AREA.".local_area_id='".$lId."'");		

			$CountryId=$PageData[0]['country_id'];

			$StateId=$PageData[0]['state_id'];
			$CityName=$PageData[0]['city_id'];
			$sub_area_value = $PageData[0]['sub_area_id'];
			$local_area_value = $PageData[0]['local_area_name'];
		}
		
		$CountryArr=array();
		$CountryArr[0]['key']="";
		$CountryArr[0]['value']="- - Country - -";
		$CountryData=$db->runQuery("select * from ".COUNTRIES." order by country_name");
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
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","required")
		->setAttrib("onchange","getCountryState(this.value);")	
		->setValue($CountryId);
		
		$StateArr=array();
		$StateArr[0]['key']="";
		$StateArr[0]['value']="- - State - -";
		$chkCountryId=$CountryId;
		if(isset($_REQUEST['country_id']) && $_REQUEST['country_id']!="")
		{
			$chkCountryId = $_REQUEST['country_id'];
		}
		if($chkCountryId!="")
		{
			$StateData = $db->runQuery("select * from ".STATE." where country_id='".$chkCountryId."' order by state_name");
			if($StateData!="" and count($StateData)>0)
			{
				$i=1;
				foreach($StateData as $key=>$StateValues)
				{
				$StateArr[$i]['key']=$StateValues['state_id'];
				$StateArr[$i]['value']=$StateValues['state_name'];
				$i++;
				}
			}
		}
		$state_id= new Zend_Form_Element_Select('state_id');
		$state_id->setRequired(true)
		->addMultiOptions($StateArr)
		->addValidator('NotEmpty',true,array('messages' =>'State is required.'))
		->setAttrib("onchange","getStateCity(this.value);")		
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","required")
		->setValue($StateId);
		
			
		
		$CityArr=array();
		$CityArr[0]['key']="";
		$CityArr[0]['value']="- - City - -";
		
		$chkStateId = $StateId;
		
		if(isset($_REQUEST['state_id']) && $_REQUEST['state_id']!="")
		{
			$chkStateId = $_REQUEST['state_id'];
		}
		if($chkStateId!="")
		{
			$CityData = $db->runQuery("select * from ".CITIES." where state_id='".$chkStateId."' order by city_name");
			if($CityData!="" and count($CityData)>0)
			{
				$i=1;
				foreach($CityData as $key=>$CityValues)
				{
					$CityArr[$i]['key'] = $CityValues['city_id'];
					$CityArr[$i]['value'] = $CityValues['city_name'];
					$i++;
				}
			}
		}
		
		$city_name=  new Zend_Form_Element_Select('city_name');
		$city_name->setRequired(true)
		->addMultiOptions($CityArr)
		->addValidator('NotEmpty',true,array('messages' =>'City is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("onchange","getCitySub(this.value);")		
		->setAttrib("class","required")
		->setValue($CityName);
		
		
		
		$SubArr=array();
		$SubArr[0]['key']="";
		$SubArr[0]['value']="- - Sub Area - -";
		
		$chkSubId = $CityName;
		
		if(isset($_REQUEST['city_name']) && $_REQUEST['city_name']!="")
		{
			$chkSubId = $_REQUEST['city_name'];
		}
		if($chkSubId != "")
		{
			$SubArrData = $db->runQuery("select * from ".SUB_AREA." where city_id = '".$chkSubId."' order by sub_area_name");

			if($SubArrData != "" and count($SubArrData)>0)
			{
				$i=1;
				foreach($SubArrData as $key=>$SubArrValues)
				{
					$SubArr[$i]['key'] = $SubArrValues['sub_area_id'];
					$SubArr[$i]['value'] = $SubArrValues['sub_area_name'];
					$i++;
				}
			}
		}
		
		
		
		$sub_area_name = new Zend_Form_Element_Select('sub_area_name');
		$sub_area_name->setRequired(true)
		->addMultiOptions($SubArr)
		->addValidator('NotEmpty',true,array('messages' =>'Sub Area Name is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required") 	
		->setValue($sub_area_value);

		$local_area_name = new Zend_Form_Element_Text('local_area_name');
		$local_area_name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Local Area Name is required.'))
                ->addValidator('regex', true, array(
                        'pattern' => '/^[a-zA-Z\-]+$/',
                        'messages' => array(
                            'regexNotMatch' => 'Please enter proper name and without space'
                        )
                            )
                )
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required") 	
		->setValue($local_area_value);		
		
		
		$this->addElements(array($country_id,$state_id,$city_name,$sub_area_name,$local_area_name));
	}
}	

?>