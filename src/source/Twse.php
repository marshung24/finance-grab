<?php

namespace marsapp\grab\finance\source;

use marsapp\grab\finance\tools\Curl;
use Exception;

/**
 * 台灣證券交易所資料抓取函式庫
 * 
 * - 同一個ip資料抓取不應過於頻繁，否則會被鎖ip
 * - 資料提供開始日：2004-02-11
 * 
 * @author Mars Hung
 */
class Twse
{

    /**
     * 資料提供開始日
     * @var string
     */
    protected static $dataStart = '2004-02-11';

    /**
     * 查詢網址格式樣版
     * 
     * @var array
     */
    protected static $_baseUrlTemplate = [
        'base' => 'https://www.twse.com.tw/zh/',
        'desc' => 'xxx年xx月xx日 大盤統計資訊',
        'trading' => 'https://www.twse.com.tw/zh/page/trading/exchange/MI_INDEX.html',
        'tradingajax' => [
            'url' => 'https://www.twse.com.tw/exchangeReport/MI_INDEX',
            'data' => [
                'response' => 'json',
                'date' => '',
                'type' => '',
            ]
        ]
    ];

    // 分類
    protected static $_type = [
        // 'MS' => '大盤統計資訊',
        // 'MS2' => '委託及成交統計資訊',
        // 'ALL' => '全部',
        'ALLBUT0999' => '全部(不含權證、牛熊證、可展延牛熊證)',
        // '0049' => '封閉式基金',
        // '0099P' => 'ETF',
        // '019919T' => '受益證券',
        // '0999' => '認購權證(不含牛證)',
        // '0999P' => '認售權證(不含熊證)',
        // '0999C' => '牛證(不含可展延牛證)',
        // '0999B' => '熊證(不含可展延熊證)',
        // '0999X' => '可展延牛證',
        // '0999Y' => '可展延熊證',
        // '0999GA' => '附認股權特別股',
        // '0999GD' => '附認股權公司債',
        // '0999G9' => '認股權憑證',
        // 'CB' => '可轉換公司債',
        // '01' => '水泥工業',
        // '02' => '食品工業',
        // '03' => '塑膠工業',
        // '04' => '紡織纖維',
        // '05' => '電機機械',
        // '06' => '電器電纜',
        // '07' => '化學生技醫療',
        // '21' => '化學工業',
        // '22' => '生技醫療業',
        // '08' => '玻璃陶瓷',
        // '09' => '造紙工業',
        // '10' => '鋼鐵工業',
        // '11' => '橡膠工業',
        // '12' => '汽車工業',
        // '13' => '電子工業',
        // '24' => '半導體業',
        // '25' => '電腦及週邊設備業',
        // '26' => '光電業',
        // '27' => '通信網路業',
        // '28' => '電子零組件業',
        // '29' => '電子通路業',
        // '30' => '資訊服務業',
        // '31' => '其他電子業',
        // '14' => '建材營造',
        // '15' => '航運業',
        // '16' => '觀光事業',
        // '17' => '金融保險',
        // '18' => '貿易百貨',
        // '9299' => '存託憑證',
        // '23' => '油電燃氣業',
        // '19' => '綜合',
        // '20' => '其他',
    ];

    /**
     * *********************************************
     * ************** Public Function **************
     * *********************************************
     */

    /**
     * 抓取 TWSE 臺灣證券交易所資料
     * 
     * @param string $date
     * @param string $type
     * @throws Exception
     * @return array|mixed
     */
    public static function getTrading($date, $type)
    {
        // 參數處理
        $url = self::$_baseUrlTemplate['tradingajax']['url'];
        $data = self::$_baseUrlTemplate['tradingajax']['data'];
        $qDate = date('Ymd', strtotime($date));

        // 資料檢查 - 時間
        if ($qDate < self::$dataStart) {
            throw new Exception('Date Error: ' . var_export($date, 1), 400);
        }
        // 資料檢查 - 分類
        if (!isset(self::$_type[$type])) {
            throw new Exception('Type Error: ' . var_export($type, 1), 400);
        }

        $data['date'] = $qDate;
        $data['type'] = $type;

        // 抓取資料
        $data = Curl::get($url, $data);
        $data = json_decode($data, 1);

        return $data;
    }

    /**
     * 取得資料類型
     * 
     * @return array
     */
    public static function getTypeList()
    {
        return self::$_type;
    }

    /**
     * 取得資料提供開始日
     * 
     * @return string
     */
    public static function getDataStart()
    {
        return self::$dataStart;
    }

    /**
     * **********************************************
     * ************** Private Function **************
     * **********************************************
     */
}
