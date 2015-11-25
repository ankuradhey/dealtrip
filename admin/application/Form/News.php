<?php
class Form_News extends Zend_Form
{
	public function __construct($newsId="")
	{
		$this->init($newsId);
	}
    public function init($newsId)
	{   
		global $mySession;
        $db=new Db();
		
		$posted_on_value="";$news_value="";$subject_value="";
		if($newsId!="")
		{
			$newsArr = $db->runQuery("select * from ".NEWS." where news_id ='".$newsId."'");
			$subject_value = $newsArr[0]['subject'];
			$news_value = $newsArr[0]['news_update'];
			$posted_on_value = date('m/d/Y',strtotime($newsArr[0]['posted_on']));
		}
		
		
		if($newsId != "")
		{
			$posted_on = new Zend_Form_Element_Text('posted_on');
			$posted_on->setRequired(true)
			->addValidator('NotEmpty',true,array('messages' =>'Date is required.'))
			->addDecorator('Errors', array('class'=>'error'))
			->setAttrib("class","mws-datepicker required")	
			->setAttrib("maxlength",'65')
			->setAttrib("minlength","3")
			->setAttrib("tabindex",'1')
			->setValue($posted_on_value);
			
			$this->addElement($posted_on);
		
		}
			$subject = new Zend_Form_Element_Text('subject');
			$subject->setRequired(true)
			->addValidator('NotEmpty',true,array('messages' =>'Date is required.'))
			->addDecorator('Errors', array('class'=>'error'))
			->setAttrib("class","mws-textinput required")	
			->setAttrib("maxlength",'65')
			->setAttrib("minlength","3")
			->setAttrib("tabindex",'1')
			->setValue($subject_value);
			
			$news = new Zend_Form_Element_Textarea('news');
			$news->addDecorator('Errors', array('class'=>'error'))
			->setAttrib("class","mws-textinput required")	
			->setAttrib("maxlength",'100')
			->setValue($news_value);
			
			$this->addElements(array($news,$subject));
			
			
		
	}
}




?>