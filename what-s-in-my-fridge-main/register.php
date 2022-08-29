<?php
    session_start();            //Pagina di registrazione dell'account
?>

<?php
    require 'config/db_config.php';     //import di tutti i file PHP che ci permettono di comunicare con il database
    require 'db_coding.php';
  

    if(isset($_POST['register']))  //se è stato dato il submit di register allora effettua la registrazione dell'utente
    {   

        $validate_mail = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);        //filtra la mail in modo da controllare se la mail inserita è effettivamente valida
        $error = "";

        if(empty($_POST['email'])){
            $error.="Non hai inserito il campo e-mail\n";                           //vari messaggi di errore che vengono aggiunti in fase di riempimento del form di registrazione 
        }

        if(!empty($_POST['email']) && $validate_mail==false){                       //legato a sopra
            $error.="Non hai inserito una e-mail valida\n";
        }

        if(empty($_POST['password'])){
            $error.="Non hai inserito il campo Password\n";
        }

        if($_POST['password'] !== $_POST['password_cmp']){
            $error.="Le password inserite non coincidono\n";
        }

        if(checkEmail(clearInput($_POST['email'])))
        {
            $error.="Un utente si è già registrato con questa mail";
        }


        if(clearInput($_POST['password'])!=NULL && clearInput($_POST['password']) == clearInput($_POST['password_cmp']))        //se non ci sono errori e la password è stata confermata allora effettua la registrazione
        {
            $encoded_psw = password_hash(clearInput($_POST['password']), PASSWORD_DEFAULT);           //codifica la password e la inserisce nel database
            $op = register(clearInput($_POST['email']),$encoded_psw);
            if($op[0] == 'OK'){
                $_SESSION['user'] = $_POST['email'];                                                //se è andato tutto bene setta l'user all'email inserita dall'utente
            }else{
                $j_error = json_encode($error);
                echo "<script>alert('Errore: $j_error.');</script>\n";        
            }
        }
        else
        {
            $j_error = json_encode($error);
            echo "<script>alert('Errore: $j_error.');</script>\n";
        }
    }

    if (isset($_SESSION['user'])){          //una volta effettuato il login si verrà renderizzati alla pagina index.php
        header("Location:index.php");
        exit();    
    }else if(!isset($_SESSION['user']))     //se l'utente non è loggato o registrato gli permette di visitare la pagina di registrazione in maniera corretta
    {
        include "navbar.html"
    ?>
        <html>
            <head>
                <link rel="stylesheet" href="formtype.css">                           <!--CSS esterno utilizzato sia per il login che per la registrazione-->
                <meta name="viewport" content="width=device-width, initial-scale=1">  <!--Serve per fare scalare la grandezza della schermata in base al dispositivo-->
                <title>Registrazione</title>
            </head>
            <body>
                
                
                <form method = "post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">   <!-- usiamo il metodo post per la sicurezza, per lo stesso motivo utilizziamo il metodo htmlspecialchars -->
                    <h3>Registrazione</h3>
                    <label for="email">Email</label><br>
                    <input type="email" name="email"/><br>
                    <label for="password">Password</label><br>
                    <input type="password" name="password"/><br>
                    <label for="password">Conferma Password</label><br>
                    <input type="password" name="password_cmp"/> <br>
                    <button type="submit" class="btn" value="Registrati" name ="register">REGISTRATI</button>
                </form>
            </body>
        </html>
    <?php
    }
    ?>