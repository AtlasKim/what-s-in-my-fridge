<?php
    session_start();    //Pagina di login
?>

<?php
    require 'db_coding.php'; //Import dei file contententi metodi e variabili globali per effettuare operazioni sul DB
    require 'config/db_config.php';
    /*$aliments = json_encode(getContainedFood($_SESSION["fridge"]));
    echo $aliments;
    $id_food = json_encode(getContainedFoodId($_SESSION["fridge"]));*/

    if(isset($_GET["cancel_food"]))
    {
        //echo $_GET["cancel_food"];
        clearContainedFood($_GET["cancel_food"],getFridgeId($_SESSION["user"]));                     //dovrebbe cancellare l'alimento selezionato tramite bottoni, poi fare l'unset per evitare che resti settato
        unset($_GET["cancel_food"]);
        echo "<script>alert('Alimento cancellato con successo!');</script>";  
    }
?>

<html>
    <head>
        <title>Frigo utente</title>
    </head>
    <body>
        <!--Visualizza tutti gli alimenti presenti nel frigo con la data di scadenza e un tasto accanto a ciascuno di essi per cancellarli-->
        <div id = "message"></div>
        <form method="get" id="bottoni" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">       <!--Serve per inserire i pulsanti all'interno della tabella-->
            <table id="frigo" style="width:100%">
                <tr>
                    <th>Nome alimento</th>
                    <th>Quantità</th>
                    <th>Data di scadenza</th>
                </tr>
                <tr>
                </tr>
                <script>
                    <?php 
                        $aliments = json_encode(getContainedFood($_SESSION["fridge"]));       //la funzione restituisce l'array contenente tutti i cibi ma è codificato in PHP, usiamo json_encode per fare la conversione
                        echo "const food = ". $aliments . ";\n";                             //usiamo echo per dichiarare l'array in javascript

                        $id_food = json_encode(getContainedFoodId($_SESSION["fridge"]));
                        echo "const jid_food =". $id_food . ";\n";
                    ?>

                    var food_table = document.getElementById('frigo');

                    for(let i = 0; i<food.length; i++)
                    {
                        const tr = food_table.insertRow();
                        for(let j=0;j<3;j++)
                        {
                            let td = tr.insertCell();                    //provare a usare questa funzione per dare a ogni pulsante di cancellazione l'id del cibo corrispondente
                            td.innerHTML = food[i][j];                 //aggiunge al datalist dinamicamente tutte le opzioni presenti nel database
                        }

                        var button = document.createElement('button');
                        
                        button.name = "cancel_food";                                //scoprire come dare l'input a PHP per passare l'id dell'alimento da cancellare tramite la funzione
                        button.type = "submit";
                        button.value = jid_food[i];                                 //prende l'id corrispondente all'alimento in questione
                        button.innerHTML = "Cancella alimento";
                        //button.onclick = cancelFood(jid_food[i]);
                        
                                                                                //crea automaticamente le righe contenenti i vari alimenti
                        tr.appendChild(button);                                                    //inserire qui pulsante per la cancellazione dell'oggetto in questione dalla tabella contain
                    }


                    /*function cancelFood(id_food) {
                        let text = "Sei sicuro di voler eliminare l'alimento "+id_food+"?";
                        if (confirm(text) == true) {
                            text = "You pressed OK!";
                        } else {
                            text = "You canceled!";
                        }
                        document.getElementById("message").innerHTML = text;
                        }*/
                </script>
            </table>
        </form>
    </body>
</html>