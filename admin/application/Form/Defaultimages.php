<?PHP
class Form_Defaultimages extends Zend_Form
{  
	public function init()
	{
		global $mySession;
        $db=new Db();
		
		$default_business_image= new Zend_Form_Element_File('default_business_image');
		$default_business_image->setDestination(SITE_ROOT.'images/businesses/')
        ->addValidator('Extension', false, 'jpg,jpeg,png,gif')
		->addDecorator('Errors', array('class'=>'errmsg'));
		$this->addElement($default_business_image);
		
		$default_event_image= new Zend_Form_Element_File('default_event_image');
		$default_event_image->setDestination(SITE_ROOT.'images/events/')
        ->addValidator('Extension', false, 'jpg,jpeg,png,gif')
		->addDecorator('Errors', array('class'=>'errmsg'));
		$this->addElement($default_event_image);
		
		$default_male_image= new Zend_Form_Element_File('default_male_image');
		$default_male_image->setDestination(SITE_ROOT.'images/profileimgs/')
        ->addValidator('Extension', false, 'jpg,jpeg,png,gif')
		->addDecorator('Errors', array('class'=>'errmsg'));
		$this->addElement($default_male_image);
		
		$default_female_image= new Zend_Form_Element_File('default_female_image');
		$default_female_image->setDestination(SITE_ROOT.'images/profileimgs/')
        ->addValidator('Extension', false, 'jpg,jpeg,png,gif')
		->addDecorator('Errors', array('class'=>'errmsg'));
		$this->addElement($default_female_image);
		
		$default_both_image= new Zend_Form_Element_File('default_both_image');
		$default_both_image->setDestination(SITE_ROOT.'images/profileimgs/')
        ->addValidator('Extension', false, 'jpg,jpeg,png,gif')
		->addDecorator('Errors', array('class'=>'errmsg'));
		$this->addElement($default_both_image);
		
	}
}	
?>