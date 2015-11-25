<?
	$current_year  = date('Y');
	define("CURRENT_YEAR",$current_year);
	$current_mnth = $mnth_display = date('m');
	date('F',mktime(0,0,0,$current_mnth+2,0,0));
	
	date('w',mktime(0, 0, 0, date('m'),date('d'),date('Y')));
	
	$week = array('M','T','W','T','F','S','S');
	
	
	//echo date('t',mktime(0,0,0,($mnth_display+2)%12,0,0));
	//echo date('m')+8;
	echo date('w',mktime(0, 0, 0, date('m')+12,date('d'),date('Y')+1));
//echo ($mnth_display+1)%12;
?>

<div > <!-- main wrapper div container -->
	
    <div class = 'calendar_box'><!-- first calendar box-->
		<div class = 'calendar_head'>
        <label><?=date('F Y')?></label>
        </div>	
<?
        for($w=0;$w<7;$w++)
		{
?>	
        <div class = 'date_block' align="center"><!--smaller blocks of week-->
       <label><?= $week[$w]?></label>
        </div>
<?
		}
		
			for($i=1,$k=1;$i<=42;$i++)
			{
				
		?>			<div class = 'date_block' align="center"><!--smaller blocks of dates--> 	
        				<?	
						if($i < date('w',mktime(0, 0, 0, date('m'),date('d'),$current_year)) || $k > date('t'))
						echo "&nbsp";
						else
						{	echo $k;
							$k++;
						}
						?>
                        
					</div>			
		<?	

			}
		?>
    
    </div><!-- first calendar box ends-->
    <!-- CHECKING IF THE DISPLAYED MONTH WAS THE LAST MONTH OR NOT -->
   <?

	if($current_mnth > 12)
	{	$current_mnth = 1;
		$current_year = date('Y')+1;
	}
	elseif($current_mnth == 12)
	{
		$current_year = date('Y')+1;
		$current_mnth++;
	}
	else
	$current_mnth++;
	?>
   
    
    <div class = 'calendar_box'><!-- 2 calendar box-->
    	<div class = 'calendar_head'>
        <label><?=date('F',mktime(0,0,0,$current_mnth,1,$current_year))." ".$current_year?></label>
        </div>	

<?
        for($w=0;$w<7;$w++)
		{
?>	
        <div class = 'date_block' align="center"><!--smaller blocks of week-->
        <label><?= $week[$w]?></label>
        </div>
<?
		}		
			for($i=1,$k=1;$i<=42;$i++)
			{
		?>			<div class = 'date_block'><!--smaller blocks of dates--> 	
        				<?
                        if($i < date('w',mktime(0, 0, 0, $current_mnth,date('d'),$current_year)) || $k >  date('t',mktime(0,0,0,$current_mnth,0,$current_year)))
						echo "&nbsp";
						else
						{	echo $k;
							$k++;
						}
						?>
					</div>			
		<?
			}
		?>
    
    
    </div><!-- 2 calendar box ends-->
   
       <!-- CHECKING IF THE DISPLAYED MONTH WAS THE LAST MONTH OR NOT -->
    <?

	if($current_mnth > 12)
	{	$current_mnth = 1;
		//$current_year = date('Y')+1;
	}
	elseif($current_mnth == 12)
	{
		$current_year = date('Y')+1;
		$current_mnth++;
	}
	else
	$current_mnth++;
	?>
   
    
    
    <div class = 'calendar_box'><!-- 3 calendar box-->
    	<div class = 'calendar_head'>
        <label><?= date('F',mktime(0,0,0,$current_mnth,0,$current_year)); ?></label>
        </div>	

<?
        for($w=0;$w<7;$w++)
		{
?>	
        <div class = 'date_block' align="center"><!--smaller blocks of week-->
       <label><?= $week[$w]?></label>
        </div>
<?
		}
			
			for($i=1,$k=1;$i<=42;$i++)
			{
		?>			<div class = 'date_block'><!--smaller blocks of dates--> 	
        			<?
						 if($i < date('w',mktime(0, 0, 0, $current_mnth,date('d'),$current_year)) || $k >  date('t',mktime(0,0,0,$current_mnth,0,$current_year)))
						echo "&nbsp";
						else
						{	echo $k;
							$k++;
						}
					?>
					</div>			
		<?
			}
		?>
    
    
    </div><!-- 3 calendar box ends-->
    
        <!-- CHECKING IF THE DISPLAYED MONTH WAS THE LAST MONTH OR NOT -->
  <?

	if($current_mnth > 12)
	{	$current_mnth = 1;
		//$current_year = date('Y')+1;
	}
	elseif($current_mnth == 12)
	{
		$current_year = date('Y')+1;
		$current_mnth++;
	}
	else
	$current_mnth++;
	?>
    
    
    <div class = 'calendar_box'><!-- 4 calendar box-->
    	<div class = 'calendar_head'>
        <label><?= date('F',mktime(0,0,0,$current_mnth,0,$current_year)); ?></label>
        </div>	

<?
        for($w=0;$w<7;$w++)
		{
?>	
        <div class = 'date_block' align="center"><!--smaller blocks of week-->
      <label> <?= $week[$w]?></label>
        </div>
<?
		}
		
			for($i=1,$k=1;$i<=42;$i++)
			{
		?>			<div class = 'date_block'><!--smaller blocks of dates--> 	
        			<?	
						if($i < date('w',mktime(0, 0, 0,$current_mnth,date('d'),$current_year)) || $k >  date('t',mktime(0,0,0,$current_mnth,0,$current_year)))
						echo "&nbsp";
						else
						{	echo $k;
							$k++;
						}
				     ?>
					</div>			
		<?
			}
		?>
    
    
    </div><!-- 4 calendar box ends-->
    
       <!-- CHECKING IF THE DISPLAYED MONTH WAS THE LAST MONTH OR NOT -->
   <?

	if($current_mnth > 12)
	{	$current_mnth = 1;
		//$current_year = date('Y')+1;
	}
	elseif($current_mnth == 12)
	{
		$current_year = date('Y')+1;
		$current_mnth++;
	}
	else
	$current_mnth++;
	?>
   
   
    
    <div class = 'calendar_box'><!-- 5 calendar box-->
    	<div class = 'calendar_head'>
        <label><?= date('F',mktime(0,0,0,$current_mnth,0,$current_year)); ?></label>
        </div>	

<?
        for($w=0;$w<7;$w++)
		{
?>	
        <div class = 'date_block' align="center"><!--smaller blocks of week-->
      <label> <?= $week[$w]?></label>
        </div>
<?
		}
		
			for($i=1,$k=1;$i<=42;$i++)
			{
		?>			<div class = 'date_block'><!--smaller blocks of dates--> 	
        			<?
						if($i < date('w',mktime(0, 0, 0, $current_mnth,date('d'),$current_year)) || $k >  date('t',mktime(0,0,0,$current_mnth,0,$current_year)))
						echo "&nbsp";
						else
						{	echo $k;
							$k++;
						}
					?>
					</div>			
		<?
			}
		?>
    
    
    </div><!-- 5 calendar box ends-->
    
    <!-- CHECKING IF THE DISPLAYED MONTH WAS THE LAST MONTH OR NOT -->
   <?

	if($current_mnth > 12)
	{	$current_mnth = 1;
		//$current_year = date('Y')+1;
	}
	elseif($current_mnth == 12)
	{
		$current_year = date('Y')+1;
		$current_mnth++;
	}
	else
	$current_mnth++;
	?>
    
    
    <div class = 'calendar_box'><!-- 6 calendar box-->
    	<div class = 'calendar_head'>
        <label><?= date('F',mktime(0,0,0,$current_mnth,0,$current_year)); ?></label>
        </div>	

<?
        for($w=0;$w<7;$w++)
		{
?>	
        <div class = 'date_block' align="center"><!--smaller blocks of week-->
       <label><?= $week[$w]?></label>
        </div>
<?
		}
			for($i=1,$k=1;$i<=42;$i++)
			{
		?>			<div class = 'date_block'><!--smaller blocks of dates--> 	
        				<?
						if($i < date('w',mktime(0, 0, 0, $current_mnth,date('d'),$current_year)) || $k >  date('t',mktime(0,0,0,$current_mnth,0,$current_year)))
						echo "&nbsp";
						else
						{	echo $k;
							$k++;
						}
					?>
					</div>			
		<?
			}
		?>
    
    	
    </div><!-- 6 calendar box ends-->
    
    
        <!-- CHECKING IF THE DISPLAYED MONTH WAS THE LAST MONTH OR NOT -->
   <?

	if($current_mnth > 12)
	{	$current_mnth = 1;
		//$current_year = date('Y')+1;
	}
	elseif($current_mnth == 12)
	{
		$current_year = date('Y')+1;
		$current_mnth++;
	}
	else
	$current_mnth++;
	?>
    
    <div class = 'calendar_box'><!-- 7 calendar box-->
    	<div class = 'calendar_head'>
        <label><?= date('F',mktime(0,0,0,$current_mnth,0,$current_year)); ?></label>
        </div>	

<?
        for($w=0;$w<7;$w++)
		{
?>	
        <div class = 'date_block' align="center"><!--smaller blocks of week-->
       <label><?= $week[$w]?></label>
        </div>
<?
		}
			for($i=1,$k=1;$i<=42;$i++)
			{
		?>			<div class = 'date_block'><!--smaller blocks of dates--> 	
        			<?
						if($i < date('w',mktime(0, 0, 0,$current_mnth,date('d'),$current_year)) || $k >  date('t',mktime(0,0,0,$current_mnth,0,$current_year)))
						echo "&nbsp";
						else
						{	echo $k;
							$k++;
						}
					?>
					</div>			
		<?
			}
		?>
    
    
    </div><!-- 7 calendar box ends-->
    
    
        <!-- CHECKING IF THE DISPLAYED MONTH WAS THE LAST MONTH OR NOT -->
    <?

	if($current_mnth > 12)
	{	$current_mnth = 1;
		//$current_year = date('Y')+1;
	}
	elseif($current_mnth == 12)
	{
		$current_year = date('Y')+1;
		$current_mnth++;
	}
	else
	$current_mnth++;
	?>
        
    <div class = 'calendar_box'><!-- 8 calendar box-->
    	<div class = 'calendar_head'>
        <label><?= date('F',mktime(0,0,0,$current_mnth,0,$current_year)); ?></label>
        </div>	
<?
        for($w=0;$w<7;$w++)
		{
?>	
        <div class = 'date_block' align="center"><!--smaller blocks of week-->
      <label> <?= $week[$w]?></label>
        </div>
<?
		}

			for($i=1,$k=1;$i<=42;$i++)
			{
		?>			<div class = 'date_block'><!--smaller blocks of dates--> 	
        			<?
						if($i < date('w',mktime(0, 0, 0,$current_mnth,date('d'),$current_year)) || $k >  date('t',mktime(0,0,0,$current_mnth,0,$current_year)))
						echo "&nbsp";
						else
						{	echo $k;
							$k++;
						}
					?>
					</div>			
		<?
			}
		?>
    
    
    </div><!-- 8 calendar box ends-->
    
        <!-- CHECKING IF THE DISPLAYED MONTH WAS THE LAST MONTH OR NOT -->
 <?

	if($current_mnth > 12)
	{	$current_mnth = 1;
		//$current_year = date('Y')+1;
	}
	elseif($current_mnth == 12)
	{
		$current_year = date('Y')+1;
		$current_mnth++;
	}
	else
	$current_mnth++;
	?>   
    
    <div class = 'calendar_box'><!-- 9 calendar box-->
    	<div class = 'calendar_head'>
        <label><?= date('F',mktime(0,0,0,$current_mnth,0,$current_year)); ?></label>
        </div>	

<?
        for($w=0;$w<7;$w++)
		{
?>	
        <div class = 'date_block' align="center"><!--smaller blocks of week-->
       <label><?= $week[$w]?></label>
        </div>
<?
		}
			for($i=1,$k=1;$i<=42;$i++)
			{
		?>			<div class = 'date_block'><!--smaller blocks of dates--> 	
        			<?
						if($i < date('w',mktime(0, 0, 0, $current_mnth,date('d'),$current_year)) || $k >  date('t',mktime(0,0,0,$current_mnth,0,$current_year)))
						echo "&nbsp";
						else
						{	echo $k;
							$k++;
						}
					?>
					</div>			
		<?
			}
		?>
    
    
    </div><!-- 9 calendar box ends-->
    
        <!-- CHECKING IF THE DISPLAYED MONTH WAS THE LAST MONTH OR NOT -->
  <?

	if($current_mnth > 12)
	{	$current_mnth = 1;
		//$current_year = date('Y')+1;
	}
	elseif($current_mnth == 12)
	{
		$current_year = date('Y')+1;
		$current_mnth++;
	}
	else
	$current_mnth++;
	?>
    
    
    <div class = 'calendar_box'><!-- 10 calendar box-->
    	<div class = 'calendar_head'>
        <label><?= date('F',mktime(0,0,0,$current_mnth,0,$current_year)); ?></label>
        </div>	

<?
        for($w=0;$w<7;$w++)
		{
?>	
        <div class = 'date_block' align="center"><!--smaller blocks of week-->
       <label><?= $week[$w]?></label>
        </div>
<?
		}
			for($i=1,$k=1;$i<=42;$i++)
			{
		?>			<div class = 'date_block'><!--smaller blocks of dates--> 	
        			<?
						if($i < date('w',mktime(0, 0, 0, $current_mnth,date('d'),$current_year)) || $k >  date('t',mktime(0,0,0,$current_mnth,0,$current_year)))
						echo "&nbsp";
						else
						{	echo $k;
							$k++;
						}
					?>
					</div>			
		<?
			}
		?>
    
    
    </div><!-- 10 calendar box ends-->
    
        <!-- CHECKING IF THE DISPLAYED MONTH WAS THE LAST MONTH OR NOT -->
   <?

	if($current_mnth > 12)
	{	$current_mnth = 1;
		//$current_year = date('Y')+1;
	}
	elseif($current_mnth == 12)
	{
		$current_year = date('Y')+1;
		$current_mnth++;
	}
	else
	$current_mnth++;
	?>
    
    
    <div class = 'calendar_box'><!-- 11 calendar box-->
    	<div class = 'calendar_head'>
        <label><?= date('F',mktime(0,0,0,$current_mnth,0,$current_year)); ?></label>
        </div>	

<?
        for($w=0;$w<7;$w++)
		{
?>	
        <div class = 'date_block' align="center"><!--smaller blocks of week-->
      <label> <?= $week[$w]?></label>
        </div>
<?
		}
			for($i=1,$k=1;$i<=42;$i++)
			{
		?>			<div class = 'date_block'><!--smaller blocks of dates--> 	
        			<?
						if($i < date('w',mktime(0, 0, 0,$current_mnth,date('d'),$current_year)) || $k >  date('t',mktime(0,0,0,$current_mnth,0,$current_year)))
						echo "&nbsp";
						else
						{	echo $k;
							$k++;
						}
					?>
					</div>			
		<?
			}
		?>
    
    
    </div><!-- 11 calendar box ends-->
    
       <!-- CHECKING IF THE DISPLAYED MONTH WAS THE LAST MONTH OR NOT -->
   <?

	if($current_mnth > 12)
	{	$current_mnth = 1;
		//$current_year = date('Y')+1;
	}
	elseif($current_mnth == 12)
	{
		$current_year = date('Y')+1;
		$current_mnth++;
	}
	else
	$current_mnth++;
	?>
    
    <div class = 'calendar_box'><!-- 12 calendar box-->
    	<div class = 'calendar_head'>
        <label><?= date('F',mktime(0,0,0,$current_mnth,0,0)); ?></label>
        </div>	

<?
        for($w=0;$w<7;$w++)
		{
?>	
        <div class = 'date_block' align="center"><!--smaller blocks of week-->
       <label><?= $week[$w]?></label>
        </div>
<?
		}
			for($i=1,$k=1;$i<=42;$i++)
			{
		?>			<div class = 'date_block'><!--smaller blocks of dates--> 	
        			<?
						if($i < date('w',mktime(0, 0, 0,$current_mnth,date('d'),$current_year)) || $k >  date('t',mktime(0,0,0,$current_mnth,0,$current_year)))
						echo "&nbsp";
						else
						{	echo $k;
							$k++;
						}
					
					?>
					</div>			
		<?
			}
		?>
    
    
    </div><!-- 12 calendar box ends-->


</div>