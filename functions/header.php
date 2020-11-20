<?php
    /*********************************************
    *? NAME        : SanJeosutin
    *? TITLE       : header.php
    *? DESCRIPTION : Reusable header accross the site. 
    *? CREATED ON  : 24-09-2020
    *? EDITED ON   : 09-10-2020
    *********************************************/
    if (session_status() == PHP_SESSION_NONE)session_start();

    /*GET CURRENT PAGE & RID OF EXTENSION FILE*/
    $currLocation = basename($_SERVER['PHP_SELF']);
    $currLocation = str_replace('.php', '', $currLocation);
    $currLocation = ucfirst($currLocation);
    /*SET DEFAULT TIMEZONE TO MELB TIME*/
    date_default_timezone_set('Australia/Melbourne');
    $currDate = date("Y/m/d");

    if($currLocation == "Friendadd"){
        $currLocation = "Add Friend";
    }elseif($currLocation == "Friendlist"){
        $currLocation = $_SESSION['name']."'s Friend List";
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="Author" content="SanJeosutin">
    <meta name="Title" content="Assignment 2">
    <link href="https://fonts.googleapis.com/css2?family=Belgrano&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="icon" href="images/MyFriends-Logo.png">
    <title>My Friends System</title>
</head>

<body>
    <header>
        <h1>My Friends System</h1>
        <h2><?php echo $currLocation; ?></h2>
    </header>
    <nav class="page">

        <nav class="navBar">
            <img src="images/MyFriends-Logo.png" alt="My Friends Logo" class="logo">
            <a href="index.php" <?php echo(($currLocation == "Index")? "class='current'": ""); ?>>Home</a>
            <?php
                /*IF SESSION LOGIN IS CREATED AND HAVE A VALUE 'success' SHOW THE FOLLOWING*/
                if(isset($_SESSION['login']) && $_SESSION['login']  == "success"){
                    $_SESSION['ID'] = "";
            ?>
                <a href="friendadd.php" <?php echo(($currLocation == "Add Friend")? "class='current'": ""); ?>>Add Friend</a>
                <a href="friendlist.php" <?php echo(($currLocation == $_SESSION['name']."'s Friend List")? "class='current'": ""); ?>>Friend List</a>
                <a href="logout.php" <?php echo(($currLocation == "Logout")? "class='current'": ""); ?>>Logout</a>
            <?php
                /*ELSE SHOW THE USER TO signup OR login*/
                }else{
            ?>
                <a href="signup.php" <?php echo(($currLocation == "Signup")? "class='current'": ""); ?>>Sign Up</a>
                <a href="login.php" <?php echo(($currLocation == "Login")? "class='current'": ""); ?>>Login</a>
            <?php      
                }
            ?>
            <a href="about.php" <?php echo(($currLocation == "About")? "class='current'": ""); ?>>About</a>
        </nav>

        <nav class="content">