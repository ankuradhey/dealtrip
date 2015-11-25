<?PHP
class Form_Amenities extends Zend_Form
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
		
		$airport1_value = ""; $airport2_value = ""; $distance1_value = ""; $distance2_value = "";$overview_value = "";$description_value="";
		$amenityArr = $db->runQuery("select * from ".AMENITY." where amenity_status = '1' ");
		
		if($ppty_id != "")
		{
			$pptyData = $db->runQuery("select * from ".PROPERTY." where id = '".$ppty_id."' ");	
			$airport1_value = $pptyData[0]['airport1'];
			$airport2_value = $pptyData[0]['airport2'];
			$distance1_value = $pptyData[0]['distance_airport1'];
			$distance2_value = $pptyData[0]['distance_airport2'];
			$overview_value = $pptyData[0]['big_desc'];
			$description_value = $pptyData[0]['amenity_ques'];			
		}
		
		
		$i = 0;
		
		
		
		foreach($amenityArr as $key=>$value)
		{		
			$OptionsArr = array();
			$OptionsArr[0]['key'] = $value['amenity_id'];
			$OptionsArr[0]['value'] = $value['title'];
			$quest = 'ques'.$i;
			 
			$ans[$i] = "";
			
			if($ppty_id != "")
			{
				$amenityValue = $db->runQuery("select * from ".AMENITY_ANS." where property_id = '".$ppty_id."' and amenity_id = '".$value['amenity_id']."' ");	
				foreach($amenityValue as $val)
				$ans[$i] = $val['amenity_value']; 				

			}
			
			$ques[$i] =  new Zend_Form_Element_Checkbox($quest);
			$ques[$i]->setAttrib("class","mws-textinput")
			->setAttrib("style","margin-top:10px;");
			
			if($ans[$i])
			$ques[$i]->setAttrib("checked","checked");
			
			$i++;
		}
		
		
		
		for($t = 0;$t<$i;$t++)
		$this->addElement($ques[$t]);
		
		$step =  new Zend_Form_Element_Hidden("step");
		$step->setValue("3");
		
		$this->addElement($step);		
		
		
		
		$airport1 = new Zend_Form_Element_Text('airport1');
		$airport1->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Enter Name of airport.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")
		->setValue($airport1_value);
		$this->addElement($airport1);		
		
		$distance1 = new Zend_Form_Element_Text('distance1');
		$distance1->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Enter Airport distance from Property location'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required number")
		->setValue($distance1_value);
		$this->addElement($distance1);		
		
		$airport2 = new Zend_Form_Element_Text('airport2');
		$airport2
                //->setRequired(true)
		//->addValidator('NotEmpty',true,array('messages' =>'Enter Name of airport.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput")
		->setValue($airport2_value);		
		$this->addElement($airport2);		
		
		$distance2 = new Zend_Form_Element_Text('distance2');
		$distance2->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Enter Airport distance from Property location'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required number")
		->setValue($distance2_value);
		$this->addElement($distance2);		
		
		
		$overview =  new Zend_Form_Element_Textarea("overview");
		$overview->setAttrib("class","mws-textinput")
		->setAttrib("maxlength","2000")
		->setAttrib("cols","60")
		->setValue($overview_value);
		$this->addElement($overview);		
		
		/** question regarding amenities **/		
		$description =  new Zend_Form_Element_Textarea("description");
		$description->setAttrib("class","mws-textinput")
		->setAttrib("maxlength","200")
		->setAttrib("cols","60")
		->setValue($description_value);
		$this->addElement($description);		
		
	}
}