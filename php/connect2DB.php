<?php
    $dbhost = "localhost:3306";
    $dbuser = "root";
    $dbpass = "";
    $dbname = "ecommerce";

    $dbconn =mysqli_connect($dbhost, $dbuser, $dbpass)  
            or die("Connessione FALLITA\n");
    // echo "Connessione OK <br/>";

    mysqli_select_db($dbconn, $dbname) or die("Non trovo il DB"); 
    // echo "Database OK <br/><hr/>";

?>