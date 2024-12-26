<?php
require __DIR__ . '/../vendor/autoload.php';

use function Jawira\PlantUml\encodep;

$quizzes = getQuizzes('quizzes.json');

$id = 1;
foreach ($quizzes as &$quiz) {
    $umlImg = getUmlImg($quiz["uml code"]);
    $quiz["uml image"] = $umlImg;
    $quiz["id"] = $id++;
}

//忘れずに
unset($quiz);


function getQuizzes($filePath)
{
    $jsonContent = file_get_contents($filePath);

    // JSONをPHPの配列にデコード
    $quizzes = json_decode($jsonContent, true);

    return $quizzes;
}
function getUmlImg($umlCode)
{
    $encode = encodep($umlCode);
    $umlURL = "http://www.plantuml.com/plantuml/svg"  . "/" . $encode;
    $content = @file_get_contents($umlURL);
    if ($content === false) {
        error_log("変換失敗");
    } else {
        error_log("成功");
        return $content;
    }
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>練習問題一覧</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>

    <h1>練習問題一覧</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>タイトル</th>
                <th>テーマ</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($quizzes as $quiz) {
                echo "<tr>
                        <td>{$quiz['id']}</td>
                        <td>
                            <form action='../practice.php' method='post'>
                                <input type='hidden' name='code' value='" . htmlspecialchars($quiz['uml code'], ENT_QUOTES) . "'>
                                <input type='hidden' name='image' value='" . htmlspecialchars($quiz['uml image'], ENT_QUOTES) . "'>
                                <input type='hidden' name='id' value='" . htmlspecialchars($quiz['id'], ENT_QUOTES) . "'>
                                <input type='submit' name='title' value='" . htmlspecialchars($quiz['title'], ENT_QUOTES) . "'>
                            </form>
                        </td>
                        <td>{$quiz['theme']}</td>
                    </tr>";
            }
            ?>
        </tbody>
    </table>
</body>

</html>
