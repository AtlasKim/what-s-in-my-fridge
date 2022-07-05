<?php
    session_start();    //Pagina del frigo
?>

<?php
    require 'db_coding.php'; //Import dei file contententi metodi e variabili globali per effettuare operazioni sul DB
    require 'config/db_config.php';
    
    $error =" ";

    if(!isset($_SESSION['user']))                                       //se l'utente non è loggato allora viene rimandato alla pagina del login
        header("Location:login.php"); 

    if(isset(($_POST['new_fridge']))){                                  //se è stata selezionata l'opzione nuovo frigo allora crea un nuovo frigo collegato all'account
        $op = createFridge($_SESSION["user"]);
        if($op[0] == 'OK')
        {
            echo "<script>alert('Frigo creato con successo');</script>\n";
            $_SESSION["fridge"] = getFridgeId($_SESSION["user"]);
        }else{
            echo "<script>alert('Errore tecnico: $op[1]');</script>\n";
        }
    }else if(isset(($_POST['old_fridge'])))
    {   
        $op = linkFridge($_SESSION["user"],$_POST["id_fridge"]);
        if($op[0] == 'OK')
        {
            echo "<script>alert('Frigo collegato con successo');</script>\n";
            $_SESSION["fridge"] = $_POST["id_fridge"];
        }else{
            echo "<script>alert('Errore tecnico: $op[1]');</script>\n";
        }
            
    }

    if(isset (($_POST['insert_food'])))                     //Metodo per l'inserimento del cibo nella tabella
    {   
        if(empty($_POST['scadenza']))                       //l'unico campo che non viene inserito automaticamente è la scadenza
            $error.="Inserire il campo scadenza<br>";
        //metodo inserimento cibo
        else if(empty($_POST['quantity']) && empty($_POST['gram']))     //se non sono stati inseriti entrambi i campi ritorna errore
            $error.="Inserire una tra quantità o grammi<br>";
        else if($_POST['alimenti']!="empty")                    //se il campo alimenti è vuoto allora controlla se l'utente ha inserito a "mano" un alimento
        {
            //inserisci il cibo preso dal menù a tendina nella tabella contains
            if(checkContainedFood(getFoodId($_POST['alimenti']),$_SESSION["fridge"],$_POST['scadenza'])==true)             //Se l'alimento è già presente in frigo e ha quella stessa data di scadenza allora aggiornare la quantità (sommandola)
            {
                if((getContainedFoodQuantity(getFoodId($_POST['alimenti']),$_SESSION["fridge"],$_POST['scadenza'])!=NULL && $_POST['quantity']!=NULL) && $_POST['selected'] == "quantity_selected")   //controlla se l'alimento già presente era salvato in grammi o in quantità
                    updateContainedFoodQuantity(getFoodId($_POST['alimenti']),$_SESSION["fridge"],$_POST['quantity'],$_POST['scadenza']);
                else if ((getContainedFoodGram(getFoodId($_POST['alimenti']),$_SESSION["fridge"],$_POST['scadenza'])!=NULL && $_POST['gram']!=NULL) && $_POST['selected'] == "gram_selected")
                    updateContainedFoodGram(getFoodId($_POST['alimenti']),$_SESSION["fridge"],$_POST['gram'],$_POST['scadenza']);
                else
                    $error.="Campo quantità/grammi non adatto al tipo di cibo già inserito, prego riprovare<br>";

            }
            else if (isset($_POST['selected']))
            {
                if ($_POST['selected'] == "quantity_selected")                                                                          //controlli necessari a capire se sono stati settati i grammi o la quantità
                    insert_food_fridge_quantity(getFoodId($_POST['alimenti']),$_POST['quantity'],$_POST['scadenza'],$_SESSION["fridge"]);
                else if($_POST['selected'] == "gram_selected")
                    insert_food_fridge_gram(getFoodId($_POST['alimenti']),$_POST['gram'],$_POST['scadenza'],$_SESSION["fridge"]);
            }
                
        }else if($_POST['alimenti']=="empty" && !empty($_POST['other_food'])){                                                //inserisci il nuovo cibo nel database food se non era già presente e dopo inseriscilo nella tabella contains 
            if(checkFood($_POST['other_food'])==false)
            {
                insert_food_db($_POST['other_food']);    
            }

            if(checkContainedFood(getFoodId($_POST['other_food']),$_SESSION["fridge"],$_POST['scadenza'])==true)             //Se l'alimento è già presente in frigo e ha quella stessa data di scadenza allora aggiornare la quantità (sommandola)
            {
                if((getContainedFoodQuantity(getFoodId($_POST['other_food']),$_SESSION["fridge"],$_POST['scadenza'])!=NULL && $_POST['quantity']!=NULL) && $_POST['selected'] == "quantity_selected")   //controlla se l'alimento già presente era salvato in grammi o in quantità
                    updateContainedFoodQuantity(getFoodId($_POST['other_food']),$_SESSION["fridge"],$_POST['quantity'],$_POST['scadenza']);
                else if ((getContainedFoodGram(getFoodId($_POST['other_food']),$_SESSION["fridge"],$_POST['scadenza'])!=NULL && $_POST['gram']!=NULL) && $_POST['selected'] == "gram_selected")
                    updateContainedFoodGram(getFoodId($_POST['other_food']),$_SESSION["fridge"],$_POST['gram'],$_POST['scadenza']);
                else
                    $error.="Campo quantità/grammi non adatto al tipo di cibo già inserito, prego riprovare<br>";

            }

            else if (isset($_POST['selected'])){ 
                if ($_POST['selected'] == "quantity_selected")                                                                          //controlli necessari a capire se sono stati settati i grammi o la quantità
                    insert_food_fridge_quantity(getFoodId($_POST['other_food']),$_POST['quantity'],$_POST['scadenza'],$_SESSION["fridge"]);
                else if($_POST['selected'] == "gram_selected")
                    insert_food_fridge_gram(getFoodId($_POST['other_food']),$_POST['gram'],$_POST['scadenza'],$_SESSION["fridge"]);
            }
        }
        else
        {
            $error.="Campo altro cibo vuoto, selezionarne uno dal menù a tendina o inserirne uno nuovo<br>";
            echo '<script type="text/javascript" >alert('.$error.');</script>';
        }
    }
    else if(isset($_POST['modify_food']))               //se l'alimento deve essere modificato allora la sua quantità va ridotta
    {
        if(checkContainedFood(getFoodId($_POST['alimenti']),$_SESSION["fridge"],$_POST['scadenza'])==true)             //Se l'alimento è già presente in frigo e ha quella stessa data di scadenza allora aggiornare la quantità (sommandola)
        {     
            if((getContainedFoodQuantity(getFoodId($_POST['alimenti']),$_SESSION["fridge"],$_POST['scadenza'])[0]!=NULL && $_POST['quantity']!=NULL) && $_POST['selected'] == "quantity_selected")   //controlla se l'alimento già presente era salvato in grammi o in quantità
            {    
                updateContainedFoodQuantity(getFoodId($_POST['alimenti']),$_SESSION["fridge"],-$_POST['quantity'],$_POST['scadenza']);
                if (getContainedFoodQuantity(getFoodId($_POST['alimenti']),$_SESSION["fridge"],$_POST['scadenza'])[0]==0)          //se la quantità è sotto lo zero cancella l'alimento
                    clearContainedFoodModify(getFoodId($_POST['alimenti']),$_SESSION["fridge"],$_POST['scadenza']);           
                }
                else if ((getContainedFoodGram(getFoodId($_POST['alimenti']),$_SESSION["fridge"],$_POST['scadenza'])[0]!=NULL && $_POST['gram']!=NULL) && $_POST['selected'] == "gram_selected")
                {
                    updateContainedFoodGram(getFoodId($_POST['alimenti']),$_SESSION["fridge"],-$_POST['gram'],$_POST['scadenza']);
                    if(getContainedFoodGram(getFoodId($_POST['alimenti']),$_SESSION["fridge"],$_POST['scadenza'])[0]==0)               //se il peso è sotto lo zero cancella l'alimento
                        clearContainedFoodModify(getFoodId($_POST['alimenti']),$_SESSION["fridge"],$_POST['scadenza']);
                }
                else
                    echo "<script>alert('Errore! Inserire valore corrispondente di quantità o grammi!');</script>";
        }
        else if (checkContainedFood(getFoodId($_POST['other_food']),$_SESSION["fridge"],$_POST['scadenza'])==true)
        {
            if(checkContainedFood(getFoodId($_POST['other_food']),$_SESSION["fridge"],$_POST['scadenza'])==true)             //Se l'alimento è già presente in frigo e ha quella stessa data di scadenza allora aggiornare la quantità (sommandola)
            { 
                if((getContainedFoodQuantity(getFoodId($_POST['other_food']),$_SESSION["fridge"],$_POST['scadenza'])[0]!=NULL && $_POST['quantity']!=NULL) && $_POST['selected'] == "quantity_selected")   //controlla se l'alimento già presente era salvato in grammi o in quantità
                {    
                    updateContainedFoodQuantity(getFoodId($_POST['other_food']),$_SESSION["fridge"],-$_POST['quantity'],$_POST['scadenza']);
                    if (getContainedFoodQuantity(getFoodId($_POST['other_food']),$_SESSION["fridge"],$_POST['scadenza'])[0]==0)          //se la quantità è sotto lo zero cancella l'alimento
                        clearContainedFoodModify(getFoodId($_POST['other_food']),$_SESSION["fridge"],$_POST['scadenza']);           
                }
                else if ((getContainedFoodGram(getFoodId($_POST['other_food']),$_SESSION["fridge"],$_POST['scadenza'])[0]!=NULL && $_POST['gram']!=NULL) && $_POST['selected'] == "gram_selected")
                {
                    updateContainedFoodGram(getFoodId($_POST['other_food']),$_SESSION["fridge"],-$_POST['gram'],$_POST['scadenza']);
                    if(getContainedFoodGram(getFoodId($_POST['other_food']),$_SESSION["fridge"],$_POST['scadenza'])[0]==0)               //se il peso è sotto lo zero cancella l'alimento
                        clearContainedFoodModify(getFoodId($_POST['other_food']),$_SESSION["fridge"],$_POST['scadenza']);
                }
                else
                    echo "<script>alert('Errore! Inserire valore corrispondente di quantità o grammi!');</script>";
            }
        }
    }

    if(isset($_GET["cancel_food"]))
    {
        clearContainedFood($_GET["cancel_food"]);      //cancella l'alimento selezionato tramite bottoni, poi fare l'unset per evitare che resti settato
        unset($_GET["cancel_food"]);
        echo "<script>alert('Alimento cancellato con successo!');</script>";  
    }

?>

<?php
    include "navbar.html";

    if (!isset($_SESSION["fridge"]))            //se il frigo non è ancora settato allora scegliere se crearne uno nuovo o inserire un codice frigo già esistente
    {
    ?>
        <html>
            <head>
                <link rel="stylesheet" href="fridge.css"/>
                <meta name="viewport" content="width=device-width, initial-scale=1">  <!--Serve per fare scalare la grandezza della schermata in base al dispositivo-->
                <title>Frigo utente</title>
            </head>

            <body>
                <div>
                    <h1>Area My Fridge</h1>
                    <div id = "message"></div>

                    <form class="form-inline" method = "post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                        <button class="form-button" type="submit" value="Crea nuovo frigo" name="new_fridge">Crea nuovo frigo</button>
                        <input type="text" placeholder="Codice frigo" name ="id_fridge"/>
                        <button class="form-button" type="submit" value="Codice frigo" name="old_fridge">Ricerca</button>
                    </form>
                </div>
            </body>    
        </html><?php
    }
    else                //altrimenti stampa il form di inserimento e la tabella del frigo
    {?>
        <html>
            <head>
                <link rel="stylesheet" href="fridge.css"/>
                <meta name="viewport" content="width=device-width, initial-scale=1">  <!--Serve per fare scalare la grandezza della schermata in base al dispositivo--> 
                <title>Frigo utente</title>
            </head>

            <body>
                <h2>Area My Fridge</h2>

                <div class="second-div">                                        <!--Form di inserimento o modifica dell'alimento-->
                    <form class="add-food"method = "post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                        <label for="alimenti"><b>Tipo di alimento:</b></label>
                        <select id="alimenti" name="alimenti"></select>                                  <!-- Inserire le quantità -->
                        <input type="text" placeholder="Altro" name ="other_food"/>
                        <input type="radio" id="quantity_selected" name="selected" value="quantity_selected">
                        <label for="quantity"><b>Quantità:</b></label>
                        <input type="number" id="quantity" name="quantity" min = "1"/>
                        <input type="radio" id="gram_selected" name="selected" value="gram_selected">
                        <label for="gram"><b>Grammi:</b></label>             
                        <input type="number" id="gram" name="gram" step= 50 min = "0"/>
                        <label for="date"><b>Data di scadenza:</b></label>
                        <input type="date" name="scadenza" required/>                       <!-- Required fa si che non possa accettare l'input senza l'inserimento della data -->
                        
                        <button class="form-button" type="submit" value="Inserisci alimento" name="insert_food">Inserisci alimento</button>
                        <button class="form-button" type="submit" value="Modifica quantità alimento" name="modify_food">Modifica quantità</button>
                        <script>
                                                                                //script utilizzato per avere come opzioni selezionabili tutti i cibi contenuti nella tabella food
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
                    </form>
                </div>            
                
                <form class="table-form" method="get" id="bottoni" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">       <!--Serve per inserire i pulsanti all'interno della tabella-->
                    <table id="frigo">
                        <tr>
                            <th>Alimento</th>
                            <th>Quantità</th>
                            <th>Peso (grammi)</th>
                            <th>Data di scadenza</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                        <tr>
                        </tr>
                        <script>                                                                      //script utilizzato per la stampa dinamica di tutti gli alimenti salvati nel frigo
                            <?php 
                                $aliments = json_encode(getContainedFood($_SESSION["fridge"]));       //la funzione restituisce l'array contenente tutti i cibi ma è codificato in PHP, usiamo json_encode per fare la conversione
                                echo "const food_fridge = ". $aliments . ";\n";                             //usiamo echo per dichiarare l'array in javascript

                                $id_food = json_encode(getContainedFoodId($_SESSION["fridge"]));
                                echo "const jid_food =". $id_food . ";\n";
                            ?>

                            var food_table = document.getElementById('frigo');

                            for(let i = 0; i<food_fridge.length; i++)
                            {
                                const tr = food_table.insertRow();
                                for(let j=0;j<4;j++)
                                {
                                    let td = tr.insertCell();                               
                                    if(j==2 && food_fridge[i][j])
                                        td.innerHTML = food_fridge[i][j]+"g";                              //se l'alimento è salvato in grammi allora stampa la g alla fine
                                    else
                                        td.innerHTML = food_fridge[i][j];
                                }
                                
                                let status = tr.insertCell();                                               //dopo aver stampato tutte le informazioni allora aggiunge lo status alla riga corrispondente al cibo
                                let emoji = emoji_status(food_fridge[i][3]);
                                status.innerHTML = emoji;

                                var button = document.createElement('button');
                                
                                button.className = "table-button";
                                button.name = "cancel_food";                                //Crea il bottone di cancellazione dell'alimento
                                button.type = "submit";
                                button.value = food_fridge[i][4];                           //il valore è corrispondente all'id della riga della tabella contain per il bottone
                                button.innerHTML = "Cancella";
                                tr.appendChild(button);                                    //crea automaticamente le righe contenenti i vari alimenti
                                                          
                            }

                            function emoji_status(expiration_date) {
                                const current_date = new Date();                                                //funzione che controlla la data di scadenza dell'alimento e restituisce uno status con colori differenti
                                const exp_date = new Date(expiration_date);

                                let difference_in_time = exp_date.getTime() - current_date.getTime();           //variabile contenente la differenza in millisecondi tra la data di scadenza e la data odierna
                                let difference_in_days = difference_in_time / (1000 * 3600 * 24);               //calcola invece la differenza in giorni
                                
                                if (difference_in_days <0 )                                                     //se la differenza è minore di 0 allora l'alimento è scaduto o scade oggi
                                    return "&#128308;";                                                         //ritorna il codice corrispondente all'emoji del cerchio rosso
                                else if(7>difference_in_days>0)                                                 //se l'alimento scade tra una settimana ritorna un cerchio giallo
                                    return "&#128993;";
                                else if(difference_in_days>7)                                                   //se scade tra più di una settimana cerchio verde
                                    return "&#128994;";
                            }
                        </script>
                    </table>
                </form>
            </body>    
        </html><?php
    }?>
