<?php
    include('login_timeout.php');
    include('../../php/connect2DB.php');
    include('../../php/query_products.php');
    include('../../php/global_configs.php');
    include('../../php/csrf_token.php');

    isset($_SESSION['email']) or die('E\' necessario accedere per poter utilizzare questa funzionalità!');
    csrf_token_validate_request();

    $querystring = '';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="../css/header.css">
        <link rel="stylesheet" href="../css/searchbar.css">
        <title>Azienda & Logo</title>
    </head>
    <body style="margin: 0px;">
        <?php
            include('../../php/header.php');
        ?>
        <div>
            <?php
                $orders_meta = query_purchase_history_orders($dbconn);
                $rows = query_purchase_history($dbconn);
                for($id=0; $id<count($orders_meta); $id++){
                    $tot = 0;
                    
                    echo "<div align='center'>";
                    echo 'Ordine #'.$orders_meta[$id][0].' ('.$orders_meta[$id][1].'):<br>';
                    for($i=0; $i<count($rows); $i++){
                        if($rows[$i][0] == $orders_meta[$id][0]){
                            echo $rows[$i][4].' x '.$rows[$i][3].' da '.$rows[$i][2].' €<br>';
                            $tot += $rows[$i][2];
                        }
                    }
                    echo '<b>Totale: '.$tot.' €</b>';
                    echo "</div><br>";
                }
            ?>
        </div>

        <?php include('../../php/footer.php') ?>
    </body>
</html>