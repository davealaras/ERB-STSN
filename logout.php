<?php
session_start();
session_destroy();
setcookie('username','', time()-3600*24);
setcookie('password','', time()-3600*24);
setcookie('remember','', time()-3600*24);
header("Location: index.php");
?>