<?php
    echo "<div class='table-responsive m-2'>";
        echo "<table id='tabella-completo' class='table table-bordered table-hover sortable sortable-table'>";
            echo "<thead class='thead-light'>";
                echo "<tr>";
                    echo "<th scope='col' class='td-table'>#</th>";
                    echo "<th scope='col' class='td-table sortable'>Codice Carcere <span class='sort-icon'><i class='fas fa-sort'></i></span></th>";
                    echo "<th scope='col' class='td-table sortable'>Codice CSB <span class='sort-icon'><i class='fas fa-sort'></i></span></th>";
                    echo "<th scope='col' class='td-table sortable'>Descrizione Articolo <span class='sort-icon'><i class='fas fa-sort'></i></span></th>";
                    echo "<th scope='col' class='td-table sortable'>Rilevamento <span class='sort-icon'><i class='fas fa-sort'></i></span></th>";
                    echo "<th scope='col' class='td-table sortable'>Carcere <span class='sort-icon'><i class='fas fa-sort'></i></span></th>";
                    echo "<th scope='col' class='td-table sortable'>Prezzo <span class='sort-icon'><i class='fas fa-sort'></i></span></th>";
                    echo "<th scope='col' class='td-table sortable'>Scostamento <span class='sort-icon'><i class='fas fa-sort'></i></span></th>";
                    echo "<th scope='col' class='td-table sortable'>Consegnatario <span class='sort-icon'><i class='fas fa-sort'></i></span></th>";
                echo "</tr>";
            echo "</thead>";
            echo "<tbody id='articoli'>";
                $completoVuoto = true;
                foreach($completo as $id_database => $articolo){
                    echo "<tr class='clickable-row' data-id_database='" . $id_database . "'>";
                        echo "<td>" . $id_database . "</td>";
                        echo "<td>" . $articolo['codice_carcere'] . "</td>";
                        echo "<td>" . $articolo['codice_CSB'] . "</td>";
                        echo "<td>" . strtoupper($articolo['descrizione_articolo']) . "</td>";
                        echo "<td>" . strtoupper($articolo['rilevamento']) . "</td>";
                        echo "<td data-idcarcere='".$articolo['id_carcere']."'>" . ucwords($articolo['carcere']) . "</td>";
                        echo "<td>" . number_format($articolo['prezzo'], 2) . "€</td>";
                        echo "<td>" . intval($articolo['scostamento']) . "%</td>";
                        echo "<td>" . strtoupper($articolo['consegnatario']) . "</td>";
                    echo "</tr>";
                    $completoVuoto = false;
                }
                if($completoVuoto){                    
                    echo "<tr>";
                        echo "<td colspan='9'>Non ho trovato articoli caricati</td>";
                    echo "</tr>";
                }
            echo "</tbody>";
        echo "</table>";
    echo "</div>";
?>