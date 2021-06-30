


<?php
/****************************/
/*Semplice Pagina di login**/
/**************************/

/*Da migliorare*/

session_start();   //Avvia la sessione
unset($_SESSION['password']); //Verifica che sia settata
$file="pwd.txt"; //file txt della password  
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script src="jquery.min.js"></script> <!-- Includo la libreria jQuery-->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login</title> <!-- Titolo della pagina -->
</head>

<body>
<center>
<form action="" method="post"> <!-- Form di login -->
Inserisci La Password <input type="password" name="pwd"/>
<input type="submit" value="Invia" name="invia" />
</form>
</center>
</body>
</html>

<?php
if(isset($_POST['pwd'])&&$_POST['pwd']!="") //Se è tutto settato...
{
$_SESSION['password']=$_POST['pwd'];
        $handle=fopen($file,"r");
		$read=fgets($handle);
		if($read==md5($_POST['pwd'])) //Se la password inserita è uguale a quella nel file...
		header("location: control.php");//...Redirect alla pagina di controllo
		else //Altrimenti...
		{
			?>
			<center>
			<p id="pwderr">
			<?php
            echo "Password Errata!!!";
			?>
            </p>
			</center>
            <?php
			unset($_SESSION['password']);
			header("Refresh: 2; url= index.php");
		}
}
if(isset($_POST['pwd'])&&$_POST['pwd']=="")
{
?>
<center>
<p id="noins">
<?php
echo "Inserisci la Password!!!";
header("Refresh: 2; url= index.php");
?>
</p>
</center>
<?php
}?>

<script>
$("#noins").css("color","red").css("font-size","24px");
$("#noins").fadeOut(2000,function(){});
$("#pwderr").css("color","red").css("font-size","24px");
$("#pwderr").fadeOut(2000,function(){});
</script>