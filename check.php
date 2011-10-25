
<?php 
	include('header.php');

if(isset($_REQUEST['value']))//If a username has been submitted
{
$username = $_REQUEST['value'];
$EGB->db_connect();

if($EGB->check_user($username))
{
$json["valid"] = false;
$json["message"] = 'Username not available';
}
else
{
$json["valid"] = true;
}
print json_encode($json);
$EGB->db_close();
}

?>