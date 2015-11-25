<?PHP
class Form_State extends Zend_Form
{  
	public function __construct($stateId="")
	{
		$this->init($stateId);
	}
    public function init($stateId)
	{   
		global $mySession;
        $db=new Db();
		
		$CountryId="";$StateName="";
		if($stateId!="")
		{
		$PageData=$db->runQuery("select * from ".STATE." where state_id='".$stateId."'");		
		$CountryId=$PageData[0]['country_id'];
		$StateName=$PageData[0]['state_name'];
		}
		
		$CounyryArr=array();
		$CounyryArr[0]['key']="";
		$CounyryArr[0]['value']="- - Country - -";
		$CounyryData=$db->runQuery("select * from ".COUNTRIES." order by country_name");
		if($CounyryData!="" and count($CounyryData)>0)
		{
			$i=1;
			foreach($CounyryData as $key=>$CounyryValues)
			{
			$CounyryArr[$i]['key']=$CounyryValues['country_id'];
			$CounyryArr[$i]['value']=$CounyryValues['country_name'];
			$i++;
			}
		}
		
		$country_id= new Zend_Form_Element_Select('country_id');
		$country_id->setRequired(true)
		->addMultiOptions($CounyryArr)
		->addValidator('NotEmpty',true,array('messages' =>'Country is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","required")
		->setValue($CountryId);
		
		$state_name= new Zend_Form_Element_Text('state_name');
		$state_name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'State Name is required.'))
                ->addValidator('regex', true, array(
                        'pattern' => '/^[a-zA-Z\-]+$/',
                        'messages' => array(
                            'regexNotMatch' => 'Please enter proper name and without space'
                        )
                            )
                    )
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","mws-textinput required") 
		->setValue($StateName);	
		
		$this->addElements(array($country_id,$state_name));
	}
}	
 
?>