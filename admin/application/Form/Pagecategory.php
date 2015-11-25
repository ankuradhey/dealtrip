<?PHP
class Form_Pagecategory extends Zend_Form
{  
	public function __construct($categoryId="")
	{
		$this->init($categoryId);
	}
    public function init($categoryId)
	{   
		global $mySession;
        $db=new Db();
		
		$CategoryName="";
		if($categoryId!="")
		{
		$PageData=$db->runQuery("select * from ".PAGE_CAT." where cat_id='".$categoryId."'");		
		$CategoryName=$PageData[0]['category_name'];
		}
		
		$cat_name= new Zend_Form_Element_Text('cat_name');
		$cat_name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Category Name is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setAttrib("style","width:350px;")		
		->setValue($CategoryName);	
		
		$this->addElements(array($cat_name));		
	}
}	

?>