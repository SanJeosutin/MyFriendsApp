<?php
    /*********************************************
    *? NAME        : SanJeosutin
    *? TITLE       : functions.php
    *? DESCRIPTION : Main functions for MyFriends app. 
    *? CREATED ON  : 24-09-2020
    *? EDITED ON   : 21-10-2020
    *********************************************/

/****************************************************************/

    /*SANITISED USER'S INPUT*/
    function cleanInput($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    /*DISPLAY MESSAGE AT THE BOTTOM RIGHT WHEN THE USER FINISHED THEIR ACTIONS*/
    function displayMessage($errMsg, $state){
        $dataAmount = count($errMsg);
        $fieldName = array();
        $reason = array();
        $data = "";
        /*
        *SPLIT $errMsg INTO 2 SEPERATE DATA / ARRAY:
        *    -$fieldName
        *    -$reason
        */
        //FIELD NAME
        for($i=0; $i < $dataAmount; $i+=2){
            array_push($fieldName, $errMsg[$i]);
        }

        //REASON
        for($i=1; $i < $dataAmount; $i+=2){
            array_push($reason, $errMsg[$i]);
        }

        /*
        NEATLY PUT THE MESSAGE BACK INTO A 
        READABLE HTML FORMAT
        */
        for($i=0; $i < count($reason); $i++){
            $data .= "
            <p><strong>".$fieldName[$i].":</strong> - 
            <em>".$reason[$i].".</em></p>
            ";
        }
        /*
        SWITCH CASE TO INDICATE IF ITS AN ERROR,
        WARNING OR SUCCESS. CHANGE THE BACKGROUND
        OF THE MESSAGE. 
        */
        switch ($state) {
            case 'error':
                $state = "alertFail";
                break;
            
            case 'warn':
                $state = "alertWarning";
                break;

            case 'success':
                $state = "alertSuccess";
                break;

            default:
                $state = "NADA";
                break;
        }

        $displayMessage = "
        <nav class='alertMessage' id='$state'>
            $data
        </nav>
        ";
        return $displayMessage;
    }

    /*CHECK IF THERE IS ANY DUPLICATE ON EMAIL INPUT AT signup.php*/
    function checkDuplicateEmail($conn, $userInput){
        $state = "error";
        $errMsg = array();
        if(!$conn){
            array_push($errMsg, "Mercury Server", "Cannot connect to the database");
            return displayMessage($errMsg, $state);
        }else{
            $query = "SELECT friend_email FROM friends";
            $result = mysqli_query($conn, $query);

            while($row = mysqli_fetch_assoc($result)){
                if($row["friend_email"] == $userInput){
                    return true;
                }
            }
            return false;
        }
    }

    /*CHECK IF PROVIDED LOGIN ARE FOUND INSIDE THE DATABASE*/
    function checkLoginInfo($conn, $userEmail, $userPassword){
        $state = "error";
        $errMsg = array();
        if(!$conn){
            array_push($errMsg, "Mercury Server", "Cannot connect to the database");
            return displayMessage($errMsg, $state);
        }else{
            $query = "SELECT * FROM friends";
            $result = mysqli_query($conn, $query);

            if(!$result){
                array_push($errMsg, "Query", "Cannot fetch requested query");
                return displayMessage($errMsg, $state);
            }else{
                while($row = mysqli_fetch_assoc($result)){
                    if($row["friend_email"] == $userEmail && $row["password"] == $userPassword){
                        $_SESSION['name'] = $row['profile_name'];
                        $_SESSION['noOfFriends'] = $row['num_of_friends'];
                        return true;
                    }
                }
                return false;
            }

        }
    }

    /*CHECK IF ANY OF THE TABLES HAVE VALUES*/
    function checkIfTableHasValue($conn){
        $state = "error";
        $data = array();

        if(!$conn){
            array_push($data, "Mercury Server", "Cannot connect to the database");
            return displayMessage($data, $state);
        } else {
            $query = "SELECT * FROM myfriends WHERE 1";
            $result = mysqli_query($conn, $query);

            if($result){
                if(mysqli_num_rows($result) == 0)
                {
                    populateFriendsTable($conn);
                    populateTableMyFriends($conn);
                    updateNumOfFriends($conn);
                    $state = "success";
                    array_push($data, "Success", "Table 'friends' has been created & populated successfully");
                    array_push($data, "Success", "Table 'myfriends' has been created & populated successfully");

                    return displayMessage($data, $state);
                }else{
                    $state = "warn";
                    array_push($data, "Warning", "Table 'friends' already populated");
                    array_push($data, "Warning", "Table 'myfriends' already populated");
                    return displayMessage($data, $state);
                }
            }
        }
    }

    /*CREATE TABLES IF IT DOESNT ALREADY EXIST*/
    function createTables($conn){
        $state = "error";
        $errMsg = array();

        if(!$conn){
            array_push($errMsg, "Mercury Server", "Cannot connect to the database");
            return displayMessage($errMsg, $state);
        } else {
            //CREATE friends & myfriends TABLE
            $query = "CREATE TABLE IF NOT EXISTS friends (
            friend_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            friend_email varchar(50) NOT NULL,
            password varchar(20) NOT NULL,
            profile_name varchar(30) NOT NULL,
            date_started date NOT NULL,
            num_of_friends int(10) UNSIGNED NOT NULL
            );";
            mysqli_query($conn, $query);

            $query = "CREATE TABLE IF NOT EXISTS myfriends (
            friend_id1 int(10) UNSIGNED NOT NULL, 
            friend_id2 int(10) UNSIGNED NOT NULL,
            FOREIGN KEY (friend_id1) REFERENCES friends(friend_id),
            FOREIGN KEY (friend_id2) REFERENCES friends(friend_id)
            );";
            mysqli_query($conn, $query);
        }
    }

    /*GET CURRENT SESSION ID AND PUT IT AS A SESSION VARIABLE FOR ID*/
    function getCurrentSessionID($conn){
        $state = "error";
        $errMsg = array();
        $query = "SELECT * FROM friends ORDER BY profile_name ASC";
        $result = mysqli_query($conn, $query);

        if(!$result){
            array_push($errMsg, "Query", "Cannot fetch requested query");
            return displayMessage($errMsg, $state);
        }else{
            while ($row = mysqli_fetch_assoc($result)) {
                if ($_SESSION['name'] == $row['profile_name']) {
                    $_SESSION['ID'] = $row['friend_id'];
                }
            }
        }
    }

    function getTotalUsers($conn){
        $state = "error";
        $errMsg = array();
        $query = "SELECT COUNT(*) total FROM friends";
        $result = mysqli_query($conn, $query);

        if(!$result){
            array_push($errMsg, "Query", "Cannot fetch requested query");
            return displayMessage($errMsg, $state);
        }else{
            $row = mysqli_fetch_assoc($result);
            return $row['total'];
        }
    }
    /***********************************************************
      ALL THE FUNCTIONS BELLOW ARE USED TO SHOW FRIENDS THAT
       THE USER CURRENTLY HAD INSIDE THEIR myfriends TABLE
    /***********************************************************/

    /*SHOW CURRENT USER'S FRIENDS LIST WITH PAGINATION USING LIMIT*/
    function showFriendsList($conn, $offset, $numOfPage){
        $state = "error";
        $errMsg = array();

        if(!$conn){
            array_push($errMsg, "Mercury Server", "Cannot connect to the database");
            return displayMessage($errMsg, $state);
        } else {
            $query = "SELECT * FROM friends ORDER BY profile_name ASC";
            $result = mysqli_query($conn, $query);

            if(!$result){
                array_push($errMsg, "Query", "Cannot fetch requested query");
                return displayMessage($errMsg, $state);
            }else{
                getCurrentSessionID($conn); 
                while ($row = mysqli_fetch_assoc($result)) {
                    $f_friendID = $row['friend_id'];
                    $f_name = $row['profile_name'];

                    $searchQuary = "SELECT * FROM myfriends WHERE friend_id1 = '".$_SESSION['ID']."' LIMIT $offset, $numOfPage";
                    $searchResult = mysqli_query($conn, $searchQuary);

                    while ($row = mysqli_fetch_assoc($searchResult)) {
                        $myf_friendID2 = $row['friend_id2'];
                        if ($myf_friendID2 == $f_friendID) {
                            echo "
                            <tr>
                                <td>
                                    <p> $f_name </p>
                                </td>
                                <td>
                                    <input type='submit' name='FRND_".$f_friendID."' value='unfriend'>
                                </td>
                            </tr>
                            ";
                        }
                    }
                }
                mysqli_free_result($searchResult);
                mysqli_free_result($result);
                removeFriendLogic($conn);
            }
        }
    }

    /*MAKE REMOVE FRIENDS BUTTON FUNCTIONAL BY HAVING BUTTON NAME AS A USER'S FRIENDS ID*/
    function removeFriendLogic($conn){
        $state = "error";
        $errMsg = array();

        if(!$conn){
            array_push($errMsg, "Mercury Server", "Cannot connect to the database");
            return displayMessage($errMsg, $state);
        } else {
            $query = "SELECT * FROM myfriends WHERE friend_id1 = '".$_SESSION['ID']."'";
            $result = mysqli_query($conn, $query);

            if (!$result) {
                array_push($errMsg, "Query", "Cannot fetch requested query");
                return displayMessage($errMsg, $state);
            } else {
                while ($row = mysqli_fetch_assoc($result)) {
                    $myf_friendID2 = $row['friend_id2'];
                    /*set the buttons to FRND_(their id) and called removeFriend to get functions*/
                    echo((isset($_POST["FRND_$myf_friendID2"]))? removeFriend($conn, $myf_friendID2): "");
                }

                mysqli_free_result($result);
                mysqli_close($conn);
            }
        }
    }

    /*REMOVE FRIEND WHEN 'remove Friend' IS CLICKED*/
    function removeFriend($conn, $userID){
        $state = "error";
        $errMsg = array();

        if (!$conn) {
            array_push($errMsg, "Mercury Server", "Cannot connect to the database");
            return displayMessage($errMsg, $state);
        } else {
            $query = "DELETE FROM myfriends WHERE friend_id1 = ".$_SESSION['ID']." AND friend_id2 = $userID";
            $result = mysqli_query($conn, $query);

            if (!$result) {
                array_push($errMsg, "Query", "Cannot fetch requested query");
                return displayMessage($errMsg, $state);
            } else {
                $state = "success";
                $_SESSION['noOfFriends']--;
                $query = "UPDATE friends SET num_of_friends = '".$_SESSION['noOfFriends']."' WHERE friend_id  = '".$_SESSION['ID']."'";
                $result = mysqli_query($conn, $query);

                $query = "SELECT profile_name FROM friends WHERE friend_id  = '$userID'";
                $result = mysqli_query($conn, $query);

                while ($row = mysqli_fetch_assoc($result)) {
                    array_push($errMsg, "Friend Removed", $row['profile_name']." is no longer your friend. <br> <em>Please refresh your page to see changes.</em>");
                    return displayMessage($errMsg, $state);
                }
            }
        }
    }

    /***********************************************************
      ALL THE FUNCTIONS BELLOW ARE USED TO SHOW REGISTERED USERS
    /***********************************************************/

    /*SHOW ALL REGISTERED USER EXCEPT IF THEY'RE FRIENDS WITH THE LOGGED IN USER*/
    function showRegisteredUsers($conn, $offset, $numOfPage){
        $state = "error";
        $errMsg = array();
        if (!$conn) {
            array_push($errMsg, "Mercury Server", "Cannot connect to the database");
            return displayMessage($errMsg, $state);
        } else {
            getCurrentSessionID($conn);
            $query = "SELECT friend_id, profile_name FROM friends 
            WHERE friend_id NOT IN (SELECT friend_id2 FROM myfriends where friend_id1=".$_SESSION['ID'].")  AND friend_id != ".$_SESSION['ID']."
            GROUP BY profile_name ASC LIMIT $offset, $numOfPage"; 
            $result = mysqli_query($conn, $query);

            if (!$result) {
                array_push($errMsg, "Query", "Cannot fetch requested query");
                return displayMessage($errMsg, $state);
            } else {
                while ($row = mysqli_fetch_assoc($result)) {
                    $f_userName = $row['profile_name'];
                    $f_userID = $row['friend_id'];

                    echo "
                        <tr>
                            <td>
                                <p>$f_userName</p>
                            </td>
                            <td>
                                <input type='submit' name='FRND_".$f_userID."' value='Add Friend'>
                            </td>
                        </tr>
                        ";
                }
                mysqli_free_result($result);
                addFriendLogic($conn);
            }
        }
    }

    /*MAKE ADD FRIENDS BUTTON FUNCTIONAL*/
    function addFriendLogic($conn){
        $state = "error";
        $errMsg = array();

        if(!$conn){
            array_push($errMsg, "Mercury Server", "Cannot connect to the database");
            return displayMessage($errMsg, $state);
        } else {
            $query = "SELECT * FROM friends WHERE friend_id != '".$_SESSION['ID']."'";
            $result = mysqli_query($conn, $query);

            if (!$result) {
                array_push($errMsg, "Query", "Cannot fetch requested query");
                return displayMessage($errMsg, $state);
            } else {
                while ($row = mysqli_fetch_assoc($result)) {
                    $f_userID = $row['friend_id']; 
                    echo((isset($_POST["FRND_$f_userID"]))? addFriend($conn, $f_userID): "");
                }

                mysqli_free_result($result);
                mysqli_close($conn);
            }
        }
    }

    /*ADD FRIENDS TO USER'S DB WHEN  'add friend' IS CLICKED*/
    function addFriend($conn, $userID){
        $state = "error";
        $errMsg = array();

        if (!$conn) {
            array_push($errMsg, "Mercury Server", "Cannot connect to the database");
            return displayMessage($errMsg, $state);
        } else {
            getCurrentSessionID($conn);
            $query = "INSERT INTO myfriends VALUES(".$_SESSION['ID'].", $userID)";
            $result = mysqli_query($conn, $query);

            if (!$result) {
                array_push($errMsg, "Query", "Cannot fetch requested query");
                return displayMessage($errMsg, $state);
            } else {
                $state = "success";
                $_SESSION['noOfFriends']++;
                $query = "UPDATE friends SET num_of_friends = '".$_SESSION['noOfFriends']."' WHERE friend_id = '".$_SESSION['ID']."'";
                $result = mysqli_query($conn, $query);

                $query = "SELECT profile_name FROM friends WHERE friend_id  = '$userID'";
                $result = mysqli_query($conn, $query);

                while ($row = mysqli_fetch_assoc($result)) {
                    array_push($errMsg, "Friend Added", $row['profile_name']." is now your new friend!<br> <em>Please refresh your page to see changes.</em>");
                    return displayMessage($errMsg, $state);
                }
            }
        }
    }


    function getMutualFriend($conn){
        $arrCurrentFriends = array();
        $state = "error";
        $errMsg = array();

        if (!$conn) {
            array_push($errMsg, "Mercury Server", "Cannot connect to the database");
            return displayMessage($errMsg, $state);
        } else {
            $sql = "SELECT * FROM myfriends WHERE friend_id1 = ".$_SESSION['ID'];
            $result = mysqli_query($conn, $sql);

            while($row = mysqli_fetch_assoc($result)){
                array_push($arrCurrentFriends, $row['friend_id2']);
            }

            $sql = "SELECT * FROM myfriends WHERE friend_id1 = ".$_SESSION['ID'];
            $result = mysqli_query($conn, $sql);

            while($row = mysqli_fetch_assoc($result)){
                array_push($arrCurrentFriends, $row['friend_id2']);
            }
        }
    }

    /***********************************************************
         ALL THE FUNCTIONS BELLOW ARE USED TO GENERATE USERS
       RECORDS AT RANDOM FOR BOTH friends AND myfriends TABLE
    /***********************************************************/

    #generate Profile Name
    function randGenProfName(){
        $fNames = ["Adam", "Alex", "Aaron", "Ben", "Carl", "Dan", "David", "Edward", "Fred", "Frank", "George", "Hal", "Hank", "Ike", "John", "Jack", "Joe", "Larry", "Monte", "Matthew", "Mark", "Nathan", "Otto", "Paul", "Peter", "Roger", "Roger", "Steve", "Thomas", "Tim", "Ty", "Victor", "Walter"];
        $lNames = ["Anderson", "Ashwoon", "Aikin", "Bateman", "Bongard", "Bowers", "Boyd", "Cannon", "Cast", "Deitz", "Dewalt", "Ebner", "Frick", "Hancock", "Haworth", "Hesch", "Hoffman", "Kassing", "Knutson", "Lawless", "Lawicki", "Mccord", "McCormack", "Miller", "Myers", "Nugent", "Ortiz", "Orwig", "Ory", "Paiser", "Pak", "Pettigrew", "Quinn", "Quizoz", "Ramachandran", "Resnick", "Sagar", "Schickowski", "Schiebel", "Sellon", "Severson", "Shaffer", "Solberg", "Soloman", "Sonderling", "Soukup", "Soulis", "Stahl", "Sweeney", "Tandy", "Trebil", "Trusela", "Trussel", "Turco", "Uddin", "Uflan", "Ulrich", "Upson", "Vader", "Vail", "Valente", "Van Zandt", "Vanderpoel", "Ventotla", "Vogal", "Wagle", "Wagner", "Wakefield", "Weinstein", "Weiss", "Woo", "Yang", "Yates", "Yocum", "Zeaser", "Zeller", "Ziegler", "Bauer", "Baxster", "Casal", "Cataldi", "Caswell", "Celedon", "Chambers", "Chapman", "Christensen", "Darnell", "Davidson", "Davis", "DeLorenzo", "Dinkins", "Doran", "Dugelman", "Dugan", "Duffman", "Eastman", "Ferro", "Ferry", "Fletcher", "Fietzer", "Hylan", "Hydinger", "Illingsworth", "Ingram", "Irwin", "Jagtap", "Jenson", "Johnson", "Johnsen", "Jones", "Jurgenson", "Kalleg", "Kaskel", "Keller", "Leisinger", "LePage", "Lewis", "Linde", "Lulloff", "Maki", "Martin", "McGinnis", "Mills", "Moody", "Moore", "Napier", "Nelson", "Norquist", "Nuttle", "Olson", "Ostrander", "Reamer", "Reardon", "Reyes", "Rice", "Ripka", "Roberts", "Rogers", "Root", "Sandstrom", "Sawyer", "Schlicht", "Schmitt", "Schwager", "Schutz", "Schuster", "Tapia", "Thompson", "Tiernan", "Tisler"];

        $randFName = rand(0, count($fNames)-1);
        $randLName = rand(0, count($lNames)-1);
        return "$fNames[$randFName] $lNames[$randLName]";
    }

    #generate Email
    function randGenEmail(){
        $atEmail = ["@gmail", "@yahoo", "@hotmail", "@ymail"];
        $profileNames = randGenProfName();

        $atEmailTemp = rand(0, count($atEmail)-1);
        $temp = substr($profileNames, 0, 1);
        $tempName = explode(" ", "$profileNames");
        $email = strtolower($temp.$tempName[1]);
        $email = "$email$atEmail[$atEmailTemp].com";
        return $email;
    }

    #generate password
    function randGenPass() {
        $randNum = rand(8, 20);
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        $pass = array();
        $len = strlen($chars) - 1;
        for ($i = 0; $i < $randNum; $i++) {
            $lenNum = rand(0, $len);
            $pass[] = $chars[$lenNum];
        }
        return implode($pass);
    }
    
    #populate friends table
    function populateFriendsTable($conn){
        $state = "error";
        $errMsg = array();
        $currDate = date("Y/m/d");

        if (!$conn) {
            array_push($errMsg, "Mercury Server", "Cannot connect to the database");
            return displayMessage($errMsg, $state);
        }else{
            for ($i=0; $i < 20; $i++) {
                $uProfileName = randGenProfName();
                $uEmail = randGenEmail();
                $uPassword = randGenPass();
                
                $query = "INSERT INTO friends 
                (friend_email, password, profile_name, date_started) 
                VALUES ('$uEmail', '$uPassword', '$uProfileName', '$currDate')
                ";
                mysqli_query($conn, $query);
            }
        }
    }

    #get total 'friends' from friends table
    function getTotalFriendsFromTable($conn){
        $state = "error";
        $errMsg = array();

        if (!$conn) {
            array_push($errMsg, "Mercury Server", "Cannot connect to the database");
            return displayMessage($errMsg, $state);
        }else{
            $query = "SELECT friend_id FROM friends";

            if($result = mysqli_query($conn, $query)){
                $totalRecords = mysqli_num_rows($result);
                mysqli_free_result($result);
                return $totalRecords;
            }
        }
    }

    #populate myfriends table
    function populateTableMyFriends($conn){
        $state = "error";
        $errMsg = array();

        if (!$conn) {
            array_push($errMsg, "Mercury Server", "Cannot connect to the database");
            return displayMessage($errMsg, $state);
        } else {
            $myID = 1;
            for($j=0; $j < getTotalFriendsFromTable($conn); $j++){

                $arrMyFriends = array();
                $myFriendsTotal = rand(1, 12);
                
                for ($i=0; $i < $myFriendsTotal; $i++) {
                    $myFriendID = rand(1, getTotalFriendsFromTable($conn));
                    
                    if ($myID != $myFriendID) {
                        array_push($arrMyFriends, $myFriendID);
                    }
                }
                $arrMyFriends = array_unique($arrMyFriends);
                sort($arrMyFriends);

                for ($i=0; $i < count($arrMyFriends); $i++) {
                    $query = "INSERT INTO myfriends VALUES ($myID, $arrMyFriends[$i])";
                    mysqli_query($conn, $query);
                }
                $myID++;
            }
        }
    }
    
    #update num_of_friends from myfriends table to friends table
    function updateNumOfFriends($conn){
        $state = "error";
        $errMsg = array();

        if (!$conn) {
            array_push($errMsg, "Mercury Server", "Cannot connect to the database");
            return displayMessage($errMsg, $state);
        } else {
            $query = "UPDATE friends SET num_of_friends = (SELECT COUNT(*) FROM myfriends WHERE friend_id1 = friends.friend_id)";
            mysqli_query($conn, $query);
        }
    }
?>