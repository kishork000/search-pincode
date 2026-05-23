<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>

    <body>
       
        <p id="msg"></p>

        <label for="text">Text</label>
        <input type="text" id="text" x-webkit-speech>

    </body>
    <script>
    var msg = document.getElementById('msg');

    if (document.createElement('input').webkitSpeech === undefined) {
        msg.innerHTML = "x-webkit-speech is <strong>not supported</strong> in your browser.";
    } else {
        msg.innerHTML = "x-webkit-speech is <strong>supported</strong> in your browser.";
    }
    </script>

</html>
