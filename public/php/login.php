<!-- L'utente si scollega se viene ricevuta una query "?exit" -->
<?php
    include('login_timeout.php');
    include('../../php/connect2DB.php');

    // Restituisce il record di un utente
    // prende la password in chiaro
    function query_user($dbconn, $usr, $pwd){
        // Ricordati di convertire la stringa esadecimale in binario (il confronto dello statement è binario)!
        $pwd = hex2bin(hash('sha256', $pwd));

        $user_exist = "select * from customers where email = (?) and binary password = (?);";
        $stmt = mysqli_prepare($dbconn, $user_exist);
        $stmt->bind_param("ss", $usr, $pwd);
        $stmt->execute();
        $res = $stmt->get_result();
        // $stmt->close();
        return $res;
    }
?>

<?php
    $ins_user = "insert into customers (email, password) values (?, ?);";
    $stmt = mysqli_prepare($dbconn, $ins_user);

    // Registrazione
    if(isset($_POST['register_name']) and isset($_POST['register_password'])){
        $usr = $htmlpurifier->purify($_POST['register_name']);
        $pwd = $htmlpurifier->purify($_POST['register_password']);

        // Controllo che l'utente non esista già
        if(mysqli_num_rows(query_user($dbconn, $usr, $pwd)) == 1){
            echo "<script>alert('Impossibile continuare, l\'utente è già registrato!')</script>";
        } else{
            $pwd = hex2bin(hash('sha256', $pwd));
            $stmt->bind_param("ss", $usr, $pwd);
            $stmt->execute();
            $stmt->free_result();
            echo "<script>alert('Registrazione avvenuta correttamente!')</script>";
        }

        unset($_POST['register_name']);
        unset($_POST['register_password']);
        $_SESSION['connected'] = TRUE;
    }
?>

<?php
    // Login
    if(isset($_POST['login_name']) and isset($_POST['login_password'])){
        $usr = $htmlpurifier->purify($_POST['login_name']);
        $pwd = $htmlpurifier->purify($_POST['login_password']);
        $q_res = query_user($dbconn, $usr, $pwd);
        $n_rows = mysqli_num_rows($q_res);
        $q_user_data = $q_res->fetch_assoc();

        if($n_rows == 1){
            echo "<script>alert('Accesso effettuato')</script>";

            if($_POST['keep_me_in'] == "on"){
                $_SESSION['access_timeout'] = 3600; // 1 ora
            }else{
                $_SESSION['access_timeout'] = 10; // 60 sec
            }
            $_SESSION['access_date'] = time();
            $_SESSION['connected'] = TRUE;
            $_SESSION['email'] = $usr;
            $_SESSION['ID'] = $q_user_data['ID'];

            unset($_POST['login_name']);
            unset($_POST['login_password']);
            unset($_POST['keep_me_in']);
            header("Location: catalog.php");
        } else{
            echo "<script>alert('Credenziali errate')</script>";

            unset($_POST['login_name']);
            unset($_POST['login_password']);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="../css/header.css">
        <link rel="stylesheet" href="../css/horiz_list.css">
        <link rel="stylesheet" href="../css/vert_divider.css">
        <link rel="stylesheet" href="../css/form_boxes.css">
        <title>Azienda & Logo</title>
    </head>
    <body style="margin: 0px; overflow: hidden;">
        <?php
            include('../../php/header.php');
        ?>
        <ul class="horiz_list">
            <div>
                <li>
                    <div style="font-size: 200%; text-align: center;">Registrati</div>
                    <form class="box_outer" action="login.php" method="post">
                        <input name="register_name" placeholder="Nome" type="text"><br/><br/>
                        <input name="register_password" placeholder="Password" type="password"><br/><br/>
                        <input value="Registrati" type="submit"><input value="Pulisci" type="reset">
                    </form>
                </li>
            </div>
            
            <div class="divider" id="divider">
            </div>
            <!-- <script>
                document.getElementById("divider").setAttribute("style", "height: "+window.innerHeight+"px");
            </script> -->

            <div>
                <li>
                    <div style="font-size: 200%; text-align: center;">Accedi</div>
                    <form class="box_outer" action="login.php" method="post">
                        <input name="login_name" placeholder="Nome" type="text"><br/><br/>
                        <input name="login_password" placeholder="Password" type="password"><br/><br/>
                        Rimani connesso<input name="keep_me_in" type="checkbox"><br/><br/>
                        <input value="Accedi" type="submit"><input value="Pulisci" type="reset">
                    </form>
                </li>
            </div>
        </ul>

        <?php include('../../php/footer.php') ?>
    </body>
</html>