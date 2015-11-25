<?php
class Form_Latestreview extends Zend_Form
{  
    
	public function __construct($Id="")
	{
		$this->init($Id);
	}
	
	
	public function init($Id)
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
								   (select r_property_id from ".LATEST_REVIEW." where and r_id != '".$Id."' )
								   order by id desc
								   ");
		
		  
		  $photoData=$db->runQuery("select ".LATEST_REVIEW.".*, ".PROPERTY.".propertycode as propertycode, id  from ".LATEST_REVIEW." 
		  							inner join ".PROPERTY." on ".PROPERTY.".id = ".LATEST_REVIEW.".r_property_id
		  							where r_id='".$Id."'
									order by ".LATEST_REVIEW.".r_order asc
									");	
			
		  $propertycode_value = $photoData[0]['id'];
		  $order_value = $photoData[0]['r_order'];
		  $status_value =  $photoData[0]['r_status'];
		  
		}
		else
		$lpptyArr = $db->runQuery("select id, propertycode from ".PROPERTY." where 
								   id not in 
								   (select r_property_id from ".LATEST_REVIEW." )
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


