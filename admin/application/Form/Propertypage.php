<?PHP
class Form_Propertypage extends Zend_Form
{  
	public function __construct($pageId="")
	{
		$this->init($pageId);
	}
    public function init($pageId)
	{   
		global $mySession;
        $db=new Db();
		
		
		if($pageId != "")
		{
			$PageData=$db->runQuery("select * from ".AMENITY_PAGE." where id = '".$pageId."'");		
			$PageTitle=$PageData[0]['page_title'];
			$PageContent=$PageData[0]['content'];
		}
		  
		
		$page_title= new Zend_Form_Element_Text('page_title');
		$page_title->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Page title is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")
		->setValue($PageTitle);
		
		$page_content= new Zend_Form_Element_Textarea('page_content');
		$page_content->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Page content is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("id","elrte")->setAttrib("cols","auto")
		->setAttrib("rows","auto")
		->setValue($PageContent);
		
		$this->addElements(array($page_title,$page_content));
		
	}
}	

?>