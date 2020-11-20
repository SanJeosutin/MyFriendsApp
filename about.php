<?php
/*********************************************
 *? NAME        : SanJeosutin
 *? TITLE       : index.php
 *? DESCRIPTION : Main content of the web application. 
 *? CREATED ON  : 24-10-2020
 *? EDITED ON   : 24-10-2020
 *********************************************/

include("functions/header.php");
echo "
    <h2>About This Assignment</h2>
    <nav class='display'>
        <p><strong>Task that are not attempted or completed?</strong></p>
        <ul>
            <li>Task 9 - Mutual Friend Count</li>
        </ul>
        <br/>
        <p><strong>Special Features?</strong></p>
        <ul>
            <li>Have a reuseable 'error handling' system. Now with it's own seperate error box and comes with colour!</li>
            <li>Split 'header.php' & 'footer.php'</li>
            <li>Have a 'functions.php' to handle most of the work</li>
            <li>Changes navigation button depends on where the user are in the website.</li>
        </ul>
        <br/>
        <p>Part(s) that give the most pain during this assignment?</p>
        <ul>
            <li>Got to be Task 9. I just could not figure the right query to get the total mutual friends</li>
        </ul>
        <br/>
        <p>What things that can be improve in the future?</p>
        <ul>
            <li>Some of the things can definitly be improved would be try to spread out the functions.php as of right now<br>
            it is to clump together. I would try to make this assignment as a true OO (Object Oriented) program. As some of<br>
            the functions could be improved and can be handled a whole lot better.
            </li>
        </ul>
        <br/>
        <p><strong>What discussion points did you participated on in the unit’s discussion board for Assignment 2?</strong></p>
        <ul>
            <li>For this assignment, I did not participate on any of the discussion as most of the relavent questions has been
            answered by multiple people.</li>
        </ul>
        <p><strong>List of links:</strong></p>
        <ul>
            <li><a href='friendlist.php'>Friends List (required to be logged in)</a></li>
            <li><a href='friendadd.php'>Add Friends (required to be logged in)</a></li>
            <li><a href='index.php'>Home Page</a></li>
        </ul>
    </nav>
";
    include("functions/footer.php");
?>