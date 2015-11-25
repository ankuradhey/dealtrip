<style>
</style>

<script type="text/javascript">
$(document).ready(function(e) {

    jQuery.validator.addMethod("urls", function(value, element) {
		if(value != "")
			return 	/^([a-zA-Z0-9]+(\.[a-zA-Z0-9]+)+.*)$/.test(value);
			else
			return true;
				}, "Enter proper url");

});

</script>

<?PHP


class Form_User extends Zend_Form
{  
	public function __construct($userId="")
	{
		global $mySession;
		$this->init($userId);
	}
    public function init($userId = "")
	{   
		global $mySession;
        $db=new Db();

		$country_id_value="";$zipcode_value="";$home_number_value="";$subscribe_value="1";$state_id_value="";$city_id_value="";$website_address_value="";$business_category_id_value="";$old_profile_image_value="";$dob_month_value="";$dob_year_value="";$dob_day_value="";$user_name_value="";$accountType_value=$SignUpfor;
		$username_value = "";$cemail_address = "";$work_number_value="";$phone_number_value="";$title_value = "";
		
		
				
		if($userId !="")
		{
			$adminData=$db->runQuery("select * from ".USERS." where user_id='".$userId."'");
			//$old_profile_image_value=$adminData[0]['profile_image'];
			$first_name_value=$adminData[0]['first_name'];
			$user_name_value=$adminData[0]['user_name'];
			$title_value = $adminData[0]['title'];
			$last_name_value=$adminData[0]['last_name'];
			$email_address_value=$adminData[0]['email_address'];
			$cemail_address_value=$adminData[0]['email_address'];
			$country_id_value=$adminData[0]['country_id'];
			$zipcode_value=$adminData[0]['zipcode'];
			$home_number_value=$adminData[0]['home_number'];
			$work_number_value=$adminData[0]['work_number'];
			$phone_number_value=$adminData[0]['mobile_number'];						
			$subscribe_value=$adminData[0]['newsletter_subscribe'];
			$sex_value=$adminData[0]['sex'];
			/*if($adminData[0]['date_of_birth']!="0000-00-00")
			{
			$date_of_birth_value=explode("-",$adminData[0]['date_of_birth']);
			$dob_month_value=$date_of_birth_value[1];$dob_year_value=$date_of_birth_value[0];$dob_day_value=$date_of_birth_value[2];
			}*/
			
			$state_id_value=$adminData[0]['state_id'];
			$city_id_value=$adminData[0]['city_id'];
			$address_value=$adminData[0]['address'];
			$website_address_value=$adminData[0]['web'];
			$business_category_id_value=$adminData[0]['business_category_id'];		
		}
			
		//Common Fields for all type of users
		$first_name= new Zend_Form_Element_Text('first_name');
		$first_name->addDecorator('Errors', array('class'=>'errmsg'))
		->setAttrib("class","mws-textinput required")
		->setValue($first_name_value);
		$this->addElement($first_name);

		
		$last_name= new Zend_Form_Element_Text('last_name');
		$last_name->addDecorator('Errors', array('class'=>'errmsg'))
		->setAttrib("class","mws-textinput required")
		->setValue($last_name_value);
		$this->addElement($last_name);
		
		
		

		$titles = array('Mr.','Mrs.','Miss.', 'Ms.', 'Dr.');
		$titleArr = array();
		
		
		$titleArr[$k]['key'] = "";
		$titleArr[$k]['value'] = "- -Select- -";
		
		for($i = 0,$k=1;$i<5;$i++,$k++)
		{
			$titleArr[$k]['key'] = $i;
			$titleArr[$k]['value'] = $titles[$i];
			
		}


		
		
			
		

		$home_number= new Zend_Form_Element_Text('home_number');
		$home_number->setAttrib("class","mws-textinput required")
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setValue($home_number_value);
		$this->addElement($home_number);
		
		$work_number= new Zend_Form_Element_Text('work_number');
		$work_number->setAttrib("class","mws-textinput")
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setValue($work_number_value);
		$this->addElement($work_number);
			
		$mobile_number= new Zend_Form_Element_Text('mobile_number');
		$mobile_number->setAttrib("class","mws-textinput")
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setValue($phone_number_value);
		$this->addElement($mobile_number);	

		
		if($userId == "")
		{	

			/*$username = new Zend_Form_Element_Text('username');
			$username->setRequired(true)
			->addValidator('NotEmpty',true,array('messages' =>'Display name is required'))
			->addDecorator('Errors', array('class'=>'errmsg'))
			->setAttrib("class","mws-textinput required noSpace checkuser")
			//->setAttrib("onblur","return checkuser()")		
			->setValue($username_value);
			$this->addElement($username);*/
			
			
			$title =  new Zend_Form_Element_Select('title');
			$title->addMultiOptions($titleArr)
			->addDecorator('Errors', array('class'=>'errmsg'))
			->setAttrib("class","mws-textinput required")
			->setValue($title_value);
			$this->addElement($title);
			
			
			$email_address = new Zend_Form_Element_Text('email_address');
			$email_address->setRequired(true)
			->addValidator('NotEmpty',true,array('messages' =>'Email address is required.'))
			->addDecorator('Errors', array('class'=>'errmsg'))
			->setAttrib("class","mws-textinput required email checkemail")
			->setValue($email_address_value);
			
			$this->addElement($email_address);
			
			if(@$_REQUEST['email_address']!="")
			{
			$email_address-> addValidator('EmailAddress', true)
			->addErrorMessage('Please enter a valid email address');
			}
		 
		 	$cemail_address= new Zend_Form_Element_Text('cemail_address');
			$cemail_address->setRequired(true)
			->addValidator('NotEmpty',true,array('messages' =>'Email address is required.'))
			->addDecorator('Errors', array('class'=>'errmsg'))
			->setAttrib("class","mws-textinput required email")
			->setAttrib("equalTo","#email_address")
			->setValue($cemail_address_value);
		 	
			$this->addElement($cemail_address);
			
			
			$password= new Zend_Form_Element_Password('password');
			$password->setAttrib("class","required")
			->setRequired(true)
			->addValidator('NotEmpty',true,array('messages' =>'Password is required.'))
			->addDecorator('Errors', array('class'=>'errmsg'))
			->setAttrib("id","password")
			->setAttrib("class","mws-textinput required");
			$this->addElement($password);
		
			$password_c= new Zend_Form_Element_Password('password_c');
			$password_c->setAttrib("class","textInput passwordc required")
			->setRequired(true)
			->addValidator('NotEmpty',true,array('messages' =>'Confirm password is required.'))
			->addDecorator('Errors', array('class'=>'errmsg'))
			->setAttrib("class","mws-textinput equals required");
			$this->addElement($password_c);
		
		
	
		}
		
		$address= new Zend_Form_Element_Text('address');
		$address->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Street address is required.'))
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setAttrib("class","mws-textinput required")
		->setValue($address_value);
		$this->addElement($address);
		
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
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setRegisterInArrayValidator(false)
		->setAttrib("class","required")
		->setValue($country_id_value);
		
		$this->addElement($country_id);
		
		
		
		/*$StateArr=array();
		$StateArr[0]['key']="";
		$StateArr[0]['value']="- - State - -";
		
		if(isset($_REQUEST['country_id']) && $_REQUEST['country_id']!="")
		{
			$country_id_value=$_REQUEST['country_id'];
		}
		

		if(@$country_id_value!="")
		{
			$StateData=$db->runQuery("select * from ".STATE." where country_id=".$country_id_value." order by state_name");
			if($StateData!="" and count($StateData)>0)
			{
				//prd($StateData);
				$i=1;
				foreach($StateData as $key=>$StateValues)
				{
				$StateArr[$i]['key']=$StateValues['state_id'];
				$StateArr[$i]['value']=$StateValues['state_name'];
				//echo $StateArr[$i]['value'];die;
				$i++;
				}
			}
		}*/
		
		
		
		$state_id= new Zend_Form_Element_Text('state_id');
		$state_id->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'State name is required.'))
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setAttrib("class","mws-textinput required")
		->setValue($state_id_value);
		$this->addElement($state_id);
		

		
		/*$CityArr=array();
		$CityArr[0]['key']="";
		$CityArr[0]['value']="- - City - -";
		
		
		if(isset($_REQUEST['state_id']) && $_REQUEST['state_id']!="")
		{
			$state_id_value=$_REQUEST['state_id'];
		}
		
		
		if(@$state_id_value!="")
		{
			$CityData=$db->runQuery("select * from ".CITIES." where state_id=".$state_id_value." order by city_name");
			if($CityData!="" and count($CityData)>0)
			{
				//prd($StateData);
				$i=1;
				foreach($CityData as $key=>$CityValues)
				{
				$CityArr[$i]['key']=$CityValues['city_id'];
				$CityArr[$i]['value']=$CityValues['city_name'];
				//echo $StateArr[$i]['value'];die;
				$i++;
				}
			}
		}
		
		if(isset($_REQUEST['city_id']) && $_REQUEST['city_id']!="")
		{
			$city_id_value=$_REQUEST['city_id'];
		}*/
		
		
		$city_id= new Zend_Form_Element_Text('city_id');
		$city_id->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'City name is required.'))
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setAttrib("class","mws-textinput required")
		->setValue($city_id_value);
		$this->addElement($city_id);
		
			
			
		
		
		
		
		$zipcode= new Zend_Form_Element_Text('zipcode');
		$zipcode->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Zipcode is required.'))
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setAttrib("class","mws-textinput required")
		->setAttrib("maxlength","7")
		->setValue($zipcode_value);
		$this->addElement($zipcode);
		
		
		$webaddress = new Zend_Form_Element_Text('webaddress');
		$webaddress->addDecorator('Errors', array('class'=>'errmsg'))
		->setAttrib("class","mws-textinput urls httpadder")
		->setAttrib("style","border:0;box-shadow:none;width:80%;float:left;box-shadow:none;")
		->setValue($website_address_value);
		$this->addElement($webaddress);
		
	
	
		$photo= new Zend_Form_Element_File('photo');
		$photo->setDestination(SITE_ROOT.'images/')
		->setAttrib("id","photo")
		->setAttrib("class","checkimage")
		->addValidator('Extension', false, 'jpg,jpeg,png,gif')
		->addValidator('Size', false, 102400)
		->addDecorator('Errors', array('class'=>'error'));	
	
		$this->addElement($photo);
		
		
		
		/*$humen_verification= new Zend_Form_Element_Text('humen_verification');
		$humen_verification->setAttrib("class","textInput");
		$this->addElement($humen_verification);*/
		//Common Fields for all type of users
	
		
	}
}	

?>
