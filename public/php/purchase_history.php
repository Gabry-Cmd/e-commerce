<?php
    include('login_timeout.php');
    include('../../php/connect2DB.php');
    include('../../php/query_products.php');
    include('../../php/global_configs.php');

    isset($_SESSION['email']) or die('Non autorizzato');

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
                $rows = query_purchase_history($dbconn);
                for($i=0; $i<count($rows); $i++){
                    echo 'Ordine #'.$i.' ('.$rows[$i][0].'): '.$rows[$i][3].' x '.$rows[$i][2].' da '.$rows[$i][1].' €<br>';
                }
            ?>
        </div>

        <?php include('../../php/footer.php') ?>
    </body>
</html>