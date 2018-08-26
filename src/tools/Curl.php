<?php
namespace marshung\finance\tools;

/**
 *
 * @author Mars Hung
 *        
 */
class Curl
{

    protected $_debug = false;
    
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
    public function post($url = '', $data = [])
    {
        return $this->send($url, $data = [], $method = 'POST');
    }

    public function get($url = '', $data = [])
    {
        // 取得時間 - 仿原介面jquery ajax - 防cache
        $t = explode('.', microtime(true));
        $time = $t[0] . str_pad(substr($t[1], 0, 3), 3, '0', STR_PAD_RIGHT);
        
        // get資料處理
        $param = http_build_query($data);
        $param = (strlen($param) ? '&' : '') . '_=' . $time;
        
        // 串連網址
        $connSign = strpos($url, '?') ? '&' : '?';
        $url .= $connSign . $param;
        
        return $this->sendGet($url);
    }

    public function send($url, $data = [], $method = 'GET')
    {
        return $this->sendGet($url);
    }

    /**
     * GET函式
     *
     * @param string $url
     *            目標網址，GET格式
     * @param boolean $debug
     *            是否使用Debug模式，預設false
     * @return mixed 回傳值 json
     */
    public function sendGet($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        
        if ($this->_debug) {
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
    public function sample2($post_data, $url, $request, $accessToken)
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

/**
 * **********************************************
 * ************** Private Function **************
 * **********************************************
 */
    
}