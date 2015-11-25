<?PHP
class Form_Photo extends Zend_Form
{  
	public function init()
	{   
		global $mySession;
        				
		$photo_title=new Zend_Form_Element_Text('photo_title');
		$photo_title->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Photo title is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput");	
		
		$photo_path= new Zend_Form_Element_File('photo_path');
		$photo_path->setDestination(SITE_ROOT.'images/floorplan/')
        ->addValidator('Extension', false, 'jpg,pdf')
		->setAttrib("class","mws-textinput")	
		->addDecorator('Errors', array('class'=>'error'));
		
		$step = new Zend_Form_Element_Hidden('step');
		$step->setValue("5");	
		
				
		$this->addElements(array($photo_path,$step));		
	}
}
?>