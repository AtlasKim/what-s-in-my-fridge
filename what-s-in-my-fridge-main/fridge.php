<?php
    session_start();    //Pagina di login
?>

<?php
    require 'db_coding.php'; //Import dei file contententi metodi e variabili globali per effettuare operazioni sul DB
    require 'config/db_config.php';

    if(isset(($_POST['new_fridge']))){
        $op = createFridge($_SESSION["user"]);
        if($op[0] == 'OK')
        {
            echo "Frigo creato con successo";
        }else
            echo "Errore tecnico: ".$op[1];
    }else if(isset(($_POST['old_fridge'])))
    {   
        $op = linkFridge($_SESSION["user"],$_POST["id_fridge"]);
        if($op[0] == 'OK')
        {
            echo "Frigo collegato con successo";
        }else
            echo "Errore tecnico: ".$op[1];
    }

?>

<html>
    <head>
        <title>Frigo utente</title>
    </head>

    <body>
        <h2>Area My Fridge</h2>

        <form method = "post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                <input type="submit" value="Crea nuovo frigo" name="new_fridge"/>
                <input type="text" placeholder="Codice frigo" name ="id_fridge"/>
                <input type="submit" value="Codice frigo" name="old_fridge"/>
        </form>
    </body>    
</html>