<?php
    require ('../vendor/autoload.php');
    require_once('config.php');

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Reader\Csv;

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['completo'])) {
        // Controllare se c'è stato un errore durante il caricamento
        if ($_FILES['completo']['error'] === UPLOAD_ERR_OK) {
            $csvFilePath = $_FILES['completo']['tmp_name'];

            // Eliminare i dati esistenti nella tabella e resettare il contatore dell'auto-incremento
            $truncate_query = "TRUNCATE TABLE completocarceri";
            if (!mysqli_query($connessione_db, $truncate_query)) {
                $risposta = array(
                    "stato" => 0,
                    "messaggio" => "Errore durante l'eliminazione dei dati esistenti: " . mysqli_error($connessione_db)
                );
                exit;
            }

            // Creare il reader per i file CSV
            $reader = new Csv();
            $reader->setDelimiter(',');
            $reader->setEnclosure('"');
            $reader->setSheetIndex(0);

            // Caricare il file CSV in un oggetto Spreadsheet
            $spreadsheet = $reader->load($csvFilePath);

            // Ottenere il foglio attivo (il primo foglio nel caso del CSV)
            $sheet = $spreadsheet->getActiveSheet();

            // Ottenere i dati come array
            $data = $sheet->toArray(null, true, true, true);

            // Convertire i dati in un array associativo
            $header = array('codice_carcere', 'codice_CSB', 'descrizione_articolo', 'rilevamento', 'consegnatario', 'id_carcere', 'prezzo_finale', 'scostamento'); 
            $completo = [];

            foreach ($data as $rowIndex => $row) {
                // Rimuovere eventuali spazi bianchi dai valori
                $row = array_map('trim', $row);
            
                // Controllare se il numero di colonne corrisponde all'intestazione
                if (count($row) !== count($header)) {
                    error_log("Errore: numero di colonne nella riga $rowIndex non corrisponde all'intestazione.");
                    continue; // Ignora la riga con un numero errato di colonne
                }
            
                // Combina i dati della riga con l'intestazione
                $row = array_combine($header, $row);
            
                // Convertire descrizione_articolo in minuscolo
                $row['descrizione_articolo'] = strtolower($row['descrizione_articolo']);
            
                // Rimuovere simboli non numerici dal prezzo_finale
                if (isset($row['prezzo_finale'])) {
                    $row['prezzo_finale'] = preg_replace('/[^\d,.-]/', '', $row['prezzo_finale']);
                    $row['prezzo_finale'] = str_replace(',', '.', $row['prezzo_finale']);
                    $row['prezzo_finale'] = number_format((float)$row['prezzo_finale'], 2, '.', '');
                }
            
                // Convertire id_carcere in codice
                $row['id_carcere'] = strtolower($row['id_carcere']);
                $stmt = mysqli_stmt_init($connessione_db);
                $sql_query = "SELECT codice_indirizzoCSB FROM carceri WHERE carcere = ?";
                mysqli_stmt_prepare($stmt, $sql_query);
                mysqli_stmt_bind_param($stmt, 's', $row['id_carcere']);
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_bind_result($stmt, $id_carcere);
                    if (mysqli_stmt_fetch($stmt)) {
                        $row['id_carcere'] = $id_carcere;
                    }
                }
            
                // Rimuovere il simbolo % da scostamento
                if (isset($row['scostamento'])) {
                    $row['scostamento'] = str_replace('%', '', $row['scostamento']);
                    $row['scostamento'] = (int)$row['scostamento'];
                }
            
                // Aggiungere la riga al completo
                $completo[] = $row;
            }            
            
            $completoCaricare = [];
            foreach ($completo as $valore) {
                if($valore['codice_carcere'] == ''){
                    $valore['codice_carcere'] = '00000';
                }
                if($valore['codice_CSB'] == ''){
                    $valore['codice_CSB'] = '00000';
                }
                $stmt = mysqli_stmt_init($connessione_db);
                $sql_query = "INSERT INTO completocarceri VALUES(NULL, ?, ?, ?, ?, ?, ?, ?, ?)";
                mysqli_stmt_prepare($stmt, $sql_query);
                mysqli_stmt_bind_param($stmt, 'sssssdis', $valore['codice_carcere'], $valore['codice_CSB'], $valore['descrizione_articolo'], $valore['rilevamento'], $valore['id_carcere'], $valore['prezzo_finale'], $valore['scostamento'], $valore['consegnatario']);
                if(mysqli_stmt_execute($stmt)){
                    $successo = true;
                } else {
                    $successo = false;
                    break;
                }
            }
            if($successo){
                $risposta = array(
                    "stato" => 1,
                    "messaggio" => "Aggiornamento Avvenuto!"
                );
            } else {
                $risposta = array(
                    "stato" => 0,
                    "messaggio" => "Errore durante l'aggiornamento" . mysqli_stmt_error($stmt)
                );
            }
        } else {
            $risposta = array(
                "stato" => 0,
                "messaggio" => "Errore durante il caricamento del file."
            );
        }
    } else {
        $risposta = array(
            "stato" => 0,
            "messaggio" => "Nessun file caricato."
        );
    }
    echo json_encode($risposta);
?>