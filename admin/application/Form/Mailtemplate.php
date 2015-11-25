<?PHP
class Form_Mailtemplate extends Zend_Form
{  
	public function __construct($templateId="")
	{
		$this->init($templateId);
	}
    public function init($templateId)
	{   
		global $mySession;
        $db=new Db();
		
		$EmailSubject="";$MessageBody="";
		if($templateId!="")
		{
		$templateData=$db->runQuery("select * from ".EMAIL_TEMPLATES." where template_id='".$templateId."'");		
		$EmailSubject=$templateData[0]['email_subject'];
		$MessageBody=$templateData[0]['email_body'];
		}
				
		
		$email_subject= new Zend_Form_Element_Text('email_subject');
		$email_subject->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Page title is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required")
		->setValue($EmailSubject);
		
		$email_body= new Zend_Form_Element_Textarea('email_body');
		$email_body->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Page content is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("id","elrte")
		->setAttrib("cols","auto")
		->setAttrib("rows","auto")
		->setValue($MessageBody);
		
		$this->addElements(array($email_subject,$email_body));
		
	}
}	

?>