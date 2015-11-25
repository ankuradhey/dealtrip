<?php
class Form_Speccat extends Zend_Form
{
	public function __construct($cId="")
	{
		$this->init($cId);
	}
    public function init($cId)
	{   
		global $mySession;
        $db=new Db();
		
		$amenity_value=""; $description_value = "";$status_value = "";
		
		
		if($cId!="")
		{
			$catData=$db->runQuery("select * from ".PROPERTY_SPEC_CAT." where cat_id ='".$cId."'");
			$category_value = $catData[0]['cat_name'];
			$status_value = $catData[0]['cat_status'];
			
		}
		
			$category = new Zend_Form_Element_Text('category');
			$category->setRequired(true)
			->addValidator('NotEmpty',true,array('messages' =>'Category Name is required.'))
			->addDecorator('Errors', array('class'=>'error'))
			->setAttrib("class","mws-textinput required")	
			->setAttrib("maxlength",'65')
			->setAttrib("minlength","3")
			->setAttrib("tabindex",'1')
			->setValue($category_value);
			
		
			$statusArr[0]['key'] = '0';
			$statusArr[0]['value'] = 'Disable';
			$statusArr[1]['key'] = '1';
			$statusArr[1]['value'] = 'Enable';
			
			
			$status = new Zend_Form_Element_Select('status');
			$status->addDecorator('Errors', array('class'=>'error'))
			->addMultiOptions($statusArr)
			->setAttrib("class","mws-textinput required")	
			->setValue($status_value);
			
			
			
			
			
			$this->addElements(array($category,$status));
			
			
		
	}
}




?>