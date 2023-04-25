<?php
    include('login_timeout.php');
    include('../../php/query_products.php');
    include('../../php/connect2DB.php');
    include('../../php/global_configs.php');
    include('../../php/csrf_token.php');

    csrf_token_validate_request();
?>

<?php
    if(!isset($_SESSION['userCart'])){
        $_SESSION['userCart'] = [];
    }
    if(isset($_POST['payed'])){
        isset($_SESSION['email']) or die('E\' necessario accedere per poter utilizzare questa funzionalità!');
        
        // Devo inserire un'ordine
        // L'ordine ha dei dettagli d'ordine associati, uno per ogni prodotto selezionato
        // L'ordine è associato a un cliente
        // L'ID del cliente è nella sessione, inserisco l'ordine,
        // poi ottengo gli id dei prodotti dal carrello, ottengo i prodotti dal DB sapendo gli id,
        // infine inserisco i dettagli d'ordine per ogni prodotto acquistato
        $current_date = date("Y-m-d");
        $order = '
            INSERT INTO orders (date, id_customer) VALUES (\''.$current_date.'\', '.$_SESSION['ID'].');
        ';
        // Cosa succede se ID va in overflow?
        $get_order_id = '
            SELECT ID from orders where date=\''.$current_date.'\' AND id_customer='.$_SESSION['ID'].' ORDER BY ID DESC;
        ';

        mysqli_query($dbconn, $order) or die('<script>alert("Impossibile completare l\'ordine, error=1")</script>');
        $order_id = (mysqli_query($dbconn, $get_order_id))->fetch_assoc()['ID'] or die('<script>alert("Impossibile completare l\'ordine, error=2")</script>');

        // Ottengo gli id dei prodotti nel carrello
        $ids = [];
        for($i=0; $i<query_num_prods($dbconn); $i++){
            if(isset($_SESSION['userCart'][$i])){
                if($_SESSION['userCart'][$i] > 0){
                    $ids[count($ids)] = $i;
                }
            }
        }

        $prods = query_products_in($dbconn, $ids);

        for($i=0; $i<count($ids); $i++){
            $p = $prods->fetch_assoc();
            $order_detail = '
                INSERT INTO orderdetails (id_product, id_order, unitPrice, quantity, discount) VALUES ('.$ids[$i].', '.$order_id.', '.$p['unitPrice'].', '.$_SESSION['userCart'][$ids[$i]].', 0.0);
            ';
            if(mysqli_query($dbconn, $order_detail) == false){
                echo mysqli_error($dbconn).'<br>';
                $del_order = '
                    DELETE FROM orders WHERE ID='.$order_id.' AND date=\''.$current_date.'\' and id_customer='.$_SESSION['ID'].'
                ';
                mysqli_query($dbconn, $del_order);
                echo $order_detail.'<br>';
            }
        }

        $_SESSION['userCart'] = [];
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
    <body style="margin: 0px;">
        <?php
            include('../../php/header.php');
        ?>

        <ul class="horiz_list">
                <li>
                    <?php
                        $ids = [];
                        $nProducts = 0;
                        for($i=0; $i<count($_SESSION['userCart']) -1; $i++){
                            if(isset($_SESSION['userCart'][$i]) and $_SESSION['userCart'][$i] > 0){
                                array_push($ids, $i);
                            }
                        }

                        if(!empty($ids)){    
                            $res = query_products_in($dbconn, $ids);
                            $nProducts = mysqli_num_rows($res);
                            $products = mysqli_fetch_all($res);
                            for($i=0; $i<$nProducts; $i++){
                                $id = $products[$i][0];
                                if(isset($_SESSION['userCart'][$id]) and $_SESSION['userCart'][$id] > 0){
                                    echo('<div>
                                        <img src=/products/'.$id.'.png width="20%" height="20%">
                                        '.$products[$i][1].' '.$products[$i][3].' €
                                        <form action="cart.php" method="post">
                                            <input type="submit" name="+'.$id.'" value="+">
                                        ');

                                        if(isset($_POST['+'.$id])){
                                            $_SESSION['userCart'][$id] += 1;
                                        }if(isset($_POST['-'.$id])){
                                            if($_SESSION['userCart'][$id] > 0){
                                                $_SESSION['userCart'][$id] -= 1;
                                            }
                                        }
                                        echo($_SESSION['userCart'][$id]);
                                
                                        echo('
                                                <input type="submit" name="-'.$id.'" value="-">
                                                <input type="hidden" name="csrf_token" value='.$_SESSION['csrf_token'].'>
                                            </form>
                                    </div>');
                                }
                            }
                        }
                    ?>
            </li>
            
            <li class="divider" id="divider">
            </li>
            <!-- <script>
                document.getElementById("divider").setAttribute("style", "height: "+window.innerHeight+"px");
            </script> -->
            <li>
                <div>
                    <?php
                        $tot = 0.0;
                        for($i=0; $i<$nProducts; $i++){
                            $tot += $products[$i][3] * $_SESSION['userCart'][$products[$i][0]];
                        }
                        echo('Costo:    '.$tot.' €<br/>');
                        echo('IVA:      22%<br/>');
                        echo('Totale:   '.($tot*1.22).' €<br/>');
                    ?>
                    <form action="" method="POST">
                        Metodo di pagamento:
                        <select name="payment" id="payment">
                            <option value="mastercard">MasterCard</option>
                            <option value="visa">Visa</option>
                            <option value="paypal">PayPal</option>
                            <option value="bitcoin">Bitcoin</option>
                        </select>
                        <button id="payed" name="payed">Paga ora</button>
                        <input type="hidden" name="csrf_token" value=<?php echo $_SESSION['csrf_token'] ?>>
                    </form>
                </div>
            </li>
        </ul>

        <?php include('../../php/footer.php') ?>
    </body>
</html>