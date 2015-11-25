<?PHP
class Form_Blog extends Zend_Form
{  
	public function __construct($blogId="")
	{
		$this->init($blogId);
	}
    public function init($blogId)
	{ //  echo "hello ".$blogId; exit();
		global $mySession;
        $db=new Db();
		
		$title_value="";$description_value="";$status_value="1";
		if($blogId!="")
		{
		$blogData=$db->runQuery("select * from ".BLOG_POST." where blog_post_id='".$blogId."'");		
		$title_value=$blogData[0]['title'];
		$description_value=$blogData[0]['description'];
		$status_value=$blogData[0]['status'];
		}
						
		$StatusArr=array();
		$StatusArr[0]['key']="1";
		$StatusArr[0]['value']="Enable";
		
		
		$status= new Zend_Form_Element_Select('status');
		$status->addMultiOptions($StatusArr)
		->setAttrib("class","textInput")
		->setValue($status_value);
		
		$title= new Zend_Form_Element_Text('title');
		$title->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'blog title is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")		
		->setValue($title_value);
		
		$description= new Zend_Form_Element_Textarea('description');
		$description->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'blog description is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setAttrib("rows","3")
		->setAttrib("style","height:100px;")
		->setValue($description_value);

		$this->addElements(array($status,$title,$description));
		
	}
}	

?>