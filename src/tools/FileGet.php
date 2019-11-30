<?php
namespace marsapp\grab\finance\tools;

/**
 * 資料抓取工具函式庫 file_get_contents
 * 
 * @author Mars Hung
 *        
 */
class FileGet
{

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
    {
        // Create a stream
        $data = http_build_query($data);
        $opts = [
            'http' => [
                'method' => "POST",
                'header' => "Content-type: application/x-www-form-urlencoded\r\n" . "Content-length:" . strlen($data) . "\r\n" . "\r\n",
                'content' => $data,
                'ignore_errors' => true
            ],
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false
            ]
        ];
        // Open the file using the HTTP headers set above
        $result = file_get_contents($url, false, stream_context_create($opts));
        
        return $result;
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
    protected static function sendGet($url)
    {
        // Create a stream
        $opts = [
            'http' => [
                'method' => 'GET',
                'ignore_errors' => true,
            ],
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false
            ]
        ];
        
        // Open the file using the HTTP headers set above
        $result = file_get_contents($url, false, stream_context_create($opts));
        
        return $result;
    }
    
}