<?PHP
class Form_Admincities extends Zend_Form
{  
	public function __construct($Id="")
	{
		$this->init($Id);
	}
    public function init($Id)
	{   
		global $mySession;
        $db=new Db();
		
		$Name_value="";$Zipcode_value="";
		if($Id!="")
		{
			$PageData=$db->runQuery("select * from ".ADMIN_CITIES." where id='".$Id."'");		
			$Name_value=$PageData[0]['name'];
			$Zipcode_value=	$PageData[0]['zipcode'];
		
		}
		
		$name= new Zend_Form_Element_Text('name');
		$name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'City Name is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setAttrib("style","width:200px;")		
		->setValue($Name_value);			
		$this->addElements(array($name));		

		$zipcode= new Zend_Form_Element_Text('zipcode');
		$zipcode->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Zipcode is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setAttrib("style","width:200px;")	
		->setAttrib("maxlength","5")
		->setAttrib("onkeypress","return is_valid_zip(event,this);")
		->setValue($Zipcode_value);			
		$this->addElements(array($zipcode));		

		
	}
}	

?>