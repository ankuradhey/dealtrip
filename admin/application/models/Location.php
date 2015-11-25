<?php
__autoloadDB('Db');
class Location extends Db
{
	public function SaveCountry($dataForm)
	{
		global $mySession;
		$db=new Db();
		$chkQry=$db->runQuery("select * from ".COUNTRIES." where country_name='".$dataForm['country_name']."'");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;	
		}
		else
		{
		$dataInsert['country_name']=$dataForm['country_name'];
		$db->save(COUNTRIES,$dataInsert);
		return 1;	
		}
	}
	public function UpdateCountry($dataForm,$countryId)
	{
		global $mySession;
		$db=new Db();
		$chkQry=$db->runQuery("select * from ".COUNTRIES." where country_name='".$dataForm['country_name']."' and country_id!='".$countryId."'");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;	
		}
		else
		{
		$dataUpdate['country_name']=$dataForm['country_name'];
		$conditionUpdate="country_id='".$countryId."'";
		$db->modify(COUNTRIES,$dataUpdate,$conditionUpdate);
		return 1;	
		}
	}
	public function SaveState($dataForm)
	{
		global $mySession;
		$db=new Db();
		$chkQry=$db->runQuery("select * from ".STATE." where state_name='".$dataForm['state_name']."' and country_id = '".$dataForm['country_id']."' ");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;	
		}
		else
		{
		$dataInsert['country_id']=$dataForm['country_id'];
		$dataInsert['state_name']=$dataForm['state_name'];
		$db->save(STATE,$dataInsert);
		return 1;	
		}
	}
	public function UpdateState($dataForm,$stateId)
	{
		global $mySession;
		$db=new Db();
		$chkQry=$db->runQuery("select * from ".STATE." where state_name='".$dataForm['state_name']."' and state_id!='".$stateId."' and country_id = '".$dataForm['country_id']."' ");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;	
		}
		else
		{
		$dataUpdate['country_id']=$dataForm['country_id'];
		$dataUpdate['state_name']=$dataForm['state_name'];
		$conditionUpdate="state_id='".$stateId."'";
		$db->modify(STATE,$dataUpdate,$conditionUpdate);
		return 1;	
		}
	}
	public function SaveCity($dataForm)
	{
		global $mySession;
		$db=new Db();
		$chkQry=$db->runQuery("select * from ".CITIES." where city_name='".$dataForm['city_name']."' and state_id='".$dataForm['state_id']."' ");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;	
		}
		else
		{
		$dataInsert['country_id']=$dataForm['country_id'];
		$dataInsert['state_id']=$dataForm['state_id'];
		$dataInsert['city_name']=$dataForm['city_name'];
		$db->save(CITIES,$dataInsert);
		return 1;	
		}
	}
	public function UpdateCity($dataForm,$cityId)
	{
		global $mySession;
		$db=new Db();
		$chkQry=$db->runQuery("select * from ".CITIES." where city_name='".$dataForm['city_name']."' and city_id!='".$cityId."' and state_id='".$dataForm['state_id']."' ");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;	
		}
		else
		{
		$dataUpdate['country_id']=$dataForm['country_id'];
		$dataUpdate['state_id']=$dataForm['state_id'];
		$dataUpdate['city_name']=$dataForm['city_name'];
		$conditionUpdate="city_id='".$cityId."'";
		$db->modify(CITIES,$dataUpdate,$conditionUpdate);
		return 1;	
		}
	}
	
	public function SaveSubarea($dataForm)
	{
		global $mySession;
		$db=new Db();
		$chkQry = $db->runQuery("select * from ".SUB_AREA." where sub_area_name = '".trim($dataForm['sub_area_name'])."' and city_id = '".$dataForm['city_name']."' ");
		if($chkQry!="" and count($chkQry)>0)
		{
			return 0;	
		}
		else
		{

			//$dataInsert['country_id']=$dataForm['country_id'];
			//$dataInsert['state_id']=$dataForm['state_id'];
			$dataInsert['city_id'] = $dataForm['city_name'];
			$dataInsert['sub_area_name'] = $dataForm['sub_area_name'];
			$db->save(SUB_AREA,$dataInsert);
			return 1;	
		}
	}
	public function UpdateSubarea($dataForm,$sId)
	{
		global $mySession;
		$db=new Db();
		$chkQry = $db->runQuery("select * from ".SUB_AREA." where sub_area_name='".trim($dataForm['sub_area_name'])."' and sub_area_id != '".$sId."' and city_id = '".$dataForm['city_name']."' ");
		if($chkQry!="" and count($chkQry)>0)
		{
			return 0;	
		}
		else
		{
			//$dataUpdate['country_id']=$dataForm['country_id'];
			//$dataUpdate['state_id']=$dataForm['state_id'];
			$dataUpdate['city_id'] = $dataForm['city_name'];
			$dataUpdate['sub_area_name'] = $dataForm['sub_area_name'];
			$conditionUpdate="sub_area_id='".$sId."'";
			$db->modify(SUB_AREA,$dataUpdate,$conditionUpdate);
			return 1;	
		}
	}
	public function SaveLocalarea($dataForm)
	{
		global $mySession;
		$db=new Db();
		$chkQry = $db->runQuery("select * from ".LOCAL_AREA." where local_area_name = '".trim($dataForm['local_area_name'])."' and sub_area_id = '".$dataForm['sub_area_name']."' ");
		if($chkQry!="" and count($chkQry)>0)
		{
			return 0;	
		}
		else
		{
			$dataInsert['local_area_name'] = $dataForm['local_area_name'];
			$dataInsert['sub_area_id'] = $dataForm['sub_area_name'];
			$db->save(LOCAL_AREA,$dataInsert);
			return 1;	
		}
	}
	public function UpdateLocalarea($dataForm,$lId)
	{
		global $mySession;
		$db=new Db();
		$chkQry = $db->runQuery("select * from ".LOCAL_AREA." where local_area_name='".trim($dataForm['local_area_name'])."' and local_area_id != '".$lId."' and sub_area_id = '".$dataForm['sub_area_name']."' ");
		if($chkQry!="" and count($chkQry)>0)
		{
			return 0;	
		}
		else
		{
			$dataUpdate['sub_area_id'] = $dataForm['sub_area_name'];
			$dataUpdate['local_area_name'] = $dataForm['local_area_name'];
			$conditionUpdate="local_area_id='".$lId."'";
			$db->modify(LOCAL_AREA,$dataUpdate,$conditionUpdate);
			return 1;	
		}
	}
}
?>