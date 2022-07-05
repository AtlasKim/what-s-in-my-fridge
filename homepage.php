<?php
    session_start();                //Homepage del sito, racchiude poche semplici informazioni al riguardo
    include "navbar.html"
?>

<html>
    <head>
            <meta name="viewport" content="width=device-width, initial-scale=1">  <!--Serve per fare scalare la grandezza della schermata in base al dispositivo-->
            <title>Homepage</title>
            <style>
                h1 {  
                    font-family: 'Exo 2', sans-serif;
                    text-align: center;
                    color: #0059B3;
                    font-size: 100px;
                    font-weight: 600;
                    margin-top: 30px;
                }
                
                h3 {
                    font-family: 'Exo 2', sans-serif;
                    text-align: center;
                    color: #0059B3;
                    margin-top: 30px;
                    font-size: 25px;
                }
                
                .home {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    font-weight: 600;
                }
                
                .last
                {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    margin-bottom: 150px;
                    font-weight: 600;
                }
            </style>
    </head>

    <body>
        <h1>WIMF</h1>
        <div class="home">
            <img src="logo.png" alt="Logo WIMF">
        </div>
        <br>
        <div>
        <h3>Presentazione</h3>
        <div class="home">
            What's In My Fridge è una web app creata con lo scopo di limitare gli sprechi di cibo permettendo all'utente di registrare la sua spesa, in modo tale da poter controllare il suo frigo al volo.
        </div>

        <h3>Funzionalità</h3>
        <div class="last">
            What's In My Fridge permette all'utente di creare un frigo virtuale e di condividere con familiari o coinquilini il suo codice, in modo che ognuno possa controllare cos'è presente nel frigo.
        </div>
    </body>
</html>