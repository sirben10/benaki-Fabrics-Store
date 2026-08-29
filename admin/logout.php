<?php require __DIR__.'/_bootstrap.php'; $_SESSION=[]; if(ini_get('session.use_cookies')){setcookie(session_name(),'',time()-42000,'/');}session_destroy();redirect_to('login.php');
