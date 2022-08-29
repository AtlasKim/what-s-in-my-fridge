<html>
    <body>

        <p>
            <a id="link" href="https://www.w3schools.com"></a>
        </p>
        
    </body>

    <script>
        let alimento = "cioccolato";
        const element = document.getElementById("link");
        element.innerHTML = "Per ricette con "+alimento+" clicca qui!";
        element.href = "http://www.google.com/search?q=ricette+"+alimento;
    </script>
</html>