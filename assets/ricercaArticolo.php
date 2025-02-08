<?php
    require_once('config.php');

    $carcere = $_POST['carcere'] ?? '';
    $tipoRicerca = $_POST['tipoRicerca'] ?? '';
    $valoreRicerca = $_POST['valoreRicerca'] ?? '';
    $risposta = array(
        "stato" => 0,
        "messaggio" => ""
    );
    $risultatiRicerca = [];
    //controllo che i dati per la ricerca siano validi
    if($tipoRicerca != '' && $valoreRicerca != ''){
        if($tipoRicerca == 'codice_carcere' || $tipoRicerca == 'codice_CSB'){
            //LA RICERCA VIENE FATTA PER I CODICI DELL'ARTICOLO
            $stmt = mysqli_stmt_init($connessione_db);
            $sql_query = "SELECT 
                                completocarceri.*,
                                carceri.carcere
                            FROM 
                                completocarceri
                            INNER JOIN 
                                carceri
                                ON completocarceri.id_carcere = carceri.codice_indirizzoCSB
                            WHERE completocarceri.$tipoRicerca = ?";
            if($carcere != 'all'){
                $sql_query .= ' AND completocarceri.id_carcere = ?'; 
            }
            mysqli_stmt_prepare($stmt, $sql_query);
            if($carcere != 'all'){
                mysqli_stmt_bind_param($stmt, 'si', $valoreRicerca, $carcere);
            } else {
                mysqli_stmt_bind_param($stmt, 's', $valoreRicerca);
            }
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_bind_result($stmt, $id, $codice_carcere, $codice_CSB, $descrizione_articolo, $rilevamento, $id_carcere, $prezzo, $scostamento, $consegnatario, $carcere);
                while(mysqli_stmt_fetch($stmt)){
                    $risultatiRicerca[$id] = array(
                        "codice_carcere" => $codice_carcere,
                        "codice_CSB" => $codice_CSB,
                        "descrizione_articolo" => strtoupper($descrizione_articolo),
                        "rilevamento" => $rilevamento,
                        "id_carcere" => $id_carcere,
                        "prezzo" => $prezzo . '€',
                        "scostamento" => $scostamento . '%',
                        "carcere" => strtoupper($carcere),
                        "consegnatario" => $consegnatario
                    );
                }
            } else {
                $risposta = array(
                    "stato" => 0,
                    "messaggio" => "Errore stmt"
                );
            }
        } else if($tipoRicerca == 'descrizione_articolo') {
            $valoreRicerca = '%' . strtolower($valoreRicerca) . '%';
            //LA RICERCA VIENE FATTA PER LA DESCRIZIONE//LA RICERCA VIENE FATTA PER I CODICI DELL'ARTICOLO
            $stmt = mysqli_stmt_init($connessione_db);
            $sql_query = "SELECT 
                                completocarceri.*,
                                carceri.carcere
                            FROM 
                                completocarceri
                            INNER JOIN 
                                carceri
                                ON completocarceri.id_carcere = carceri.codice_indirizzoCSB
                            WHERE completocarceri.$tipoRicerca LIKE ?";
            if($carcere != 'all'){
                $sql_query .= ' AND completocarceri.id_carcere = ?'; 
            }
            mysqli_stmt_prepare($stmt, $sql_query);
            if($carcere != 'all'){
                mysqli_stmt_bind_param($stmt, 'si', $valoreRicerca, $carcere);
            } else {
                mysqli_stmt_bind_param($stmt, 's', $valoreRicerca);
            }
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_bind_result($stmt, $id, $codice_carcere, $codice_CSB, $descrizione_articolo, $rilevamento, $id_carcere, $prezzo, $scostamento, $consegnatario, $carcere);
                while(mysqli_stmt_fetch($stmt)){
                    $risultatiRicerca[$id] = array(
                        "codice_carcere" => $codice_carcere,
                        "codice_CSB" => $codice_CSB,
                        "descrizione_articolo" => strtoupper($descrizione_articolo),
                        "rilevamento" => $rilevamento,
                        "id_carcere" => $id_carcere,
                        "prezzo" => $prezzo . '€',
                        "scostamento" => $scostamento . '%',
                        "carcere" => strtoupper($carcere),
                        "consegnatario" => $consegnatario
                    );
                }
            } else {
                $risposta = array(
                    "stato" => 0,
                    "messaggio" => "Errore stmt"
                );
            }
        }
    }
    if(!empty($risultatiRicerca)){
        $risposta = array(
            "stato" => 1,
            "messaggio" => $risultatiRicerca
        );
    } else {
        $risposta = array(
            "stato" => 0,
            "messaggio" => "Nessun risultato"
        );
    }
    echo json_encode($risposta);
?>