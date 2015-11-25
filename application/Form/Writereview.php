<?PHP
class Form_Writereview extends Zend_Form
{  
	public function init()
	{   
		global $mySession;
        
		$review_text= new Zend_Form_Element_Textarea('review_text');
		$review_text->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Please enter your review.'))
		->addDecorator('Errors', array('class'=>'errmsg'))
		->setAttrib("rows","3")
		->setAttrib("style","width:300px;height:100px;")
		->setAttrib("class","textInput");
				
		$this->addElements(array($review_text));		
	}
}
?>