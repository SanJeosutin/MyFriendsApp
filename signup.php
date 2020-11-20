<?php
    /*********************************************
     *? NAME        : SanJeosutin
     *? TITLE       : signup.php
     *? DESCRIPTION : signing up form for My Friends App. 
     *? CREATED ON  : 24-09-2020
     *? EDITED ON   : 09-10-2020
     *********************************************/
    
    include("functions/header.php");
    include("functions/functions.php");
    $errMsg = array();
?>

    <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
        <table>
            <tr>
                <td>
                    <p>Email</p>
                    <p>Profile Name</p>
                    <p>Password</p>
                    <p>Confirm Password</p>
                </td>
                <td>
                    <p><input type="email" name="userEmail" value="<?php echo isset($_POST["userEmail"]) ? $_POST["userEmail"] : ''; ?>"></p>
                    <p><input type="text" name="userProfileName" value="<?php echo isset($_POST["userProfileName"]) ? $_POST["userProfileName"] : ''; ?>"></p>
                    <p><input type="password" name="userPassword"></p>
                    <p><input type="password" name="userCPassword"></p>
                </td>
            </tr>
        </table>
        <input type="submit" name="postForm" value="Register">
        <input type="reset" name="resetForm" value="Clear">
    </form>
    
<?php
    if(isset($_POST['userEmail'])) $uEmail = cleanInput($_POST['userEmail']);
    if(isset($_POST['userProfileName'])) $uProfileName = cleanInput($_POST['userProfileName']);
    if(isset($_POST['userPassword'])) $uPassword = cleanInput($_POST['userPassword']);
    if(isset($_POST['userCPassword'])) $uCPassword = cleanInput($_POST['userCPassword']);

    if(isset($_POST['postForm'])){
        $state = "error";
        require_once("functions/settings.php");

        if($uEmail == ""){
            array_push($errMsg, "Email", "Required to be filled out");
        }elseif(checkDuplicateEmail($conn, $uEmail)){
            array_push($errMsg, "Email", "Email already registered. Try a different one");
        }

        if(strlen($uEmail) > 50){
            array_push($errMsg, "Email", "Characters amount exceeded. Must be less than 50 charaters");
        }

        if($uProfileName == ""){
            array_push($errMsg, "Profile Name", "Required to be filled out");
        }else if(!preg_match("/^([A-Za-z][\s]*){1,20}$/", $uProfileName)){
            if(strlen($uProfileName) > 30){
                array_push($errMsg, "Profile Name", "Characters amount exceeded. Must be less than 30 charaters");
            }else{
                array_push($errMsg, "Profile Name", "Cannot contain number or any non-alpha characters");
            }
        }

        if ($uPassword == "") {
            array_push($errMsg, "Password", "Required to be filled out");
        }else if(!preg_match("/^(\w*){1,20}$/", $uPassword)){
            if(strlen($uPassword) > 20){
                array_push($errMsg, "Password", "Characters amount exceeded. Must be less than 20 charaters");
            }else{
                array_push($errMsg, "Password", "Cannot contain any non-alphanumeric characters");
            }
        }

        if(strcmp($uCPassword, $uPassword)){
            array_push($errMsg, "Password", "Does not match. Try again");
        }

        if($errMsg == array()){
            require_once("functions/settings.php");

            if ($conn) {
                $query = "INSERT INTO friends 
                (friend_email, password, profile_name, date_started) 
                VALUES ('$uEmail', '$uPassword', '$uProfileName', '$currDate')
                ";
                $insert = mysqli_query($conn, $query);

                if ($insert) {
                    //CHANGE STATE ONLY WHEN THE DATA IS SUCCESSFULLY INSERTED
                    $state = "success";
                    $_SESSION['login'] = "success";
                    $_SESSION['name'] = $uProfileName;
                    $_SESSION['noOfFriends'] = 0;
                    header("Location: friendadd.php");
                } else {
                    array_push($errMsg, "Failed", "Cannot enter your last request. Please try again");
                }
            }
            mysqli_close($conn);
        }
        echo displayMessage($errMsg, $state);
    }
    include("functions/footer.php");
?>