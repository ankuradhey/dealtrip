<?php
error_reporting(0);
global $mySession;
//$mySession->LoggedUserId
	$q=$_GET['q'];
	$selfuserid=$_GET['mySession'];
	$my_data=mysql_real_escape_string($q);
	

	$mysqli=mysqli_connect('localhost','root','','toddlez') or die("Database Error");
 	$sql="SELECT * FROM users 
	inner join state on users.state_id=state.state_id
	WHERE first_name LIKE '%$my_data%'  and  user_id!=".$selfuserid." and user_type!='3' ORDER BY first_name"; 
	$result = mysqli_query($mysqli,$sql) or die(mysqli_error());	
	if($result)
	{
		while($row=mysqli_fetch_array($result))
		{
			$type="Babysitter";	
			if($row['user_type']=='1')
			{
				$type="User";	
			}
			echo $row['first_name'].' '.$row['last_name']." From ".$row['city_id'].' '.$row['state_name'].' '.$row['zipcode']." (".$type.")\n ";
			
		}
	}
	
/*
error_reporting(0);
global $mySession;
//$mySession->LoggedUserId
	$q=$_GET['q'];
	$selfuserid=$_GET['mySession'];
	$my_data=mysql_real_escape_string($q);
	

	$mysqli=mysqli_connect('localhost','tbs11co_toddlez','g.g%e5#iX^NA','tbs11co_toddlez') or die("Database Error");
 	$sql="SELECT * FROM users 
	inner join state on users.state_id=state.state_id
	WHERE first_name LIKE '%$my_data%'  and  user_id!=".$selfuserid." and user_type!='3' ORDER BY first_name"; 
	$result = mysqli_query($mysqli,$sql) or die(mysqli_error());	
	if($result)
	{
		while($row=mysqli_fetch_array($result))
		{
			$type="Babysitter";	
			if($row['user_type']=='1')
			{
				$type="User";	
			}
			echo $row['first_name'].' '.$row['last_name']." From ".$row['city_id'].' '.$row['state_name'].' '.$row['zipcode']." ".$row['email_address']." (".$type.")\n ";
		}
	}

*/
?>