<?php
namespace marshung\finance\source;

use marshung\finance\tools\Curl;

/**
 *
 * @author Mars Hung
 *        
 */
class Twse
{

    protected $_baseUrl = [
        'base' => 'http://www.twse.com.tw/zh/',
        'trading' => 'http://www.twse.com.tw/zh/page/trading/exchange/MI_INDEX.html',
        'tradingajax' => [
            'url' => 'http://www.twse.com.tw/exchangeReport/MI_INDEX?response=json&date=20180824&type=MS',
            'data' => [
                'response' => 'json',
                'date' => '',
                'type' => ''
            ]
        ]
    ];

    /**
     * Construct
     */
    public function __construct()
    {}

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
    public function getTrading($date = '', $type = '')
    {
        // 參數處理
        $url = $this->_baseUrl['tradingajax']['url'];
        $data = $this->_baseUrl['tradingajax']['data'];
        $data['date'] = $date;
        $data['type'] = $type;
        
        // 抓取資料
        $curl = new Curl();
        $data = $curl->get($url, $data);
        $data = json_decode($data, 1);
        
        return $data;
    }

/**
 * **********************************************
 * ************** Private Function **************
 * **********************************************
 */
}