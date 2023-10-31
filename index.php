<!-- todo 新增專案功能 -->
<?php
if(isset($_GET['code'])){
    if($_GET['code']==200){
        echo "
        <script>
        alert('Succesfully Imported');
        document.location.href = 'index.php?table={$_GET['table']}';
        </script>
        ";
    }else if($_GET['code']==403){
        echo
        "
            <script>
            alert('Only accept xlsx');
            </script>
            ";
    }else{
        echo
        "
            <script>
            alert('error'+`{$_GET['error']}`);
            </script>
            ";
    }
}
if(isset($_GET['table'])){
    $table=$_GET['table'];
}else{
    $table='';
}

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
        <form action="./add_data.php" method="post" enctype="multipart/form-data">
            <label for="excel" class="form-label">匯入excel新增資料</label>
            <div class="input-group mb-3">
                <input type="file" name="excel_file" id="excel" accept=".xlsx" class="form-control">
                <input type="hidden" name="table" id="table" class="form-control" value="<?=$table;?>">
                <input type="submit" value="匯入" name="import" class="btn btn-primary">
            </div>
        </form>
        <select name="project" id="project">
            <option value="fmarketing_schema" <?=($table=='fmarketing_schema')?'selected':'';?>>fMarketing</option>
            <option value="test" <?=($table=='test')?'selected':'';?>>test</option>
        </select>
        <div style="overflow: scroll;height: 85vh;" id='scrolldiv'>
            <table class="table table-striped table-bordered ">
                <thead style="position: sticky;top: 0;z-index: 1; background-color: lightgray;">
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
        <div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function() {
            /**
             * 呼叫api去資料庫撈資料後載入到表格中
             */
            function load_data(query) {
                let data = {
                    'query': query,
                    'table': table,
                    'num': num
                }
                $.get("./get_data.php", data, function(res) {
                    $('#sheet').html(res);
                })
            }

            function add_data(tableName) {
                $.get("./get_data.php", tableName, function(res) {
                    table = tableName;
                })
            }

            // 基本設定
            let num = 20;
            let project = document.getElementById('project');
            let table = project.value;
            document.getElementById('table').value = table;
            load_data();

            // 監控選擇的專案
            project.addEventListener('change',function () {
                table = project.value;
                document.getElementById('table').value = table;
                load_data();
            })


            // 監控搜尋欄鍵盤動作自動搜尋
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


            //  監控表格的卷軸，滾到底時新增筆數
            let scrollDiv = document.getElementById('scrolldiv');
            scrollDiv.addEventListener('scroll', function(event) {

                var scrollTop = scrollDiv.scrollTop;
                var scrollHeight = scrollDiv.scrollHeight;
                var clientHeight = scrollDiv.clientHeight;
                var isAtBottom = scrollTop + clientHeight >= scrollHeight;

                if (isAtBottom) {
                    num += 20;
                    load_data();
                }
            });
        })
    </script>
</body>

</html>