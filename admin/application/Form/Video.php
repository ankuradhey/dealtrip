<?PHP
class Form_Video extends Zend_Form
{  
    
	public function __construct($videoId)
	{
		$this->init($videoId);
	}
	public function init($videoId)
	{  
		global $mySession;
        $db=new Db(); 
		
		$path="";
		$title="";
		$date="";
		$description="";
		$status="";
		$featuredstatus="";
		$featurepath="";
		$videotype="";
		
		if($videoId!="")
		{  
		  $videoData=$db->runQuery("select * from ".VIDEOGALLERY." where 	video_id='".$videoId."'");	
		  $type = $videoData[0]['video_type'];
		  $path = $videoData[0]['video_path'];
		  $title = $videoData[0]['video_title'];
		  $date = date('F d-Y, h:i:s a',strtotime($videoData[0]['date_uploaded']));
		  $description = $videoData[0]['video_description'];
		  $keyword = $videoData[0]['video_keyword'];
		  $status = $videoData[0]['video_status'];
		  $videotype=$photoData[0]['video_type'];
		  /*$featuredstatus =$videoData[0]['featured_status'];*/
		  $featurepath=$photoData[0]['featured_path'];
		}
		
	
		$Type[0]['key']="1";
		$Type[0]['value']="URL from YouTube";
		$Type[1]['key']="2";
		$Type[1]['value']="Upload from computer";
		
		$video_type= new Zend_Form_Element_Radio('video_type');
		$video_type->addMultiOptions($Type)
		->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Video type required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib('onclick','changetype();');
		
		$video_path1= new Zend_Form_Element_Text('video_path1');
		$video_path1->setAttrib("class","textInput");
		 	
		if($_REQUEST['video_type']==1)
		{
		$video_path1->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Video path required.'))
		->addDecorator('Errors', array('class'=>'error'));
		}
		
		$video_path2= new Zend_Form_Element_File('video_path2');
		$video_path2->setAttrib("class","textInput")
		->setDestination(SITE_ROOT.'images/videos/')
        ->addValidator('Extension', false, 'flv');
	
		if($_REQUEST['video_type']==2)
		{ 
		$video_path2->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Video path required.'))
		->addDecorator('Errors', array('class'=>'error'));
		}
	
	
		$video_title= new Zend_Form_Element_Text('video_title');
		$video_title->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Video Title is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setValue($title);
		
		$up_date= new Zend_Form_Element_Text('up_date');
		$up_date->setAttrib("class","textInput")
		->setAttrib("disabled","disabled")
		->setValue($date);
		
		$video_description= new Zend_Form_Element_Textarea('video_description');
		$video_description->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Video description is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textarea_medium")
		->setValue($description);
		
		
		$Option[0]['key']="1";
		$Option[0]['value']="Active";
		$Option[1]['key']="0";
		$Option[1]['value']="Inactive";
		
		$video_status= new Zend_Form_Element_Radio('video_status');
		$video_status->setRequired(true)
		->addMultiOptions($Option)
		->addValidator('NotEmpty',true,array('messages' =>'Please select status of video.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setValue($status);

		
		$Option1[1]['key']="1";
		$Option1[1]['value']="Featured";
		$Option1[2]['key']="2";
		$Option1[2]['value']="Not Featured";
		
		/*$featured_status= new Zend_Form_Element_Radio('featured_status');
		$featured_status->setRequired(true)
		->addMultiOptions($Option1)
		->addValidator('NotEmpty',true,array('messages' =>'Please select status of featured.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setValue($featuredstatus);*/
		
		
		
		$this->addElements(array($video_type,$video_path1,$video_path2,$video_title,$up_date,$video_description,$video_keyword,$video_status, $feature_path));
		
	}
}	

?>