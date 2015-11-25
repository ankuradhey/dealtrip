<?PHP
class Form_Subcate extends Zend_Form
{  
	public function __construct($subcateId="")
	{
		$this->init($subcateId);
	}
    public function init($subcateId)
	{   
		global $mySession;
        $db=new Db();
		
		$SubCategoryName="";$old_subcat_image_value="";$category_value=""; 
		
		if($subcateId!="")
		{
//echo "select * from ".SUBCATE." where subcate_id='".$subcateId."'";exit();
		$PageData=$db->runQuery("select * from ".SUBCATE." where subcate_id='".$subcateId."'");		
		$SubCategoryName=$PageData[0]['subcate_name'];
		$old_subcat_image_value=$PageData[0]['subcate_img_path'];
		$category_value=$PageData[0]['cat_id'];
		
		}
		
		$CateArr=array();
		$CateArr[0]['key']="";
		$CateArr[0]['value']="- - Categories - -";
		$CateData=$db->runQuery("select * from ".SERVICE_CATEGORIES." order by category_name");

		if($CateData!="" and count($CateData)>0)
		{
			$i=1;
			foreach($CateData as $key=>$CateValue)
			{
				$CateArr[$i]['key']=$CateValue['cat_id'];
				$CateArr[$i]['value']=$CateValue['category_name'];
				$i++;
			}
		}

		
		$category= new Zend_Form_Element_Select('category');
		$category->setRequired(true)
		->addMultiOptions($CateArr)
		->addValidator('NotEmpty',true,array('messages' =>'Category is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("style","width:250px;")	
		->setValue($category_value);
		$this->addElement($category);
		

		$subcate_name= new Zend_Form_Element_Text('subcate_name');
		$subcate_name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Sub Category Name is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setAttrib("style","width:350px;")		
		->setValue($SubCategoryName);
		$this->addElement($subcate_name);
	
		$subcate_image= new Zend_Form_Element_File('subcate_image');
		$subcate_image->setDestination(SITE_ROOT.'images/Subcate/')
        ->addValidator('Extension', false, 'jpg,jpeg,png,gif')
		->addDecorator('Errors', array('class'=>'error'));
		$this->addElement($subcate_image);

		
		$old_subcat_image= new Zend_Form_Element_Hidden('old_subcat_image');
		$old_subcat_image->setValue($old_subcat_image_value);
		$this->addElement($old_subcat_image);
		
//		$this->addElements(array($subcate_name,$subcate_image,$old_subcat_image,));		
	}
}	

?>