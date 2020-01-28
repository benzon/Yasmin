<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\HTTP\Endpoints;

use CharlotteDunois\Yasmin\HTTP\APIEndpoints;
use CharlotteDunois\Yasmin\HTTP\APIManager;

/**
 * Handles the API endpoints "User".
 * @internal
 */
class User {
    /**
     * Endpoints Users.
     * @var array
     */
    const ENDPOINTS = array(
        'get' => 'users/%s',
        'current' => array(
            'get' => 'users/@me',
            'modify' => 'users/@me',
            'guilds' => 'users/@me/guilds',
            'leaveGuild' => 'users/@me/guilds/%s',
            'dms' => 'users/@me/channels',
            'createDM' => 'users/@me/channels',
            'createGroupDM' => 'users/@me/channels',
            'connections' => 'users/@me/connections'
        )
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
    
    function getCurrentUser() {
        $url = APIEndpoints::format(self::ENDPOINTS['current']['get']);
        return $this->api->makeRequest('GET', $url, array());
    }
    
    function getUser(string $userid) {
        $url = APIEndpoints::format(self::ENDPOINTS['get'], $userid);
        return $this->api->makeRequest('GET', $url, array());
    }
    
    function modifyCurrentUser(array $options) {
        $url = APIEndpoints::format(self::ENDPOINTS['current']['modify']);
        return $this->api->makeRequest('PATCH', $url, array('data' => $options));
    }
    
    function getCurrentUserGuilds() {
        $url = APIEndpoints::format(self::ENDPOINTS['current']['guilds']);
        return $this->api->makeRequest('GET', $url, array());
    }
    
    function leaveUserGuild(string $guildid) {
        $url = APIEndpoints::format(self::ENDPOINTS['current']['leaveGuild'], $guildid);
        return $this->api->makeRequest('DELETE', $url, array());
    }
    
    function getUserDMs() {
        $url = APIEndpoints::format(self::ENDPOINTS['current']['dms']);
        return $this->api->makeRequest('GET', $url, array());
    }
    
    function createUserDM(string $recipientid) {
        $url = APIEndpoints::format(self::ENDPOINTS['current']['createDM']);
        return $this->api->makeRequest('POST', $url, array('data' => array('recipient_id' => $recipientid)));
    }
    
    function createGroupDM(array $accessTokens, array $nicks) {
        $url = APIEndpoints::format(self::ENDPOINTS['current']['createGroupDM']);
        return $this->api->makeRequest('POST', $url, array('data' => array('access_tokens' => $accessTokens, 'nicks' => $nicks)));
    }
    
    function getUserConnections(string $accessToken) {
        $url = APIEndpoints::format(self::ENDPOINTS['current']['connections']);
        return $this->api->makeRequest('GET', $url, array('auth' => 'Bearer '.$accessToken));
    }
}
