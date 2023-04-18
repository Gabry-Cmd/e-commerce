<?php
    // $dbhost = "localhost:3306";
    // $dbuser = "www-data";
    // $dbpass = "";
    // $dbname = "ecommerce";

    // $dbconn =mysqli_connect($dbhost, $dbuser, $dbpass)  
    //         or die("Connessione FALLITA\n");
    // // echo "Connessione OK <br/>";

    // mysqli_select_db($dbconn, $dbname) or die("Non trovo il DB"); 
    // // echo "Database OK <br/><hr/>";

?>
<?php
    $dbhost = "localhost";
    $dbuser = "www-data";
    $dbpass = "";
    $dbname = "ecommerce";
    $dbsock = "/run/mysqld/mysqld.sock";
    
    $dbconn = mysqli_connect($dbhost, $dbuser, $dbpass, null, null, $dbsock) or die("Connessione FALLITA\n");
    // echo "DBMS ok";

    mysqli_select_db($dbconn, $dbname) or die("Non trovo il DB"); 
    // echo "Database ok";
?>