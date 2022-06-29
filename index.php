<?php
    session_start();    //Pagina di login
?>


<?php
//require 'config.php';
require 'db_coding.php';                //Import dei file contententi metodi e variabili globali per effettuare operazioni sul DB
require 'config/db_config.php';


if(isset($_POST['changepsw'])){         //se viene dato il submit per la modifica password viene effettuata l'operazione sul database
    $encoded_psw = password_hash(clearInput($_POST['password']), PASSWORD_DEFAULT);
    $op = changepassword(clearInput($_SESSION["user"]),$encoded_psw);
    if($op[0] == 'OK'){
        echo "Password cambiata";
    }else{
        echo "Errore tecnico: ".$op[1];
    }
}else if(isset($_POST['logout']))
{
    unset($_SESSION["user"]);
    unset($_SESSION["fridge"]);
    header("Location:login.php");  
}

if(isset(($_POST['old_fridge'])))
{   
    $op = linkFridge($_SESSION["user"],$_POST["id_fridge"]);
    if($op[0] == 'OK')
    {
        echo "Frigo collegato con successo";
        $_SESSION["fridge"] = $_POST["id_fridge"];
    }else{
        echo "Errore tecnico: ".$op[1];
    }    
}
?>

<html>      <!-- Pagina visualizzata con le informazioni dell'utente -->
    <head>
        <link rel="stylesheet" href="fridge.css"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">  <!--Serve per fare scalare la grandezza della schermata in base al dispositivo-->
        <title>Pagina Utente</title>
    </head>

    <body>
        <?php
        include "navbar.html";
               
        if(isset($_SESSION["user"])){
        ?>

        <h2> Benvenuto <?php echo $_SESSION["user"]; ?></h2>
        
        <div>
            <form class="form-inline" method = "post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                <input type="password" name="password"/>
                <button class="form-button" type="submit" value="Cambia Password" name ="changepsw">Cambia password</button>
                <button class="form-button" type="submit" value="Logout" name="logout">Logout</button>
            </form>
        </div>

        <div class="profile-2">
            <form class="form-inline" method = "post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                <input type="text" placeholder="Codice frigo" name ="id_fridge"/>
                <button class="form-button" type="submit" value="Codice frigo" name="old_fridge">Ricerca</button>
            </form>
        </div>
        <?php
    }else echo"<h1>Effettuare prima il login</h1>";

    ?>
    </body>
</html>