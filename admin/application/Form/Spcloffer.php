<?PHP
class Form_Spcloffer extends Zend_Form
{  
	public function __construct($sId="")
	{
		$this->init($sId);
	}
    public function init($sId)
	{   
		global $mySession;
        $db=new Db();
		
		$OfferName="";$min_nights_value = "0";$min_nights_def_value = "";
		if($sId!="")
		{
			$PageData = $db->runQuery("select * from ".SPCL_OFFER_TYPES." where id='".$sId."'");		
			$OfferName = $PageData[0]['type_name'];
			$status_value = $PageData[0]['status'];
			$promo_code_value = $PageData[0]['promo_code'];
			$discount_type_value = 	$PageData[0]['discount_type'];	
			$min_nights_value = $PageData[0]['min_nights_type'];
			if($min_nights_value == '1')
			$min_nights_def_value = $PageData[0]['min_nights'];
		}
		
		$offer_name = new Zend_Form_Element_Text('offer_name');
		$offer_name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Country Name is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")	
		->setValue($OfferName);	
		
		$statusArr[0]['key'] = '0';
		$statusArr[0]['value'] = 'Disable';
		$statusArr[1]['key'] = '1';
		$statusArr[1]['value'] = 'Enable';
		
		
		$status = new Zend_Form_Element_Select('status');
		$status->addDecorator('Errors', array('class'=>'error'))
		->addMultiOptions($statusArr)
		->setAttrib("class","mws-textinput required")	
		->setValue($status_value);
		
		$promo_code = new Zend_Form_Element_Text('promo_code');
		$promo_code->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Promo Code is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")
		->setAttrib("maxlength","5")	
		->setValue($promo_code_value);	
		
		

		$nightsArr[1]['key'] = "0";
		$nightsArr[1]['value'] = "Not Fixed";
		$nightsArr[2]['key'] = "1";
		$nightsArr[2]['value'] = "Fixed";
		
		$min_nights = new Zend_Form_Element_Radio('min_nights');
		$min_nights->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Minimum stay value is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")
		->setAttrib("onclick","min_nightss();")
		->addMultiOptions($nightsArr)
		->setValue($min_nights_value);	
		
		$min_nights_def = new Zend_Form_Element_Text('min_nights_def');
		$min_nights_def->setAttrib("class","mws-textinput required")
		->setAttrib("maxlength","3")	
		->setValue($min_nights_def_value);	

		$discountArr[1]['key'] = "0";
		$discountArr[1]['value'] = "Discount Percentage";
		$discountArr[2]['key'] = "1";
		$discountArr[2]['value'] = "Nights Free";
		$discountArr[3]['key'] = "2";
		$discountArr[3]['value'] = "Free Pool heating";
		$discountArr[4]['key'] = "3";
		$discountArr[4]['value'] = "7 Nights Free";		
		
		
		$discount_type = new Zend_Form_Element_Radio('discount_type');
		$discount_type->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Minimum stay value is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")
		->addMultiOptions($discountArr)
		->setValue($discount_type_value);	
		
		$this->addElements(array($offer_name,$status,$promo_code,$min_nights,$min_nights_def,$discount_type));		
	}
}	

?>