<?php
  
  // Configurazione Database  
  define('DB_HOST', 'localhost');  
  define('DB_USERNAME', 'root');  
  define('DB_PASSWORD', '');
  define('DB_NAME', 'convertitore-ordini');

  $connessione_db = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
  !$connessione_db ? mysqli_errors() : "";

?>