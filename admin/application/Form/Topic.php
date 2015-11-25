<?PHP
class Form_Topic extends Zend_Form
{  
	public function __construct($topicId="")
	{
		$this->init($topicId);
	}
    public function init($topicId)
	{   
		global $mySession;
        $db=new Db();
		
		$topicTitle="";$topicDescription="";$topicStatus="1";
		if($topicId!="")
		{
		$topicData=$db->runQuery("select * from ".FORUM_TOPICS." where topic_id='".$topicId."'");		
		$topicTitle=$topicData[0]['topic_title'];
		$topicDescription=$topicData[0]['topic_description'];
		$topicStatus=$topicData[0]['topic_status'];
		}
				
		$StatusArr=array();
		$StatusArr[0]['key']="1";
		$StatusArr[0]['value']="Enable";
		$StatusArr[1]['key']="0";
		$StatusArr[1]['value']="Disable";
		$topic_status= new Zend_Form_Element_Select('topic_status');
		$topic_status->addMultiOptions($StatusArr)
		->setAttrib("class","textInput")
		->setValue($topicStatus);
		
		$topic_title= new Zend_Form_Element_Text('topic_title');
		$topic_title->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Topic title is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")		
		->setValue($topicTitle);
		
		$topic_description= new Zend_Form_Element_Textarea('topic_description');
		$topic_description->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Topic description is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setAttrib("rows","3")
		->setAttrib("style","height:100px;")
		->setValue($topicDescription);
		
		$this->addElements(array($topic_status,$topic_title,$topic_description));
		
	}
}	

?>