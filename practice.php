<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlantUML Server</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1><?php echo $_POST["id"] . ":" . $_POST["title"] ?></h1>
        <button onclick="location.href='quiz/quiz.php'">問題一覧に戻る</button>
    </header>
    <div class="button-container">
        <button id="code">Answer Code</button>
        <button id="svg">Answer UML</button>
    </div>
    <div class="container">
        <div id="editor-container" class="box"></div>
        <div id="user answer" class="box"> </div>
        <div id="answer" class="box"></div>
    </div>

    <script src=" https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.34.1/min/vs/loader.min.js">
    </script>

    <script>
        require.config({
            paths: {
                'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.34.1/min/vs'
            }
        });
        const svg = document.getElementById("svg");
        const code = document.getElementById("code");
        const codeArea = document.getElementById("user answer");
        const answerAra = document.getElementById("answer");
        answerAra.innerHTML = <?php echo json_encode($_POST["image"]) ?>;
        require(['vs/editor/editor.main'], function() {
            editor = monaco.editor.create(document.getElementById('editor-container'), {
                value: "@startuml\nAlice -> Bob: Hello\n@enduml",
                language: 'plaintext'
            });
            fetchUML(editor.getValue());
            svg.addEventListener("click", function() {
                display("svg");
            })
            code.addEventListener("click", function() {
                display("code");
            })
            editor.onDidChangeModelContent((event) => {
                fetchUML(editor.getValue(), "user");
            });
        });

        function display(format) {
            if (format == "code") {
                if (svg.classList.contains("pressed")) svg.classList.remove("pressed");
                code.classList.toggle("pressed");
                answerAra.innerHTML = <?php echo json_encode($_POST["code"]); ?>;
            } else if (format == "svg") {
                if (code.classList.contains("pressed")) code.classList.remove("pressed");
                svg.classList.toggle("pressed");
                answerAra.innerHTML = <?php echo json_encode($_POST["image"]); ?>;
            } else if (format == "user") {
                fetchUML(editor.getValue());
            }
        }

        function fetchUML(value) {
            value = value.replace("@startuml", "").replace("@enduml", "");
            value = encodeURIComponent(value);
            fetch("convert.php?plantUML=" + value + "&format=svg").then(response => {
                if (response.ok) return response.text()
                else throw new Error(response.statusText)
            }).then(data => {
                codeArea.innerHTML = data;
            }).catch(error => {
                //console.log(error);
            });
        }
    </script>


</body>

</html>
