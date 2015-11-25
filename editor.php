﻿<link rel="Stylesheet" type="text/css" href="<?=APPLICATION_URL?>tinymce/jscripts/tiny_mce/resources/ext4/resources/css/ext-all.css" />
<script type="text/javascript" src="<?=APPLICATION_URL?>tinymce/jscripts/tiny_mce/resources/ext4/ext-all.js"></script>
<script type="text/javascript" src="<?=APPLICATION_URL?>tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="<?=APPLICATION_URL?>tinymce/jscripts/tiny_mce/resources/js/Ext-extensions.js"></script>
<script type="text/javascript">
  function myCustomOnChangeHandler(inst) {
  				$('#<?=$strfld_name;?>').html(inst.getBody().innerHTML);
            }
	tinyMCE.init({
		// General options
		mode : "exact",
		elements : "<?=$strfld_name;?>",
		theme : "advanced",
		skin : "o2k7",
		skin_variant : "silver",
		
		
		plugins : "lists,pagebreak,style,layer,table,save,advhr,advlink,advimage,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups,autosave,imgmanager",
		// Theme options
		//formatselect
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,fontselect,fontsizeselect,|,visualchars,nonbreaking,template,pagebreak,restoredraft,|,cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist",
		theme_advanced_buttons2 : "outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,cleanup,code,|,insertdate,inserttime,preview,|,forecolor,backcolor,|,insertlayer,moveforward,movebackward,absolute,styleprops,|,ltr,rtl,|,cite,abbr,acronym,del,ins,attribs,|,imgmanager,|,charmap,emotions,iespell,advhr",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup",
		
		relative_urls : false, 
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true	,
		  onchange_callback : "myCustomOnChangeHandler",
		  
		template_external_list_url: "resources/templates/html/list.js",
		external_link_list_url: "resources/templates/links/list.js",
		external_image_list_url: "resources/templates/images/list.js",
		media_external_list_url: "resources/templates/media/list.js",
		/*extjsfilemanager_handlerurl: '<?=APPLICATION_URL?>BrowserHandler.php',
		extjsfilemanager_extraparams: { param1: 'value1', param2: 'value2' },*/
		 // Style formats
		style_formats: [
				{ title: 'Bold text', inline: 'b' },
				{ title: 'Red text', inline: 'span', styles: { color: '#ff0000'} },
				{ title: 'Red header', block: 'h1', styles: { color: '#ff0000'} },
				{ title: 'Example 1', inline: 'span', classes: 'example1' },
				{ title: 'Example 2', inline: 'span', classes: 'example2' },
				{ title: 'Table styles' },
				{ title: 'Table row 1', selector: 'tr', classes: 'tablerow1' }
			],

		formats: {
			/*alignleft: { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'left' },
			aligncenter: { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'center' },
			alignright: { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'right' },
			alignfull: { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'full' },
			bold: { inline: 'span', 'classes': 'bold' },
			italic: { inline: 'span', 'classes': 'italic' },
			underline: { inline: 'span', 'classes': 'underline', exact: true },
			strikethrough: { inline: 'del' }*/
		},
		autosave_ask_before_unload : false // Disable for example purposes
	});

	

	 

</script>