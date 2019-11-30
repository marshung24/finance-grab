<?php
namespace marsapp\grab\finance\tools;

/**
 * 資料抓取工具函式庫
 * 
 * @author Mars Hung
 *        
 */
class Curl
{

    protected static $_debug = false;
    
    /**
     * Construct
     */
    public function __construct()
    {}

    /**
     * *********************************************
     * ************** Public Function **************
     * *********************************************
     */
    public static function post($url = '', $data = [])
    {
        return self::sendPost($url, $data);
    }

    public static function get($url = '', $data = [])
    {
        // 取得時間 - 仿原介面jquery ajax - 防cache
        $t = explode('.', microtime(true));
        $time = $t[0] . str_pad(substr($t[1], 0, 3), 3, '0', STR_PAD_RIGHT);
        
        // get資料處理
        $param = http_build_query($data);
        $param .= (strlen($param) ? '&' : '') . '_=' . $time;
        
        // 串連網址
        $connSign = strpos($url, '?') ? '&' : '?';
        $url .= $connSign . $param;
        
        return self::sendGet($url);
    }

    
    
    /**
     * **********************************************
     * ************** Private Function **************
     * **********************************************
     */
    
    /**
     * POST函式
     *
     * @param string $url
     *            目標網址
     * @param array $data
     *            POST資料
     * @return string 回傳值 json
     */
    protected static function sendPost($url, $data = [])
    {}
// https://www.twse.com.tw/exchangeReport/MI_INDEX?response=json&date=20181109&type=MS&_=1575092979293
// https://www.twse.com.tw/exchangeReport/MI_INDEX?response=json&date=20191129&type=MS&_=1575093061501
    /**
     * GET函式
     *
     * @param string $url
     *            目標網址，GET格式
     * @param boolean $debug
     *            是否使用Debug模式，預設false
     * @return mixed 回傳值 json
     */
    protected static function sendGet($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36');
        // curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //強制協議為1.0
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect: ')); //要送出'Expect: '
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); //強制使用IPV4協議解析域名

        if (self::$_debug) {
            curl_setopt($ch, CURLOPT_VERBOSE, true); // cURL Debug
            curl_setopt($ch, CURLOPT_HEADER, true); // cURL Debug
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        }
        
        $result = curl_exec($ch);
        curl_close($ch);
        
        return $result;
    }

    /**
     * 樣版2:POST,GET函數
     *
     * @param String $post_data
     *            要post傳送的參數
     * @param String $url
     *            目標網址
     * @param String $request
     *            POST/GET
     * @param String $accessToken
     *            access token
     *            
     * @return String 處理結果 json
     */
    private function sample2($post_data, $url, $request, $accessToken)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (strtoupper($request) == "POST") {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/x-www-form-urlencoded"
            ));
        } else {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Bearer ' . $accessToken
            ));
        }
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
}