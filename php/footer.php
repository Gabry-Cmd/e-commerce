<?php
    $today = getdate();
    echo '<link rel="stylesheet" href="../css/footer.css">';
    echo '<footer id="footer" style="display: flex; justify-content: space-between;">';
    echo($today["year"]."-".($today["year"]+1));
    echo '</footer>';

    // $_SESSION['lastpage'] = $_SERVER['HTTP_REFERER'];
?>