<?php
class Form_Protype extends Zend_Form
{
	public function __construct($ptyleId="")
	{
		$this->init($ptyleId);
	}
    public function init($ptyleId)
	{   
		global $mySession;
        $db=new Db();
		# this section initialize all variables with NULL/"" values 
		$ptyle_name_value="";
		
		
		if($ptyleId!="")
		{
			$ptyleData=$db->runQuery("select * from ".PROPERTYTYPE." where ptyle_id='".$ptyleId."'");
			$ptyle_name_value=$ptyleData[0]['ptyle_name'];
			
			
		}
		
			$ptyle_name= new Zend_Form_Element_Text('ptyle_name');
			$ptyle_name->setRequired(true)
			->addValidator('NotEmpty',true,array('messages' =>'Property Type Name is required.'))
			->addDecorator('Errors', array('class'=>'error'))
			->setAttrib("class","mws-textinput required")	
			->setAttrib("maxlength",'65')
			->setAttrib("minlength","3")
			->setAttrib("tabindex",'1')
			->setValue($ptyle_name_value);
			$this->addElements(array($ptyle_name));
			
			
		
	}
}




?>