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
        <link rel="stylesheet" href="../css/fill_empty_space.css">
        <link rel="stylesheet" href="../css/purchase_history.css">
        <title>Azienda & Logo</title>
    </head>
    <body class="box">
        <div class="row header">
            <?php
                include('../../php/header.php');
            ?>
        </div>
        <div class="row content item">
            <?php
                $orders_meta = query_purchase_history_orders($dbconn);
                $rows = query_purchase_history($dbconn);
                for($id=0; $id<count($orders_meta); $id++){
                    $tot = 0;
                    
                    echo "<div align='center'>";
                    echo '<div>Ordine #'.($id+1).'<br>('.$orders_meta[$id][1].'):</div>';
                    echo '<div>';
                    for($i=0; $i<count($rows); $i++){
                        if($rows[$i][0] == $orders_meta[$id][0]){
                            echo "<a href='/php/catalog.php?search=".$rows[$i][4]."'>".$rows[$i][4]."</a>".' x '.$rows[$i][3].' da '.$rows[$i][2].' €<br>';
                            $tot += $rows[$i][2];
                        }
                    }
                    echo '</div>';
                    echo '<div><b>Totale: '.$tot.' €</b></div>';
                    echo "</div><br>";
                }
            ?>  
        </div>
        <div class="row footer">
            <?php include('../../php/footer.php') ?>
        </div>
    </body>
</html>