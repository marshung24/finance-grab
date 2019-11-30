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
    
    // 分類
    protected static $_type = [
        'MS' => '大盤統計資訊',
        'MS2' => '委託及成交統計資訊',
        'ALL' => '全部',
        'ALLBUT0999' => '全部(不含權證、牛熊證、可展延牛熊證)',
        '0049' => '封閉式基金',
        '0099P' => 'ETF',
        '019919T' => '受益證券',
        '0999' => '認購權證(不含牛證)',
        '0999P' => '認售權證(不含熊證)',
        '0999C' => '牛證(不含可展延牛證)',
        '0999B' => '熊證(不含可展延熊證)',
        '0999X' => '可展延牛證',
        '0999Y' => '可展延熊證',
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
     * Construct
     */
    public function __construct()
    {
        
    }
    
    /**
     * Destruct
     */
    public function __destruct()
    {}
    
    
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
     * **********************************************
     * ************** Private Function **************
     * **********************************************
     */
    
}