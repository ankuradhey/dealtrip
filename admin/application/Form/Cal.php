<?PHP
class Form_Cal extends Zend_Form
{  
	public function init()
	{   
		global $mySession;
        $db = new Db();
						
		$chkQuery = $db->runQuery("select * from ".PROPERTY." where id = '".$mySession->property_id."' ");
		
		$date_from = new Zend_Form_Element_Text('date_from');
		$date_from->setRequired(true)
		->setAttrib("class","mws-textinput required ")
		->setAttrib("style","float:left;");	
		
			

		
		$date_to = new Zend_Form_Element_Text('date_to');
		$date_to->setRequired(true)
		->setAttrib("class","mws-textinput required dateCheck");	
		
		$statusArr[0]['key'] = '0';
		$statusArr[0]['value'] = 'Booked';
		$statusArr[1]['key'] = '1';
		$statusArr[1]['value'] = 'On Request';
		$statusArr[2]['key'] = '2';
		$statusArr[2]['value'] = 'Available';
		
		$status = new Zend_Form_Element_Select('status');
		$status->addMultiOptions($statusArr)
		->setAttrib("class","mws-textinput required");
		
		
		if($chkQuery != "" && count($chkQuery) > 0 && $chkQuery[0]['cal_default'] == '2')
		{
			$date_from->setAttrib("disabled","disabled");	
			$date_to->setAttrib("disabled","disabled");
			$status->setAttrib("disabled","disabled");		
			
		}
		$this->addElements(array($date_from,$date_to,$status));		
	}
}
?>