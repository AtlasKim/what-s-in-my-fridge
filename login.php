<?php                   
    session_start();        //Pagina di login
?>
    
      
<?php
    require 'config/db_config.php';             //import di tutti i file PHP che ci permettono di comunicare con il database
    require 'db_coding.php';

    if(isset($_POST['login']))  //se è stato dato il submit di login viene effettuata l'operazione di login con email e password inseriti
    {
        $validate_mail = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);        //filtra la mail in modo da controllare se la mail inserita è effettivamente valida
        $error = "";

        if(empty($_POST['email'])){                                                 //vari messaggi di errore che vengono aggiunti in fase di riempimento del form di login
            $error.="Non hai inserito il campo e-mail\n";
        }

        else if(!empty($_POST['email']) && $validate_mail==false){                       //legato a sopra
            $error.="Non hai inserito una e-mail valida\n";
        }

        else if(empty($_POST['password'])){
            $error.="Non hai inserito il campo Password\n";
        }

        else if(checkpassword(clearInput($_POST['email']),clearInput($_POST['password'])) == false && checkEmail(clearInput($_POST['email']))){
            $error.="Password errata\n";
        }
        else if(checkEmail(clearInput($_POST['email']))==true && checkpassword(clearInput($_POST['email']),clearInput($_POST['password'])) == true)  //ripulisce e controlla l'input della mail e della passsword
        {
            $_SESSION['user'] = $_POST['email'];                        //setta l'user con il valore della mail inserita

            $id_fridge = getFridgeId($_SESSION["user"]);                //e se presente setta l'id del frigo a quello associato all'utente

            if (!empty($id_fridge))
                $_SESSION['fridge'] = $id_fridge;                       //e l'id del frigo a quello corrispondente all'utente se è stato già inserito
        }else{                  //se sono errati riporta un messaggio di errore
            $error.="La mail inserita non è associata a nessun account\n";
        }
        $j_error = json_encode($error);
        echo "<script>alert('Errore:".$j_error."');</script>\n";
    }

    if (isset($_SESSION['user'])){          //una volta effettuato il login si verrà renderizzati alla pagina index.php
        header("Location:myFridge.php");
        exit();    
    }else if(!isset($_SESSION['user'])){    //al primo avvio entrerà in questa sessione dove verrà visualizzato il form dove inserire email e password
?>
        <html>
        <head>
            <?php
                include "navbar.html"           //inclusione della navbar
            ?>
            <link rel="stylesheet" href="formtype.css">                           <!--CSS esterno utilizzato sia per il login che per la registrazione-->
            <meta name="viewport" content="width=device-width, initial-scale=1">  <!--Serve per fare scalare la grandezza della schermata in base al dispositivo-->
            <title>Pagina Login</title>
        </head>
        <body>
            
            <form method = "post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">   <!-- usiamo il metodo post per la sicurezza, per lo stesso motivo utilizziamo il metodo htmlspecialchars -->
                <h3>Login</h3>
                <label for="email">Email</label><br>
                <input type="email" vale= "email" name="email"/><br>
                <label for="password">Password</label><br>
                <input type="password" name="password"/><br>
                <button type="submit" class="btn" value="Login" name ="login">LOGIN</button>
                <a class="link" href="register.php">Non hai un account? Registrati qui</a><br>
            </form>
            
        </body>
        </html>
        <?php
    }

?>

