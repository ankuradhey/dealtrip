<?PHP
class Form_Event extends Zend_Form
{  
	public function __construct($eventId="")
	{
		global $mySession;
		$this->init($eventId);
	}
    public function init($eventId)
	{
		global $mySession;
        $db=new Db();
		$event_title_value="";$event_date_value="";$event_time_from_value="";$event_time_to_value="";$event_venue_value="";$event_location_value="";$event_lat_value="";$event_long_value="";$event_image_old_value="";$event_description_value="";
		if($eventId!="")
		{
		$eventData=$db->runQuery("select * from ".EVENTS." where event_id='".$eventId."'");
		$event_image_old_value=$eventData[0]['event_image'];
		$event_title_value=$eventData[0]['event_title'];
		$event_date_value=changeDate($eventData[0]['event_date'],1);
		$event_time_from_value=$eventData[0]['event_time_from'];
		$event_time_to_value=$eventData[0]['event_time_to'];
		$event_description_value=$eventData[0]['event_description'];
		$event_venue_value=$eventData[0]['event_venue'];
		$event_location_value=$eventData[0]['event_location'];
		$event_lat_value=$eventData[0]['event_lat'];
		$event_long_value=$eventData[0]['event_long'];
		}
		$hour_from_value="";$minute_from_value="";$ampm_from_value="";
		if($event_time_from_value!="")
		{
			$explode=explode("::",$event_time_from_value);
			$hour_from_value=$explode[0];$minute_from_value=$explode[1];$ampm_from_value=$explode[2];
		}
		$hour_to_value="";$minute_to_value="";$ampm_to_value="";
		if($event_time_to_value!="")
		{
			$explode=explode("::",$event_time_to_value);
			$hour_to_value=$explode[0];$minute_to_value=$explode[1];$ampm_to_value=$explode[2];
		}
		$event_title= new Zend_Form_Element_Text('event_title');
		$event_title->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Event title is required.'))
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setAttrib("class","textInput")
		->setValue($event_title_value);
		$this->addElement($event_title);
		
		$event_date= new Zend_Form_Element_Text('event_date');
		$event_date->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Event date is required.'))
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setAttrib("class","textInput")
		->setAttrib("readonly","readonly")
		->setAttrib("onclick","displayCalendar(this,'mm-dd-yyyy',this)")
		->setValue($event_date_value);
		$this->addElement($event_date);
		
		$MinutesArr=array();
		$MinutesArr[0]['key']="";
		$MinutesArr[0]['value']="Minutes";
		$Counter=1;
		for($j=0;$j<=59;$j++)
		{
			$Value=$j;
			if(strlen($j)=='1')
			{
			$Value="0".$j;
			}
		$MinutesArr[$Counter]['key']=$Value;
		$MinutesArr[$Counter]['value']=$Value;
		$Counter++;
		}
		
		$HoursArr=array();
		$HoursArr[0]['key']="";
		$HoursArr[0]['value']="Hours";
		for($j=1;$j<=12;$j++)
		{
			$Value=$j;
			if(strlen($j)=='1')
			{
			$Value="0".$j;
			}
		$HoursArr[$j]['key']=$Value;
		$HoursArr[$j]['value']=$Value;
		}
		
		$AmPmArr=array();
		$AmPmArr[0]['key']="AM";
		$AmPmArr[0]['value']="AM";		
		$AmPmArr[1]['key']="PM";
		$AmPmArr[1]['value']="PM";		
		
		$hour_from= new Zend_Form_Element_Select('hour_from');
		$hour_from->addMultiOptions($HoursArr)
		->setAttrib("class","textInput")
		->setAttrib("style","width:80px;")
		->setValue($hour_from_value);
		$this->addElement($hour_from);
		
		$minute_from= new Zend_Form_Element_Select('minute_from');
		$minute_from->addMultiOptions($MinutesArr)
		->setAttrib("class","textInput")
		->setAttrib("style","width:80px;")
		->setValue($minute_from_value);
		$this->addElement($minute_from);
		
		$ampm_from= new Zend_Form_Element_Select('ampm_from');
		$ampm_from->addMultiOptions($AmPmArr)
		->setAttrib("class","textInput")
		->setAttrib("style","width:50px;")
		->setValue($ampm_from_value);
		$this->addElement($ampm_from);
		
		
		$hour_to= new Zend_Form_Element_Select('hour_to');
		$hour_to->addMultiOptions($HoursArr)
		->setAttrib("class","textInput")
		->setAttrib("style","width:80px;")
		->setValue($hour_to_value);
		$this->addElement($hour_to);
		
		$minute_to= new Zend_Form_Element_Select('minute_to');
		$minute_to->addMultiOptions($MinutesArr)
		->setAttrib("class","textInput")
		->setAttrib("style","width:80px;")
		->setValue($minute_to_value);
		$this->addElement($minute_to);
		
		$ampm_to= new Zend_Form_Element_Select('ampm_to');
		$ampm_to->addMultiOptions($AmPmArr)
		->setAttrib("class","textInput")
		->setAttrib("style","width:50px;")
		->setValue($ampm_to_value);
		$this->addElement($ampm_to);
		
		/*$event_time= new Zend_Form_Element_Text('event_time');
		$event_time->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Event time is required.'))
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setAttrib("class","textInput")
		->setValue($event_time_value);
		$this->addElement($event_time);*/
		
		if($event_image_old_value!="")
		{
			$explode=explode(",",$event_image_old_value);	
		}
		for($counter=1;$counter<=10;$counter++)
		{
		$eventImageName='event_image'.$counter;
		$eventImageName= new Zend_Form_Element_File($eventImageName);
		$eventImageName->setDestination(SITE_ROOT.'images/events/')
        ->addValidator('Extension', false, 'jpg,jpeg,png,gif')
		->addDecorator('Errors', array('class'=>'errmsg'));
		$this->addElement($eventImageName);
		
		$eventImagePath='event_image_Path'.$counter;
		$eventImagePath= new Zend_Form_Element_Hidden($eventImagePath);
		$eventImagePath->setValue(@$explode[$counter-1]);
		$this->addElement($eventImagePath);
		
		}
				
		$event_venue= new Zend_Form_Element_Textarea('event_venue');
		$event_venue->setAttrib("class","textInput")
		->setAttrib("rows","3")
		->setAttrib("style","height:100px;")
		->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Event venue is required.'))
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setValue($event_venue_value);
		$this->addElement($event_venue);
		
		$event_description= new Zend_Form_Element_Textarea('event_description');
		$event_description->setAttrib("class","textInput")
		->setAttrib("rows","3")
		->setAttrib("style","height:100px;")
		->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Event description is required.'))
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setValue($event_description_value);
		$this->addElement($event_description);
		
		$address= new Zend_Form_Element_Text('address');
		$address->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Location on map is required.'))
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setAttrib("class","textInput")
		->setValue($event_location_value);
		$this->addElement($address);
		
		$latitude= new Zend_Form_Element_Hidden('latitude');
		$latitude->setValue($event_lat_value);
		$this->addElement($latitude);
		
		$longitude= new Zend_Form_Element_Hidden('longitude');
		$longitude->setValue($event_long_value);
		$this->addElement($longitude);
	}
}	
?>