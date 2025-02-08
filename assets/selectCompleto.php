<?php
    require_once('config.php');
    
    $completo = [];
    $stmt = mysqli_stmt_init($connessione_db);
    $sql_query = "SELECT 
                    completocarceri.*, 
                    carceri.carcere
                FROM 
                    completocarceri
                INNER JOIN 
                    carceri
                    ON completocarceri.id_carcere = carceri.codice_indirizzoCSB";
    mysqli_stmt_prepare($stmt, $sql_query);
    if(mysqli_stmt_execute($stmt)){
        mysqli_stmt_bind_result($stmt, $id_database, $codice_carcere, $codice_CSB, $descrizione_articolo, $rilevamento, $id_carcere, $prezzo, $scostamento, $consegnatario, $carcere);

        while(mysqli_stmt_fetch($stmt)){
            $completo[$id_database] = array(
                "codice_carcere" => $codice_carcere,
                "codice_CSB" => $codice_CSB,
                "descrizione_articolo" => $descrizione_articolo,
                "rilevamento" => $rilevamento,
                "id_carcere" => $id_carcere,
                "prezzo" => $prezzo,
                "scostamento" => $scostamento,
                "carcere" => strtoupper($carcere),
                "consegnatario" => $consegnatario
            );
        }
    }
?>