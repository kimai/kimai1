<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>KIMAI Auswertung</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="css/screen.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="css/print.css" media="print" />
		<link rel="stylesheet" type="text/css" href="css/all.css" media="all" />
		
		<script src="../../libraries/jQuery/jquery-1.3.2.min.js" type="text/javascript" charset="utf-8"></script>
		
		
		{$xajax_js}
		{literal}
			<script type="text/javascript">
				<!-- 
					function openBrWindow(theURL,winName,b,h,features) 
					{ 			
						var eigenschaft,fenster02;
						x = (screen.width-b)/2;
						y = (screen.height-h)/2;
						var eigenschaften="left="+x+",top="+y+",screenX="+x+",screenY="+y;
						
						eigenschaften= eigenschaften + ",width="+b+",height="+h+","+features;		
						fenster02=window.open(theURL,winName,eigenschaften);			
					}
					
					function switchView(obj_id)
					{                                                                                                      
						if(document.getElementById(obj_id).style.display == '')
						{
							document.getElementById(obj_id).style.display = 'none';     
						} else {
							document.getElementById(obj_id).style.display = '';
						}		                                                                                                                          
					}
					function form_changeProject() {
						xajax_selectProjects(xajax.getFormValues('selectform'));
					}
					
					function form_showJobs() {
						xajax_showJobs(xajax.getFormValues('selectform'));
					}
					
					function form_showEvents() {
						xajax_selectEvents(xajax.getFormValues('selectform'));
					}
					
					function form_showUser() {
						xajax_selectUser(xajax.getFormValues('selectform'));
					}
						
					function form_exportExcell() {
						xajax_exportExcell(xajax.getFormValues('selectform'));
					}
								
				//-->
			</script>
		{/literal}
	</head>
	<body>
		 {*$kga*}