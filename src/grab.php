<?php
namespace marshung\finance;

use marshung\finance\source\Twse;

/**
 * 
 * @author Mars Hung
 *
 */
class Grab
{
    
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
    
    public function grab()
    {
        $twse = new Twse();
        
        $data = $twse->getTrading();
        
        return $data;
    }
    
    /**
     * **********************************************
     * ************** Private Function **************
     * **********************************************
     */
    
}