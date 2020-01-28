<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\HTTP\Endpoints;

use CharlotteDunois\Yasmin\HTTP\APIManager;

/**
 * Handles the API endpoints "Voice".
 * @internal
 */
class Voice {
    /**
     * Endpoints Voice.
     * @var array
     */
    const ENDPOINTS = array(
        'regions' => 'voice/regions'
    );
    
    /**
     * @var APIManager
     */
    protected $api;
    
    /**
     * Constructor.
     * @param APIManager $api
     */
    function __construct(APIManager $api) {
        $this->api = $api;
    }
    
    function listVoiceRegions() {
        $url = self::ENDPOINTS['regions'];
        return $this->api->makeRequest('GET', $url, array());
    }
}
