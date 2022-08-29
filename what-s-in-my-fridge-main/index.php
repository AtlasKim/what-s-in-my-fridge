<?php
    session_start();    //Pagina impostazioni dell'account
?>


<?php
require 'db_coding.php';                //Import dei file contententi metodi e variabili globali per effettuare operazioni sul DB
require 'config/db_config.php';


if(isset($_POST['changepsw']) && !empty(clearInput($_POST['password']))){           //se viene dato il submit per la modifica password viene effettuata l'operazione sul database
    $encoded_psw = password_hash(clearInput($_POST['password']), PASSWORD_DEFAULT);
    $op = changepassword(clearInput($_SESSION["user"]),$encoded_psw);
    if($op[0] == 'OK'){
        echo "<script>alert('Password cambiata con successo');</script>";
    }else{
        echo "Errore tecnico: ".$op[1];
        echo "<script>alert('".$op[1]."');</script>";
    }
}
else if(isset($_POST['changepsw']) && empty(clearInput($_POST['password'])))        //se non viene inserita una password riporta errore
    echo "<script>alert('Campo password vuoto, prego inserire una password');</script>";
if(isset($_POST['logout']))                                                         //se viene dato il submit per il logout allora viene effettuato l'unset delle variabili user e fridge
{
    unset($_SESSION["user"]);
    unset($_SESSION["fridge"]);
    header("Location:login.php");  
}

if(isset(($_POST['old_fridge'])))           //se viene dato il codice di un frigo già esistente allora prova a collegarlo
{   
    $op = linkFridge($_SESSION["user"],$_POST["id_fridge"]);
    if($op[0] == 'OK')
    {
        echo "<script>alert('Frigo collegato con successo');</script>";
        $_SESSION["fridge"] = $_POST["id_fridge"];
    }else{
        echo "<script>alert('".$op[1]."');</script>";
    }    
}
include "navbar.html";
?>

<html>      <!-- Pagina visualizzata con le informazioni dell'utente -->
    <head>
        <link rel="stylesheet" href="fridge.css"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">  <!--Serve per fare scalare la grandezza della schermata in base al dispositivo-->
        <title>Pagina Utente</title>
    </head>

    <body>
        <?php  
            if(isset($_SESSION["user"])){       //se l'utente è loggato stampa la pagina di impostazioni dell'account
        ?>

        <h2> Benvenuto <?php echo $_SESSION["user"]; ?></h2>
        
        <div>
            <form class="form-inline" method = "post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                <input type="password" name="password"/>
                <button class="form-button" type="submit" value="Cambia Password" name ="changepsw">Cambia password</button>
                <button class="form-button" type="submit" value="Logout" name="logout">Logout</button>
            </form>
        </div>
        
        <div>
            <?php
                if (isset($_SESSION['fridge']))             //stampa il codice frigo se è stato settato, altrimenti chiede all'utente di inserire un codice frigo esistente o uno totalmente nuovo
                    echo "<h2>ID Frigo utente: ".$_SESSION['fridge']."</h2>";
                else
                    echo "<h2>ID Frigo assente, prego creare un nuovo frigo o inserire un ID Frigo</h2>";
            ?>         
        </div>

        <div class="profile-2">
            <form class="form-inline" method = "post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                <input type="text" placeholder="Codice frigo" name ="id_fridge"/>
                <button class="form-button" type="submit" value="Codice frigo" name="old_fridge">Ricerca</button>
            </form>
        </div>
        <?php
    }else echo"<h1>Effettuare prima il login</h1>";             //se non si è loggati stampa questo messaggio a schermo

    ?>
    </body>
</html>