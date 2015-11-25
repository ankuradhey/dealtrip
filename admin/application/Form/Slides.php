<?PHP
class Form_Slides extends Zend_Form
{  
    
	public function __construct($lppty_type,$Id="")
	{
		$this->init($lppty_type,$Id);
	}
	
	
	public function init($lppty_type,$Id)
	{  
		global $mySession;
        $db=new Db(); 
		
		$path="";
		$title="";
		$date="";
		$description="";
		$status="";
		$featuredstatus="";
		$phototype="";
		$featurepath="";
		
		
		
		if($Id!="")
		{  
		  $lpptyArr = $db->runQuery("select id, propertycode from ".PROPERTY." where 
								   id not in 
								   (select lppty_property_id from ".SLIDES_PROPERTY." where lppty_type = '".$lppty_type."' and lppty_id != '".$Id."' )
								   order by id desc
								   ");
		
		  
		  $photoData=$db->runQuery("select ".SLIDES_PROPERTY.".*, ".PROPERTY.".propertycode as propertycode, id  from ".SLIDES_PROPERTY." 
		  							inner join ".PROPERTY." on ".PROPERTY.".id = ".SLIDES_PROPERTY.".lppty_property_id
		  							where lppty_id='".$Id."'
									order by ".SLIDES_PROPERTY.".lppty_order asc
									");	
			
		  $propertycode_value = $photoData[0]['id'];
		  $order_value = $photoData[0]['lppty_order'];
		  $status_value =  $photoData[0]['lppty_status'];
		  
		}
		else
		$lpptyArr = $db->runQuery("select id, propertycode from ".PROPERTY." where 
								   id not in 
								   (select lppty_property_id from ".SLIDES_PROPERTY." where lppty_type = '".$lppty_type."' )
								   order by id desc
								   ");
		
		

		//->setValue($phototype);
		$pptyArr = array();
		$pptyArr[0]['key']	 = "";
		$pptyArr[0]['value']	 = "- - Select - -";		
		
		foreach($lpptyArr as $keys=>$values)
		{
			$pptyArr[$keys+1]['key']	 = $values['id'];
			$pptyArr[$keys+1]['value']  = $values['propertycode'];
		}

		$propertycode = new Zend_Form_Element_Select('propertycode');
		$propertycode->setRequired(true)
		->setMultiOptions($pptyArr)
		->addValidator('NotEmpty',true,array('messages' =>'Property code is required'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required digit chzn-select")
		->setValue($propertycode_value);
		
	
		
		
		$Status[0]['key']="1";
		$Status[0]['value']="Active";
		$Status[1]['key']="0";
		$Status[1]['value']="Inactive";
		
		
		
		$status = new Zend_Form_Element_Select('status');
		$status->setRequired(true)
		->addMultiOptions($Status)
		->addValidator('NotEmpty',true,array('messages' =>'Please select status.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setValue($status_value);

		
		
		$this->addElements(array($propertycode,$order,$status));
		
	}
}	

?>