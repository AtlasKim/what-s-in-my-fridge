<?php
    $alimento = "carciofi";
    $curl= curl_init();
    curl_setopt($curl, CURLOPT_URL, 'http://www.google.com/search?q=ricette+'.$alimento.'');
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($curl);
    curl_close($curl);

    echo $result;
?>

<html>
<body>
    <div id="link">
    </div>
</body>

<script>
    g
</script>
</html>