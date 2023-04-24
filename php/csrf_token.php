<?php
    function csrf_token_new(){
        $csrf_token = openssl_random_pseudo_bytes(8);
        $csrf_token = bin2hex($csrf_token);
        $_SESSION['csrf_token'] = $csrf_token;
    }

    function csrf_token_check($token){
        // scusate, mi stavo divertendo
        return ($token === $_SESSION['csrf_token'] ? true : false);
    }

    // Controllo che la richiesta sia intenzionale
    function csrf_token_validate_request(){
        if($_SERVER['REQUEST_METHOD'] === "POST"){
            $is_valid = csrf_token_check($_POST['csrf_token']);
            csrf_token_new();

            if($is_valid === false){
                header("HTTP/1.1 403 Forbidden");
                echo "Non sei autorizzato!";
                exit(0);
            }
        } else{
            csrf_token_new();
        }
    }
?>