<?php
class Form_Currency extends Zend_Form
{
	public function __construct($currencyId="")
	{
		$this->init($currencyId);
	}
    public function init($currencyId)
	{   
		global $mySession;
        $db=new Db();
		# this section initialize all variables with NULL/"" values 
		$currency_name_value="";
		$currency_code_value="";
		$exchange_rate_value="";
		
		
		if($currencyId!="")
		{
			$currencyData=$db->runQuery("select * from ".CURRENCY." where currency_id='".$currencyId."'");
			$currency_name_value=$currencyData[0]['currency_name'];
			$currency_code_value=$currencyData[0]['currency_code'];
			$exchange_rate_value = $currencyData[0]['exchange_rate'];
		}
		
			$currency_name= new Zend_Form_Element_Text('currency_name');
			$currency_name->setRequired(true)
			->addValidator('NotEmpty',true,array('messages' =>'Currency Name is required.'))
			->addDecorator('Errors', array('class'=>'error'))
			->setAttrib("class","mws-textinput required")	
			->setAttrib("maxlength",'65')
			->setAttrib("minlength","3")
			->setAttrib("tabindex",'1')
			->setValue($currency_name_value);
			$this->addElements(array($currency_name));
			
			$currency_code= new Zend_Form_Element_Text('currency_code');
			$currency_code->setRequired(true)
			->addValidator('NotEmpty',true,array('messages' =>'Currency Code is required.'))
			->addDecorator('Errors', array('class'=>'error'))
			->setAttrib("class","mws-textinput required")	
			->setAttrib("maxlength",'65')
			->setAttrib("minlength","3")
			->setAttrib("tabindex",'1')
			->setValue($currency_code_value);
			$this->addElements(array($currency_code));
			
			$exchange_rate= new Zend_Form_Element_Text('exchange_rate');
			$exchange_rate->setRequired(true)
			->addValidator('NotEmpty',true,array('messages' =>'Currency Exchange Rate is required.'))
			->addDecorator('Errors', array('class'=>'error'))
			->setAttrib("class","mws-textinput required doubles")	
			->setAttrib("maxlength",'6')
			->setAttrib("minlength","3")
			->setAttrib("tabindex",'1')
			->setValue($exchange_rate_value);
			$this->addElements(array($exchange_rate));
		
	}
}




?>