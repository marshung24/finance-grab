<?php

namespace marsapp\grab\finance;

use marsapp\grab\finance\source\Twse;

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
     * 抓取股票交易資料
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
     * 抓取股票交易資料類型
     * 
     * @return array
     */
    public static function getTypeList()
    {
        return Twse::getTypeList();
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
