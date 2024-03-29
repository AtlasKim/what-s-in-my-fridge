<?php
    session_start();    //Pagina di login
?>

<?php
    require 'db_coding.php'; //Import dei file contententi metodi e variabili globali per effettuare operazioni sul DB
    require 'config/db_config.php';

    if(isset(($_POST['new_fridge']))){                                  //se è stata selezionata l'opzione nuovo frigo allora crea un nuovo frigo collegato all'account
        $op = createFridge($_SESSION["user"]);
        if($op[0] == 'OK')
        {
            echo "Frigo creato con successo";
            $_SESSION["fridge"] = getFridgeId($_SESSION["user"]);
        }else{
            echo "Errore tecnico: ".$op[1];
        }
    }else if(isset(($_POST['old_fridge'])))
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

    if(isset (($_POST['insert_food'])))
    {
        $error =" ";
        if(empty($_POST['scadenza']))                       //l'unico campo che non viene inserito automaticamente è la scadenza
            $error.="Inserire il campo scadenza<br>";
        //metodo inserimento cibo
        else if(empty($_POST['quantity']) && empty($_POST['gram']))     //se non sono stati inseriti entrambi i campi ritorna errore
            $error.="Inserire una tra quantità o grammi<br>";
        else if($_POST['alimenti']!="empty")
        {
            //inserisci il cibo preso dal menù a tendina nella tabella contains
            if(checkContainedFood(getFoodId($_POST['alimenti']),$_SESSION["fridge"],$_POST['scadenza'])==true)             //Se l'alimento è già presente in frigo e ha quella stessa data di scadenza allora aggiornare la quantità (sommandola)
            {
                if(getContainedFoodQuantity(getFoodId($_POST['alimenti']),$_SESSION["fridge"],$_POST['scadenza'])!=NULL && $_POST['quantity']!=NULL)   //controlla se l'alimento già presente era salvato in grammi o in quantità
                    updateContainedFoodQuantity(getFoodId($_POST['alimenti']),$_SESSION["fridge"],$_POST['quantity'],$_POST['scadenza']);
                else if (getContainedFoodGram(getFoodId($_POST['alimenti']),$_SESSION["fridge"],$_POST['scadenza'])!=NULL && $_POST['gram']!=NULL)
                    updateContainedFoodGram(getFoodId($_POST['alimenti']),$_SESSION["fridge"],$_POST['gram'],$_POST['scadenza']);
                else
                    $error.="Campo quantità/grammi non adatto al tipo di cibo già inserito, prego riprovare<br>";

            }
            else
            {
                if (isset($_POST['quantity_selected']))                                                                          //controlli necessari a capire se sono stati settati i grammi o la quantità
                    insert_food_fridge_quantity(getFoodId($_POST['alimenti']),$_POST['quantity'],$_POST['scadenza'],$_SESSION["fridge"]);
                else if(isset(($_POST['gram_selected'])))
                    insert_food_fridge_gram(getFoodId($_POST['alimenti']),$_POST['gram'],$_POST['scadenza'],$_SESSION["fridge"]);
            }
                
        }else if($_POST['alimenti']=="empty" && !empty($_POST['other_food'])){                                                 //inserisci il nuovo cibo nel database food se non era già presente e dopo inseriscilo nella tabella contains 
            if(checkFood($_POST['other_food'])==false)
            {
                insert_food_db($_POST['other_food']);
            }else if (checkFood($_POST['other_food'])==true)
            {
                if (isset($_POST['quantity_selected']))                                                                          //controlli necessari a capire se sono stati settati i grammi o la quantità
                    insert_food_fridge_quantity(getFoodId($_POST['alimenti']),$_POST['quantity'],$_POST['scadenza'],$_SESSION["fridge"]);
                else if(isset(($_POST['gram_selected'])))
                    insert_food_fridge_gram(getFoodId($_POST['alimenti']),$_POST['gram'],$_POST['scadenza'],$_SESSION["fridge"]);
            }        
        }
        else
        {
            $error.="Campo altro cibo vuoto, selezionarne uno dal menù a tendina o inserirne uno nuovo<br>";
            echo $error;
        }

        
        //echo "Inserimento avvenuto con successo!<br>";
    }
    ?>

    <?php
    if (!isset($_SESSION["fridge"]))
    {
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
        </html><?php
    }
    else
    {?>
        <html>
            <head>
                <title>Frigo utente</title>
            </head>

            <body>
                <h2>Area My Fridge</h2>

                <form method = "post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                    <label for="alimenti">Tipo di alimento</label>
                    <select id="alimenti" name="alimenti"></select>                                  <!-- Inserire le quantità -->
                    <input type="text" placeholder="Altro" name ="other_food"/>
                    <input type="radio" id="quantity_selected" name="quantity_selected">
                    <label for="quantity">Quantità:</label>
                    <input type="number" id="quantity" name="quantity" min = "1"/>
                    <input type="radio" id="gram_selected" name="gram_selected">
                    <label for="gram">Grammi:</label>             
                    <input type="number" id="gram" name="gram" step= 50 min = "0"/>
                    <label for="date">Data di scadenza:</label>
                    <input type="date" name="scadenza" required/>                       <!-- Required fa si che non possa accettare l'input senza l'inserimento della data -->
                    <input type="submit" value="Inserisci cibo" name="insert_food"/>
                </form>
            
            <script>
                <?php 
                    $foods = json_encode(getFood());                //la funzione restituisce l'array contenente tutti i cibi ma è codificato in PHP, usiamo json_encode per fare la conversione
                    echo "const food = ". $foods . ";\n";           //usiamo echo per dichiarare l'array in javascript
                ?>
  
                var food_list = document.getElementById('alimenti');

                var option = document.createElement('option');
                option.value = "empty";                               
                option.text = "--";
                food_list.appendChild(option); 

                for(let i = 0; i<food.length; i++){                 //inserisce dinamicamente le opzioni di tutti i cibi già presenti nel database
                    var option = document.createElement('option');  //crea l'opzione
                    option.value = food[i];                         //il valore dell'opzione sarà corrispondente al nome dei cibi      
                    option.text = food[i];                          //così come il testo al suo interno
                    food_list.appendChild(option);                  //aggiunge al datalist dinamicamente tutte le opzioni presenti nel database
                }
            </script>        
            </body>    
        </html><?php
    }