<?PHP
class Form_Managead extends Zend_Form
{  
	public function init()
	{
		global $mySession;
        $db=new Db();
		//echo SITE_ROOT.'images/adimage/';
		//echo "select * from ".ADMINISTRATOR." where admin_id='1'";
		$Managead=$db->runQuery("select * from ".ADMINISTRATOR." where admin_id='1'"); 
		$ad_url_value=$Managead[0]['ad_url'];
		 $old_advertise_image_value=$Managead[0]['ad_image']; 
		
		$advertise_image= new Zend_Form_Element_File('advertise_image');
		$advertise_image->setDestination(SITE_ROOT.'images/adimage/')
        ->addValidator('Extension', false, 'jpg,jpeg,png,gif')
		->addDecorator('Errors', array('class'=>'error'));	
		$this->addElement($advertise_image);
		
		$old_advertise_image= new Zend_Form_Element_Hidden('old_advertise_image');
		$old_advertise_image->setValue($old_advertise_image_value);
		$this->addElement($old_advertise_image);
		
		
		$ad_url= new Zend_Form_Element_Text('ad_url');
		$ad_url->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Advertise url is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setValue($ad_url_value);
		$this->addElement($ad_url);
	}
}	
?>