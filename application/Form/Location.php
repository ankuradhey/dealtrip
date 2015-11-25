<?PHP
class Form_Location extends Zend_Form
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
		
		
		$airport1_value = "";$airport2_value = "";$distance2_value =""; $distance1_value = "";
		$latitude_value = "41.659"; $longitude_value = "-4.714"; $address_value = "";
		
		if($ppty_id != "")
		{
			$locationValue = $db->runQuery("select * from ".PROPERTY." where id = '".$ppty_id."' ");	
			if($locationValue != "" && count($locationValue) > 0 && $locationValue[0]['cletitude'] != "")
			{
				$latitude_value = $locationValue[0]['cletitude'];
				$longitude_value =  $locationValue[0]['clongitude'];
				$address_value = $locationValue[0]['address'];
			}
		}
		
		
		$latitude = new Zend_Form_Element_Hidden('latitude');   	
		$latitude->setAttrib("class","textInput")
		->setAttrib("readonly","readonly")
		//->setRequired(true)
		//->addValidator('NotEmpty',true,array('messages' =>'Latitude is required.'))
		//->addDecorator('Errors', array('class'=>'error'))
		->setValue($latitude_value);
		
		$longitude= new Zend_Form_Element_Hidden('longitude');
		$longitude->setAttrib("class","textInput")
		->setAttrib("readonly","readonly")
		//->setRequired(true)
		//->addValidator('NotEmpty',true,array('messages' =>'Longitude is required.'))
		//->addDecorator('Errors', array('class'=>'error'))
		->setValue($longitude_value);
		
		$address=new Zend_Form_Element_Text('address');
		$address->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Enter Your location is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")
		->setValue($address_value);
		
		/*$airport1 = new Zend_Form_Element_Text('airport1');
		$airport1->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Enter Name of airport.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")
		->setValue($airport1_value);
		
		$distance1 = new Zend_Form_Element_Text('distance1');
		$distance1->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Enter Airport distance from Property location'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required number")
		->setValue($distance1_value);
		
		$airport2 = new Zend_Form_Element_Text('airport2');
		$airport2->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Enter Name of airport.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")
		->setValue($airport2_value);		
		
		$distance2 = new Zend_Form_Element_Text('distance2');
		$distance2->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Enter Airport distance from Property location'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required number")
		->setValue($distance2_value);*/
		
		$step =  new Zend_Form_Element_Hidden("step");
		$step->setValue("4");
		
		$this->addElements(array($latitude,$longitude,$address,$step));		
		
		
	}
}