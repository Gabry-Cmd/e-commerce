<?php
    include('login_timeout.php');
    include('../../php/query_products.php');
    include('../../php/connect2DB.php');
    include('../../php/global_configs.php');

    isset($_SESSION['email']) or die('Non autorizzato');
?>

<?php
    if(!isset($_SESSION['userCart']) or isset($_POST['payed'])){
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
                                            <input type="hidden" value="1">
                                        </form>');

                                        if(isset($_POST['+'.$id])){
                                            $_SESSION['userCart'][$id] += 1;
                                        }if(isset($_POST['-'.$id])){
                                            if($_SESSION['userCart'][$id] > 0){
                                                $_SESSION['userCart'][$id] -= 1;
                                            }
                                        }
                                        echo($_SESSION['userCart'][$id]);
                                
                                        echo('<form action="cart.php" method="post">
                                                <input type="submit" name="-'.$id.'" value="-">
                                                <input type="hidden" value="1">
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
                    </form>
                </div>
            </li>
        </ul>

        <?php include('../../php/footer.php') ?>
    </body>
</html>