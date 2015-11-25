<?php
__autoloadDB('Db');
class Video extends Db
{

  public function Addvideo($dataForm,$videoId='')
  {
    global $mySession;
	$db=new Db();
	
   	 
	$data_update['video_type']=$dataForm['video_type'];
     
	if($dataForm['video_path1']!="")
	{
	 $data_update['video_path'] = $dataForm['video_path1'];
	}
	else if($dataForm['video_path2']!="")
	{
	 $videoname = explode('.',$dataForm['video_path2']);  
	 $len = count($videoname); 
	 $extension = $videoname[$len - 1]; 
	 $datetime = date(d.m.y_h.i.s); 
	 $new_name = $datetime.'.'.$extension; 
	 rename(SITE_ROOT.'video/'.$dataForm['video_path2'],SITE_ROOT.'video/'.$new_name); 
	 $data_update['video_path']=$new_name;
	} 
	
	
	$data_update['video_title']=$dataForm['video_title'];
	$data_update['video_description']=$dataForm['video_description'];
	$data_update['video_status']=$dataForm['video_status'];
	$data_update['featured_status']=$dataForm['featured_status'];
	$data_update['website_id']=$mySession->WebsiteId;
	
	if($videoId!="")
	{	
	//print_r($data_update);die;
	$condition='video_id='.$videoId;		
	$result=$db->modify(VIDEO,$data_update,$condition); 
    }
	else
	{
	
	 $result=$db->save(VIDEO,$data_update); 
    }
	return 1;
  }

}
?>