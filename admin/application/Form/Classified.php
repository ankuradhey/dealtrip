<?PHP
class Form_Classified extends Zend_Form
{  
	public function __construct($categoryId)
	{
		$this->init($categoryId);
	}
    public function init($categoryId)
	{   
		global $mySession;
        $db=new Db();
		
		$CategoryName="";$old_cat_image_value="";
		if($categoryId!="")
		{
		$PageData=$db->runQuery("select * from ".CATEGORY." where cat_id='".$categoryId."'");		
		$CategoryName=$PageData[0]['cat_name'];
		$old_cat_image_value=$PageData[0]['cat_image'];
		}
		
		$cat_name= new Zend_Form_Element_Text('cat_name');
		$cat_name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Category Name is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setAttrib("style","width:350px;")		
		->setValue($CategoryName);
		
		
		$cat_image= new Zend_Form_Element_File('cat_image');
		$cat_image->setDestination(SITE_ROOT.'images/')
        ->addValidator('Extension', false, 'jpg,jpeg,png,gif')
		->addDecorator('Errors', array('class'=>'error'));
		$this->addElement($cat_image);
		
		$old_cat_image= new Zend_Form_Element_Hidden('old_cat_image');
		$old_cat_image->setValue($old_cat_image_value);
		$this->addElement($old_cat_image);
		
		
		$this->addElements(array($cat_name,$cat_image,$old_cat_image));		
	}
}	

?>