<?php
    /*********************************************
     *? NAME        : SanJeosutin
     *? TITLE       : login.php
     *? DESCRIPTION : login form for My Friends App. 
     *? CREATED ON  : 24-09-2020
     *? EDITED ON   : 01-10-2020
     *********************************************/

    include("functions/header.php");
    include("functions/functions.php");
?>

    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']?>">
        <table>
            <tr>
                <td>
                    <p>Email</p>
                    <p>Password</p>
                </td>
                <td>
                    <p><input type="email" name="userEmail" value="<?php echo isset($_POST["userEmail"]) ? $_POST["userEmail"] : ''; ?>"></p>
                    <p><input type="password" name="userPassword"></p>
                </td>
            </tr>
        </table>
        <input type="submit" name="postForm" value="Login">
        <input type="reset" name="resetForm" value="Clear">
    </form>
    
<?php
    $errMsg = array();
    $state = "error";
    if(isset($_POST['userEmail'])) $uEmail = cleanInput($_POST['userEmail']);
    if(isset($_POST['userPassword'])) $uPassword = cleanInput($_POST['userPassword']);

    if(isset($_POST['postForm'])){
        include_once("functions/settings.php");
        if(checkLoginInfo($conn, $uEmail, $uPassword)){
            $state = "success";
            $_SESSION['login'] = "success";
            header("Location: friendlist.php");
        }else{
            array_push($errMsg, "Login", "Incorrect login details");
        }
    }
    echo displayMessage($errMsg, $state);
    include("functions/footer.php");
?>


