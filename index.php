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
        <h1>PlantUML Server</h1>
        <p>変化がない場合は構文が間違えてる可能性があります</p>
        <button onclick="location.href='quiz/quiz.php'">練習問題</button>
    </header>
    <div class="button-container">
        <button id="png">PNG</button>
        <button id="svg">SVG</button>
        <button id="txt">ASCII-Art</button>
        <button id="download">Download</button>

    </div>
    <div class="container">
        <div id="editor-container" class="box"></div>
        <div id="output-container" class="box"> </div>
    </div>
    <script src=" https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.34.1/min/vs/loader.min.js">
    </script>

    <script>
        require.config({
            paths: {
                'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.34.1/min/vs'
            }
        });
        let png = document.getElementById("png");
        let svg = document.getElementById("svg");
        let txt = document.getElementById("txt");
        let outputArea = document.getElementById("output-container");
        let download = document.getElementById("download");

        require(['vs/editor/editor.main'], function() {
            editor = monaco.editor.create(document.getElementById('editor-container'), {
                value: "@startuml\nAlice -> Bob: Hello\n@enduml",
                language: 'plaintext'
            });
            fetchUML(editor.getValue(), "svg");

            png.addEventListener("click", function() {
                pressButton("png");
            })
            svg.addEventListener("click", function() {
                pressButton("svg");
            })
            txt.addEventListener("click", function() {
                pressButton("txt");
            })
            download.addEventListener("click", function() {
                pressButton("download");
            })
            editor.onDidChangeModelContent((event) => {
                if (txt.classList.contains("pressed")) fetchUML(editor.getValue(), "txt");
                else if (png.classList.contains("pressed")) fetchUML(editor.getValue(), "png");
                else fetchUML(editor.getValue(), "svg"); //指定なしならsvg
            });
        });

        function pressButton(format) {
            if (format == "png") {
                if (txt.classList.contains("pressed")) txt.classList.remove("pressed");
                if (svg.classList.contains("pressed")) svg.classList.remove("pressed");
                png.classList.toggle("pressed");
                fetchUML(editor.getValue(), "png");

            } else if (format == "svg") {
                if (txt.classList.contains("pressed")) txt.classList.remove("pressed");
                if (png.classList.contains("pressed")) png.classList.remove("pressed");
                svg.classList.toggle("pressed");
                fetchUML(editor.getValue(), "svg");

            } else if (format == "txt") {
                if (png.classList.contains("pressed")) png.classList.remove("pressed");
                if (svg.classList.contains("pressed")) svg.classList.remove("pressed");
                txt.classList.toggle("pressed");
                fetchUML(editor.getValue(), "txt");

            } else {
                if (txt.classList.contains("pressed")) downloadFile("UML.txt");
                else if (png.classList.contains("pressed")) downloadFile("UML.png");
                else downloadFile(); //指定なしならsvg
            }
        }

        function downloadFile(fileName = "UML.svg") {
            let url;
            if (fileName == "UML.txt ") {
                const blob = new Blob([outputArea.innerHTML], {
                    type: "text/plain"
                });
                url = URL.createObjectURL(blob);

            } else if (fileName == "UML.png") {
                const imageSrc = document.getElementsByTagName('img')[0].src;
                url = imageSrc;
            } else {
                const blob = new Blob([outputArea.innerHTML], {
                    type: "text/html"
                });
                url = URL.createObjectURL(blob);
            }

            let a = document.createElement("a");
            a.href = url;
            a.download = fileName;
            document.body.append(a);
            a.click();
            document.body.removeChild(a);
            if (url.startsWith('blob:')) URL.revokeObjectURL(url);
        }

        function fetchUML(value, format) {
            value = value.replace("@startuml", "").replace("@enduml", "");
            value = encodeURIComponent(value);
            if (format == "png") {
                fetch("convert.php?plantUML=" + value + "&format=" + format).then(response => {
                    if (response.ok) return response.blob()
                    else throw new Error(response.statusText);
                }).then(data => {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(data);
                    outputArea.innerHTML = "";
                    outputArea.appendChild(img);
                }).catch(error => {
                    //console.log(error);
                });
            } else {
                fetch("convert.php?plantUML=" + value + "&format=" + format).then(response => {
                    if (response.ok) return response.text()
                    else throw new Error(response.statusText)
                }).then(data => {
                    outputArea.innerHTML = data;
                }).catch(error => {
                    //console.log(error);
                });
            }
        }
    </script>


</body>

</html>
