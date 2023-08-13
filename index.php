<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir archivo .xlsx</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <script>src="clearInput.js"</script>
</head>
<body style="margin: 25px;">
    <form action="index.php" method="post" enctype="multipart/form-data">
        <input type="file" name="xlsxFile" accept=".xlsx"><br><br>
        <button type="submit" name="upload">Upload</button>
    </form>
    <h1>Number list from .xls file</h1>
    <br>
    <table class="table">
        <thead>
            <tr>
                <th>primero</th>
                <th>segundo</th>
                <th>tercero</th>
                <th>cuarto</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include 'dbConfig.php';
            if(isset($_FILES["xlsxFile"]) && $_FILES["xlsxFile"]["error"] === UPLOAD_ERR_OK){ //for if the file was uploaded succesfully
                try {
                    $sql = "SELECT * FROM numeric_data";
                    $result = $db->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>" . $row["primero"] . "</td>
                            <td>" . $row["segundo"] . "</td>
                            <td>" . $row["tercero"] . "</td>
                            <td>" . $row["cuarto"] . "</td>
                            </tr>";
                    }
                } catch (Exception $e) {}
            }
            ?>
        </tbody>
    </table>
</body>

</html>

<?php
include 'dbConfig.php';
require 'vendor\autoload.php';
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

//we upload the file
if (isset($_POST["upload"])) {
    $upDirectory = "uploads/";
    $tFile = $upDirectory . basename($_FILES["xlsxFile"]["name"]);
    //echo "{$tFile}<br>\n";
    $tipo = strtolower(pathinfo($tFile, PATHINFO_EXTENSION));
    if ($tipo != "xlsx") {
        echo "Only .xlsx files are allowed";
        $subido = false;
    } else {
        $subido = true;
    }
    if ($subido) {
        try {
            move_uploaded_file($_FILES["xlsxFile"]["tmp_name"], $tFile);
            //echo "File has been uploaded.<br>\n";

            //we read the contents of the .xlsx
            $reader = new Xlsx();
            $spreadsheet = $reader->load($tFile);
            $worksheet = $spreadsheet->getActiveSheet();
            $worksheetArr = $worksheet->toArray();

            $query = file_get_contents('sql\schema.sql');
            $stmt = $db->prepare($query);
            try {
                $stmt->execute();
                //echo "Table created";
                foreach ($worksheetArr as $row) {
                    $prim = $row[0];
                    $seg = $row[1];
                    $ter = $row[2];
                    $cuar = $row[3];
                    $db->query("INSERT INTO numeric_data (primero, segundo, tercero, cuarto) VALUES (
                   '$prim', '$seg','$ter','$cuar')");
                }
                header("refresh:0");
            } catch (Exception $e) {
                echo "Table has been created/already exists.";
            }
            $db->close();
        } catch (Exception $e) {
            echo "<br>\nError while uploading file.";
        }
    } else {
        echo "<br>\nError. Invalid file type.";
    }
}
?>