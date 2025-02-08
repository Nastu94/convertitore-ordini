<?php
require '../vendor/autoload.php';
require_once 'config.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;

$id_carcere = $_POST['carcere'] ?? '';
$dataConsegna = $_POST['dataConsegna'] ?? '';

if ($id_carcere == '32115') {
    $id_carcere = '32114';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['ordine'])) {
    // Controllare se c'è stato un errore durante il caricamento
    if ($_FILES['ordine']['error'] === UPLOAD_ERR_OK) {
        $csvFilePath = $_FILES['ordine']['tmp_name'];

        // Creare il reader per i file CSV
        $reader = new CsvReader();
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
        $header = array('quantita', 'prezzo', 'articolo', 'prezzo_totale');

        $ordine = [];

        // CREO L'ARRAY PER L'ORDINE
        foreach ($data as $row) {
            // Utilizzare array_map per rimuovere eventuali spazi bianchi dai valori, controllando null
            $row = array_map(function($value) {
                return is_null($value) ? '' : trim($value);
            }, $row);

            // Verifica se il numero di elementi corrisponde all'intestazione
            if (count($row) === count($header)) {
                $row = array_combine($header, $row);
            } else {
                continue;
            }

            if ((int)$row['quantita'] > 0) {
                // Convertire il campo 'prezzo' in float
                if (isset($row['prezzo'])) {
                    // Rimuovere tutti i caratteri non numerici eccetto il punto e la virgola
                    $row['prezzo'] = preg_replace('/[^\d,.-]/', '', $row['prezzo']);
                    // Sostituire la virgola con un punto (per compatibilità con float)
                    $row['prezzo'] = str_replace(',', '.', $row['prezzo']);
                    // Convertire in float
                    $row['prezzo'] = (float)$row['prezzo'];
                }

                // Separare 'articolo' in 'numero' e 'descrizione'
                if (isset($row['articolo']) && $row['articolo'] !== 'Genere') {
                    $articolo_parts = explode(' - ', $row['articolo'], 2);
                    if (count($articolo_parts) === 2) {
                        $row['codice_carcere'] = $articolo_parts[0];
                        $row['descrizione'] = $articolo_parts[1];
                    } else {
                        $row['codice_carcere'] = $row['articolo'];
                        $row['descrizione'] = '';
                    }
                    unset($row['articolo']); // Rimuovere il campo 'articolo' originale
                }

                // Convertire il campo 'quantita' in int
                if (isset($row['quantita'])) {
                    $row['quantita'] = (int)$row['quantita'];
                }

                // ASSEGNO I DATI MODIFICATI ALL'ARRAY
                $ordine[] = $row;
            }
        }

        // RECUPERO GLI ARTICOLI IN BASE AL CARCERE SELEZIONATO
        $stmt = mysqli_stmt_init($connessione_db);
        $sql_query = "SELECT * FROM completocarceri WHERE id_carcere = ?";
        mysqli_stmt_prepare($stmt, $sql_query);
        mysqli_stmt_bind_param($stmt, 's', $id_carcere);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_bind_result($stmt, $id_articolo, $codice_carcere, $codice_CSB, $descrizione_articolo, $rilevamento, $carcere_id, $prezzo_finale, $scostamento, $consegnatario);
            $completoCarcere = [];
            while (mysqli_stmt_fetch($stmt)) {
                $completoCarcere[$id_articolo] = array(
                    "codice_carcere" => $codice_carcere,
                    "codice_CSB" => $codice_CSB,
                    "descrizione_articolo" => strtoupper($descrizione_articolo),
                    "rilevamento" => $rilevamento,
                    "carcere_id" => $carcere_id,
                    "prezzo_finale" => $prezzo_finale,
                    "scostamento" => $scostamento,
                    "consegnatario" => $consegnatario
                );
            }
        }

        // EFFETTUO I VARI CONTROLLI
        $nonTrovati = [];
        $fornitoreEsterno = [];
        $sottocosto = [];
        $prezzoDifferente = [];
        $valoreOrdineCompleto = 0;
        $valoreOrdineCarcere = 0;

        foreach ($ordine as $id => $valori) {
            $abbinato = false;
            $prezzoUguale = true;
            $differenza = 0;
            $rilevato = true;
            $consegnatarioLadisa = false;

            foreach ($completoCarcere as $id_articolo => $dettagli) {
                // ABBINO I CODICI CARCERE
                if ($valori['codice_carcere'] == $dettagli['codice_carcere']) {
                    $abbinato = true;
                    // CONTROLLO IL CONSEGNATARIO
                    if ($dettagli['consegnatario'] === 'LADISA') {
                        $consegnatarioLadisa = true;
                        // AGGIUNGO IL CODICE CSB ALL'ARRAY DELL'ORDINE
                        $ordine[$id]['codice_CSB'] = $dettagli['codice_CSB'];
                    } else {
                        $valori['consegnatario'] = $dettagli['consegnatario'];
                        break;
                    }

                    // CONTROLLO SE RISULTA RILEVATO
                    if ($dettagli['rilevamento'] == '' && $dettagli['scostamento'] < 0) {
                        $scostamento = $dettagli['scostamento'];
                        $rilevato = false;
                    }

                    // CONTROLLO SE IL PREZZO è LO STESSO
                    if ($valori['prezzo'] != $dettagli['prezzo_finale']) {
                        $differenza = $valori['prezzo'] - $dettagli['prezzo_finale'];
                        $prezzoUguale = false;
                    }

                    $costoUnitarioCompleto = $dettagli['prezzo_finale'];

                    // SOMMO IL COSTO DELL'ARTICOLO
                    $costoProdottoCarcere = $valori['prezzo'] * $valori['quantita'];
                    $costoProdottoCompleto = $costoUnitarioCompleto * $valori['quantita'];

                    $valoreOrdineCompleto += $costoProdottoCompleto;
                    $valoreOrdineCarcere += $costoProdottoCarcere;

                    break;
                }
            }

            unset($ordine[$id]['prezzo']);
            unset($ordine[$id]['codice_carcere']);

            $_dataConsegna = new DateTime($dataConsegna);
            $_dataConsegna = $_dataConsegna->format('d/m/Y');

            $ordine[$id]['dataConsegna'] = $_dataConsegna;
            $ordine[$id]['id_carcere'] = $id_carcere;

            if (!$abbinato) {
                $nonTrovati[] = array(
                    "codice_carcere" => $valori['codice_carcere'],
                    "descrizione" => $valori['descrizione'],
                    "quantita" => $valori['quantita']
                );
                unset($ordine[$id]);
            } else {
                if (!$consegnatarioLadisa) {
                    $fornitoreEsterno[] = array(
                        "codice_carcere" => $valori['codice_carcere'],
                        "descrizione" => $valori['descrizione'],
                        "quantita" => $valori['quantita'],
                        "consegnatario" => $valori['consegnatario']
                    );
                    unset($ordine[$id]);
                }
                if (!$prezzoUguale) {
                    $prezzoDifferente[] = array(
                        "codice_CSB" => $ordine[$id]['codice_CSB'],
                        "descrizione" => $valori['descrizione'],
                        "prezzo_carcere" => $valori['prezzo'],
                        "prezzo_completo" => $costoUnitarioCompleto,
                        "differenza" => number_format($differenza, 2)
                    );
                }
                if (!$rilevato) {
                    $sottocosto[] = array(
                        "codice_CSB" => $ordine[$id]['codice_CSB'],
                        "descrizione" => $valori['descrizione'],
                        "scostamento" => $scostamento
                    );
                }
            }
        }

        // INSERIRE SALVATAGGIO FILE CSV IN BASE ALL'ARRAY $ordine
        // Ordina le colonne secondo un ordine personalizzato
        $ordered_columns = ['dataConsegna', 'id_carcere', 'quantita', 'descrizione', 'codice_CSB'];

        // Riordina i dati in base all'ordine delle colonne desiderato
        $ordered_ordine = [];
        foreach ($ordine as $row) {
            $ordered_row = [];
            foreach ($ordered_columns as $col) {
                $ordered_row[$col] = $row[$col] ?? ''; // Assicurati che l'indice esista
            }
            $ordered_ordine[] = $ordered_row;
        }

        // INSERIRE SALVATAGGIO FILE CSV IN BASE ALL'ARRAY $ordine
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Inserisci i dati
        $sheet->fromArray($ordered_ordine, null, 'A1');

        // Definire il nome del file
        $fileName = $id_carcere . '-' . str_replace('-', '', $dataConsegna) . '.csv';
        $filePath = 'C:/laragon/www/convertitore-ordini/ordiniGenerati/' . $fileName;

        // Scrivere il file CSV
        $writer = new CsvWriter($spreadsheet);
        $writer->save($filePath);

        $risposta = array(
            "stato" => 1,
            "messaggio" => "File convertito.",
            "note" => array(
                "articoli_non_trovati" => $nonTrovati,
                "sottocosto" => $sottocosto,
                "prezzoDifferente" => $prezzoDifferente,
                "fornitoreEsterno" => $fornitoreEsterno,
                "valoreOrdineCompleto" => number_format($valoreOrdineCompleto, 2),
                "valoreOrdineCarcere" => number_format($valoreOrdineCarcere, 2)
            )
        );
    } else {
        $risposta = array(
            "stato" => 0,
            "messaggio" => "Errore durante il caricamento del file."
        );
        echo json_encode($risposta);
        exit;
    }
} else {
    $risposta = array(
        "stato" => 0,
        "messaggio" => "Nessun file caricato."
    );
    echo json_encode($risposta);
    exit;
}
echo json_encode($risposta);
?>
