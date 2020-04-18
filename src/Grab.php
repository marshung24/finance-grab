<?php

namespace marsapp\grab\finance;

use marsapp\grab\finance\source\Twse;
use marsapp\grab\finance\source\Djia;

/**
 * 
 * @author Mars Hung
 *
 */
class Grab
{

    /**
     * *********************************************
     * ************** Public Function **************
     * *********************************************
     */

    /**
     * 抓取股票交易資料 - TWSE
     * 
     * 輸出格式：
     * $opt[] = [
     *      'subtitle' => '',   // 標題
     *      'type' => '',       // 分類
     *      'provider' => '',   // 指數提供者
     *      'data' => [],       // 資料
     * ];
     * 
     * @param string $date 目標日期
     * @param string $type 資料類型
     * @return array|mixed
     */
    public static function grab($date, $type)
    {
        return Twse::getTrading($date, $type);
    }

    /**
     * 抓取 台灣證券交易所 股票交易資料
     * 
     * 輸出格式：
     * $opt[] = [
     *      'subtitle' => '',   // 標題
     *      'type' => '',       // 分類
     *      'provider' => '',   // 指數提供者
     *      'data' => [],       // 資料
     * ];
     * 
     * @param string $date 目標日期
     * @param string $type 資料類型
     * @return array|mixed
     */
    public static function grabTwse($date, $type)
    {
        return Twse::getTrading($date, $type);
    }

    /**
     * 抓取 道瓊工業平均指數
     * 
     * 輸出格式：
     * $opt[] = [
     *      'open' => $number,
     *      'high' => $number,
     *      'low' => $number,
     *      'close' => $number,
     * ];
     * 
     * @param string $date 目標日期
     * @return array|mixed
     */
    public static function grabDjia($date)
    {
        return Djia::getTrading($date);
    }

    /**
     * 抓取股票交易資料類型
     * 
     * @return array
     */
    public static function getTypeList()
    {
        return Twse::getTypeList();
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
        return Twse::getIndustryType();
    }

    /**
     * 取得資料提供開始日
     * 
     * @return string
     */
    public static function getDataStart()
    {
        return Twse::getDataStart();
    }

    /**
     * **********************************************
     * ************** Private Function **************
     * **********************************************
     */
}
