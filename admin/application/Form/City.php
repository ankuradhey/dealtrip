<?PHP
class Form_City extends Zend_Form
{  
	public function __construct($cityId="")
	{
		$this->init($cityId);
	}
    public function init($cityId)
	{   
		global $mySession;
        $db=new Db();
		
		$CountryId="";$StateId="";$CityName="";
		if($cityId!="")
		{
			$PageData=$db->runQuery("select * from ".CITIES." where city_id='".$cityId."'");		
			$CountryId=$PageData[0]['country_id'];
			$StateId=$PageData[0]['state_id'];
			$CityName=$PageData[0]['city_name'];
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
			$chkCountryId=$_REQUEST['country_id'];
		}
		if($chkCountryId!="")
		{
			$StateData=$db->runQuery("select * from ".STATE." where country_id='".$chkCountryId."' order by state_name");
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
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","required")
		->setValue($StateId);
		
		$city_name= new Zend_Form_Element_Text('city_name');
		$city_name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'City Name is required.'))
                ->addValidator('regex', true, array(
                        'pattern' => '/^[a-zA-Z\-]+$/',
                        'messages' => array(
                            'regexNotMatch' => 'Please enter proper name and without space'
                        )
                            )
                    )
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required") 	
		->setValue($CityName);	
		
		$this->addElements(array($country_id,$state_id,$city_name));
	}
}	

?>