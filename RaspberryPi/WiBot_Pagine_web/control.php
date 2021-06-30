<?php
session_start();
if(!isset($_SESSION['password']))
header("location: index.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="style/style.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" href="/style/jquery-ui.css" /> <!-- File per lo stile della pagina -->
<script src="jquery.min.js"></script><!-- Includo la libreria jQuery-->
<script src="jquery-ui.js"></script><!-- Includo la libreria jQueryUI-->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Control Page</title>
</head>

<body id="body">
<input type="button" id="Controlli" value="Controlli" class="button_control"></input>
<!-- <input type="button" id="spegni" value="Spegni"  class="button_control" style="margin-left:85%"/> -->
<div id="contenitore"><!--Contenitore dello streaming video e comandi direzioni -->
<form action="serial.php" method="post" class="box" id="form"> <!-- Form dei comandi delle direzioni -->
<input type="button" value="A" 	class="up" 	  name="submit" id="a" />
<input type="button" value="Sx" class="arrow" name="submit" id="sx" style="margin-top:145px; margin-left:90px;"/>
<input type="button" value="I" 	class="arrow" name="submit"	id="i"	style="margin-top:145px; margin-left:200px;"/>
<input type="button" value="Dx" class="arrow" name="submit"	id="dx"	style="margin-top:145px; margin-left:310px;" />
</form>
</div><!-- Fine Div Contenitore --> 
<div id="risultato">
</div>
<div id="dialog" title="Controlli">
<div>Rampa di<br>Accelerazione: 
<input type="text" id="accelerazione" style="border:0; color:#ff0000; background-color:#ffffff" disabled></input>
</div><br>
<div id="slider_rampa_acc"></div><br><br>
<div>Rampa di<br>Velocità: 
<input type="text" id="velocità" style="border:0; color:#ff0000; background-color:#ffffff" disabled></input></div><br>
<div id="slider_rampa_vel"></div><br>
Allerta Ostacoli<br>
<div style="margin-top:30px;margin-left:190px">
<input type="button" id="ostacoli" value="off" style="width:70px;height:35px;color:#ff0000;border-color:#ff0000"></input>
</div>

</div>
</body>
</html>


<script>
/******************************/
/**Variabili delle direzioni**/
/****************************/
var a=false;
var i=false;
var sx=false;
var dx=false;
/***********************/
/**Variabili dei dati**/
/*********************/
var dati;
//var cnt=2;
var vecchio_dato="N";
var acc;
var vel;
var ao;

/************************************************/
/**Funzione sleep per aspettare N millisecondi**/
/**********************************************/
function sleep(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}
/************************************************************************/
/**Funzione ArrowSender per inviare i dati utilizzando i tasti freccia**/ 
/**********************************************************************/
function ArrowSender()
{
	if(a==false && i==false && sx==false && dx==false)
	{
		dati="N";
	}
	if(a==true && i==false && sx==false && dx==false)
	{
		dati="A";
	}
	if(i==true && a==false && sx==false && dx==false)
	{
		dati="I";
	}
	if(sx==true && i==false && a==false && dx==false)
	{
		dati="S";
	}
	if(dx==true && i==false && sx==false && a==false)
	{
		dati="D";
	}
	if(a==true && sx==true && i==false && dx==false)
	{
		dati="C";
	}
	if(a==true && dx==true && i==false && sx==false)
	{
		dati="B";
	}
	if(i==true && sx==true && dx==false && a==false)
	{
		dati="G";
	}
	if(i==true && dx==true && sx==false && a==false)
	{
		dati="F";
	}
	if(vecchio_dato!=dati)/**Invia solo se il dato cambia**/
	{
		/*******************************************************************/
		/**ajax mi permette di inviare i dati, senza ricaricare la pagina**/
		/*****************************************************************/
		$.ajax(
			{
				url:$("#form").attr("action"),
				type:"POST",
				data:{submit:dati,acc:acc,vel:vel,ao:ao},
				success:function(msg)
						{
							$("#risultato").html(msg);
						},
				error:	function(err)
						{
							alert("Si è verificato un errore "+err.status);
						}

			});
			vecchio_dato=dati;
			sleep(100);
	}

	return false;
}

/**************************************************************/
/**Serve per aspettare il corretto caricamento del documento**/
/************************************************************/
$(document).ready(function()
{

	/***************************************/
	/**Per mobile, se clicco sui pulsanti**/
	/*************************************/
	$("#a,#sx,#i,#dx").mousedown(function ProcessForm()
	{
		dati=$(this).val();
		if(vecchio_dato!=dati)
		{
		$.ajax(
		{
			url:$("#form").attr("action"),
			type:"POST",
			data:{submit:dati,acc:acc,vel:vel,ao:ao},
			success:function(msg)
					{
					},
			error:	function(err)
					{
						alert("Si è verificato un errore "+err.status);
					}

		});
		vecchio_dato=dati;
		}
		return false;
	});

	/******************************/
	/**Per rilascio del pulsante**/
	/****************************/
	$("#a,#sx,#i,#dx").mouseup(function ()
	{
		dati="N";
		if(vecchio_dato!=dati)
		{
			$.ajax(
			{
				url:$("#form").attr("action"),
				type:"POST",
				data:{submit:dati},
				success:function(msg)
					{
						$("#risultato").html(msg);
					},
				error:function(err)
					{
						alert("Errore "+err.status);
					}
			});
		vecchio_dato=dati;
		}
		return false;
	});

	/*************************************/
	/**Quando viene rilasciato il tasto**/
	/***********************************/
	document.onkeyup=function(e)
	                {
						if(e.which==37)//sinistra
						{
							sx=false;
							$("#sx").css("color","#000000");
						}
						if(e.which==38)//avanti
						{
							a=false;
							$("#a").css("color","#000000");
						}
						if(e.which==39)//destra
						{
							dx=false;
							$("#dx").css("color","#000000");
						}
						if(e.which==40)//indietro
						{
							i=false;
							$("#i").css("color","#000000");
						}
						ArrowSender();
					}


	/******************************************/
	/**Quando viene premuto un tasto freccia**/
	/****************************************/
	document.onkeydown=function(e)
						{
							if(e.which==37)//sinistra
							{
								sx=true;
								$("#sx").css("color","#ff0000");
							}
							if(e.which==38)//avanti
							{
								a=true;
								$("#a").css("color","#ff0000");
							}
							if(e.which==39)//destra
							{	
								dx=true;
								$("#dx").css("color","#ff0000");
							}
							if(e.which==40)//indietro
							{	
								i=true;
								$("#i").css("color","#ff0000");
							}

							ArrowSender();
						}


	
	/**Finestra di dialogo per i controlli**/
	$("#dialog").dialog({
							autoOpen: false,
							modal: true,
							width:500,
							height:450
						});

    /**Rampa di accellerazione**/							
	$("#slider_rampa_acc").slider({
									min:20,
									max:100,
									step:20,
									value:60,
									slide: function(event,ui){
																$("#accelerazione").val(ui.value+"%");
																acc=ui.value;
															}
								});
	$( "#accelerazione" ).val($( "#slider_rampa_acc" ).slider( "value" )+"%");


	/**Rampa della velocità**/
	$("#slider_rampa_vel").slider({
									min:20,
									max:100,
									step:20,
									value:60,
									slide: function(event,ui){
																$("#velocità").val(ui.value+"%");
																vel=ui.value;
															}
								});
	
	$( "#velocità" ).val($( "#slider_rampa_vel" ).slider( "value" )+"%");
	
	/**Pulsante allerta ostacoli**/
	$("#ostacoli").button().click(function(){
											if($("#ostacoli").button("option","label")=="off")
											{
												$("#ostacoli").button("option","label","on");
												$("#ostacoli").button().css("color","#00bb00");
												$("#ostacoli").button().css("border-color","#00ff00");
												ao=$("#ostacoli").button("option","label");
											}
											else
											{
												$("#ostacoli").button("option","label","off");
												$("#ostacoli").button().css("color","#ff0000");
												$("#ostacoli").button().css("border-color","#ff0000");
												ao=$("#ostacoli").button("option","label");
											}
	
											});
									
	
	/**Se premo pulsante controlli**/
	$("#Controlli").click(function()
							{
								$("#dialog").dialog("open");
							});
							
	
	acc=$( "#slider_rampa_acc" ).slider( "value" );
	vel=$( "#slider_rampa_vel" ).slider( "value" );
	ao=$("#ostacoli").button("option","label");

	/**Spegnimento**/
	$("#spegni").click(function spegni()
								{
									
									$.ajax({
											url:"spegni.php",
											type:"POST",
											data:{spegni:"spegni"},
											success:function(msg)
															{
																//alert(msg);
																$("#risultato").html(msg);
															},
											error:function(err)
															{
																alert(err);
															}
										  });
									return false;
									
								});
});
</script>
