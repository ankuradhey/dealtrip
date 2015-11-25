<?php
//require_once("/models/DbTable/Table.php");
class Db 
{
	protected $db;
	
	function __construct()
	{
		$this->db = Zend_Registry::get('dbAdapter');
		return $this->db;
	}
	
	function runQuery($qry)
	{
		$result = $this->db->fetchAll($qry);
		return $result;
	}
	
	function firequery($qry){
	 $db = Zend_Registry::get('dbAdapter');

	 return $db->query($qry);
 
	
	}
	//send $orderby as array['field'] ='DESC /ASC';
	function showAll($tbl,$columns='*', $where=NULL, $whereNot=NULL, $orderby=NULL, $tblJoin=NULL,$joinCondition=NULL,$group=NULL,$having=NULL,$distinct=NULL,$offset=NULL,$limit=NULL)
	{
		
		if(is_array($distinct))
		{
				$select = $this->db->select()
				             ->distinct()
							->from($tbl); 
		}
		else 
		{
				$select = $this->db->select()
							->from($tbl);

				if($columns != '*')			
				{
					$select -> columns($columns);
				}
				if($tblJoin != NULL  && $joinCondition != NULL)
				{
					for($i=0; $i<count($tblJoin); $i++)
					{
						$t = $tblJoin[$i];
						$jc = $joinCondition[$i];
						$select -> join($t,$jc);
					}
				}
				if(is_array($where))
				{
					foreach($where as $key => $value)
					{
						$select -> where($key.' = ?', $value);
					}
				}
				if(is_array($whereNot))
				{
					foreach($whereNot as $key => $value)
					{
						$select -> where($key.' <> ?', $value);
					}
				}
				
				if($group != NULL)
				{
						$select ->group($group);
				}
				if($having != NULL)
				{
						$select ->having($having);
				}
				
				if($orderby != NULL)
				{	
					$key = array_keys($orderby);
					$value = array_values($orderby);
					
						$select -> order("$key[0] $value[0]");
				}		
				
				if($limit != NULL)
				{	
						$select ->limit($limit,$offset);
				}		
			
				/*if($orderby != NULL)
				{	
					$key = array_keys($orderby);
					$value = array_values($orderby);
					
						$select -> order("$key[0] $value[0]");
				}	*/						
		}
		//echo $sql = $select->__toString(); echo "<br />"; // die;
		
		$result = $this->db->fetchAll($select);
		
		if(empty($result))
		{
			return false; 
		}else
		{
			return $result;
		}
	}
	
	
	//New show all with OR in where clause.
	//send $orderby as array['field'] ='DESC /ASC';
	function newShowAll($tbl,$columns='*', $where=NULL, $orderby=NULL, $tblJoin=NULL, $joinCondition=NULL, $group=NULL,$having=NULL,$distinct=NULL,$offset=NULL,$limit=NULL)
	{

		if(is_array($distinct))
		{
				$select = $this->db->distinct()
							->from($tbl);
		}
		else 
		{
					$select = $this->db->select()->from( $tbl, $columns );				
				//echo $sql = $select->__toString();
/*				if($columns != '*')			
				{
					$select -> columns($columns);
				} else {
					$select = $this->db->select()->from( $tbl );				
					$select -> columns($columns);					
				}*/
				
				if($tblJoin != NULL  && $joinCondition != NULL)
				{
					for($i=0; $i<count($tblJoin); $i++)
					{
						$t = $tblJoin[$i];
						$jc = $joinCondition[$i];
						$select -> join($t,$jc,'');
					}
				}
				if(is_array($where))
				{
					foreach($where as $key => $value)
					{
						$select -> where($key.$value);
					}
				}
				
				if($group != NULL)
				{
						$select ->group($group);
				}
				if($having != NULL)
				{
						$select ->having($having);
				}
				
				if($orderby != NULL)
				{	
					$key = array_keys($orderby);
					$value = array_values($orderby);
					
						$select -> order("$key[0] $value[0]");
				}		
				if($offset != NULL)
				{	
						$select -> $offset;
				}	
					
				//echo $sql = $select->__toString();				
		
				/*if($orderby != NULL)
				{	
					$key = array_keys($orderby);
					$value = array_values($orderby);
					
						$select -> order("$key[0] $value[0]");
				}	*/						
		}
									
		
		//echo $sql = $select->__toString(); echo "<br />"; // die;
		
		$result = $this->db->fetchAll($select);
		

		if(empty($result))
		{
			return false; 
		}else
		{
			return $result;
		}
	}
	
	
	function save($tbl,$data)
	{
		try {
			
			//$data = addslashesInputVar($data);
				
			$result = $this->db->insert($tbl,$data);
			if($result)
			{
				return true;
			}
			else
			{	return false;
			}
		}
		catch(Exception $e){
			echo $e;exit();
			echo "Error in insert operation";
			return false;
		}
	}
	
	//function modify($tbl,$data_as_an_array,$condition_as_a_string)
	function modify($tbl,$data,$condition)		
	{
		try 
		{
			$result = $this->db->update($tbl,$data, $condition);
			if($result)	{
				return true;
			} else {
				return false;
			}
		}	
		catch(Exception $e){
			echo "Error in modify operation";
			return false;
		}
	}
	
	function delete($tbl,$condition)
	{
		try {
			$result = $this->db->delete($tbl,$condition);
			if($result)
			{
				return true;
			}
			else
			{	return false;
			}
		}
		catch(Exception $e){
			echo "Error in delete operation";
			return false;
		}
	}
	
	function GetData($tab, $field, $id)
	{
		$GetDataQry = "SELECT * FROM ".$tab." WHERE ".$field." = '".$id."'";
		$GetDataQryres = mysql_query(GetDataQry);
		$num = mysql_num_rows($GetDataQryres);
		if($num > 0)
		{
			while($refetch = @mysql_fetch_assoc($GetDataQryres))
			{
				$result[] = $refetch;
			}
			return $result;
		}
	}
	
	function lastInsertId()
	{
		$id = $this->db->lastInsertId();
		return $id;
	}
	
}
?>