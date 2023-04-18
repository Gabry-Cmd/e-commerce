<?php
    $connected_text = "Accedi";
    $connected_query = "";

    session_start();
    if(isset($_GET['exit']))
    {
        unset($_SESSION['connected']);
        session_destroy();
        session_start();
    }
    if(isset($_SESSION['connected'])){
        $connected_text = "Esci";
        $connected_query = "?exit";
        // l'utente viene ricordato per access_timeout secondi 
        if(time() >= $_SESSION['access_date'] + $_SESSION['access_timeout']){
            unset($_SESSION['connected']);
        }
    }
?>