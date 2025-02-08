<?php
    $stmt = mysqli_stmt_init($connessione_db);
    $sql_query = "SELECT * FROM carceri ORDER BY codice_indirizzoCSB ASC";
    mysqli_stmt_prepare($stmt, $sql_query);
    if(mysqli_stmt_execute($stmt)){
        mysqli_stmt_bind_result($stmt, $id_carcere, $carcere, $codice_indirizzoCSB);

        $carceri = [];
        while(mysqli_stmt_fetch($stmt)){
            $carceri[$id_carcere] = array(
                "carcere" => ucwords($carcere),
                "codice_indirizzoCSB" => $codice_indirizzoCSB
            );
        }        
    } else {
        echo "Errore stmt";
        exit;
    }

    foreach($carceri as $valore){
        echo '<option value="'.$valore['codice_indirizzoCSB'].'">'.$valore['carcere'].'</option>';
    }
?>