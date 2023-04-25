<?php
    include('login_timeout.php');
    include('../../php/connect2DB.php');
    include('../../php/query_products.php');
    include('../../php/global_configs.php');
    include('../../php/csrf_token.php');

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
                $filtro_ricerca = '';
                if(isset($_GET["search"])){
                    $filtro_ricerca = $htmlpurifier->purify($_GET["search"]);
                    $querystring = $querystring.'search='.$filtro_ricerca.'&';
                }

                $page = 0;
                if(isset($_GET['page'])){
                    $page = $htmlpurifier->purify($_GET['page']);
                    $querystring = $querystring.'page='.$page.'&';
                }

                $order_by = '';
                if(isset($_GET['order_by'])){
                    $order_by = $htmlpurifier->purify($_GET['order_by']);
                    $querystring = $querystring.'order_by='.$order_by.'&';
                }
            
                echo '
                <div>
                    <form method="get" action="catalog.php">
                        <input id="searchbar" name="search" placeholder="Cerca un prodotto" value="'.$filtro_ricerca.'">
                        <select name="order_by" id="order_by">
                            <option value="" '.($order_by=='' ? 'selected' : '').'>Nessun filtro</option>
                            <option value="price_asc" '.($order_by=='price_asc' ? 'selected' : '').'>Prezzo crescente</option>
                            <option value="price_desc" '.($order_by=='price_desc' ? 'selected' : '').'>Prezzo decrescente</option>
                            <option value="popularity_desc" '.($order_by=='popularity_desc' ? 'selected' : '').'>Più acquistato</option>
                        </select>
                ';
                $n_prods = query_num_prods($dbconn);
                $n_pages = ceil($n_prods/$prods_per_page);
                for($i=0; $i<$n_pages; $i++){
                    echo '<button id="page" name="page" value="'.$i.'">'.($i+1).'</button>';
                }
                echo '
                        <input type="hidden" name="csrf_token" value='.$_SESSION['csrf_token'].'>
                    </form>
                </div>
                ';
            ?>

            <?php
                // Prendo solo i prodotti necessari alla pagina
                $products = query_products($dbconn, $page*$prods_per_page, $prods_per_page, $order_by, $filtro_ricerca);
                $nProducts = mysqli_num_rows($products);

                echo "<div>";
                echo "<table>";
                for($i=0; $i<$prods_rows; $i++){
                    echo "<tr>";
                    for($j=0; $j<$prods_cols; $j++){
                        echo "<td>";
                        if($i*$prods_cols + $j < $nProducts){                         
                            $p = mysqli_fetch_assoc($products);

                            echo('<img src=/products/'.$p['ID'].'.png width="30%" height="30%" />
                                '.ucfirst($p['name']).' '.$p['unitPrice'].' €
                                <form action="catalog.php?'.$querystring.'" method="post">
                                    <input type="submit" name="+'.$p['ID'].'" value="+">
                                    <input type="hidden" name="csrf_token" value='.$_SESSION['csrf_token'].'>
                                ');

                            if(isset($_POST['+'.$p['ID']])){
                                $_SESSION['userCart'][$p['ID']] += 1;
                            }if(isset($_POST['-'.$p['ID']])){
                                if($_SESSION['userCart'][$p['ID']] > 0){
                                    $_SESSION['userCart'][$p['ID']] -= 1;
                                }
                            }
                            if(!isset($_SESSION['userCart'][$p['ID']])){
                                $_SESSION['userCart'][$p['ID']] = 0;
                            }
                            echo($_SESSION['userCart'][$p['ID']]);
                    
                            echo('<input type="submit" name="-'.$p['ID'].'" value="-">
                                </form>');
                        }
                        echo "</td>";
                    }

                    echo "</tr>";
                    if($i*$prods_cols + $j < $nProducts)
                            continue;
                }
                $products->free_result();
                echo "</table>";
                echo "</div>";
            ?>
        </div>

        <?php include('../../php/footer.php') ?>
    </body>
</html>