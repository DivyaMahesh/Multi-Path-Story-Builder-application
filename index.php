<?php
    $conn = mysqli_connect("localhost", "root", "root", "divya") or die("Cannot Connect to MySQL Server!");
    function render($Location) {
        global $conn;
        $SentenceID = (isset($_GET["ID"])) ? mysqli_real_escape_string($conn, $_GET["ID"]) : "1";
        $layout = array(
            "T" => null,
            "L" => null,
            "C" => $SentenceID,
            "R" => null,
            "B" => null
        );
        $res = mysqli_query($conn, "SELECT * FROM `sentence` WHERE `SentenceParent`='$SentenceID'");
        if ($res && mysqli_num_rows($res) > 0)
            while (false != ($dat = mysqli_fetch_assoc($res))) {
                $layout[$dat["SentenceLocation"]] = $dat["SentenceID"];
            }
        renderBasedOnID($layout[$Location], $Location);
    }
    function renderBasedOnID($SentenceID, $Location) {
        global $conn;
        $res = mysqli_query($conn, "SELECT * FROM `sentence` WHERE `SentenceID`='$SentenceID'");
        if ($res && mysqli_num_rows($res) == 1 && $dat = mysqli_fetch_assoc($res))
            if ($dat["SentenceID"] == 1)
                echo $dat["SentenceCont"];
            else
                echo '<a href="./?ID=' . $dat["SentenceID"] .  '">' . $dat["SentenceCont"] . '</a>';
        else { ?>
            <form action="" method="post">
                <input type="hidden" name="Parent" value="<?php echo (($SentenceID == 1 && $Location == "C") ? '0' : ((isset($_GET["ID"])) ? mysqli_real_escape_string($conn, $_GET["ID"]) : "1")); ?>" />
                <input type="hidden" name="Location" value="<?php echo $Location; ?>" />
                <textarea name="SentenceCont"></textarea>
                <input type="submit" value="Save" />
            </form>
<?php }
    }
    // POST
    if (count($_POST)) {
        header("Content-type: text/plain");
        $SentenceCont = mysqli_real_escape_string($conn, $_POST["SentenceCont"]);
        $SentenceLocation = mysqli_real_escape_string($conn, $_POST["Location"]);
        $SentenceParent = mysqli_real_escape_string($conn, $_POST["Parent"]);
        if (mysqli_query($conn, "INSERT INTO `sentence` (`SentenceCont`, `SentenceLocation`, `SentenceParent`) VALUES ('$SentenceCont', '$SentenceLocation', '$SentenceParent')")) {
            header("Location: ./?ID=" . ((isset($_GET["ID"])) ? mysqli_real_escape_string($conn, $_GET["ID"]) : "1"));
            die();
        } else {
            die("DB Error: " . mysqli_error($conn));
        }

    }
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Sentence Connector</title>
        <style>
            * {margin: 0; padding: 0; list-style: none; font-family: 'Segoe UI'; line-height: 1; box-sizing: border-box;}
            body {text-align: center; font-size: 10pt;}
            h1 {font-weight: normal; font-size: 1.5em; margin: 0 0 10px; line-height: 2;}
            table {width: 50%; margin: 25px auto; border-collapse: collapse;}
            table td textarea,
            table td {border: 1px solid #ccc; padding: 5px;}
            table td textarea {font-family: 'Monaco', 'Consolas'; resize: none; height: 5em; display: block; width: 100%;}
            table td textarea:focus {outline: none;}
            table td input {padding: 2px 5px; cursor: pointer; display: block; margin: 5px auto 0; line-height: 1.3; border: 1px solid #ccc; background-color: #eef;}
            table td textarea:focus + input {border: 1px solid #99f; background-color: #ccf;}
        </style>
    </head>
    <body>
        <h1>Sentence Maker</h1>
        <a href="./">Back to Start</a>
        <table>
            <tr>
                <td></td>
                <td><?php render("T"); ?></td>
                <td></td>
            </tr>
            <tr>
                <td><?php render("L"); ?></td>
                <td><?php render("C"); ?></td>
                <td><?php render("R"); ?></td>
            </tr>
            <tr>
                <td></td>
                <td><?php render("B"); ?></td>
                <td></td>
            </tr>
        </table>
    </body>
</html>
