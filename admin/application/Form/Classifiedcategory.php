<?PHP
class Form_Classifiedcategory extends Zend_Form
{  
	public function __construct($c_id="")
	{
		
		$this->init($c_id);
	}
    public function init($c_id)
	{   
		global $mySession;
        $db=new Db();
	
		
		if($c_id!="")
		{

		$PageData=$db->runQuery("select * from ".CLASSIFIED." where c_id='".$c_id."'");		
		$cat_id_value=$PageData[0]['cat_id'];
		$c_name_value=$PageData[0]['c_name'];
		$price_value=$PageData[0]['price'];
		$location_value=$PageData[0]['location'];
		$description_value=$PageData[0]['description'];
		$zipcode_value=$PageData[0]['zipcode'];
		}
		
		$CateArr=array();
		$CateArr[0]['key']="";
		$CateArr[0]['value']="- - Categories - -";
		$CateData=$db->runQuery("select * from ".CATEGORY." order by cat_name");

		if($CateData!="" and count($CateData)>0)
		{
			$i=1;
			foreach($CateData as $key=>$CateValue)
			{
				$CateArr[$i]['key']=$CateValue['cat_id'];
				$CateArr[$i]['value']=$CateValue['cat_name'];
				$i++;
			}
		}

		
		$cat_id= new Zend_Form_Element_Select('cat_id');
		$cat_id->setRequired(true)
		->addMultiOptions($CateArr)
		->addValidator('NotEmpty',true,array('messages' =>'Category is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("style","width:250px;")	
		->setValue($cat_id_value);
		$this->addElement($cat_id);
		
		$c_name= new Zend_Form_Element_Text('c_name');
		$c_name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Classified Name is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setAttrib("style","width:300px;")		
		->setValue($c_name_value);
		$this->addElement($c_name);

		$price= new Zend_Form_Element_Text('price');
		$price->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Classified Price is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setAttrib("onkeypress","return checknummsp(event);")
		->setAttrib("style","width:80px;")		
		->setValue($price_value);
		$this->addElement($price);
	
		$location= new Zend_Form_Element_Text('location');
		$location->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Classified location is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setAttrib("style","width:250px;")		
		->setValue($location_value);
		$this->addElement($location);

		$zipcode= new Zend_Form_Element_Text('zipcode');
		$zipcode->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Classified zipcode is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setAttrib("style","width:100px;")		
		->setValue($zipcode_value);
		$this->addElement($zipcode);

		
		$description= new Zend_Form_Element_Textarea('description');
		$description->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Classified Description is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setAttrib("style","width:800px;height:200px")		
		->setValue($description_value);
		$this->addElement($description);
		
	
		
//		$this->addElements(array($subcate_name,$subcate_image,$old_subcat_image,));		
	}
}	

?>