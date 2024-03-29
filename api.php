<?php
    require 'config/db_config.php';

    define('DEBUG',false);

    $method = $_SERVER['REQUEST_METHOD'];
    $request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
    $input = json_decode(file_get_contents('php://input'),true);
    
  
    if(DEBUG){
      var_dump($method);
      var_dump($request);
      var_dump($input);
    }

    $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $table = preg_replace('/[^a-z0-9_]+/i','',array_shift($request)); /* con array_shift diciamo che vogliamo il primo elemento dell'array $request (quindi "utente") 
                                                                         e stiamo specificando che se ci sono caratteri diversi (diversi=^) da a-z0-9_ (insensitive: /i), vanno sostituiti 
                                                                         con un carattere vuoto. (questo è un check utile ai fini della sicurezza)
                                                                         NB: array_shift preleva e POI RIMUOVE l'elemento dall'array */
    $key = array_shift($request);
    //$id = array_shift($request); //nel caso del put abbiamo un uri un po' più lungo e quindi abbiamo bisogno di salvare qui dentro l'id della riga che dobbiamo modificare

    if(DEBUG){
      var_dump($table);
      var_dump($key);
      //var_dump($id);
    }

    if(isset($input)){
        $columns = preg_replace('/[^a-z0-9_]+/i','',array_keys($input));
        $values = array_map(function ($value) use ($conn) {/* array_map è una funzione che va ad applicare ad ogni elemento di un array che passiamo come parametro, una funzione di
                                                              callback. La funzione di callback è tutto il pezzo:
                                                              function ($value) use ($conn)*/
            if ($value===null) return null;
            return $value;
        },array_values($input));
    }

    if(DEBUG){
      var_dump($columns);
      var_dump($values);
    }

    if(isset($input)){
        $set = '';
        for ($i=0;$i<count($columns);$i++) {
            $set.=($i>0?',':'').'`'.$columns[$i].'`=';
            $set.=($values[$i]===null?'NULL':'"'.$values[$i].'"');
        }
    }

    

    switch ($method) {
        case 'GET':
           //se $key è valorizzata allora devi effettiare l'inner join perché stiamo effettuando il get degli alimenti nel frigo, altrimenti farà un select della teballa
          $sql = "select * from ".($key? "food inner join $table on food.id = $table.id_cibo  WHERE id_frigo=".$key." order by data_scadenza" : "".$table."" )."";
          break;
        case 'PUT':
          $sql = "update `$table` SET $set WHERE id_riga=\"$key\""; 
          break;
        case 'POST':
          $sql = "insert into `$table` set $set"; 
          break;
        case 'DELETE':
          $sql = "delete from `$table` where id_riga=\"$key\""; 
          break;
    }

    if(DEBUG){
      var_dump($sql);
    }

    try
    {
      $stmt = $conn->prepare($sql);
      $stmt->execute();

      if(DEBUG){
        var_dump($sql);
      }
      //se l'esecuzione della query è andata a buon fine, impachettiamo (prepariamo) il risultato per mandarlo al client
      // print results, insert id or affected row count
      if ($method == 'GET') {
        echo '['; /*se non ho specificato una chiave, verrà restituito un array di oggetti con tutti gli elementi della tabella quindi serve stampare a video
                              [ ad indicare che si tratta di un array di oggetti */
        for ($i=0;$i<$stmt->rowCount();$i++) {
          echo ($i>0?',':'').json_encode($stmt->fetch()); //codifico in JSON l'oggetto ottenuto da fetch().
        }
        echo ']'; // alla fine metterò la ] di chiusura della [ sopra
      } elseif ($method == 'POST') {
        echo "INSERT OK";
      } else {
        echo "AFFECTED ROWS: " . $stmt->rowCount();
      }


    }catch(PDOException $e){
      http_response_code(404);
      return NULL;
    }
    
?>