<?php
require 'vendor/autoload.php';
require 'config.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;

    $row = null;
    if (isset($_POST['import'])) {
        $file_name = $_FILES['excel_file']['name'];
        $file_type = $_FILES['excel_file']['type'];

        $table=$_POST['table'];

        if ($file_type == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
            $targetDirectory = "uploads/" . $file_name;
            move_uploaded_file($_FILES['excel_file']['tmp_name'], $targetDirectory);
            try {
                $pdo->beginTransaction();
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
                    $sql = "INSERT INTO `$table`(`name`, `name_zh`, `table_name`,`table_name_zh`, `type`, `description`, `key`, `fk`, `default`, `remark`) VALUES (?,?,?,?,?,?,?,?,?,?)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($data);
                }
                $pdo->commit();
                header("location:index.php?table=$table&code=200");
            } catch (PDOException $e) {
                $pdo->rollBack();
                header("location:index.php?table=$table&code=502&error={$e->getMessage()}");
            }
        } else {

            header("location:index.php?table=$table&code=403");
        }
    };