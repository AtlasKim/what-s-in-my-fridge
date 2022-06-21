<?php
    session_start();
?>

<?php
    require 'config/config.php';
    require 'config/db_config.php';
    require 'db_coding.php';



    

    if(isset($_POST['register']))  //se è stato dato il submit di register allora effettua la registrazione dell'utente
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

        if($_POST['password'] !== $_POST['password_cmp']){
            $error.="Le password inserite non coincidono<br>";
        }

        if(checkEmail(clearInput($_POST['email'])))
        {
            $error.="Un utente si è già registrato con questa mail<br>";
        }


        if(clearInput($_POST['password']) == clearInput($_POST['password_cmp']))
        {
            $op = register(clearInput($_POST['email']),clearInput($_POST['password']));
            if($op[0] == 'OK'){
                $_SESSION['user'] = $_POST['email'];
            }else{
                echo "Errore tecnico:<br> ".$error;//$op[1];
            }
        }
        else
            echo $error;//"Password non corrispondente";
    }

    if (isset($_SESSION['user'])){          //una volta effettuato il login si verrà renderizzati alla pagina index.php
        header("Location:index.php");
        exit();    
    }else if(!isset($_SESSION['user']))
    {
    ?>
        <html>
            <head>
            <title>Registrazione</title>
            </head>
            <body>
            <h2>Registrazione</h2>
            <form method = "post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">   <!-- usiamo il metodo post per la sicurezza, per lo stesso motivo utilizziamo il metodo htmlspecialchars -->
                Email <br> <input type="text" name="email"/> <br>
                Password <br> <input type="password" name="password"/> <br>
                Conferma Password <br> <input type="password" name="password_cmp"/> <br>
                <br> <input type="submit" value="Registrati" name ="register"/>
            </form>
            </body>
        </html>
    <?php
    }
    ?>