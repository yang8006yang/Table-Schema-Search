<?php
require 'vendor/autoload.php';
require 'config.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Schema Search</title>
</head>
<body>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="excel_file" id="excel" accept=".xlsx">
        <input type="submit" value="上傳" name="import">
    </form>

    <table>
        <tr>
            <td>欄位名稱</td>
            <td>欄位中文名稱</td>
            <td>資料名稱</td>
            <td>資料中文名稱</td>
            <td>資料類型</td>
            <td>描述</td>
            <td>主鍵</td>
            <td>外鍵對應</td>
            <td>預設值</td>
            <td>備註</td>
        </tr>

    </table>

    <?php
    if(isset($_POST['import'])){
        $file_name =date("Y.m.d") . " - " . date("h.i.sa") . "." . $_FILES['excel_file']['name'];
        $file_type = $_FILES['excel_file']['type'];

        if($file_type == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
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
                    $data=[];
                    foreach ($cellIterator as $cell) {
                        $data[]=$cell->getValue();        
                    }
                    $sql="INSERT INTO `fmarketing_schema`(`name`, `name_zh`, `table_name`,`table_name_zh`, `type`, `description`, `key`, `fk`, `default`, `remark`) VALUES (?,?,?,?,?,?,?,?,?,?)";
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
            } catch(PDOException $e) {
                $pdo->rollBack();
                echo "錯誤：" . $e->getMessage();
            }
        }else{

            echo
                "
                <script>
                alert('Only accept xlsx, your type is {$file_type}');
                </script>
                ";
            }
    };

?>
</body>
</html>