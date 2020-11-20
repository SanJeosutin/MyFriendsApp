<?php
    /*********************************************
    *? NAME        : SanJeosutin
    *? TITLE       : settings.php
    *? DESCRIPTION : Database login info for this MyFriends app. 
    *? CREATED ON  : 24-09-2020
    *? EDITED ON   : 24-09-2020
    *********************************************/

    $host = "localhost";
    $user = "root";
    $pass = "";
    $db = "myFriendsApp_db";
    
    $conn = @mysqli_connect($host, $user, $pass, $db);
?>