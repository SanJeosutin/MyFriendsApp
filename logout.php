<?php 
    /*********************************************
     *? NAME        : SanJeosutin
     *? TITLE       : login.php
     *? DESCRIPTION : login form for My Friends App. 
     *? CREATED ON  : 24-09-2020
     *? EDITED ON   : 09-10-2020
     *********************************************/

if (session_status() == PHP_SESSION_NONE) session_start();
session_unset();
session_destroy();
header("location: index.php");
exit();
?>