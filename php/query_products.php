<?php
    // Restituisce n_ids prodotti a partire dall'id start_id
    // nell'ordine specificato dal parametro order_by
    // Questo evita di sovraccaricare il dbms
    // Restituisce il set di risultati o false in caso di errore
    // Gli id partono da 0 (LIMIT x,y)
    function query_products($dbms, $start_id, $n_ids, $order_by=''){;
        $sel_prods = "";
        // Ordine dei prodotti
        switch($order_by){
            case 'price_asc':
                $sel_prods = 'select * from products order by unitPrice asc limit ?, ?;';
                break;
            case 'price_desc':
                $sel_prods = 'select * from products order by unitPrice desc limit ?, ?;';
                break;

            case 'popularity_desc':
                $sel_prods = 'SELECT pop_desc.ID, pop_desc.qta, products.name, products.quantityPerUnit, products.unitPrice, products.unitsInStock FROM
                (
                    (SELECT t2.ID, IFNULL(qta, 0) as qta FROM
                        (SELECT ID,0 FROM products) AS t2
                        LEFT JOIN
                        (SELECT products.ID, SUM(quantity) as qta FROM
                            (
                                (
                                    (
                                        customers INNER JOIN orders ON(customers.ID=orders.id_customer)
                                    )
                                    INNER JOIN orderdetails ON(orders.ID=orderdetails.id_order)
                                )
                                INNER JOIN products ON(products.ID=orderdetails.id_product)
                            )
                            GROUP BY id_product
                        ) AS t1
                        ON (t1.ID=t2.ID)
                    ) AS pop_desc
                    LEFT JOIN products ON (pop_desc.ID=products.ID)
                ) ORDER BY qta DESC LIMIT ?, ?';
                break;
            
            // todo: Ordina per popolarità
            default:
                $sel_prods = 'select * from products limit ?, ?;';
                break;
        }

        $stmt = mysqli_prepare($dbms, $sel_prods);
        $stmt->bind_param("ii", $start_id, $n_ids);
        if($stmt->execute() == false){
            return false;
        }
        $res = $stmt->get_result();
        // for($i=0; $i<$res->num_rows; $i++){
        //     print_r($res->fetch_assoc());   
        // }
        return $res;
    }

    // Restituisce i prodotti con id contenuto nel vettore $ids
    // Questo evita di sovraccaricare il dbms
    // Restituisce il set di risultati o false in caso di errore
    // Gli id partono da 1
    function query_products_in($dbms, $ids){
        $sel_prods = "select * from products where id in (?".str_repeat(",?", count($ids) -1) .");";
        $stmt = mysqli_prepare($dbms, $sel_prods);
        $stmt->bind_param(str_repeat("i", count($ids)), ...$ids);
        if($stmt->execute() == false){
            return false;
        }
        $res = $stmt->get_result();
        return $res;
    }

    // Restituisce il numero di prodotti presenti nel db,
    // utile per calcolare le pagine etc
    function query_num_prods($dbms){
        $q = "select count(*) as n from products;";
        $res = mysqli_query($dbms, $q);
        $n = ($res->fetch_assoc())['n'];
        return $n;
    }

    // WIP !!!
    // Restituisce lo storico degli acquisti di un'utente dal DB
    function query_purchase_history($dbms){
        $q = '
        SELECT orders.ID, orders.date, orderdetails.unitPrice, orderdetails.quantity, products.name FROM
        (
            (
                (
                    customers INNER JOIN orders ON(customers.ID=orders.id_customer)
                )
                INNER JOIN orderdetails ON(orders.ID=orderdetails.id_order)
            )
            INNER JOIN products ON(products.ID=orderdetails.id_product)
        )
        WHERE customers.email="gd@mail.com";
        ';

        $res = mysqli_query($dbms, $q);
        return $res->fetch_all();
    }

    // Restituisce un array associativo con
    // gli id e le date degli ordini (metadati)
    function query_purchase_history_orders($dbms){
        $q = '
        SELECT orders.ID, orders.date FROM
        (
            customers INNER JOIN orders ON(customers.ID=orders.id_customer)
        )
        WHERE customers.email="gd@mail.com";
        ';

        $res = mysqli_query($dbms, $q);
        return $res->fetch_all();
    }
?>