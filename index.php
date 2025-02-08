<?php
    require_once('assets/config.php');
    require_once('assets/selectCompleto.php');
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convertitore ordini</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/a9ab42b9cf.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container pt-2">
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-convertitore-tab" data-bs-toggle="pill" data-bs-target="#pills-convertitore" type="button" role="tab" aria-controls="pills-convertitore" aria-selected="true">CONVERTITORE</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-tabella-tab" data-bs-toggle="pill" data-bs-target="#pills-tabella" type="button" role="tab" aria-controls="pills-tabella" aria-selected="false">TABELLA</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-aggiornaCompleto-tab" data-bs-toggle="pill" data-bs-target="#pills-aggiornaCompleto" type="button" role="tab" aria-controls="pills-aggiornaCompleto" aria-selected="false">AGGIORNA COMPLETO</button>
            </li>
        </ul>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-convertitore" role="tabpanel" aria-labelledby="pills-convertitore-tab">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Converti ordine</h2>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="form-convertiOrdine" method="post" enctype="multipart/form-data">
                            <div class="row d-flex justify-content-center pt-2">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label for="ordine">File Ordine</label>
                                        <input type="file" class="form-control" name="ordine" id="ordine" accept=".csv" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-center pt-2">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="carcere">Carcere</label>
                                        <select name="carcere" class="form-select" id="carcere" required>
                                            <option value="">Seleziona un carcere...</option>
                                            <?php require('assets/optionCarceri.php'); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="dataConsegna">Data Consegna</label>
                                        <input type="date" name="dataConsegna" class="form-control" id="dataConsegna" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group pt-2 d-flex justify-content-center">
                                <button type="submit" id="btn-convertiOrdine" class="btn btn-primary">Converti</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div id="note-ordine">
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="pills-tabella" role="tabpanel" aria-labelledby="pills-tabella-tab">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Completo</h2>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="ricercaArticoli" class="d-flex justify-content-around">
                            <form method="post" id="form-ricercaArticoli" class="row row-cols-lg-auto g-3 align-items-center">
                                <div class="col-12">
                                    <label class="visually-hidden" for="carcere-search">Carcere</label>
                                    <select name="carcere" class="form-select" id="carcere-search" required>
                                        <option value="">Seleziona un carcere...</option>
                                        <option value="all">Tutti i carceri</option>
                                        <?php require('assets/optionCarceri.php'); ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="visually-hidden" for="tipoRicerca">Tipo Ricerca</label>
                                    <select class="form-select" id="tipoRicerca" name="tipoRicerca" required>
                                        <option selected>Scegli cosa cercare...</option>
                                        <option value="codice_carcere">Codice Carcere</option>
                                        <option value="codice_CSB">Codice CSB</option>
                                        <option value="descrizione_articolo">Articolo</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="visually-hidden" for="valoreRicerca">Valore</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="valoreRicerca" name="valoreRicerca" placeholder="cerca per..." required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Ricerca</button>
                                </div>
                            </form>
                            <button id="reset-ricerca" class="btn btn-secondary btn-sm m-1">Elimina Ricerca</button>
                        </div>
                        <?php require_once('assets/tabellaCompleto.php'); ?>
                        
                        <!-- Markup della paginazione di Bootstrap -->
                        <nav aria-label="Page navigation">
                            <ul id='pagination-completo' class="pagination">
                                <!-- I pulsanti della paginazione verranno aggiunti qui con JavaScript -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="pills-aggiornaCompleto" role="tabpanel" aria-labelledby="pills-aggiornaCompleto-tab">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Aggiorna Completo</h2>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="form-aggiornaCompleto" method="post" enctype="multipart/form-data">
                            <div class="row d-flex justify-content-center pt-2">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label for="completo">File Completo</label>
                                        <input type="file" class="form-control" name="completo" id="completo" accept=".csv" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group pt-2 d-flex justify-content-center">
                                <button type="submit" id="btn-aggiornaCompleto" class="btn btn-primary">Aggiorna</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modale per modificare l'articolo -->
    <div class="modal fade" id="modaleModificaArticolo" tabindex="-1" role="dialog" aria-labelledby="modaleModificaArticoloLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modaleModificaArticoloLabel">Modifica Articolo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form-ModificaArticolo" method='post'>
                        <input type="hidden" id="id_database" name="id_database">
                        <div class="row">
                            <div class="col-md-6 form-group p-2">
                                <label for="codice_carcere">Codice Carcere</label>
                                <input type="text" class="form-control" id="codice_carcere" name="codice_carcere" required>
                            </div>
                            <div class="col-md-6 form-group p-2">
                                <label for="codice_CSB">Codice CSB</label>
                                <input type="text" class="form-control" id="codice_CSB" name="codice_CSB" required>
                            </div>
                            <div class="col-md-12 form-group p-2">
                                <label for="descrizione_articolo">Descrizione Articolo</label>
                                <input type="text" class="form-control" id="descrizione_articolo" name="descrizione_articolo" required>
                            </div>
                            <div class="col-md-6 form-group p-2">
                                <label for="rilevamento">Rilevamento</label>
                                <input type="text" class="form-control" id="rilevamento" name="rilevamento">
                            </div>
                            <div class="col-md-6 form-group p-2">
                                <label for="carcere-modale">Carcere</label>
                                <select name="carcere-modale" class="form-select" id="carcere-modale" required>
                                    <option value="">Scegli un carcere...</option>
                                    <?php require('assets/optionCarceri.php'); ?>
                                </select>
                            </div>
                            <div class="col-md-6 form-group p-2">
                                <label for="prezzo">Prezzo (in euro)</label>
                                <input type="text" class="form-control" id="prezzo" name="prezzo" required>
                            </div>
                            <div class="col-md-6 form-group p-2">
                                <label for="scostamento">Scostamento (percentuale)</label>
                                <input type="text" class="form-control" id="scostamento" name="scostamento" required>
                            </div>
                            <div class="form-group pt-2 d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary">Salva modifiche</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="form-group pt-2 d-flex justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="script/scripts.js"></script>

</body>
</html>