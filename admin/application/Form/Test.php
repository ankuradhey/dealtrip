<?PHP
class Form_Test extends Zend_Form
{  
	public function init()
	{
		global $mySession;
        $db=new Db();
	
		$excelfile= new Zend_Form_Element_File('excelfile');
		$excelfile->setDestination(SITE_ROOT.'test/')
  //      ->addValidator('Extension', false, 'xls')
		->addDecorator('Errors', array('class'=>'error'));	
		$this->addElement($excelfile);
		
		//echo SITE_ROOT.'upload/';
		$zipfile= new Zend_Form_Element_File('zipfile');
		$zipfile->setDestination(SITE_ROOT.'test/')
//        ->addValidator('Extension', false, 'zip')
		->addDecorator('Errors', array('class'=>'error'));	
		$this->addElement($zipfile);
	}
}	
?>