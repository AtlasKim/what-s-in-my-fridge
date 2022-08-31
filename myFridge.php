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

    /*if(isset (($_POST['insert_food'])))                     //Metodo per l'inserimento del cibo nella tabella
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
    }*/
    /*else if(isset($_POST['modify_food']))               //se l'alimento deve essere modificato allora la sua quantità va ridotta
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
    }*/

    /*if(isset($_GET["cancel_food"]))
    {
        clearContainedFood($_GET["cancel_food"]);      //cancella l'alimento selezionato tramite bottoni, poi fare l'unset per evitare che resti settato
        unset($_GET["cancel_food"]);
        echo "<script>alert('Alimento cancellato con successo!');</script>";  
    }*/

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


                <script>
                    window.onload = showFood(printFood);                             //DEFINIRE SEMPRE LA FUNZIONE DA CHIAMARE IN CALLBACK
                    //window.addEventListener("load",bindEvents);
                    //window.addEventListener("load",showFood);
                    

                    function foodDelete(id_row)
                    {
                        var oReq = new XMLHttpRequest();
                        oReq.onload = function(){
                            alert('Alimento cancellato con successo!');
                            showFood(printFood);
                        };          //quando la richiesta viene caricata allora mostra l'alert con il messaggio
                        oReq.open("delete", "api.php/contain/" + id_row, true);                         //passa l'id della riga da cancellare nella tabella contain
                        oReq.send();
                    }
                    
                    

                    function modifyFood(food)
                    {
                        var food_fridge = JSON.parse(food);
                        console.log("chiamata la funzione di modifica alimento");
                        let cibo = {};                                                                  //creaiamo un oggetto cibo da trasformare in stringa dopo durante la modifica
                        cibo.id_cibo = document.getElementById("alimenti").value;                      //inseriamo all'interno delle variabili tutti i campi del form che ci possono servire
                        <?php
                            $id_frigo = json_encode($_SESSION["fridge"]);
                            echo "cibo.id_frigo = ".$id_frigo. ";\n";
                        ?>
                        let q_selected = document.getElementById("quantity_selected").checked;
                        let g_selected = document.getElementById("gram_selected").checked;
                        cibo.quantita = document.getElementById("quantity").value;
                        cibo.grammi = document.getElementById("gram").value;
                        cibo.data_scadenza = document.getElementById("scadenza").value;

                        
                        for(let i = 0; i<food_fridge.length; i++)
                        {
                            if((food_fridge[i][0] == cibo.id_cibo) && food_fridge[i][6] == cibo.data_scadenza)          //se gli id corrispondono e la data di scadenza corrisponde possiamo modificare l'alimento
                            {
                                console.log("Ho trovato l'alimento con la data di scadenza");
                                if((food_fridge[i][5] != null && !cibo.grammi==false) && g_selected == true)          //se 
                                {
                                    console.log("L'alimento era salvato in grammi e tu hai inserito i grammi da modificare");
                                    cibo.quantita = null;
                                    var jsondata = JSON.stringify(cibo);
                                    

                                    var oReq = new XMLHttpRequest();
                                    oReq.onload = function(){
                                        alert('Alimento modificato con successo!');
                                        showFood(printFood);
                                        document.getElementById('form_insert').reset();
                                    };                                                          //quando la richiesta viene caricata allora mostra l'alert con il messaggio
                                    oReq.open("put", "api.php/contain/" + food_fridge[i][7], true);                                            
                                    oReq.send(jsondata);
                                    console.log(jsondata);
                                }
                                else if((food_fridge[i][4] != null && !cibo.quantita==false) && q_selected == true)
                                {
                                    console.log("L'alimento era salvato in unità e tu hai inserito l'unità da modificare");
                                    cibo.grammi = null;
                                    var jsondata = JSON.stringify(cibo);

                                    var oReq = new XMLHttpRequest();
                                    oReq.onload = function(){
                                        alert('Alimento modificato con successo!');
                                        showFood(printFood);
                                        document.getElementById('form_insert').reset();
                                    };          //quando la richiesta viene caricata allora mostra l'alert con il messaggio
                                    oReq.open("put", "api.php/contain/" + food_fridge[i][7], true);                            
                                    oReq.send(jsondata);
                                }
                                else
                                    alert("Dati incongruenti, prego rieffettuare correttamente la modifica");
                            }
                        }
                        //document.getElementById('form_insert').reset();
                    }

                    
                    function insertFood()
                    {
                        let cibo = {};   
                                                                                               //creaiamo un oggetto cibo da trasformare in stringa dopo durante la modifica
                        let alimenti = document.getElementById("alimenti").value;                      //inseriamo all'interno delle variabili tutti i campi del form che ci possono servire
                        <?php
                            $id_frigo = json_encode($_SESSION["fridge"]);
                            echo "cibo.id_frigo = ".$id_frigo. ";\n";
                        ?>
                        let q_selected = document.getElementById("quantity_selected").checked;
                        let g_selected = document.getElementById("gram_selected").checked;
                        let altri_alimenti = document.getElementById("other_food").value.toLowerCase();
                        cibo.quantita = document.getElementById("quantity").value;
                        cibo.grammi = document.getElementById("gram").value;
                        cibo.data_scadenza = document.getElementById("scadenza").value;

                        console.log(q_selected);
                        console.log(g_selected);
                                /*if (alimenti != null)
                                {
                                    food_fridge
                                }*/
                                
                        if(alimenti != "empty" && cibo.data_scadenza)
                        {
                            cibo.id_cibo = alimenti;
                            if(q_selected == true && !cibo.quantita==false)  //parte la richiesta AJAX con il POST e la quantità
                            {
                                cibo.grammi = null;
                                var jsondata = JSON.stringify(cibo);
                                        
                                var oReq = new XMLHttpRequest();
                                oReq.onload = function(){
                                    alert('Alimento inserito con successo!');
                                    showFood(printFood);
                                    document.getElementById('form_insert').reset();
                                };                                                          //quando la richiesta viene caricata allora mostra l'alert con il messaggio
                                oReq.open("post", "api.php/contain/", true);                                            
                                oReq.send(jsondata);
                                console.log(jsondata);
                            }
                                        
                            else if(g_selected == true && !cibo.grammi==false) //parte la richiesta AJAX con il POST e i grammi
                            {
                                cibo.quantita = null;
                                var jsondata = JSON.stringify(cibo);
                                        
                                var oReq = new XMLHttpRequest();
                                oReq.onload = function(){
                                    alert('Alimento inserito con successo!');
                                    showFood(printFood);
                                    document.getElementById('form_insert').reset();
                                };                                                          //quando la richiesta viene caricata allora mostra l'alert con il messaggio
                                oReq.open("post", "api.php/contain/", true);                                            
                                oReq.send(jsondata);
                                console.log(jsondata);
                            }
                                        
                        }
                                
                        else if(alimenti == "empty" && altri_alimenti !=null)
                        {
                            console.log(altri_alimenti);
                            getOptionFood(insertOptionFood);                 //deve inserire nella lista delle opzioni il nuovo alimento se non era già presente

                            /*if(q_selected == true && !cibo.quantita==false)  //parte la richiesta AJAX con il POST e la quantità
                            {
                                cibo.grammi = null;
                                var jsondata = JSON.stringify(cibo);
                                        
                                var oReq = new XMLHttpRequest();
                                oReq.onload = function(){
                                    alert('Alimento inserito con successo!');
                                    showFood(printFood);
                                    document.getElementById('form_insert').reset();
                                };                                                          //quando la richiesta viene caricata allora mostra l'alert con il messaggio
                                oReq.open("post", "api.php/contain/", true);                                            
                                oReq.send(jsondata);
                                console.log(jsondata);
                            }
                                        
                            else if(g_selected == true && !cibo.grammi==false) //parte la richiesta AJAX con il POST e i grammi
                            {
                                cibo.quantita = null;
                                var jsondata = JSON.stringify(cibo);
                                        
                                var oReq = new XMLHttpRequest();
                                oReq.onload = function(){
                                    alert('Alimento inserito con successo!');
                                    showFood(printFood);
                                    document.getElementById('form_insert').reset();
                                };                                                          //quando la richiesta viene caricata allora mostra l'alert con il messaggio
                                oReq.open("post", "api.php/contain/", true);                                            
                                oReq.send(jsondata);
                                console.log(jsondata);
                            }*/
                                        
                        }

                        else
                        {
                            alert("Errore durante l'inserimento dati");
                        }

                        //document.getElementById('form_insert').reset();
                    }

                    function insertFoodNotPresent(req)
                    {
                        let cibo = {};
                        let opzioni = JSON.parse(req);   
                        //let opzioni = JSON.parse(req);
                                                                                               //creaiamo un oggetto cibo da trasformare in stringa dopo durante la modifica
                        <?php
                            $id_frigo = json_encode($_SESSION["fridge"]);
                            echo "cibo.id_frigo = ".$id_frigo. ";\n";
                        ?>
                        let q_selected = document.getElementById("quantity_selected").checked;
                        let g_selected = document.getElementById("gram_selected").checked;
                        let altri_alimenti = document.getElementById("other_food").value.toLowerCase();
                        cibo.quantita = document.getElementById("quantity").value;
                        cibo.grammi = document.getElementById("gram").value;
                        cibo.data_scadenza = document.getElementById("scadenza").value;
                        cibo.id_cibo = 0;

                        console.log(opzioni);                       //l'idea era quella di scorrere tutto il nuovo 
                        for (let i = 0;i<opzioni.length;i++)
                        {
                            console.log(opzioni[i][0]);
                            console.log(opzioni[i][1]);
                            if(opzioni[i][1] == altri_alimenti)
                                cibo.id_cibo = opzioni[i][0];
                        }

                        if(q_selected == true && !cibo.quantita==false)  //parte la richiesta AJAX con il POST e la quantità
                            {
                                cibo.grammi = null;
                                var jsondata = JSON.stringify(cibo);
                                        
                                var oReq = new XMLHttpRequest();
                                oReq.onload = function(){
                                    alert('Alimento inserito con successo!');
                                    showFood(printFood);
                                    document.getElementById('form_insert').reset();
                                };                                                          //quando la richiesta viene caricata allora mostra l'alert con il messaggio
                                oReq.open("post", "api.php/contain/", true);                                            
                                oReq.send(jsondata);
                                console.log(jsondata);
                            }
                                        
                            else if(g_selected == true && !cibo.grammi==false) //parte la richiesta AJAX con il POST e i grammi
                            {
                                cibo.quantita = null;
                                var jsondata = JSON.stringify(cibo);
                                        
                                var oReq = new XMLHttpRequest();
                                oReq.onload = function(){
                                    alert('Alimento inserito con successo!');
                                    showFood(printFood);
                                    document.getElementById('form_insert').reset();
                                };                                                          //quando la richiesta viene caricata allora mostra l'alert con il messaggio
                                oReq.open("post", "api.php/contain/", true);                                            
                                oReq.send(jsondata);
                                console.log(jsondata);
                            }
                                        
                    }

                    function showFood(callback)                    //sfruttiamo una funzione di callback chiamata print food
                    {
                        <?php
                            $id_frigo = json_encode($_SESSION["fridge"]);
                            echo "const fridge = ".$id_frigo. ";\n";
                        ?>

                        var oReq = new XMLHttpRequest();                            //apertura richiesta HTTP
                        oReq.onload = function(){
                                callback(oReq.responseText);
                        };
                        oReq.open("get", "api.php/contain/"+ fridge, true);
                        oReq.send(); //dovrebbe permetterci di inviare l'id del frigo attualmente utilizzato altrimenti usare l'esempio sotto
                    }

                    function printFood(oReq)
                    {
                        var food_fridge = JSON.parse(oReq);        //funzione per convertire in array JSON la risposta
                        //console.log(food_fridge);
                        var food_table = document.getElementById('frigo');
                        var table_body = document.getElementById('body');
                        table_body.innerHTML = null;

                            for(let i = 0; i<food_fridge.length; i++)
                            {
                                const tr = table_body.insertRow();
                                for(let j=1;j<=6;j++)
                                {
                                    if(j!=2 & j!=3)                     //non ci servono quindi non li stampiamo a schermo
                                    {
                                        let td = tr.insertCell();                               
                                        if(j==5 && food_fridge[i][j])
                                            td.innerHTML = food_fridge[i][j]+"g";                              //se l'alimento è salvato in grammi allora stampa la g alla fine
                                        else if(j==6)
                                            td.innerHTML = food_fridge[i][j].split("-").reverse().join("-");        //modifica il formato della data da YYYY-MM-DD a DD-MM-YYYY
                                        else
                                            td.innerHTML = food_fridge[i][j];
                                    }
                                }
                                        
                                let status = tr.insertCell();                                               //dopo aver stampato tutte le informazioni allora aggiunge lo status alla riga corrispondente al cibo
                                let emoji = emoji_status(food_fridge[i][6]);
                                status.innerHTML = emoji;

                                var button = document.createElement('button');
                                        
                                button.className = "table-button";
                                button.name = "cancel_food";                                //Crea il bottone di cancellazione dell'alimento
                                button.type = "submit";
                                button.value = food_fridge[i][7];                           //il valore è corrispondente all'id della riga della tabella contain per il bottone
                                button.innerHTML = "Cancella";
                                button.onclick = function(){foodDelete(food_fridge[i][7])};         //non so come ho fatto ma funziona
                                tr.appendChild(button);                                     //crea automaticamente le righe contenenti i vari alimenti
                                                                
                            }
                                    //document.getElementById("ajaxres").innerHTML = oReq.responseText;
                    } 
                    
                    function getOptionFood(callback)                    //funzione atta ad ottenere le opzioni dei cibi già salvati nel database
                    {
                        var oReq = new XMLHttpRequest();                            //apertura richiesta HTTP
                        oReq.onload = function(){
                            callback(oReq.responseText);
                        };
                        oReq.open("get", "api.php/food/", true);
                        oReq.send(); //dovrebbe permetterci di inviare l'id del frigo attualmente utilizzato altrimenti usare l'esempio sotto
                    }

                    function showOptionFood(oReq)
                    {
                        var food = JSON.parse(oReq)       //otteniamo la lista di opzioni nel database food
                        console.log(food);
                            
                        var food_list = document.getElementById('alimenti');
                        food_list.innerHTML = null;

                        var option = document.createElement('option');
                        option.value = "empty";                               
                        option.text = "--";
                        food_list.appendChild(option); 

                        for(let i = 0; i<food.length; i++)
                        {                 //inserisce dinamicamente le opzioni di tutti i cibi già presenti nel database
                            var option = document.createElement('option');  //crea l'opzione
                            option.value = food[i][0];                         //il valore dell'opzione sarà corrispondente al nome dei cibi      
                            option.text = food[i][1];                          //così come il testo al suo interno
                            food_list.appendChild(option);                  //aggiunge al datalist dinamicamente tutte le opzioni presenti nel database
                        }
                    }

                    function insertOptionFood(food)                     //funzione atta a inserire un nuovo alimento nella tabella food
                    {
                        let opzioni = JSON.parse(food)

                        let alimento = {};
                        let altri_alimenti = document.getElementById("other_food").value.toLowerCase();
                        alimento.nome_cibo = altri_alimenti;
                        let last_id = 0;

                        let presentDatabase = false;
                        for(let i = 0; i<opzioni.length;i++)            //scorre l'array ottenuto dal get della tabella food e si assicura che l'elemento non sia già presente in tabella
                        {
                            last_id = opzioni[i][0];                    //salvo l'ultimo id ottenuto
                            console.log(last_id);
                            if(opzioni[i][1] == alimento.nome_cibo)
                            {
                                alert("Nuova opzione già presente");
                                presentDatabase = true;
                            }
                        }
                        console.log(last_id);
                        console.log(typeof opzioni);
                        
                        if(presentDatabase == false)                   //se non è presente manda il post e lo inserisce
                        {
                            var jsondata = JSON.stringify(alimento);

                            var oReq = new XMLHttpRequest();
                            oReq.onload = function(){
                                getOptionFood(showOptionFood);
                                getOptionFood(insertFoodNotPresent);
                            };
                            oReq.open("post", "api.php/food/", true);
                            oReq.send(jsondata);
                        }   
                    }
                </script>
            </head>

            <body>
                <h2>Area My Fridge</h2>

                <div class="second-div">                                        <!--Form di inserimento o modifica dell'alimento-->
                    <form class="add-food" id= "form_insert" method = "post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">    <!--  -->
                        <label for="alimenti"><b>Tipo di alimento:</b></label>
                        <select id="alimenti" name="alimenti"></select>                                  <!-- Inserire le quantità -->
                        <input type="text" placeholder="Altro" name ="other_food" id="other_food"/>
                        <input type="radio" id="quantity_selected" name="selected" value="quantity_selected">
                        <label for="quantity"><b>Quantità:</b></label>
                        <input type="number" id="quantity" name="quantity" min = "1"/>
                        <input type="radio" id="gram_selected" name="selected" value="gram_selected">
                        <label for="gram"><b>Grammi:</b></label>             
                        <input type="number" id="gram" name="gram" step= 50 min = "0"/>
                        <label for="date"><b>Data di scadenza:</b></label>
                        <input type="date" name="scadenza" id="scadenza" required/>                       <!-- Required fa si che non possa accettare l'input senza l'inserimento della data -->
                        
                        <button class="form-button" type="submit" id = "insert" value="Inserisci alimento" name="insert_food">Inserisci alimento</button>
                        <button class="form-button" type="submit" id = "modify" value="Modifica quantità alimento" name="modify_food" >Modifica quantità</button>
                        <script>
                            
                            //script utilizzato per avere come opzioni selezionabili tutti i cibi contenuti nella tabella food
                            document.getElementById("insert").onclick = function(){insertFood()};
                            //document.getElementById("modify").onclick = function(){showFood(modifyFood)};
                            document.getElementById("modify").onclick = function(){showFood(modifyFood)};
                            /*<?php 
                                $foods = json_encode(getFood());                //la funzione restituisce l'array contenente tutti i cibi ma è codificato in PHP, usiamo json_encode per fare la conversione
                                echo "const food = ". $foods . ";\n";           //usiamo echo per dichiarare l'array in javascript
                            ?>*/

                            getOptionFood(showOptionFood);       //otteniamo la lista di opzioni nel database food
                            /*console.log(food);

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
                            }*/
                        </script>        
                    </form>
                </div>            
                
                <form class="table-form" method="get" id="bottoni" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">       <!--Serve per inserire i pulsanti all'interno della tabella-->
                    <table id="frigo">
                        <thead>
                            <tr>
                                <th>Alimento</th>
                                <th>Quantità</th>
                                <th>Peso (grammi)</th>
                                <th>Data di scadenza</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="body">
                            <tr>
                            </tr>
                        </tbody>
                        <script>                                                                      //script utilizzato per la stampa dinamica di tutti gli alimenti salvati nel frigo

                            document.getElementById("form_insert").addEventListener("submit", function(event){
                                event.preventDefault()
                            });

                            document.getElementById("bottoni").addEventListener("submit", function(event){
                                event.preventDefault()
                            });


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
                <p id="ajaxres"></p>
                <p id="prova"></p>
            </body>    
        </html><?php
    }?>
