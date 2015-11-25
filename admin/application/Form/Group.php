<?PHP
class Form_Group extends Zend_Form
{  
	public function __construct($formtype,$id="")
	{
		if($formtype=="category")
			{$this->initcategory($id);}
		if($formtype=="topic")
			{$this->inittopic($id);}
		/*
		else
			{$this->init($id);}
		*/
		
	}
	public function inittopic($topic_id)
	{
		global $mySession;
        $db=new Db();
		
		$CategoryName="";
		if($topic_id!="")
		{
			$PageData=$db->runQuery("select * from ".GROUP_TPOIC." where topic_id='".$topic_id."'");
		 
			$topicname=$PageData[0]['topic_name'];
			$topicdescription=$PageData[0]['topic_description'];
			$topicaccess_type=$PageData[0]['topic_access_type'];
			$created_by=$PageData[0]['created_by'];
			$topicstatus=$PageData[0]['topic_status'];
			$topiccrdate=$PageData[0]['topic_cr_date'];
			$topic_id=$PageData[0]['topic_id'];
			$catvalueArray=array();
			$PageData=$db->runQuery("select category_id from ".GROUP_TPOIC_CATEGORIES." where topic_id='".$topic_id."'");
			foreach($PageData as $key=> $value)
			{
				$catvalueArray[]=$value['category_id'];
			}
			$groupmember=array();
			$PageData=$db->runQuery("select member_id from ".GROUP_MEMBER." where topic_id='".$topic_id."'");
			foreach($PageData as $key=> $value)
			{
				$groupmember[]=$value['member_id'];
			}
			 
			 
		}
		/*$CatArray=array();
		$PageData=$db->runQuery("select group_id,group_name from ".GROUP_CATEGORIES." where group_status=1 order by group_name");
		foreach($PageData as $key=> $value)
		{
			$CatArray[$value['group_id']]=$value['group_name'];
		}
		
		$topic_group= new Zend_Form_Element_Checkbox('topic_group');
		$topic_group->setMultiOptions($CatArray)
		->setAttrib("multiple","multiple")
		->setValue($catvalueArray);*/
		
		$groupArr=array();
		$PageData=$db->runQuery("select group_id,group_name from ".GROUP_CATEGORIES." where group_id > 0 and group_status=1 order by group_name");
		if($PageData!="" and count($PageData)>0)
		{
			$i=0;
			foreach($PageData as $key=>$pageDataValues)
			{
			$groupArr[$i]['key']=$pageDataValues['group_id'];
			$groupArr[$i]['value']=$pageDataValues['group_name'];
			$i++;
			}
		}
		
		$topic_group= new Zend_Form_Element_Multicheckbox('topic_group');
		$topic_group->setSeparator("<br>")
		->setMultiOptions($groupArr)
		->setValue($catvalueArray);
		
		unset($groupArr);
		
		$groupArr=array();

		$PageData=$db->runQuery("select user_id,username from ".USERS." where user_status='1' order by username");
		if($PageData!="" and count($PageData)>0)
		{
			$i=0;
			foreach($PageData as $key=>$pageDataValues)
			{
			$groupArr[$i]['key']=$pageDataValues['user_id'];
			$groupArr[$i]['value']=$pageDataValues['username'];
			$i++;
			}
		}
		
		$group_member= new Zend_Form_Element_Multicheckbox('group_member');
		$group_member->setSeparator("<br>")
		->setMultiOptions($groupArr)
		->setValue($groupmember);
		
		
		
		
		$topic_name= new Zend_Form_Element_Text('topic_name');
		$topic_name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Topic Name is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setValue($topicname);	
		
		$topic_description= new Zend_Form_Element_Textarea('topic_description');
		$topic_description->setRequired(true)
		->setAttrib("rows",5)
		->addValidator('NotEmpty',true,array('messages' =>'Topic description is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setValue($topicdescription);
		
		$topic_status=new Zend_Form_Element_Hidden('topic_status');
		$topic_status->setAttrib("value",1);	
		
		$topic_access_type= new Zend_Form_Element_Select('topic_access_type');
		$topic_access_type->setRequired(true)
		->setMultiOptions(array('0'=>'Public', '1'=>'Private','2'=>'Protected'))
		->addValidator('NotEmpty',true,array('messages' =>'Topic access type is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("onchange","showhide(this.value)")
		->setValue($topicaccess_type);
		
		
		$created_by=new Zend_Form_Element_Hidden('created_by');
		$created_by->setAttrib("value",$created_by);
		
		$topic_cr_date=new Zend_Form_Element_Hidden('topic_cr_date');
		$topic_cr_date->setAttrib("value",$topic_cr_date);
		
		
		
		$this->addElements(array($topic_name,$topic_description,$topic_status,$topic_access_type,$created_by,$topic_cr_date,$topic_group,$group_member));	
		
	}
	public function initcategory($categoryId)
	{
		global $mySession;
        $db=new Db();
		
		$groupdesc="";$GroupName="";$groupstatus='1';
		if($categoryId!="")
		{
			$PageData=$db->runQuery("select * from ".GROUP_CATEGORIES." where group_id='".$categoryId."'");		
			$GroupName=$PageData[0]['group_name'];
			$groupstatus=$PageData[0]['group_status'];
			$group_id=$PageData[0]['group_id'];
			$groupdesc=$PageData[0]['group_desc'];
		}
		
		$group_name= new Zend_Form_Element_Text('group_name');
		$group_name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Category Name is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setAttrib("style","width:350px;")		
		->setValue($GroupName);	
		
		$group_desc= new Zend_Form_Element_Textarea('group_desc');
		$group_desc->setAttrib("class","textInput")
		->setAttrib("style","width:350px;height:100px")		
		->setValue($groupdesc);	
		
		$group_status=new Zend_Form_Element_Hidden('group_status');
		$group_status->setValue($groupstatus);	
		 
		$this->addElements(array($group_name,$group_status,$group_desc));	
	}
	
    /*public function init($categoryId)
	{   
		global $mySession;
        $db=new Db();
		
		$CategoryName="";
		if($categoryId!="")
		{
		$PageData=$db->runQuery("select * from ".DEAL_CAT." where cat_id='".$categoryId."'");		
		$CategoryName=$PageData[0]['category_name'];
		}
		
		$cat_name= new Zend_Form_Element_Text('cat_name');
		$cat_name->setRequired(true)
		->addValidator('NotEmpty',true,array('messages' =>'Category Name is required.'))
		->addDecorator('Errors', array('class'=>'error'))
		->setAttrib("class","textInput")
		->setAttrib("style","width:350px;")		
		->setValue($CategoryName);	
		
		$this->addElements(array($cat_name));	
		echo 'hi';	
	}*/
}	

?>