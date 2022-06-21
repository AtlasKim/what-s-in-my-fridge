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
        $stmt = $conn->prepare("SELECT email FROM users where email = ? and password = ?");
        $stmt->execute([$user,$psw]);
        $res = $stmt->fetch();
        if($res!= null)
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
            $stmt = $conn->prepare("SELECT password FROM users where email = ?");
            $stmt->execute([$user]);
            $user_psw = $stmt->fetch();
            if($psw == $user_psw["password"])
                return array("OK");
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
            $stmt = $conn->prepare("UPDATE users SET password = ? where email = ?");
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
            //return $res['email']!=null;
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
            $stmt = $conn->prepare("INSERT INTO `fridge` (`id`, `marca`, `modello`) VALUES (NULL, '', '')");     //crea un nuovo frigo vuoto
            $stmt->execute();

            $stmt = $conn->prepare("SELECT id FROM fridge");                                                            
            $stmt->execute();
            $fridge = $stmt->fetch();
            $stmt = $conn->prepare("UPDATE `users` SET `id_fridge` = ? WHERE `users`.`email` = ?");             //prende l'id appena inserito e lo associa all'utente attualmente in uso
            $stmt->execute([max($fridge),$email]);
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
?>