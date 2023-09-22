<?php
require 'vendor/autoload.php';
require 'config.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;

$table = 'fmarketing_schema';
$num = 20;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Schema Search</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <form action="" method="post" enctype="multipart/form-data">
            <label for="excel" class="form-label">匯入excel新增資料</label>
            <div class="input-group mb-3">
                <input type="file" name="excel_file" id="excel" accept=".xlsx" class="form-control">
                <input type="submit" value="匯入" name="import" class="btn btn-primary">
            </div>
        </form>
        <table class="table table-striped table-bordered " style="height: fit-content;">
            <thead>
                <tr>
                    <td>count</td>
                    <td>欄位名稱</td>
                    <td>欄位中文名稱</td>
                    <td>資料表名稱</td>
                    <td>資料表中文名稱</td>
                    <td>資料類型</td>
                    <td>描述</td>
                    <td>主鍵</td>
                    <td>外鍵對應</td>
                    <td>預設值</td>
                    <td>備註</td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="text" id='name' name='name' class="query_word w-75"></td>
                    <td><input type="text" name='name_zh' class="query_word w-75"></td>
                    <td><input type="text" name='table_name_zh' class="query_word w-75"></td>
                    <td><input type="text" name='table_name' class="query_word w-75"></td>
                    <td><input type="text" name='type' class="query_word w-75"></td>
                    <td><input type="text" name='description' class="query_word w-75"></td>
                    <td><input type="text" name='key' class="query_word w-75"></td>
                    <td><input type="text" name='fk' class="query_word w-75"></td>
                    <td><input type="text" name='default' class="query_word w-75"></td>
                    <td><input type="text" name='remark' class="query_word w-75"></td>
                </tr>
            </thead>
                <tbody id='sheet'>
            </tbody>
        </table>
    </div>


    <?php
    $row = null;
    if (isset($_POST['import'])) {
        $file_name = date("Y.m.d") . " - " . date("h.i.sa") . "." . $_FILES['excel_file']['name'];
        $file_type = $_FILES['excel_file']['type'];

        if ($file_type == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
            $targetDirectory = "uploads/" . $file_name;
            move_uploaded_file($_FILES['excel_file']['tmp_name'], $targetDirectory);
            try {
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //當PDO執行期間出現錯誤，它將拋出一個異常

                /** Create a new Xls Reader  **/
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

                /** Load $inputFileName to a Spreadsheet Object  **/
                $spreadsheet = $reader->load($targetDirectory);
                $worksheet = $spreadsheet->getActiveSheet();


                foreach ($worksheet->getRowIterator() as $row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(FALSE); //空儲存格也要讀取
                    $data = [];
                    foreach ($cellIterator as $cell) {
                        $data[] = $cell->getValue();
                    }
                    $sql = "INSERT INTO `fmarketing_schema`(`name`, `name_zh`, `table_name`,`table_name_zh`, `type`, `description`, `key`, `fk`, `default`, `remark`) VALUES (?,?,?,?,?,?,?,?,?,?)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($data);
                }
                echo
                "
                    <script>
                    alert('Succesfully Imported');
                    document.location.href = '';
                    </script>
                    ";
            } catch (PDOException $e) {
                echo "錯誤：" . $e->getMessage();
                $pdo->rollBack();
            }
        } else {

            echo
            "
                <script>
                alert('Only accept xlsx, your type is {$file_type}');
                </script>
                ";
        }
    };

    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function() {
            load_data();

            function load_data(query) {
                let data = {
                    'query': query,
                    'table': '<?= $table; ?>',
                    'num': '<?= $num; ?>'
                }
                $.get("./get_data.php", data, function(res) {
                    $('#sheet').html(res);
                })
            }
            let query = {};
            $('.query_word').keyup(function() {
                query[$(this).attr('name')] = $(this).val();
                console.log("O");
                if (query != '') {
                    load_data(query);
                } else {
                    load_data();
                }
            })
        })
    </script>
</body>

</html>