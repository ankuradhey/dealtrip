/*!
 * Vallenato 1.0
 * A Simple JQuery Accordion
 *
 * Designed by Switchroyale
 * 
 * Use Vallenato for whatever you want, enjoy!
 */

$(document).ready(function()
{
	//Add Inactive Class To All Accordion Headers
	$('.accordion-header').toggleClass('inactive-header');
	
	//Set The Accordion Content Width
	var contentwidth = $('.accordion-header').width();
	//	$('.accordion-content').css({'width' : contentwidth });
	
	//Open The First Accordion Section When Page Loads
	$('.accordion-header').toggleClass('active-header').toggleClass('inactive-header');
	$('.accordion-content').slideDown().toggleClass('open-contents');

	
	var counter = 0; //id generator variable
	var in_counter = 0; //id plus generator variable (used for referencing inner input elements)
	var flag_check = 0; // for checking if any entry is selected or not
	
	$('.accordion-content').each(function(index, element) {
	        
	

		var val = "";
		in_counter = 0;

		var pptr = this;
		
	if($(this).find("input:checkbox").not("#laterDate").is(":checked"))
        {	

				 
				 $(this).find(':checkbox:checked').filter(function(e){
					 if($(this).val() != "")
					 return true;
					 else
					 return false;
					 
					 }).each(function(i){
         		 	val = $(this).val();
					
					counter++;
					in_counter++;  // increment it to 1 for use (not starting with 0) 
					
					flag_check = 1;
					
					if(index > 0)
					{
						
			
						
						
						var selector = $(this).data('vallenato');
							
					
					}
					/*console.log(selector);*/
					
        			  	$(".chzn-choices").append('<li class="search-choice" id="chosenSearch_chzn_c_'+counter+'"><span>'+selector+'</span><a href="javascript:void(0)" class="search-choice-close" id = "close_'+index+'_'+in_counter+'" rel="'+counter+'" ></a></li>').children().remove('.search-field').parent();
					
							  
				  });

			
			
		}
		

    });

	
	if($('#textfield').val() != "")
	{
		counter++;
		var selector = " Keyword or Property ref";	
		$(".chzn-choices").append('<li class="search-choice" id="chosenSearch_chzn_c_'+counter+'"><span>'+selector+'</span><a href="javascript:void(0)" class="search-choice-close" id = "close_keyword" rel="'+counter+'" ></a></li>').children().remove('.search-field').parent();
		
	}
	
	if(counter > 0)
	$(".chzn-choices").append('<li class="search-choice mws-nav-tooltip mws-tooltip" id="delete_choices" ><span class = "ui-icon ui-icon-trash">&nbsp;</span><a href="javascript:void(0)" class="search-choice-close mws-tooltip" title = "Delete all selections and start again" id = "close_delete" rel="'+counter+'" ></a></li>');
	
	
	var counter = 1;
	
	$(".search-choice-close").each(function(index, element) {
        
		$(this).click(function(e){
			
			
			if($(this).attr("id") == "close_delete")
			{
				$("#accordion-container").find("input:checkbox:checked").not($("#Bedroom").find("input:checkbox").first()).not($("#Bathroom").find("input:checkbox").first()).removeAttr("checked");	
				$("#textfield").val("");
				$("#hotelsearch").submit();
			}
			else if($(this).attr("id") == "close_keyword")
			{
				$("#textfield").val("");
				$("#hotelsearch").submit();
			}
			else
			{
				var ch = $(this).attr("id").split("_");
				$(".accordion-content").filter(":eq("+parseInt(ch[1])+")").find(":checkbox:checked").eq(parseInt(ch[2])-1).removeAttr("checked");
				$("#hotelsearch").submit();
			}
			
			});
		
		
		counter++;
		
    });
	
	$("#delete_choices").click(function(e) {
    		$("#accordion-container").find("input:checkbox:checked").not($("#Bedroom").find("input:checkbox").first()).not($("#Bathroom").find("input:checkbox").first()).removeAttr("checked");	
			$("#textfield").val("");
			$("#hotelsearch").submit();
	});
	
	
	
	
	// The Accordion Effect
	$('.accordion-header').click(function () {
		

		
		if($(this).is('.inactive-header')) 
		{
			//$('.active-header').toggleClass('active-header').toggleClass('inactive-header').next().slideToggle().toggleClass('open-contents');
			$(this).toggleClass('active-header').toggleClass('inactive-header');
			$(this).next().slideToggle().toggleClass('open-contents');
			
			console.log($(this).next().attr("class"));	
		
			
			/*if($(this).index() == '0' || $(this).next().find("input").attr("id") == 'textfield')
			{
				
				console.log( $(this).next().find('#textfield'));
				$(this).next().css('display','inline-block');	

				
			}
			else
			{
				$(this).next().css('display','list-item');
				$(this).next().css('list-style-type','none');
				
			}
			*/
			//$(this).next().css('display','inline-block');

		}
		else {
			$(this).toggleClass('active-header').toggleClass('inactive-header');
			$(this).next().slideToggle().toggleClass('open-contents');
					console.log($(this).next().attr("class"));		
		}
	});
	
	return false;
});