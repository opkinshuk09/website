<?php 

include '../../include/settings.php';   
if (!isset($_SESSION)) { 
	session_start(); }
	if (!isset($_SESSION['username'])) {
	header('Location: ../login');	
	exit();
	}
	
$usernamee = $_SESSION['username']; 
$generator = mysqli_real_escape_string($con, $_GET['generator']);
$username = mysqli_real_escape_string($con, $_GET['username']);
$date = date("Y-m-d");

			if($username !== $usernamee){  
					die("Access Denied");			 
			}


		$pos = strpos($_SERVER['HTTP_REFERER'],getenv('HTTP_HOST'));
			if($pos === false){  
					die("Access Denied");
			}

  
    if($_SESSION['rank'] < 5){
$result = mysqli_query($con, "SELECT * FROM `subscriptions` WHERE `username` = '$usernamee' AND `active` = '1' AND `expire` >= '$date'") or die(mysqli_error($con));
if (mysqli_num_rows($result) < 1) 
{	$subs = "0";
}else{
    $subs = "1"  ;
    }
			if($subs == "0" && $_SESSION['rank'] !== "5"){
						die("Access Denied");
			}

while($row = mysqli_fetch_array($result)){
	$package = $row['package'];
}

$result = mysqli_query($con, "SELECT * FROM `package` WHERE `id` = '$package'") or die(mysqli_error($con));
while($row = mysqli_fetch_array($result)){
    $haspriv = $row['haspriv'];
	
	if($haspriv !== "yes"){
		die("Access Denied");
	}
	$accounts = $row['privgenerations'];
}


if(($accounts !== "0") && ($accounts !== "") && ($_SESSION['rank'] != "5")){
	$resultss = mysqli_query($con, "SELECT * FROM `privstatistics` WHERE `username` = '$username' AND `date` = '$date'") or die(mysqli_error($con));
	while($row = mysqli_fetch_assoc($resultss)){
		if($row['generated'] >= $accounts){
			exit("G�n�rations max atteintes !");
		}
	}
}
}

$result = mysqli_query($con, "SELECT * FROM `$generator` ORDER BY RAND() LIMIT 1") or die(mysqli_error($con));
if(mysqli_num_rows($result) < 1){
	exit("Rupture de stock !");
}
while($row = mysqli_fetch_array($result)){
$alt = $row['alt'];
	echo $alt;
	mysqli_query($con, "DELETE FROM `$generator` WHERE `alt` = '$alt'") or die(mysqli_error($con));
}
$genresult = mysqli_query($con, "SELECT * FROM `settings`") or die(mysqli_error($con));
while($row = mysqli_fetch_array($genresult)){
$generateeed = $row['generations'] + "1";
mysqli_query($con, "UPDATE `settings` SET `generations` = '$generateeed'") or die(mysqli_error($con));
}
$genresulta = mysqli_query($con, "SELECT * FROM `users` WHERE `username` = '$username'") or die(mysqli_error($con));
while($row = mysqli_fetch_array($genresulta)){
$generatid = $row['generations'] + "1";
mysqli_query($con, "UPDATE `users` SET `generations` = '$generatid' WHERE `username` = '$username'") or die(mysqli_error($con));
}
$date = date("Y-m-d");

$result = mysqli_query($con, "SELECT * FROM `privstatistics` WHERE `username` = '$username' AND `date` = '$date'") or die(mysqli_error($con));
if(mysqli_num_rows($result) < 1){
	mysqli_query($con, "INSERT INTO `privstatistics` (`username`, `generated`, `date`) VALUES ('$username', '1', DATE('$date'))") or die(mysqli_error($con));
}else{
	while($row = mysqli_fetch_array($result)){
		$generated = $row['generated'] + "1";
		mysqli_query($con, "UPDATE `privstatistics` SET `generated` = '$generated' WHERE `username` = '$username' AND `date` = '$date'") or die(mysqli_error($con));
	}
}
?>