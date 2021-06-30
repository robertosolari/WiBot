<?php
if($_POST)
{
echo $_POST['submit'];
echo $_POST['acc'];
echo $_POST['vel'];
echo $_POST['ao'];

/**Variabile per l'accellerazione**/

if($_POST['acc']==20)
{
	$acc_val="P";
}
if($_POST['acc']==40)
{
	$acc_val="Q";
}
if($_POST['acc']==60)
{
	$acc_val="R";
}
if($_POST['acc']==80)
{
	$acc_val="T";
}
if($_POST['acc']==100)
{
	$acc_val="V";
}

/**Variabile per la velocità**/

if($_POST['vel']==20)
{
	$vel_val="W";
}
if($_POST['vel']==40)
{
	$vel_val="Y";
}
if($_POST['vel']==60)
{
	$vel_val="K";
}
if($_POST['v']==80)
{
	$vel_val="J";
}
if($_POST['vel']==100)
{
	$vel_val="Z";
}

/**Variabile per l'allerta ostacoli**/
if($_POST['ao']=="off")
{
	$ao_val="L";
}
if($_POST['ao']=="on")
{
	$ao_val="H";
}

require ('php_serial.class.php');//Include il file per gestire la porta seriale
error_reporting(E_ALL);
ini_set("display_errors", 1);
$r;
	$val=$_POST['submit'];

	
	// Crea una nuova classe serial
	$serial = new phpSerial;
	// Specifica la porta utilizzata
	$serial->deviceSet("/dev/ttyAMA0");
	// Setta i parametri
	$serial->confBaudRate(38400);
	$serial->confParity("none");
	$serial->confCharacterLength(8);
	$serial->confStopBits(1);
	$serial->confFlowControl("none");
	// Apre la porta per l'uso
	$serial->deviceOpen();
	
	echo $val;
    // Ivia i messaggi
	$serial->sendMessage($vel_val); 
	usleep(10000);//Aspetta 10 millisec
	$serial->sendMessage($acc_val);
	usleep(10000);
	$serial->sendMessage($ao_val);
	usleep(10000);
	$serial->sendMessage($val);
	
	//$serial->flush();

    // Chiude la porta
	$r=$serial->deviceClose();

	echo $r;

}
?>
