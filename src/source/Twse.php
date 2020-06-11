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
     * 暫存
     */
    protected static $_cache = [];

    /**
     * 查詢網址格式樣版
     * 
     * Example: https://www.twse.com.tw/exchangeReport/MI_INDEX?response=json&date=20200227&type=ALLBUT0999&_=1583557018426
     * 
     * @var array
     */
    protected static $_baseUrlTemplate = [
        // 每日收盤行情
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
        ],
        // 三大法人買賣超日報
        'corp3' => 'https://www.twse.com.tw/zh/page/trading/fund/T86.html',
        'corp3ajax' => [
            'url' => 'https://www.twse.com.tw/fund/T86',
            'data' => [
                'response' => 'json',
                'date' => '',
                'selectType' => 'ALLBUT0999',
            ]
        ],
        // 股價升降幅度 - 抓取 開盤基準價、漲停、跌停
        'basePrice' => 'https://www.twse.com.tw/zh/page/trading/exchange/TWT84U.html',
        'basePriceAjax' => [
            'url' => 'https://www.twse.com.tw/exchangeReport/TWT84U',
            'data' => [
                'response' => 'json',
                'date' => '',
                'selectType' => 'ALLBUT0999',
            ]
        ],
        // 鉅額交易日成交資訊-單一證券
        'hugeTrans' => 'https://www.twse.com.tw/zh/page/trading/block/BFIAUU.html',
        'hugeTransAjax' => [
            'url' => 'https://www.twse.com.tw/block/BFIAUU',
            'data' => [
                'response' => 'json',
                'date' => '',
                'selectType' => 'S',
            ]
        ],
    ];

    // 分類-資料查詢用
    protected static $_queryType = [
        // 'ALL' => '全部',
        'ALLBUT0999' => '全部(不含權證、牛熊證、可展延牛熊證)',
    ];

    /**
     * 分類-分類查詢用
     * 
     * - 使用「全部」查詢時，沒有分類資料，可使用本清單去查以補上分類資料
     */
    protected static $_industryType = [
        // 'MS' => '大盤統計資訊',
        // 'MS2' => '委託及成交統計資訊',
        // 'ALL' => '全部',
        // 'ALLBUT0999' => '全部(不含權證、牛熊證、可展延牛熊證)',
        '0049' => '封閉式基金',
        '0099P' => 'ETF',
        '019919T' => '受益證券',
        // '0999' => '認購權證(不含牛證)',
        // '0999P' => '認售權證(不含熊證)',
        // '0999C' => '牛證(不含可展延牛證)',
        // '0999B' => '熊證(不含可展延熊證)',
        // '0999X' => '可展延牛證',
        // '0999Y' => '可展延熊證',
        '0999GA' => '附認股權特別股',
        '0999GD' => '附認股權公司債',
        '0999G9' => '認股權憑證',
        'CB' => '可轉換公司債',
        '01' => '水泥工業',
        '02' => '食品工業',
        '03' => '塑膠工業',
        '04' => '紡織纖維',
        '05' => '電機機械',
        '06' => '電器電纜',
        '07' => '化學生技醫療',
        '21' => '化學工業',
        '22' => '生技醫療業',
        '08' => '玻璃陶瓷',
        '09' => '造紙工業',
        '10' => '鋼鐵工業',
        '11' => '橡膠工業',
        '12' => '汽車工業',
        '13' => '電子工業',
        '24' => '半導體業',
        '25' => '電腦及週邊設備業',
        '26' => '光電業',
        '27' => '通信網路業',
        '28' => '電子零組件業',
        '29' => '電子通路業',
        '30' => '資訊服務業',
        '31' => '其他電子業',
        '14' => '建材營造',
        '15' => '航運業',
        '16' => '觀光事業',
        '17' => '金融保險',
        '18' => '貿易百貨',
        '9299' => '存託憑證',
        '23' => '油電燃氣業',
        '19' => '綜合',
        '20' => '其他',
    ];

    /**
     * 資料分類
     */
    protected static $dataType = [
        'transStatistic' => '大盤統計資訊',
        'upDown' => '漲跌證券數合計',
        'priceIndex' => '價格指數',
        'rewardIndex' => '報酬指數',
        'closingPrice' => '每日收盤行情',
    ];

    protected static $corp3TypeMap = [
        '證券代號' => 's_code',
        '證券名稱' => 's_name',
        '外陸資買進股數(不含外資自營商)' => 'f_investor_buy',
        '外陸資賣出股數(不含外資自營商)' => 'f_investor_sell',
        '外陸資買賣超股數(不含外資自營商)' => 'f_investor_net_buy',
        '外資自營商買進股數' => 'f_dealer_buy',
        '外資自營商賣出股數' => 'f_dealer_sell',
        '外資自營商買賣超股數' => 'f_dealer_net_buy',
        '投信買進股數' => 'invest_buy',
        '投信賣出股數' => 'invest_sell',
        '投信買賣超股數' => 'invest_net_buy',
        '自營商買賣超股數' => 'dealer_net_buy',
        '自營商買進股數(自行買賣)' => 'dealer_s_buy',
        '自營商賣出股數(自行買賣)' => 'dealer_s_sell',
        '自營商買賣超股數(自行買賣)' => 'dealer_s_net_buy',
        '自營商買進股數(避險)' => 'dealer_h_buy',
        '自營商賣出股數(避險)' => 'dealer_h_sell',
        '自營商買賣超股數(避險)' => 'dealer_h_net_buy',
        '三大法人買賣超股數' => 'net_buy_sell',
    ];

    protected static $basePriceTypeMap = [
        '證券代號' => 's_code',
        '證券名稱' => 's_name',
        '漲停價' => 'limit_up',
        '開盤競價基準' => 'base_price',
        '跌停價' => 'limit_down',
    ];

    protected static $hugeTransTypeMap = [
        '證券代號' => 's_code',
        '證券名稱' => 's_name',
        '交易別' => 'trade_type',
        '成交價' => 'trade_price',
        '成交股數' => 'trade_count',
        '成交金額' => 'trade_amount',
    ];

    /**
     * *********************************************
     * ************** Public Function **************
     * *********************************************
     */

    /**
     * 抓取 TWSE 臺灣證券交易所資料
     * 
     * 輸出格式：
     * $opt[] = [
     *      'subtitle' => '',   // 標題
     *      'type' => '',       // 分類
     *      'provider' => '',   // 指數提供者
     *      'data' => [],       // 資料
     * ];
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
        if ($date < self::$dataStart) {
            throw new Exception('Date Error: ' . var_export($date, 1), 400);
        }
        // 資料檢查 - 分類
        $allType = self::$_queryType + self::$_industryType;
        if (!isset($allType[$type])) {
            throw new Exception('Type Error: ' . var_export($type, 1), 400);
        }

        $data['date'] = $qDate;
        $data['type'] = $type;

        // 抓取資料
        $data = Curl::get($url, $data);
        $data = json_decode($data, 1);

        // 原始資料整理
        $data = self::prepare($data);

        return $data;
    }

    /**
     * 抓取 TWSE 臺灣證券交易所資料 三大法人買賣超日報
     * 
     * 輸出格式：
     * $opt[] = [
     *      'date' => '',       // 日期
     *      'data' => [],       // 資料
     * ];
     * 
     * @param string $date
     * @throws Exception
     * @return array|mixed
     */
    public static function getCorp3($date)
    {
        // 參數處理
        $url = self::$_baseUrlTemplate['corp3ajax']['url'];
        $data = self::$_baseUrlTemplate['corp3ajax']['data'];
        $qDate = date('Ymd', strtotime($date));

        // 資料檢查 - 時間
        if ($date < self::$dataStart) {
            throw new Exception('Date Error: ' . var_export($date, 1), 400);
        }

        $data['date'] = $qDate;

        // 抓取資料
        $data = Curl::get($url, $data);
        $data = json_decode($data, 1);

        // 原始資料整理
        $data = self::prepareCorp3($data);

        return $data;
    }

    /**
     * 抓取 開盤基準價、漲停、跌停
     * 
     * 輸出格式：
     * $opt[] = [
     *      'date' => '',       // 日期
     *      'data' => [],       // 資料
     * ];
     * 
     * @param string $date
     * @throws Exception
     * @return array|mixed
     */
    public static function getBasePrice($date)
    {
        // 參數處理
        $url = self::$_baseUrlTemplate['basePriceAjax']['url'];
        $data = self::$_baseUrlTemplate['basePriceAjax']['data'];
        $qDate = date('Ymd', strtotime($date));

        // 資料檢查 - 時間
        if ($date < self::$dataStart) {
            throw new Exception('Date Error: ' . var_export($date, 1), 400);
        }

        $data['date'] = $qDate;

        // 抓取資料
        $data = Curl::get($url, $data);
        $data = json_decode($data, 1);

        // 原始資料整理
        $data = self::prepareBasePrice($data);

        return $data;
    }

    /**
     * 抓取 鉅額交易日成交資訊-單一證券
     * 
     * 單一證券同一天可能有多筆
     * 
     * 輸出格式：
     * $opt[] = [
     *      'date' => '',       // 日期
     *      'data' => [],       // 資料
     * ];
     * 
     * @param string $date
     * @throws Exception
     * @return array|mixed
     */
    public static function getHugeTrans($date)
    {
        // 參數處理
        $url = self::$_baseUrlTemplate['hugeTransAjax']['url'];
        $data = self::$_baseUrlTemplate['hugeTransAjax']['data'];
        $qDate = date('Ymd', strtotime($date));

        // 資料檢查 - 時間
        if ($date < self::$dataStart) {
            throw new Exception('Date Error: ' . var_export($date, 1), 400);
        }

        $data['date'] = $qDate;

        // 抓取資料
        $data = Curl::get($url, $data);
        $data = json_decode($data, 1);

        // 原始資料整理
        $data = self::prepareHugeTrans($data);

        return $data;
    }

    /**
     * 取得資料查詢分類
     * 
     * @return array
     */
    public static function getTypeList()
    {
        return self::$_queryType;
    }

    /**
     * 分類-產業別查詢用
     * 
     * - 使用「全部」查詢時，沒有產業別資料，可使用本清單去查以補上產業別資料
     * 
     * @return array
     */
    public static function getIndustryType()
    {
        return self::$_industryType;
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
     * 取得資料分類
     * 
     * @return array
     */
    public static function getDataType()
    {
        return self::$dataType;
    }

    /**
     * **********************************************
     * ************** Private Function **************
     * **********************************************
     */

    /**
     * 原始資料整理
     * 
     * @param array $data twse原始資料
     * @return array $opt
     */
    protected static function prepare($data)
    {
        // 重建結構
        $data = self::rebuildStructure($data);

        // 分類資料整理
        $data = self::typefilter($data);

        return $data;
    }


    /**
     * 重建結構
     * 
     * 輸出格式：
     * $opt[] = [
     *      'subtitle' => '',   // 標題
     *      'type' => '',       // 分類
     *      'provider' => '',   // 指數提供者
     *      'data' => [],       // 資料
     * ];
     * 
     * @param array $data twse原始資料
     * @return array $opt
     */
    protected static function rebuildStructure($data)
    {
        $opt = [];

        // 遍歷資料-重整資料結構
        foreach ($data as $key => $row) {
            if (strpos($key, 'subtitle') === 0) {
                // 標題處理
                $subtitle = trim(preg_replace('/[0-9]{2,3}年[0-9]{2}月[0-9]{2}日/', '', $row));
                $count = trim(str_replace('subtitle', '', $key));

                // 資料分類處理
                $type = '';
                foreach (self::$dataType as $code => $name) {
                    if (strpos($subtitle, $name) === 0) {
                        $type = $code;
                        break;
                    }
                }

                // 指數提供者
                $provider = '';
                if (in_array($type, ['priceIndex', 'rewardIndex'])) {
                    $provider = preg_match('/\((.*)\)/', $subtitle, $matches) ? $matches[1] : '';
                }

                // 新結構-資料寫入
                $opt[$count]['subtitle'] = $subtitle;
                $opt[$count]['type'] = $type;
                $opt[$count]['provider'] = $provider;
            } elseif (strpos($key, 'data') === 0) {
                // 資料內容處理
                $count = trim(str_replace('data', '', $key));

                // 新結構-資料寫入
                $opt[$count]['data'] = $row;
            }
        }
        ksort($opt);

        return $opt;
    }

    /**
     * 分類資料整理
     *
     * @param array $data
     * @return void
     */
    protected static function typefilter(array $data)
    {
        foreach ($data as $key => &$row) {
            switch ($row['type']) {
                case 'transStatistic':
                    // 大盤統計資訊
                    break;
                case 'upDown':
                    // 漲跌證券數合計
                    break;
                case 'priceIndex':
                    // 價格指數 - 移除漲跌欄中的html碼
                    $row['data'] = array_map(function ($value) {
                        isset($value[2]) && $value[2] = strip_tags($value[2]);
                        isset($value[4]) && $value[4] = $value[4] === '--' ? '0' : $value[4];
                        return $value;
                    }, $row['data']);
                    break;
                case 'rewardIndex':
                    // 報酬指數 - 移除漲跌欄中的html碼
                    $row['data'] = array_map(function ($value) {
                        isset($value[2]) && $value[2] = strip_tags($value[2]);
                        isset($value[4]) && $value[4] = $value[4] === '--' ? '0' : $value[4];
                        return $value;
                    }, $row['data']);
                    break;
                case 'closingPrice':
                    // 每日收盤行情 - 移除漲跌欄中的html碼
                    $row['data'] = array_map(function ($value) {
                        isset($value[9]) && $value[9] = strip_tags($value[9]);
                        return $value;
                    }, $row['data']);
                    break;
            }
        }

        return $data;
    }

    /**
     * 資料整理-三大法人買賣超日報
     * 
     * - 因取得資料時，欄位長度不定(值為0的有可能略過)，所以要轉換Key
     * - 2014-11-28前後的資料格式不同
     * 
     * @param array $data twse原始資料
     * @return array $opt
     */
    protected static function prepareCorp3($data)
    {
        $opt = [];

        if (isset($data['data'])) {
            $optData = [];

            // 濾空白
            foreach ($data['data'] as $key => $row) {
                foreach ($row as $k => $v) {
                    // 轉換key、濾空白
                    $newKey = self::$corp3TypeMap[$data['fields'][$k]];
                    $optData[$key][$newKey] = trim($v);
                }
            }

            $opt = [
                'date' => $data['date'],
                'data' => $optData,
            ];
        }

        return $opt;
    }

    /**
     * 資料整理-開盤基準價、漲停、跌停
     * 
     * @param array $data twse原始資料
     * @return array $opt
     */
    protected static function prepareBasePrice($data)
    {
        $opt = [];

        if (isset($data['data'])) {
            $optData = [];

            // 濾空白
            foreach ($data['data'] as $key => $row) {
                foreach ($row as $k => $v) {
                    if ($k < sizeof(self::$basePriceTypeMap)) {
                        // 轉換key、濾空白
                        $newKey = self::$basePriceTypeMap[$data['fields'][$k]] ?? null;
                        $optData[$key][$newKey] = trim($v);
                    }
                }
            }

            $opt = [
                'date' => $data['date'],
                'data' => $optData,
            ];
        }

        return $opt;
    }

    /**
     * 資料整理-鉅額交易日成交資訊-單一證券
     * 
     * @param array $data twse原始資料
     * @return array $opt
     */
    protected static function prepareHugeTrans($data)
    {
        $opt = [];

        if (isset($data['data'])) {
            $optData = [];

            // 濾空白
            foreach ($data['data'] as $key => $row) {
                foreach ($row as $k => $v) {
                    // 轉換key、濾空白
                    $newKey = self::$hugeTransTypeMap[$data['fields'][$k]] ?? null;
                    $optData[$key][$newKey] = trim($v);
                }
            }

            $opt = [
                'date' => $data['date'],
                'data' => $optData,
            ];
        }

        return $opt;
    }
}
