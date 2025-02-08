# Convertitore Ordini

## Descrizione

**Convertitore Ordini** è un'applicazione PHP progettata per semplificare il processo di conversione degli ordini ricevuti in formato Excel in file CSV compatibili con il gestionale aziendale. L'applicazione estrae solo i campi necessari e genera un file pronto per l'importazione.

Inoltre, utilizza un file di dipendenze che mappa i codici prodotto dell'ordine ricevuto ai corrispondenti codici prodotto usati nel gestionale, garantendo la corretta conversione dei dati.

L'applicazione offre anche funzionalità avanzate di verifica e reportistica:
- **Report sui prodotti non convertiti**: Identifica eventuali prodotti per i quali non è stata trovata una corrispondenza nel file delle dipendenze.
- **Verifica delle discrepanze di prezzo**: Confronta i prezzi presenti nell'ordine con quelli da contratto, segnalando eventuali differenze.
- **Gestione dei prodotti sottocosto**: Segnala i prodotti il cui prezzo è inferiore al costo di acquisto, permettendo un migliore controllo sulle vendite in perdita.

## Requisiti

- **PHP 8.3** o superiore
- **Composer** (per la gestione delle dipendenze PHP)

## Installazione

1. **Clona la repository**:

   ```sh
   git clone https://github.com/Nastu94/convertitore-ordini.git
   cd convertitore-ordini
   ```

2. **Installa le dipendenze**: Se il progetto utilizza Composer per la gestione delle dipendenze, esegui:

   ```sh
   composer install
   ```

   *Nota*: Se non c'è un file `composer.json`, puoi saltare questo passaggio.

3. **Configura il progetto**:

   - Se sono necessarie configurazioni specifiche, crea o modifica i file di configurazione appropriati.
   - Assicurati che le estensioni PHP richieste siano abilitate nel tuo `php.ini`.

## Utilizzo

1. **Avvia il server PHP integrato** (se applicabile):

   ```sh
   php -S localhost:8000
   ```

   *Nota*: Questo comando avvia un server web locale all'indirizzo `http://localhost:8000`. Assicurati che il punto di ingresso dell'applicazione (ad esempio, `index.php`) sia nella directory corrente o specifica il percorso corretto.

2. **Accedi all'applicazione**: Apri il browser e naviga all'indirizzo `http://localhost:8000` (o l'URL configurato) per utilizzare l'applicazione.

## Funzionalità

- **Importazione**: Caricamento di file di ordini in formato Excel.
- **Conversione**: Trasformazione dei dati in formato CSV compatibile con il gestionale aziendale.
- **Esportazione**: Download dei file convertiti.
- **Gestione dipendenze prodotti**: Associazione automatica tra codici prodotto dell'ordine originale e codici prodotto del gestionale tramite un file di dipendenze.
- **Report prodotti non convertiti**: Identificazione e segnalazione di prodotti senza corrispondenza nel gestionale.
- **Verifica discrepanze di prezzo**: Confronto tra i prezzi dell'ordine e i prezzi da contratto.
- **Gestione prodotti sottocosto**: Individuazione e segnalazione di prodotti venduti a un prezzo inferiore al costo di acquisto.

## Struttura del Progetto

- `index.php`: Punto di ingresso principale dell'applicazione.
- `assets/`: Contiene risorse come immagini o file statici.
- `css/`: Fogli di stile CSS.
- `script/`: Script JavaScript.
- `vendor/`: Dipendenze PHP gestite da Composer (se applicabile).
- `mappings/`: Contiene il file delle dipendenze tra i codici prodotto ricevuti e quelli del gestionale.

## Contribuire

Se desideri contribuire al progetto:

1. Fai un **fork** della repository.
2. Crea un **branch** per la tua feature o correzione:
   ```sh
   git checkout -b nome-feature
   ```
3. **Commit** delle tue modifiche:
   ```sh
   git commit -m "Descrizione della modifica"
   ```
4. **Push** del branch:
   ```sh
   git push origin nome-feature
   ```
5. Apri una **Pull Request** su GitHub.

## Licenza

Questo progetto è distribuito sotto licenza MIT. Consulta il file `LICENSE` per maggiori dettagli.

---

**Autore**: [Nastu94](https://github.com/Nastu94)

