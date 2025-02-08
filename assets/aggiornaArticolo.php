<?php
    require_once('config.php');
    

    $datiAggiornamento = array(
        'id_database' => intval($_POST['id_database']) ?? 0,
        'codice_carcere' => $_POST['codice_carcere'] ?? "",
        'codice_CSB' => $_POST['codice_CSB'] ?? "",
        'descrizione_articolo' => strtoupper($_POST['descrizione_articolo']) ?? "",
        'rilevamento' => $_POST['rilevamento'] ?? "",
        'id_carcere' => $_POST['carcere-modale'] ?? "",
        'prezzo' => $_POST['prezzo'] ?? "",
        'scostamento' => $_POST['scostamento'] ?? ""
    );

    $compilati = true;
    foreach($datiAggiornamento as $valori){
        if($valori == ''){
            $compilati = false;
            break;
        }
    }

    if(!$compilati){
        $risposta = array(
            "stato" => 0,
            "messaggio" => "Compila tutti i campi"
        );
        echo json_encode($risposta);
        exit;
    }

    $stmt = mysqli_stmt_init($connessione_db);
    $sql_query = "UPDATE 
                        completocarceri 
                    SET 
                        codice_carcere = ?, 
                        codice_CSB = ?, 
                        descrizione_articolo = ?, 
                        rilevamento = ?,
                        id_carcere = ?,
                        prezzo_finale = ?,
                        scostamento = ?
                    WHERE 
                        id = ?";
    mysqli_stmt_prepare($stmt, $sql_query);
    mysqli_stmt_bind_param($stmt, "sssssdii", $datiAggiornamento['codice_carcere'], $datiAggiornamento['codice_CSB'], $datiAggiornamento['descrizione_articolo'], $datiAggiornamento['rilevamento'], $datiAggiornamento['id_carcere'], $datiAggiornamento['prezzo'], $datiAggiornamento['scostamento'], $datiAggiornamento['id_database']);
    if(mysqli_stmt_execute($stmt)){
        $risposta = array(
            "stato" => 1,
            "messaggio" => "Aggiornamento Avvenuto"
        );
    } else {
        $risposta = array(
            "stato" => 0,
            "messaggio" => "Errore nell'aggiornamento"
        );
    }

    echo json_encode($risposta);

?>