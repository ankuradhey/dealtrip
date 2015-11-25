<?PHP
class Form_User extends Zend_Form
{  
	public function __construct($userId="")
	{
		$this->init($userId);
	}
    public function init($userId)
	{   
		global $mySession;
        $db=new Db();
		$first_name_value="";$last_name_value="";$email_address_value="";$password_o_value="";$password_c_value="";$user_status_value="";$address_value="";$country_id_value="";$state_id_value="";$city_id_value="";$zipcode_value="";$phone_number_value="";$mobile_number_value="";$subscribe_value="1";$usertype_value="1";
		if($userId!="")
		{
		$adminData=$db->runQuery("select * from ".USERS." where user_id='".$userId."'");
		$usertype_value=$adminData[0]['user_type'];
		$first_name_value=$adminData[0]['first_name'];
		$last_name_value=$adminData[0]['last_name'];
		$email_address_value=$adminData[0]['email_address'];
		$user_status_value=$adminData[0]['user_status'];
		$address_value=$adminData[0]['address'];
		$country_id_value=$adminData[0]['country_id'];
		$state_id_value=$adminData[0]['state_id'];
		$city_id_value=$adminData[0]['city_id'];
		$zipcode_value=$adminData[0]['zipcode'];
		$phone_number_value=$adminData[0]['phone_number'];
		$mobile_number_value=$adminData[0]['mobile_number'];
		$subscribe_value=$adminData[0]['newsletter_subscribe'];
		}
		
		$userTypeArr=array();
		$userTypeArr[0]['key']="1";
		$userTypeArr[0]['value']="Visitor";
		$userTypeArr[1]['key']="2";
		$userTypeArr[1]['value']="Service Provider";
		$usertype= new Zend_Form_Element_Radio('usertype');
		$usertype->addMultiOptions($userTypeArr)
		->setValue($usertype_value);
		
		$first_name= new Zend_Form_Element_Text('first_name');
		$first_name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'First Name is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setValue($first_name_value);	
		
		$last_name= new Zend_Form_Element_Text('last_name');
		$last_name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Last Name is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setValue($last_name_value);
		
		$email_address= new Zend_Form_Element_Text('email_address');
		$email_address->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Email address is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setValue($email_address_value);
		
		$password_o= new Zend_Form_Element_Password('password_o');
		$password_o->setAttrib("class","textInput");
		
		$password_c= new Zend_Form_Element_Password('password_c');
		$password_c->setAttrib("class","textInput");
		
		if($userId=="" || (isset($_REQUEST['ChangePass']) && $userId!=""))
		{
		$password_o->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Password is required.'))
		->addDecorator('Errors', array('class'=>'error'));
		
		$password_c->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Confirm password is required.'))
		->addDecorator('Errors', array('class'=>'error'));
		}
		
		$StatusArr=array();
		$StatusArr[0]['key']="1";
		$StatusArr[0]['value']="Active";
		$StatusArr[1]['key']="0";
		$StatusArr[1]['value']="Inactive";
		
		$user_status= new Zend_Form_Element_Select('user_status');
		$user_status->addMultiOptions($StatusArr)
		->setAttrib("class","textInput")
		->setValue($user_status_value);
		
		$address= new Zend_Form_Element_Textarea('address');
		$address->setAttrib("class","textInput")
		->setAttrib("rows","3")
		->setValue($address_value);
		
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
		->setAttrib("class","textInput")
		->setAttrib("onchange","getCountryState(this.value);")	
		->setValue($country_id_value);
		
		$StateArr=array();
		$StateArr[0]['key']="";
		$StateArr[0]['value']="- - State - -";
		$chkCountryId=$country_id_value;
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
		->setAttrib("class","textInput")
		->setAttrib("onchange","getStateCity(this.value);")
		->setValue($state_id_value);
		
		$CityArr=array();
		$CityArr[0]['key']="";
		$CityArr[0]['value']="- - City - -";
		$chkStateId=$state_id_value;
		if(isset($_REQUEST['state_id']) && $_REQUEST['state_id']!="")
		{
			$chkStateId=$_REQUEST['state_id'];
		}
		if($chkStateId!="")
		{
			$CityData=$db->runQuery("select * from ".CITIES." where state_id='".$chkStateId."' order by city_name");
			if($CityData!="" and count($CityData)>0)
			{
				$i=1;
				foreach($CityData as $key=>$CityValues)
				{
				$CityArr[$i]['key']=$CityValues['city_id'];
				$CityArr[$i]['value']=$CityValues['city_name'];
				$i++;
				}
			}
		}
		$city_id= new Zend_Form_Element_Select('city_id');
		$city_id->setRequired(true)
		->addMultiOptions($CityArr)
		->addValidator('NotEmpty',true,array('messages' =>'City is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setValue($city_id_value);
		
		$zipcode= new Zend_Form_Element_Text('zipcode');
		$zipcode->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Zipcode is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setValue($zipcode_value);
		
		$phone_number= new Zend_Form_Element_Text('phone_number');
		$phone_number->setAttrib("class","textInput")
		->setValue($phone_number_value);
		
		$mobile_number= new Zend_Form_Element_Text('mobile_number');
		$mobile_number->setAttrib("class","textInput")
		->setValue($mobile_number_value);
		
		$SubscribeArr=array();
		$SubscribeArr[0]['key']="1";
		$SubscribeArr[0]['value']="Yes";
		$SubscribeArr[1]['key']="0";
		$SubscribeArr[1]['value']="No";
		$subscribe= new Zend_Form_Element_Radio('subscribe');
		$subscribe->addMultiOptions($SubscribeArr)
		->setValue($subscribe_value);
		
		$this->addElements(array($usertype,$first_name,$last_name,$email_address,$password_o,$password_c,$user_status,$address,$country_id,$state_id,$city_id,$zipcode,$phone_number,$mobile_number,$subscribe));		
	}
}	

?>