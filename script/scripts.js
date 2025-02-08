////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////FUNZIONI PER ORDINARE LE RIGHE DELLE TABELLE////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$(document).ready(function() {
    var numRighe = 50; // Numero di righe per pagina
    var ordineOriginaleNoleggi = $("#tabella-completo tbody tr").toArray();
    var ricercaEffettuata = false; // Variabile per controllare se è stata effettuata una ricerca
    var ordineRicerca = []; // Variabile per mantenere l'ordinamento dopo la ricerca

    function aggiornaPagina(table, pagina) {
        var start = (pagina - 1) * numRighe;
        var end = start + numRighe;
        table.find('tbody tr').hide().slice(start, end).show();

        var paginationId = '';
        if(table.attr('id') === 'tabella-completo') {
            paginationId = '#pagination-completo';
        }
        aggiornaPaginazione(table.find('tbody tr').length, paginationId, pagina);
    }

    function aggiornaPaginazione(numTotaliRighe, paginationId, paginaCorrente) {
        var numPagine = Math.ceil(numTotaliRighe / numRighe);
        $(paginationId).empty();

        // Pulsante prima pagina
        if (paginaCorrente > 1) {
            $(paginationId).append('<li class="page-item"><a class="page-link" href="#">' + 1 + '</a></li>');
        }

        // Puntini per pagine precedenti
        if (paginaCorrente > 3) {
            $(paginationId).append('<li class="page-item"><span class="page-link">...</span></li>');
        }

        // Pulsante pagina precedente
        if (paginaCorrente > 2) {
            $(paginationId).append('<li class="page-item"><a class="page-link" href="#">' + (paginaCorrente - 1) + '</a></li>');
        }

        // Pulsante pagina corrente
        $(paginationId).append('<li class="page-item active"><a class="page-link" href="#">' + paginaCorrente + '</a></li>');

        // Pulsante pagina successiva
        if (paginaCorrente < numPagine - 1) {
            $(paginationId).append('<li class="page-item"><a class="page-link" href="#">' + (paginaCorrente + 1) + '</a></li>');
        }

        // Puntini per pagine successive
        if (paginaCorrente < numPagine - 2) {
            $(paginationId).append('<li class="page-item"><span class="page-link">...</span></li>');
        }

        // Pulsante ultima pagina
        if (paginaCorrente < numPagine) {
            $(paginationId).append('<li class="page-item"><a class="page-link" href="#">' + numPagine + '</a></li>');
        }
    }

    // Mostra solo le prime 50 righe al caricamento della pagina
    $("#tabella-completo tbody tr").hide().slice(0, numRighe).show();

    var numTotaliRigheNoleggi = $("#tabella-completo tbody tr").length;
    aggiornaPaginazione(numTotaliRigheNoleggi, '#pagination-completo', 1);

    $('#pagination-completo').on('click', 'a', function(e) {
        e.preventDefault();
        var paginaPrenotazioni = $(this).text();
        aggiornaPagina($('#tabella-completo'), parseInt(paginaPrenotazioni));
    });

    $(document).on('click', 'table.sortable .sortable', function() {
        var th = $(this);
        var table = th.closest('table');
        if (table.attr('id') !== 'tabella-completo') {
            return; // Applicare solo a tabella-completo
        }
        var index = th.index();
        var reset = false;
        var prev = table.data('prev');

        if (th.is(prev)) {
            var clickCount = th.data('clickCount') || 0;
            if (clickCount === 2) {
                reset = true;
                th.data('clickCount', 0);
            } else {
                th.data('clickCount', clickCount + 1);
            }
        } else {
            table.data('prev', th);
            th.data('clickCount', 1);
        }

        if (reset) {
            if (ricercaEffettuata) {
                table.find('tbody').empty().append(ordineRicerca);
            } else {
                table.find('tbody').empty().append(ordineOriginaleNoleggi);
            }
            resetAllSortIcons(table);
            aggiornaPagina(table, 1);
            aggiornaPaginazione(table.find('tbody tr').length, '#pagination-completo', 1);
        } else {
            var asc = !th.data('asc');
            th.data('asc', asc);
            var rows = getSortedRows(table, index, asc);
            table.find('tbody').empty().append(rows);
            updateSortIcon(th, asc);
            aggiornaPagina(table, 1);
            aggiornaPaginazione(table.find('tbody tr').length, '#pagination-completo', 1);
            if (ricercaEffettuata) {
                ordineRicerca = table.find('tbody tr').toArray();
            }
        }
    });

    function resetAllSortIcons(table) {
        table.find('.sortable i').removeClass('fa-sort-asc fa-sort-desc').addClass('fa-sort');
        table.find('.sortable').data('asc', null).data('clickCount', 0);
    }

    function getSortedRows(table, index, asc) {
        return table.find('tr:gt(0)').toArray().sort(function(a, b) {
            var valA = getCellValue(a, index);
            var valB = getCellValue(b, index);

            var isNumA = !isNaN(valA);
            var isNumB = !isNaN(valB);

            if (isNumA && isNumB) {
                return compareNumbers(valA, valB, asc);
            } else {
                return compareStrings(valA, valB, asc);
            }
        });
    }

    function compareNumbers(a, b, asc) {
        a = parseFloat(a);
        b = parseFloat(b);
        return asc ? a - b : b - a;
    }

    function compareStrings(a, b, asc) {
        if (asc) {
            return a.localeCompare(b);
        } else {
            return b.localeCompare(a);
        }
    }

    function updateSortIcon(th, asc) {
        resetSortIcon(th);
        th.find('i').removeClass('fa-sort fa-sort-asc fa-sort-desc')
        .addClass(asc ? 'fa-sort-asc' : 'fa-sort-desc');
    }

    function resetSortIcon(th) {
        th.siblings().find('i').removeClass('fa-sort-asc fa-sort-desc').addClass('fa-sort');
    }

    function getCellValue(row, index) {
        var cellValue = $(row).children('td').eq(index).text();
        return $.trim(cellValue).replace('%', '');
    }

    // Funzione di ricerca integrata
    $(document).on("submit", "#form-ricercaArticoli", function(event){
        event.preventDefault();
        ricercaEffettuata = true; // Imposta la variabile a true quando viene effettuata una ricerca
        $.ajax({
            url: 'http://localhost/convertitore-ordini/assets/ricercaArticolo.php',
            type: 'post',
            data: $(this).serialize(),
            dataType: "json",
            success: function(response) {
                switch(parseInt(response.stato)) {
                    case 0: // SEGNALAZIONE ERRORE
                        alert(response.messaggio);
                    break;
                    case 1: // SEGNALAZIONE SUCCESSO
                    $('#tabella-completo tbody').empty();
                    
                    $.each(response.messaggio, function(index, elemento) {
                        var tablerowHTML = '<tr class="clickable-row" data-id_database="' + index + '">';
                            tablerowHTML += '<td>' + index + '</td>';
                            tablerowHTML += '<td>' + elemento.codice_carcere + '</td>';
                            tablerowHTML += '<td>' + elemento.codice_CSB + '</td>';
                            tablerowHTML += '<td>' + elemento.descrizione_articolo + '</td>';
                            tablerowHTML += '<td>' + elemento.rilevamento + '</td>';
                            tablerowHTML += '<td>' + elemento.carcere + '</td>';
                            tablerowHTML += '<td>' + elemento.prezzo + '</td>';
                            tablerowHTML += '<td>' + elemento.scostamento + '</td>';
                            tablerowHTML += '<td>' + elemento.consegnatario + '</td>';
                        tablerowHTML += '</tr>';
                        $('#tabella-completo tbody').append(tablerowHTML);
                    });
                    ordineRicerca = $('#tabella-completo tbody tr').toArray(); // Salva l'ordine della ricerca
                    aggiornaPaginazione($('#tabella-completo tbody tr').length, '#pagination-completo', 1);
                    aggiornaPagina($('#tabella-completo'), 1);
                    break;
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log("Si è verificato un errore.");
                console.log("Stato della richiesta: " + textStatus);
                console.log("Errore: " + errorThrown);
            }
        });
    });

    // Funzione per eliminare la ricerca e ripristinare l'ordine originale
    $('#reset-ricerca').on('click', function() {
        ricercaEffettuata = false; // Resetta la variabile di ricerca
        $('#tabella-completo tbody').empty().append(ordineOriginaleNoleggi);
        aggiornaPaginazione(ordineOriginaleNoleggi.length, '#pagination-completo', 1);
        aggiornaPagina($('#tabella-completo'), 1);
    });
});

//AGGIORNA COMPLETO
$(document).on('submit', '#form-aggiornaCompleto', function(event){
    event.preventDefault();    
    var formData = new FormData(this); // Crea un oggetto FormData dal modulo

    $.ajax({
        url: "http://localhost/convertitore-ordini/assets/aggiornaCompleto.php",
        type: "POST",
        data: formData,
        dataType: "json",
        processData: false,
        contentType: false,
        success: function(response) {
            switch(parseInt(response.stato)) {
                case 0: // SEGNALAZIONE ERRORE
                    alert(response.messaggio);
                    break;
                case 1: // SEGNALAZIONE SUCCESSO
                    alert(response.messaggio);
                    setTimeout(function() {
                        location.reload();
                    }, 1750);
                    break;
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log("Si è verificato un errore.");
            console.log("Stato della richiesta: " + textStatus);
            console.log("Errore: " + errorThrown);
        }
    });
})

//CONVERTI ORDINE
$(document).on('submit', '#form-convertiOrdine', function(event){
    event.preventDefault();
    // Crea un oggetto FormData dal modulo
    var formData = new FormData(this); 

    $.ajax({
        url: "http://localhost/convertitore-ordini/assets/convertiOrdine.php",
        type: "POST",
        data: formData,
        dataType: "json",
        processData: false,
        contentType: false,
        success: function(response) {
            switch(parseInt(response.stato)) {
                case 0: // SEGNALAZIONE ERRORE
                    alert(response.messaggio);
                    break;
                case 1: // SEGNALAZIONE SUCCESSO
                    alert(response.messaggio);

                    //inserire generazione sezione con le note nell'html nel div#note-ordine
                    var noteHtml = '<h3>Note</h3>';
                    noteHtml += '<p>Valore Ordine Carcere: ' + response.note.valoreOrdineCarcere + '</p>';
                    noteHtml += '<p>Valore Ordine Completo: ' + response.note.valoreOrdineCompleto + '</p>';

                    if(response.note.articoli_non_trovati.length > 0) {
                        noteHtml += '<h4>Articoli Non Trovati:</h4>';
                        noteHtml += '<ul>';
                        response.note.articoli_non_trovati.forEach(function(item) {
                            noteHtml += '<li>' + item.codice_carcere + ': ' + item.descrizione + ', richiesti '+item.quantita+'</li>';
                        });
                        noteHtml += '</ul>';
                    }

                    if(response.note.prezzoDifferente.length > 0) {
                        noteHtml += '<h4>Prezzo Differente:</h4>';
                        noteHtml += '<ul>';
                        response.note.prezzoDifferente.forEach(function(item) {
                            noteHtml += '<li>' + item.codice_CSB + ': ' + item.descrizione + ' (Prezzo Completo: '+item.prezzo_completo+'€ |Prezzo Carcere: '+item.prezzo_carcere+'€ |Differenza: ' + item.differenza + '€)</li>';
                        });
                        noteHtml += '</ul>';
                    }

                    if(response.note.sottocosto.length > 0) {
                        noteHtml += '<h4>Sottocosto:</h4>';
                        noteHtml += '<ul>';
                        response.note.sottocosto.forEach(function(item) {
                            noteHtml += '<li>' + item.codice_CSB + ': ' + item.descrizione + ' (Scostamento: ' + item.scostamento + '%)</li>';
                        });
                        noteHtml += '</ul>';
                    }
                    
                    if(response.note.fornitoreEsterno.length > 0) {
                        noteHtml += '<h4>Fornitori Esterni:</h4>';
                        noteHtml += '<ul>';
                        response.note.fornitoreEsterno.forEach(function(item) {
                            if(item.consegnatario == ''){
                                item.consegnatario = 'Non Trovato';
                            }
                            noteHtml += '<li>' + item.codice_carcere + ': ' + item.descrizione + ', richiesti '+item.quantita+', consegnatario '+item.consegnatario+'</li>';
                        });
                        noteHtml += '</ul>';
                    }

                    $('#note-ordine').html(noteHtml);

                    break;
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log("Si è verificato un errore.");
            console.log("Stato della richiesta: " + textStatus);
            console.log("Errore: " + errorThrown);
        }
    });
})

//TABELLA COMPLETO
// Aggiungi un listener per il click sulle righe della tabella
$(document).on('click', '.clickable-row', function() {
    var id_database = $(this).data('id_database');
    
    // Apri la modale e carica i dati dell'articolo
    $('#modaleModificaArticolo').modal('show');
    caricaDatiArticolo(id_database);
});

//FUNZIONE PER INSERIRE I DATI DELL'ARTICOLO SELEZIONATO
function caricaDatiArticolo(id_database) {
    // Carica i dati dell'articolo dalla sorgente dati
    // Questo può essere fatto con una richiesta AJAX per ottenere i dati dal server
    $.ajax({
        url: 'http://localhost/convertitore-ordini/assets/selectArticolo.php',
        type: 'post',
        data: { id_database: id_database },
        dataType: "json",
        success: function(data) {
            // Popola la modale con i dati dell'articolo
            $('#modaleModificaArticolo #id_database').val(data.id_database);
            $('#modaleModificaArticolo #codice_carcere').val(data.codice_carcere);
            $('#modaleModificaArticolo #codice_CSB').val(data.codice_CSB);
            $('#modaleModificaArticolo #descrizione_articolo').val(data.descrizione_articolo);
            $('#modaleModificaArticolo #rilevamento').val(data.rilevamento);
            $('#modaleModificaArticolo #prezzo').val(data.prezzo);
            $('#modaleModificaArticolo #scostamento').val(data.scostamento);
            $('#modaleModificaArticolo #carcere-modale').val(data.id_carcere);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log("Si è verificato un errore.");
            console.log("Stato della richiesta: " + textStatus);
            console.log("Errore: " + errorThrown);
        }
    });
}

//AGGIORNO I DATI DELL'ARTICOLO
$(document).on("submit", "#form-ModificaArticolo", function(event){
    event.preventDefault();
    $.ajax({
        url: 'http://localhost/convertitore-ordini/assets/aggiornaArticolo.php',
        type: 'post',
        data: $(this).serialize(),
        dataType: "json",
        success: function(response) {
            switch(parseInt(response.stato)) {
                case 0: // SEGNALAZIONE ERRORE
                    alert(response.messaggio);
                break;
                case 1: // SEGNALAZIONE SUCCESSO
                    alert(response.messaggio);
                    setTimeout(function() {
                        location.reload();
                    }, 1750);                    
                break;
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log("Si è verificato un errore.");
            console.log("Stato della richiesta: " + textStatus);
            console.log("Errore: " + errorThrown);
        }
    });
})