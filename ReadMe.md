# Table Schema Search
Table Schema Search提供透過上傳excel來更新資料庫結構的紀錄，並提供查詢功能讓您能方便的查詢到對應的資料

* 緣起 : 工作時需不斷地透過google sheet來查詢，而資料表又各分不同的sheet導致查詢時相當麻煩且費時，希望可以不切頁面、輸入關鍵字即可及時獲得查詢結果，故決定簡單做個頁面來達成
## Require
* 讀取excel內的資料使用的是[phpspreadsheet](https://phpspreadsheet.readthedocs.io/en/latest/)，需先進行安裝
```
composer require phpoffice/phpspreadsheet
```
* 建立資料庫，預設名稱為:table_schema，可於config中修改連線的資料庫名稱

## Excel上傳格式說明
* 僅接受Xlsx格式
* 依下範例表格欄位說明填入欄位內容，**不需有表頭**

|欄位名稱|欄位中文名稱|資料表名稱|資料表中文名稱|資料類型|描述|主鍵|外鍵對應|預設值|備註|
|--|--|--|--|--|--|--|--|--|--|
|name|名稱|table|資料表|varchar(255)|描述|1|||備註|
