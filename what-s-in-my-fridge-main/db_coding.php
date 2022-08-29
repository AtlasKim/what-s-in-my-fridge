<?php

function clearInput($data)                  //funzione che ripulisce tutti gli input
{
    $data = trim($data);                    //vengono rimossi eventuali spazi, tab e line break
    $data = stripslashes($data);            //vengono rimossi tutti i backslash
    $data = htmlspecialchars($data);        //vengono salvati tutti i caratteri speciali come testo
    $data = filter_var($data);              //Infine filtra il dato ottenuto
    return $data;
}

function logged($user,$psw)
{
    try{
        $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
        $stmt->execute([$user]);
        $res = $stmt->fetch();
        $decoded_psw = password_verify($psw,$res["password"]);
        if($decoded_psw == true)
            return true;
    }catch(PDOException $e){
        return false;
    }
}


function checkpassword($user,$psw)
{
    if(strlen($psw)!=0)
    {
        try{
            $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
            $stmt->execute([$user]);
            $user_psw = $stmt->fetch();
            $decoded_psw = password_verify($psw,$user_psw["password"]);  //decodifica la password durante le operazioni di controllo
            if($decoded_psw == true)
                return true;
            else
                return false;
        }catch(PDOException $e){
            return array("KO", $e->getMessage());
        } 
    }
    else
        return array("KO", "La password inserita è troppo breve");
}



function changepassword($user,$psw)
{
    
    if(strlen($psw)!=0)
    {
        try{
            $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->execute([$psw,$user]);
            return array("OK");
        }catch(PDOException $e){
            return array("KO", $e->getMessage());
        } 
    }
    else
        return array("KO", "La password inserita è troppo breve");
}


function register($user,$psw)
{
    
    if(strlen($psw)!=0 && strlen($user)!=0)
    {
        try{
            $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $conn->prepare("INSERT INTO users (email,password) VALUES (?,?)");
            $stmt->execute([$user,$psw]);
            return array("OK");
        }catch(PDOException $e){
            return array("KO", "La mail utilizzata appartiene a un altro account");
        } 
    }
    else
        return array("KO", "Email o password troppo brevi");
}

function checkEmail($email)
{
    try{
        $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $res = $stmt->fetch();                  //estrae il risultato della query
        if($res !=null)
            return true;
        return false;
    }catch(PDOException $e){
        return false;
    }
}

function createFridge($email)                   //funzione che crea il frigo e lo collega all'account attualmente in uso
{
    try{
        $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT id_fridge FROM users WHERE email = ?");                      //controlla se è già stato associato un frigo all'account attualmente in uso
        $stmt->execute([$email]);
        $fridge = $stmt->fetch();

        if($fridge["id_fridge"] != NULL)                                                            
            return array("KO", "Errore, frigo già associato all'account");
        else
        {
            $stmt = $conn->prepare("INSERT INTO fridge (id,marca,modello) VALUES (NULL,'','')");     //crea un nuovo frigo vuoto
            $stmt->execute();

            $stmt = $conn->prepare("SELECT id FROM fridge");                                                            
            $stmt->execute();
            $fridge = $stmt->fetchAll();
            $stmt = $conn->prepare("UPDATE `users` SET `id_fridge` = ? WHERE `users`.`email` = ?");             
            $stmt->execute([max($fridge)[0],$email]);                                                           //prende l'id più grande che corrisponde anche all'ultimo inserito e lo inserisce nella tabella users nel campo id_fridge        
            return array("OK");
        }
    }catch(PDOException $e){
        return array("KO", "Errore durante la creazione del frigo");
    }
}

function linkFridge($email,$id)
{
    try{
        $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT * FROM fridge WHERE id = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        if($res != null)
        {
            $stmt = $conn->prepare("UPDATE `users` SET `id_fridge` = ? WHERE `users`.`email` = ?");
            $stmt->execute([$id,$email]);
            return array("OK");
        }
        return array("KO", "Errore: codice frigo errato");
    }catch(PDOException $e){
        return array("KO", "Errore durante la creazione del frigo");
    }
}

function getFridgeId($email)
{
    try{
        $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT id_fridge FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $res = $stmt->fetch();
        if($res != null)
        {
            return $res[0];
        }
        return NULL;
    }catch(PDOException $e){
        return NULL;
    }
}

function getFood()
{
    try{
        $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT nome_cibo FROM food");
        $stmt->execute([]);
        $food = $stmt->fetchAll();                 //estrae il risultato della query
        if($food !=null)
            for($i = 0; $i < sizeof($food);$i++)
            {
                $food_array[$i] = $food[$i]["nome_cibo"];
            }
            return $food_array;
        return false;
    }catch(PDOException $e){
        return false;
    }
}



function insert_food_db($food_name)                                         //funzione che inserisce l'alimento nella tabella food
{
    try{
        $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("INSERT INTO food (id,nome_cibo) VALUES (NULL, ?)");
        $stmt->execute([strtolower(clearInput($food_name))]);
        return array("OK");
    }catch(PDOException $e){
        return array("KO", "Inserimento cibo nel database non riuscito");
    }
}

function insert_food_fridge_quantity($id_alimento,$quantity,$scadenza,$id_fridge)        //funzione che inserisce il cibo nella tabella contains
{
    try{
        $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("INSERT INTO contain (id_frigo,id_cibo,quantita,data_scadenza) VALUES (?,?,?,?)");
        $stmt->execute([$id_fridge,$id_alimento,$quantity,$scadenza]);          //probabilmente non sta inserendo la data di scadenza
        return array("OK");
    }catch(PDOException $e){
        return array("KO", "Inserimento cibo nel frigo non riuscito");
    }
}

function insert_food_fridge_gram($id_alimento,$gram,$scadenza,$id_fridge)        //funzione che inserisce il cibo nella tabella contains
{
    try{
        $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("INSERT INTO contain (id_frigo,id_cibo,grammi,data_scadenza) VALUES (?,?,?,?)");
        $stmt->execute([$id_fridge,$id_alimento,$gram,$scadenza]);          //probabilmente non sta inserendo la data di scadenza
        return array("OK");
    }catch(PDOException $e){
        return array("KO", "Inserimento cibo nel frigo non riuscito");
    }
}

function checkFood($alimento)
{
    try{
        $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT nome_cibo FROM food WHERE nome_cibo = ?");       //controlla se l'alimento è nella tabella food
        $stmt->execute([$alimento]);
        $res = $stmt->fetch();                  //estrae il risultato della query
        if($res !=null)
            return true;
        return false;
    }catch(PDOException $e){
        return false;
    }
}

function getFoodById($id_alimento)
{
    try{
        $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT nome_cibo FROM food WHERE id = ?");
        $stmt->execute([$id_alimento]);
        $res = $stmt->fetch();                  //estrae il risultato della query
        if($res !=null)
            return $res;                        //se l'alimento è stato trovato allora ritornalo
        return false;                           //altrimenti ritorna false, non fa distinzione se non riesce a collegarsi al database
    }catch(PDOException $e){
        return false;
    }
}

function getFoodId($alimento)
{
    try{
        $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT id FROM food WHERE nome_cibo = ?");
        $stmt->execute([$alimento]);
        $res = $stmt->fetch();                  //estrae il risultato della query
        if($res !=null)
            return $res[0];                        //ritorna l'id dell'alimento, res è un array quindi devo ritornare il primo elemento dell'array (che in teoria è anche l'unico)
        return NULL;
    }catch(PDOException $e){
        return NULL;
    }
}

//il select ritorna tutte le informazioni degli alimenti contenuti al suo interno, quindi Nome, quantità e data di scadenza
function getContainedFood($id_fridge)
{
    try{
        $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT nome_cibo, quantita, grammi, data_scadenza,id_riga FROM food INNER JOIN contain ON food.id = contain.id_cibo WHERE id_frigo = ? ORDER BY data_scadenza");       //inner join per ottenere tutti i dati relativi al cibo contenuto nel frigo
        $stmt->execute([$id_fridge]);
        $res = $stmt->fetchAll();                  //estrae il risultato della query
        
        if($res !=null)
            return $res;  
        else
            return NULL;
    }catch(PDOException $e){
        return NULL;
    }
}

function getContainedFoodId($id_fridge)
{
    try{
        $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT id_cibo FROM contain WHERE id_frigo = ?");       //estrae dalla tabella contain tutti gli id dei cibi contenuti al suo interno
        $stmt->execute([$id_fridge]);
        $res = $stmt->fetchAll();                  //estrae il risultato della query
        
        if($res !=null)
        {
            for($i = 0; $i < sizeof($res);$i++)
            {
                $food_array[$i] = $res[$i]["id_cibo"];      //$res è un dizionario, quindi per creare un array di valori scorriamo per le chiavi del dizionario e salviamo i singoli valori
            }
            return $food_array;  
        }
        else
            return NULL;
    }catch(PDOException $e){
        return NULL;
    }
}

function getContainedFoodQuantity($id_food,$id_fridge,$expire_date)
{
    try{
        $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT quantita FROM contain WHERE id_cibo = ? AND id_frigo = ? AND data_scadenza = ?");       //questa funzione ritorna la quantità di un determinato alimento con una determinata data di scadenza
        $stmt->execute([$id_food,$id_fridge,$expire_date]);
        $res = $stmt->fetch();                  
        
        if($res !=null)
            return $res;  
        else
            return NULL;
    }catch(PDOException $e){
        return NULL;
    }
}

function getContainedFoodGram($id_food,$id_fridge,$expire_date)
{
    try{
        $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT grammi FROM contain WHERE id_cibo = ? AND id_frigo = ? AND data_scadenza = ?");       //questa funzione ritorna la quantità di un determinato alimento con una determinata data di scadenza
        $stmt->execute([$id_food,$id_fridge,$expire_date]);
        $res = $stmt->fetch();                  
        
        if($res !=null)
            return $res;  
        else
            return NULL;
    }catch(PDOException $e){
        return NULL;
    }
}


function getContainedFoodExpirationDate($id_food,$id_fridge)
{
    try{
        $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT data_scadenza FROM contain WHERE id_cibo = ? AND id_frigo = ?");       //questa funzione ritorna la data di scadenza di un cibo inserito nel database, utilizzando l'id del frigo e del cibo
        $stmt->execute([$id_food,$id_fridge]);
        $res = $stmt->fetch();                  
        
        if($res !=null)
            return $res[0];  
        else
            return NULL;
    }catch(PDOException $e){
        return NULL;
    }
}

function updateContainedFoodQuantity($id_food,$id_fridge,$quantity,$expire_date)
{
    try{
        $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("UPDATE contain SET quantita = quantita + ? WHERE id_cibo = ? AND id_frigo = ? AND data_scadenza = ?");       //questa funzione ritorna la data di scadenza di un cibo inserito nel database, utilizzando l'id del frigo e del cibo
        $stmt->execute([$quantity,$id_food,$id_fridge,$expire_date]);
        $res = $stmt->fetch();                  
        
        if($res !=null)
            return true;  
        else
            return NULL;
    }catch(PDOException $e){
        return NULL;
    }
}


function updateContainedFoodGram($id_food,$id_fridge,$gram,$expire_date)
{
    try{
        $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("UPDATE contain SET grammi = grammi + ? WHERE id_cibo = ? AND id_frigo = ? AND data_scadenza = ?");       //questa funzione ritorna la data di scadenza di un cibo inserito nel database, utilizzando l'id del frigo e del cibo
        $stmt->execute([$gram,$id_food,$id_fridge,$expire_date]);
        $res = $stmt->fetch();                  
        
        if($res !=null)
            return true;  
        else
            return NULL;
    }catch(PDOException $e){
        return NULL;
    }
}


function checkContainedFood($id_food,$id_fridge,$expire_date)
{
    try{
        $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT quantita FROM contain  WHERE id_cibo = ? AND id_frigo = ? AND data_scadenza = ?");       //questa funzione ritorna true se è già presente nel frigo lo stesso elemento con la stessa data di scadenza
        $stmt->execute([$id_food,$id_fridge,$expire_date]);
        $res = $stmt->fetch();                  
        
        if($res !=null)
            return $res;  
        else
            return false;
    }catch(PDOException $e){
        return false;
    }
}

function clearContainedFood($id_row)
{
    try{
        $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("DELETE FROM contain WHERE id_riga = ? ");       //dobbiamo utilizzare anche la data di scadenza per cancellare esattamente quell'alimento
        $stmt->execute([$id_row]);
        $res = $stmt->fetch();                  
        
        if($res !=null)
            return $res;  
        else
            return NULL;
    }catch(PDOException $e){
        return NULL;
    }
}

function clearContainedFoodModify($id_food,$id_fridge,$expire_date) //funzione utilizzata quando proviamo a modificare un alimento e riduciamo la quantità sotto lo zero
{
    try{
        $conn = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'], $GLOBALS['dbuser'],$GLOBALS['dbpassword']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("DELETE FROM contain WHERE id_cibo = ? AND id_frigo = ? AND data_scadenza = ?");       //dobbiamo utilizzare anche la data di scadenza per cancellare esattamente quell'alimento
        $stmt->execute([$id_food,$id_fridge,$expire_date]);
        $res = $stmt->fetch();                  
        
        if($res !=null)
            return $res;  
        else
            return NULL;
    }catch(PDOException $e){
        return NULL;
    }
}
?>