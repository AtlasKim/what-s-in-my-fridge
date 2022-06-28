<?php
    session_start();    //Pagina di login
?>


<?php
//require 'config.php';
require 'db_coding.php';                //Import dei file contententi metodi e variabili globali per effettuare operazioni sul DB
require 'config/db_config.php';


if(isset($_POST['changepsw'])){         //se viene dato il submit per la modifica password viene effettuata l'operazione sul database
    $op = changepassword(clearInput($_SESSION["user"]),clearInput($_POST["password"]));
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
<title>Pagina Utente</title>
</head>

<body>
<?php
if(isset($_SESSION["user"])){
?>
    <h2> Benvenuto <?php echo $_SESSION["user"]; ?></h2>

    <form method = "post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <input type="password" name="password"/>
        <input type="submit" value="Cambia Password" name ="changepsw"/>
        <input type="submit" value="Logout" name="logout"/>
    </form>

    <h2>Area My Fridge</h2>
    <form method = "post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <input type="text" placeholder="Codice frigo" name ="id_fridge"/>
        <input type="submit" value="Codice frigo" name="old_fridge"/>
    </form>
    <?php
}else echo"<h1>Effettuare prima il login</h1>";

?>
</body>
</html>