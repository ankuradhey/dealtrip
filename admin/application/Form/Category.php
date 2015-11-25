<?PHP
class Form_Category extends Zend_Form
{  
	public function __construct($categoryId="")
	{
		$this->init($categoryId);
	}
    public function init($categoryId)
	{   
		global $mySession;
        $db=new Db();
		
		$CategoryName=""; $cattitle=""; $catdesc="";$catimage="";$rssurl="";
		if($categoryId!="")
		{
			//category_id, category_name, perent_id, cat_position, cat_title, cat_desc, cat_image, category_status, created_by, created_date

			$PageData=$db->runQuery("select * from ".CATEGORIES." where category_id='".$categoryId."'");		
			$CategoryName=$PageData[0]['category_name'];
			$catposition=$PageData[0]['cat_position'];

			$cattitle=$PageData[0]['cat_title'];
			$catdesc=$PageData[0]['cat_desc'];
			$catimage=$PageData[0]['cat_image'];
			$rssurl=$PageData[0]['rss_url'];
			
		}
		$cat_position= new Zend_Form_Element_Hidden('cat_position');
		$cat_position->setValue($catposition);
		
		$cat_image_file= new Zend_Form_Element_Hidden('cat_image_file');
		$cat_image_file->setValue($catimage);
		
		$cat_desc= new Zend_Form_Element_Textarea('cat_desc');
		$cat_desc->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Category Title is required.'))
		->setAttrib("style","width:350px;height:150px")
		->setValue($catdesc);
		
		$category_name= new Zend_Form_Element_Text('category_name');
		$category_name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Category Name is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setAttrib("style","width:350px;")		
		->setValue($CategoryName);	
		
		$cat_title= new Zend_Form_Element_Text('cat_title');
		$cat_title->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Category Title is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setAttrib("style","width:350px;")		
		->setValue($cattitle);	
		
		$rss_url= new Zend_Form_Element_Text('rss_url');
		$rss_url->setAttrib("class","textInput")
		->setAttrib("style","width:350px;")		
		->setValue($rssurl);	
		
		$photo_path= new Zend_Form_Element_File('photo_path');
		$photo_path->setDestination(SITE_ROOT.'images/category/')
        ->addValidator('Extension', false, 'jpg,jpeg,png,gif')
		->setRequired(false)
		->addDecorator('Errors', array('class'=>'error'));
		
		$this->addElements(array($category_name,$cat_title,$cat_desc,$cat_image_file,$rss_url,$cat_position,$photo_path));		
	}
}	

?>