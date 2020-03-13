<?php

namespace marsapp\grab\finance\source;

use marsapp\grab\finance\tools\Curl;
use Exception;

/**
 * 道瓊工業平均指數-抓取函式庫
 * 
 * - Dow Jones Industrial Average(DJIA)
 * - 同一個ip資料抓取不應過於頻繁，否則會被鎖ip
 * - 資料開始日：2004-02-11
 * 
 * @author Mars Hung
 */
class Djia
{

    /**
     * 資料開始日
     * @var string
     */
    protected static $dataStart = '2004-02-11';

    /**
     * 暫存
     */
    protected static $_cache = [];

    /**
     * 查詢網址
     * 
     * Dow Jones Industrial Average(DJIA)
     * - https://query1.finance.yahoo.com/v8/finance/chart/%5EDJI?formatted=true&crumb=Hy00mPu9sBY&lang=en-US&region=US&interval=1d&events=div%7Csplit&corsDomain=finance.yahoo.com&period1=1583971200&period2=1584057600
     * 
     * @var array
     */
    protected static $_baseUrl = [
        'url' => 'https://query1.finance.yahoo.com/v8/finance/chart/%5EDJI?formatted=true&crumb=Hy00mPu9sBY&lang=en-US&region=US&interval=1d&events=div%7Csplit&corsDomain=finance.yahoo.com',
    ];

    /**
     * *********************************************
     * ************** Public Function **************
     * *********************************************
     */

    /**
     * 抓取 道瓊工業平均指數 資料
     * 
     * @param string $date
     * @throws Exception
     * @return array|mixed
     */
    public static function getTrading($date)
    {
        // 參數處理
        $url = self::$_baseUrl['url'];
        $period1 = strtotime($date) + 57600;
        $period2 = strtotime($date) + 77400;

        // 資料檢查 - 時間
        if ($date < self::$dataStart) {
            throw new Exception('Date Error: ' . var_export($date, 1), 400);
        }

        $data['period1'] = $period1;
        $data['period2'] = $period2;

        // 抓取資料
        $data = Curl::get($url, $data);
        $data = json_decode($data, 1);

        // 原始資料整理
        $data = self::prepare($data);
        
        return $data;
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

    /**
     * 原始資料整理
     * 
     * @param array $data twse原始資料
     * @return array $opt
     */
    protected static function prepare($data)
    {
        // 重建結構
        $opt = [];

        if (isset($data['chart']['result'][0]['indicators']['quote'][0]['open'][0])) {
            $opt['open'] = $data['chart']['result'][0]['indicators']['quote'][0]['open'][0];
            $opt['high'] = $data['chart']['result'][0]['indicators']['quote'][0]['high'][0];
            $opt['low'] = $data['chart']['result'][0]['indicators']['quote'][0]['low'][0];
            $opt['close'] = $data['chart']['result'][0]['indicators']['quote'][0]['close'][0];
        }

        return $opt;
    }
}
