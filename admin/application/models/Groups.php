<?php
__autoloadDB('Db');
class Groups extends Db
{
	public function SaveGroupsTopic($dataForm)
	{
		global $mySession;
		$db=new Db();
		$chkQry=$db->runQuery("select * from ".GROUP_TPOIC." where topic_name='".$dataForm['topic_name']."'");
		if($chkQry!="" and count($chkQry)>0)
		{
			return 0;	
		}
		else
		{
			$chkQry=$db->runQuery("select max(topic_id) as maxtopic from ".GROUP_TPOIC);
			$maxid=1;

			if(count($chkQry)>0) $maxid=$chkQry[0]['maxtopic']+1;
				
			$dataForm['topic_status']=1;
			$dataForm['topic_id']=$maxid;
			$dataForm['created_by']=$mySession->adminId;
			$topicgroup=$dataForm['topic_group'];
			unset($dataForm['topic_group']);

			if($dataForm['topic_access_type']==1)
				{$groupMember=$dataForm['group_member'];}
			else
				{$groupMember="";}
			unset($dataForm['group_member']);
			 
			
			$db->save(GROUP_TPOIC,$dataForm);
			unset($dataForm);
			if(is_array($topicgroup))
			{
				foreach($topicgroup as $key => $value)
				{
					$dataForm['category_id']=$value;
					$dataForm['topic_id']=$maxid;
					$db->save(GROUP_TPOIC_CATEGORIES,$dataForm);
					unset($dataForm);
				}
			}
			if(is_array($groupMember))
			{
				foreach($groupMember as $key => $value)
				{
					if(isset($dataForm)){unset($dataForm);}
					$dataForm['member_id']=$value;
					$dataForm['topic_id']=$maxid;
					$dataForm['m_status']=1;
					$db->save(GROUP_MEMBER,$dataForm);
					unset($dataForm);
				}
			}
			 
			return 1;	
		}
	}
	public function UpdateGroupTopic($dataForm,$topic_id)	
	{
		global $mySession;
		$db=new Db();
		$sqlstr="select * from ".GROUP_TPOIC." where topic_name='".$dataForm['topic_name']."' and topic_id!='".$topic_id."'";
		 
		$chkQry=$db->runQuery($sqlstr);
		if($chkQry!="" and count($chkQry)>0)
		{
			return 0;	
		}
		else
		{
			$dataForm['topic_status']=1;
			$dataForm['created_by']=$mySession->adminId;
			$update_condition="topic_id='".$topic_id."'";

			$topicgroup=$dataForm['topic_group'];
			unset($dataForm['topic_group']);

			if($dataForm['topic_access_type']==1)
				{$groupMember=$dataForm['group_member'];}
			else
				{$groupMember="";}
			unset($dataForm['group_member']);

			$db->modify(GROUP_TPOIC,$dataForm,$update_condition);
			
			$condition="topic_id='".$topic_id."'"; 
			$db->delete(GROUP_TPOIC_CATEGORIES,$condition);

			$data['m_status']=1;
			$db->modify(GROUP_MEMBER,$data,$condition);
			//$db->delete(GROUP_MEMBER,$condition);
			
			unset($dataForm);
			if(is_array($topicgroup))
			{
				foreach($topicgroup as $key => $value)
				{
					$dataForm['category_id']=$value;
					$dataForm['topic_id']=$topic_id;
					$db->save(GROUP_TPOIC_CATEGORIES,$dataForm);
					unset($dataForm);
				}
			}
			if(is_array($groupMember))
			{
				foreach($groupMember as $key => $value)
				{
					if(isset($dataForm)){unset($dataForm);}
					$dataForm['m_status']=1;
					$sqlstr="select * from ".GROUP_MEMBER." where member_id='".$value."' and topic_id='".$topic_id."'";
					if($chkQry!="" and count($chkQry)>0)
						{
							$update_condition="member_id='".$value."' and topic_id='".$topic_id."'";
							$db->modify(GROUP_MEMBER,$dataForm,$update_condition);
						}
					else
						{
							$dataForm['member_id']=$value;
							$dataForm['topic_id']=$topic_id;
							$db->save(GROUP_MEMBER,$dataForm);
						}
					unset($dataForm);
				}
			}
			return 1;	
		}
	}
	
	public function SaveGroupsCategory($dataForm)
	{
		global $mySession;
		$db=new Db();
		$chkQry=$db->runQuery("select * from ".GROUP_CATEGORIES." where group_name='".$dataForm['group_name']."'");
		if($chkQry!="" and count($chkQry)>0)
		{
			return 0;	
		}
		else
		{
			
			$dataInsert['group_name']=$dataForm['group_name'];
			$dataInsert['group_desc']=$dataForm['group_desc'];
			$dataInsert['group_status']=1;
			$db->save(GROUP_CATEGORIES,$dataInsert);
			return 1;	
		}
	}
	public function UpdateGroupCategory($dataForm,$group_id)	
	{
		global $mySession;
		$db=new Db();
		$chkQry=$db->runQuery("select * from ".GROUP_CATEGORIES." where group_name='".$dataForm['group_name']."' and group_id!='".$group_id."'");
		if($chkQry!="" and count($chkQry)>0)
		{
			return 0;	
		}
		else
		{
			$dataUpdate['group_name']=$dataForm['group_name'];
			$dataUpdate['group_desc']=$dataForm['group_desc'];
			$update_condition="group_id='".$group_id."'";
			$db->modify(GROUP_CATEGORIES,$dataUpdate,$update_condition);
			return 1;	
		}
	}
}
?>