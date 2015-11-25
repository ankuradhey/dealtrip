<?PHP
class Form_Search extends Zend_Form
{  
	public function __construct($locationArr = '' )
	{
		global $mySession;
		$this->init($locationArr);
	}
    public function init($locationArr = "")
	{   
		global $mySession;
                $db=new Db();

		$hotel_value="";$leaving_value="";$go_value="";
		$room_value="";$adult_value="";$children_value="";$state_id_value="";$country_id_value="";$city_id_value="";
		

		if(isset($_REQUEST['country_id']) && $_REQUEST['country_id']!="")
		{
			$country_id_value=$_REQUEST['country_id'];
		}
		elseif( is_array($locationArr) &&  !empty($locationArr['country_id']))
		{
			$country_id_value = $locationArr['country_id'];
		}
		
		
		$countryArr=array();
		$countryArr[0]['key']="";
		$countryArr[0]['value']="- - All Countries - -";
		
		

									
		$countryData=$db->runQuery("select *, count(id) as _counter from ".COUNTRIES." 
									inner join ".PROPERTY." on ".PROPERTY.".country_id = ".COUNTRIES.".country_id
									where ".PROPERTY.".status = '3'
									and ".PROPERTY.".status = '3' 
									group by ".COUNTRIES.".country_id
									order by ".COUNTRIES.".country_name
									");
		

		
		if($countryData!="" and count($countryData)>0)
		{
			$i=1;
			foreach($countryData as $key=>$countryValues)
			{
				$countryArr[$i]['key']=$countryValues['country_id'];
				$countryArr[$i]['value']=$countryValues['country_name']." (".$countryValues['_counter'].")";
				$i++;
			}
		}
		
		$country_id= new Zend_Form_Element_Select('country_id');
		$country_id->setRequired(false)
		->addMultiOptions($countryArr)
		->setAttrib("onchange","getCountryState(this.value);")
		->setValue($country_id_value);
		$this->addElement($country_id);
		
		
		
		$stateArr=array();
		$stateArr[0]['key']="";
		$stateArr[0]['value']="- -All States - -";
		
		if(isset($_REQUEST['country_id']) && $_REQUEST['country_id']!="")
		{
			$country_id_value=$_REQUEST['country_id'];
		}
		

		if(@$country_id_value!="")
		{

			$stateData=$db->runQuery("select *, count(id) as _counter from ".STATE." 
									  inner join ".PROPERTY." on ".PROPERTY.".state_id = ".STATE.".state_id
									  where ".STATE.".country_id=".$country_id_value." 
									  and ".PROPERTY.".status = '3'
									  group by ".STATE.".state_id
									  order by state_name");
									  
			
									  
			if($stateData!="" and count($stateData)>0)
			{
				$i=1;
				foreach($stateData as $key=>$stateValues)
				{
				$stateArr[$i]['key']=$stateValues['state_id'];
				$stateArr[$i]['value']=$stateValues['state_name']." (".$stateValues['_counter'].")";
				$i++;
				}
			}
		}
		
		if(isset($_REQUEST['state_id']) && $_REQUEST['state_id']!="")
		{
			$state_id_value=$_REQUEST['state_id'];
		}
		elseif( is_array($locationArr) && !empty($locationArr['state_id']))
		{
			
			$state_id_value = $locationArr['state_id'];
			
		}
		
		$state_id= new Zend_Form_Element_Select('state_id');
		$state_id->setRequired(false)
		->addMultiOptions($stateArr)
		->setAttrib("onchange","getStateCity(this.value);")
		->setValue($state_id_value);
		$this->addElement($state_id);
		

		
		$cityArr=array();
		$cityArr[0]['key']="";
		$cityArr[0]['value']="- -All Cities - -";
		
		
		if(isset($_REQUEST['state_id']) && $_REQUEST['state_id']!="")
		{
			$state_id_value=$_REQUEST['state_id'];
		}
		
		

		
		if(@$state_id_value!="")
		{
			$cityData=$db->runQuery("select *, count(id) as _counter from ".CITIES."
									 inner join ".PROPERTY." on ".PROPERTY.".city_id = ".CITIES.".city_id
									 where ".CITIES.".state_id= ".$state_id_value." 
									 and ".PROPERTY.".status = '3' 
									 group by ".CITIES.".city_id
									 order by ".CITIES.".city_name");
			if($cityData!="" and count($cityData)>0)
			{
				//prd($StateData);
				$i=1;
				foreach($cityData as $key=>$cityValues)
				{
				$cityArr[$i]['key']=$cityValues['city_id'];
				$cityArr[$i]['value']=$cityValues['city_name']." (".$cityValues['_counter'].")";
				$i++;
				}
			}
		}
		
		if(isset($_REQUEST['city_id']) && $_REQUEST['city_id']!="")
		{
			$city_id_value=$_REQUEST['city_id'];
		}
		elseif( is_array($locationArr) &&  !empty($locationArr['city_id']))
		{
			$city_id_value = $locationArr['city_id'];
		}
		
		$city_id= new Zend_Form_Element_Select('city_id');
		$city_id->setRequired(false)
		->addMultiOptions($cityArr)
		->setAttrib("onchange","getCitySubarea(this.value);")
		->setValue($city_id_value);
		$this->addElement($city_id);
		
		
		
		$subareaArr=array();
		$subareaArr[0]['key']="";
		$subareaArr[0]['value']="- -All Sub Area - -";
		
		
		if(isset($_REQUEST['city_id']) && $_REQUEST['city_id']!="")
		{
			$city_id_value=$_REQUEST['city_id'];
		}
		
		
		if(@$city_id_value!="")
		{
			$subareaData = $db->runQuery("select *, count(id) as _counter from ".SUB_AREA."
										  inner join ".PROPERTY." on ".PROPERTY.".sub_area_id = ".SUB_AREA.".sub_area_id
										  where ".SUB_AREA.".city_id = '".$city_id_value."' 
										  and ".PROPERTY.".status = '3' 
										  group by ".SUB_AREA.".sub_area_id
										  order by ".SUB_AREA.".sub_area_name");
										  
			

										  
			if($subareaData!="" and count($subareaData)>0)
			{
				
				$i=1;
				foreach($subareaData as $key=>$subareaValues)
				{
				$subareaArr[$i]['key']=$subareaValues['sub_area_id'];
				$subareaArr[$i]['value']=$subareaValues['sub_area_name']." (".$subareaValues['_counter'].")";
				$i++;
				}
			}
		}
		
		
	
		
		if(isset($_REQUEST['sub_area_id']) && $_REQUEST['sub_area_id']!="")
		{
			$sub_area_id_value = $_REQUEST['sub_area_id'];
		}
		elseif( is_array($locationArr) &&  !empty($locationArr['sub_area_id']))
		{
			$sub_area_id_value = $locationArr['sub_area_id'];
		}
		
		$sub_area_id= new Zend_Form_Element_Select('sub_area_id');
		$sub_area_id->setRequired(false)
		->addMultiOptions($subareaArr)
		->setAttrib("onchange","getSubareaLocalarea(this.value);")
		->setValue($sub_area_id_value);
		$this->addElement($sub_area_id);
		
		$localareaArr=array();
		$localareaArr[0]['key']="";
		$localareaArr[0]['value']="- -All Local Areas - -";
		
		
		if(isset($_REQUEST['sub_area_id']) && $_REQUEST['sub_area_id']!="")
		{
			$sub_area_id_value=$_REQUEST['sub_area_id'];
		}
		

		if(@$sub_area_id_value!="")
		{
			$localareaData = $db->runQuery("select *, count(id) as _counter from ".LOCAL_AREA." 
										   	inner join ".PROPERTY." on ".PROPERTY.".local_area_id = ".LOCAL_AREA.".local_area_id 
                                                                                        where ".LOCAL_AREA.".sub_area_id = '".$sub_area_id_value."' 
                                                                                            and ".PROPERTY.".status = '3' 
											group by ".LOCAL_AREA.".local_area_id
											order by ".LOCAL_AREA.".local_area_name");
											/*
											prd("select *, count(id) as _counter from ".LOCAL_AREA." 
										   	inner join ".PROPERTY." on ".PROPERTY.".local_area_id = ".LOCAL_AREA.".local_area_id 
										    where ".LOCAL_AREA.".sub_area_id = '".$sub_area_id_value."' order by ".LOCAL_AREA.".local_area_name");*/
											
			//prd($localareaData);
										
			if($localareaData!="" and count($localareaData)>0)
			{
				$i=1;
				foreach($localareaData as $key=>$localareaValues)
				{
					$localareaArr[$i]['key']=$localareaValues['local_area_id'];
					$localareaArr[$i]['value']=$localareaValues['local_area_name']." (".$localareaValues['_counter'].")";
					$i++;
				}
			}
		}
		
		if(isset($_REQUEST['local_area_id']) && $_REQUEST['local_area_id']!="")
		{
			$local_area_id_value = $_REQUEST['local_area_id'];
		}
		elseif( is_array($locationArr) &&  !empty($locationArr['local_area_id']))
		{
			$local_area_id_value = $locationArr['local_area_id'];
		}

		
		$local_area_id= new Zend_Form_Element_Select('local_area_id');
		$local_area_id->setRequired(false)
		->addMultiOptions($localareaArr)
		->setValue($local_area_id_value);
		$this->addElement($local_area_id);
		
		

		
	}
	
}	

?>
