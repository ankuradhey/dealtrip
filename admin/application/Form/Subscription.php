<?PHP
class Form_Subscription extends Zend_Form
{  
	public function __construct($planId="")
	{
		global $mySession;
		$this->init($planId);
	}
    public function init($planId)
	{   
		global $mySession;
        $db=new Db();
		$plan_name_value="";$old_plan_image_value="";$nof_images_value="0";$plan_price_value="";$plan_validity_value="";$Isfree_value="0";$des_box_value="1";$featured_business_value="0";$offer_coupons_value="0";
		if($planId!="")
		{
		$planData=$db->runQuery("select * from ".SUBSCRIPTIONS." where plan_id='".$planId."'");
		$plan_name_value=$planData[0]['plan_name'];
		$old_plan_image_value=$planData[0]['plan_image'];
		$nof_images_value=$planData[0]['nof_images'];
		$Isfree_value=$planData[0]['is_free'];
		$plan_price_value=$planData[0]['plan_price'];
		$plan_validity_value=$planData[0]['plan_validity'];
		$des_box_value=$planData[0]['des_box'];
		$featured_business_value=$planData[0]['featured_business'];
		$offer_coupons_value=$planData[0]['offer_coupons'];
		}
		
		$plan_name= new Zend_Form_Element_Text('plan_name');
		$plan_name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Plan Name is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setValue($plan_name_value);
		$this->addElement($plan_name);
		
		$plan_image= new Zend_Form_Element_File('plan_image');
		$plan_image->setDestination(SITE_ROOT.'images/planimg/')
        ->addValidator('Extension', false, 'jpg,jpeg,png,gif')
		->addDecorator('Errors', array('class'=>'error'));	
		$this->addElement($plan_image);
		
		$old_plan_image= new Zend_Form_Element_Hidden('old_plan_image');
		$old_plan_image->setValue($old_plan_image_value);
		$this->addElement($old_plan_image);
		
		$nof_images= new Zend_Form_Element_Text('nof_images');
		$nof_images->setAttrib("class","textInput")
		->setAttrib("onkeypress","return checknummsp(event);")
		->setValue($nof_images_value);
		$this->addElement($nof_images);
		
		$Isfree= new Zend_Form_Element_Checkbox('Isfree');
		$Isfree->setCheckedValue('1')
		->setUncheckedValue('0')
		->setAttrib("onclick","SetFreeorPaid();")
		->setValue($Isfree_value);
		$this->addElement($Isfree);		
		
		$plan_price= new Zend_Form_Element_Text('plan_price');
		$plan_price->setAttrib("class","textInput")
		->setAttrib("onkeypress","return checknummsp(event);")
		->setValue($plan_price_value);
		$this->addElement($plan_price);
		
		if($_REQUEST['Isfree']=='0')
		{
		$plan_price->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Plan price is required.'))
		->addDecorator('Errors', array('class'=>'error'));
		}
		
		$ValidityArr=array();
		for($i=0;$i<=11;$i++)
		{
			$valueI=$i+1;
			$ValidityArr[$i]['key']=$valueI;
			$ValidityArr[$i]['value']=$valueI." Month";
		}
		
		$plan_validity= new Zend_Form_Element_Select('plan_validity');
		$plan_validity->addMultiOptions($ValidityArr)
		->setAttrib("class","textInput")
		->setValue($plan_validity_value);
		$this->addElement($plan_validity);
		
		$desBoxArr=array();
		$desBoxArr[0]['key']="1";
		$desBoxArr[0]['value']="Textbox";
		$desBoxArr[1]['key']="2";
		$desBoxArr[1]['value']="HTML Editor";
		$des_box=new Zend_Form_Element_Radio('des_box');
		$des_box->addMultiOptions($desBoxArr)
		->setValue($des_box_value);
		$this->addElement($des_box);
		
		$featured_business= new Zend_Form_Element_Checkbox('featured_business');
		$featured_business->setCheckedValue('1')
		->setUncheckedValue('0')
		->setValue($featured_business_value);
		$this->addElement($featured_business);
		
		$offer_coupons= new Zend_Form_Element_Checkbox('offer_coupons');
		$offer_coupons->setCheckedValue('1')
		->setUncheckedValue('0')
		->setValue($offer_coupons_value);
		$this->addElement($offer_coupons);
	}
}	

?>