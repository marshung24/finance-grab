finance
===

# 說明
以分析股市資料為目建構完整網頁專案，所提供的金融資料抓取函式庫

# 安裝
```
composer require marshung/finance
```

# 使用
```
$g = new \marshung\finance\Grab();

$date = '2018-11-09';
$type = 'MS';

$data = $g->grab($date, $type);
```
