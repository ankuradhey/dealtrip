<?php
__autoloadDB('Db');
class Subadmin extends Db
{
  
  public function SaveSubadmin($dataForm)
	{
		global $mySession;
		$db=new Db();
		$chkQry=$db->runQuery("select * from ".SUBADMIN." where emailID='".$dataForm['emailID']."'");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;	
		}
		else
		{
				$priv=implode(",",$dataForm['priviledges']);
				
				$dataInsert['first_name']=$dataForm['first_name'];
				$dataInsert['last_name']=$dataForm['last_name'];
				$dataInsert['username']=$dataForm['username'];
				$dataInsert['password']=md5($dataForm['password']);	
				$dataInsert['emailID']=$dataForm['emailID'];
				$dataInsert['status']=$dataForm['status'];
				$dataInsert['priviledges']=$priv;
				$db->save(SUBADMIN,$dataInsert);
				return 1;	
		}

	}
	
	public function UpdateSubadmin($dataForm,$subsID)
	{
		global $mySession;
		$db=new Db();
		$chkQry=$db->runQuery("select * from ".SUBADMIN." where emailID='".$dataForm['emailID']."' And subadmin_id!=".$subsID." ");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;	
		}
		else
		{		
				$priv=implode(",",$dataForm['priviledges']);
				
				$dataUpdate['first_name']=$dataForm['first_name'];
				$dataUpdate['last_name']=$dataForm['last_name'];		
				$dataUpdate['username']=$dataForm['username'];
				$dataUpdate['password']=md5($dataForm['password']);				
				$dataUpdate['emailID']=$dataForm['emailID'];
				$dataUpdate['status']=$dataForm['status'];
				$dataUpdate['priviledges']=$priv;	
				$conditionUpdate="subadmin_id='".$subsID."'";
				$db->modify(SUBADMIN,$dataUpdate,$conditionUpdate);;
				return 1;	
		}
	}

   public function Getsubadmin()
  { 
    global $mySession;
	$db=new Db();	
//	echo "Select * from ".SUBADMIN." order by subadmin_id DESC"; exit();
	$subsdata=$db->runQuery("Select * from ".SUBADMIN." order by subadmin_id DESC"); //

	return $subsdata; 
  }

}
