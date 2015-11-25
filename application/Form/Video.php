<?PHP
class Form_Video extends Zend_Form
{  
	public function init()
	{   
		global $mySession;
        
		$PathType='1';
		if(isset($_REQUEST['path_type']))
		{
			$PathType=$_REQUEST['path_type'];
		}
		$video_title=new Zend_Form_Element_Text('video_title');
		$video_title->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Video title is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput");
		
		$pathtypeArr=array();
		$pathtypeArr[0]['key']="1";
		$pathtypeArr[0]['value']="Computer";
		$pathtypeArr[1]['key']="2";
		$pathtypeArr[1]['value']="You Tube Url";
		$path_type= new Zend_Form_Element_Radio('path_type');
		$path_type->addMultiOptions($pathtypeArr)
		->setAttrib("onclick","setType(this.value);")
		->setValue(1);
		
		$video_path= new Zend_Form_Element_File('video_path');
		$video_path->setDestination(SITE_ROOT.'images/videos/')
        ->addValidator('Extension', false, 'flv');
		
		$you_tube_url=new Zend_Form_Element_Text('you_tube_url');
		$you_tube_url->setAttrib("class","textInput");
		
		if($PathType=='1')
		{
		$video_path->setRequired(true)
		->addDecorator('Errors', array('class'=>'error'));
		}
		else
		{
		$you_tube_url->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'You tube url is required.'))
		->addDecorator('Errors', array('class'=>'error'));
		}
				
		$this->addElements(array($video_title,$video_path,$you_tube_url,$path_type));
	}
}
/*'aiff', 'asf', 'avi', 'bmp', 'fla', 'flv', 'gif', 'jpeg', 'jpg', 'mid', 'mov', 'mp3', 'mp4', 'mpc', 'mpeg', 'mpg', 'png', 'qt', 'ram', 'rm', 'rmi', 'rmvb', 'swf', 'tif', 'tiff', 'wav', 'wma', 'wmv'*/
?>
