<?php
    session_start();    //Pagina di login
?>

<?php
    require 'db_coding.php'; //Import dei file contententi metodi e variabili globali per effettuare operazioni sul DB
    require 'config/db_config.php';

    if(isset($_GET["cancel_food"]))
    {
        //var_dump($_GET["cancel_food"]);
        //var_dump($_GET['expiration']);
        //$quantity = getContainedFoodQuantity($_GET["cancel_food"],getFridgeId($_SESSION["user"]),$_GET["expiration"]);
        clearContainedFood($_GET["cancel_food"]);      //dovrebbe cancellare l'alimento selezionato tramite bottoni, poi fare l'unset per evitare che resti settato
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
                            let td = tr.insertCell();                               //provare a usare questa funzione per dare a ogni pulsante di cancellazione l'id del cibo corrispondente
                            td.innerHTML = food[i][j];                              //aggiunge al datalist dinamicamente tutte le opzioni presenti nel database
                        }
                        
                        let status = tr.insertCell();
                        let emoji = emoji_status(food[i][2]);
                        status.innerHTML = emoji;

                        var button = document.createElement('button');
                        
                        button.name = "cancel_food";                                //scoprire come dare l'input a PHP per passare l'id dell'alimento da cancellare tramite la funzione
                        button.type = "submit";
                        button.value = food[i][3];                                 //prende l'id e la data di scadenza corrispondente all'alimento in questione
                        button.innerHTML = "Cancella alimento";
                        tr.appendChild(button);                                    //crea automaticamente le righe contenenti i vari alimenti
                                                                                //inserire qui pulsante per la cancellazione dell'oggetto in questione dalla tabella contain
                    }


                    function emoji_status(expiration_date) {
                        const current_date = new Date();
                        const exp_date = new Date(expiration_date);

                        let difference_in_time = exp_date.getTime() - current_date.getTime();    //variabile contenente la differenza in millisecondi tra le due date
                        let difference_in_days = difference_in_time / (1000 * 3600 * 24);               //calcola invece la differenza in giorni tra la data di scadenza e la data odierna
                        
                        if (difference_in_days <0 )                                                     //se la differenza è minore di 0 allora l'alimento è scaduto 
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
</html>