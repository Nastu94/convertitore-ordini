<?php
    require_once('config.php');

    $id_database = intval($_POST['id_database']) ?? 0;
    $articolo = [];

    if($id_database != 0){
        $stmt = mysqli_stmt_init($connessione_db);
        $sql_query = "SELECT * FROM completocarceri WHERE id = ?";
        mysqli_stmt_prepare($stmt, $sql_query);
        mysqli_stmt_bind_param($stmt, 'i', $id_database);
        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_bind_result($stmt, $id_articolo, $codice_carcere, $codice_CSB, $descrizione_articolo, $rilevamento, $id_carcere, $prezzo, $scostamento, $consegnatario);
            
            if(mysqli_stmt_fetch($stmt)){
                $articolo = array(
                    "id_database" => $id_articolo,
                    "codice_carcere" => $codice_carcere,
                    "codice_CSB" => $codice_CSB,
                    "descrizione_articolo" => strtoupper($descrizione_articolo),
                    "rilevamento" => $rilevamento,
                    "id_carcere" => $id_carcere,
                    "prezzo" => $prezzo,
                    "scostamento" => $scostamento,
                    "consegnatario" => $consegnatario
                );
            }
        } else {
            echo "errore stmt";
        }
    }

    echo json_encode($articolo);

?>