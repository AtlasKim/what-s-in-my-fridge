<?php
    session_start();
?>
    
      
<?php
    require 'config/config.php';
    require 'config/db_config.php';
    require 'db_coding.php';

    if(isset($_POST['login']))  //se è stato dato il submit di login viene effettuata l'operazione di login con email e password inseriti
    {
        $validate_mail = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);        //non funziona correttamente, fixarlo dopo
        $error = "";

        if(empty($_POST['email'])){
            $error.="Non hai inserito il campo 'e-mail'<br>";
        }

        if(!empty($_POST['email']) && $validate_mail==false){                       //legato a sopra
            $error.="Non hai inserito una e-mail valida'<br>";
        }

        if(empty($_POST['password'])){
            $error.="Non hai inserito il campo 'Password'<br>";
        }

        if(logged(clearInput($_POST['email']),clearInput($_POST['password'])))  //ripulisce e controlla l'input della mail e della passsword
        {
            $_SESSION['user'] = $_POST['email'];      //setta l'user con il valore della mail inserita
        }else{                  //se sono errati riporta un messaggio di errore
            $error.="La mail inserita non è associata a nessun account<br>";
        }
        echo $error;
    }

    if (isset($_SESSION['user'])){          //una volta effettuato il login si verrà renderizzati alla pagina index.php
        header("Location:index.php");
        exit();    
    }else if(!isset($_SESSION['user'])){    //al primo avvio entrerà in questa sessione dove verrà visualizzato il form dove inserire email e password
?>

        <html>
        <head>
        <title>Pagina Login</title>
        </head>
        <body>
        <h2>Login</h2>
        <form method = "post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">   <!-- usiamo il metodo post per la sicurezza, per lo stesso motivo utilizziamo il metodo htmlspecialchars -->
            Email <br> <input type="text" vale= "email" name="email"/> <br>
            Password <br> <input type="password" name="password"/> <br>
            <br><input type="submit"  value="Login" name ="login"/>
        </form>
        <a href="register.php">Non hai un account? Registrati qui</a><br>
        </body>
        </html>
        <?php
    }

?>

