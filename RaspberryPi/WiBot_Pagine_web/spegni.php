<?php  
if ($_POST)
{
	echo $_POST['spegni'];
	$val=shell_exec("ifconfig"); //Non funziona, probabilmente è un problema di permessi
	echo $val;
	if($_POST=="spegni")
	{
		echo "ok";
	}

}
?>
