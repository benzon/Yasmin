<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\Models;

use CharlotteDunois\Yasmin\Client;
use Exception;
use RuntimeException;
use function array_map;
use function property_exists;

/**
 * Represents a presence.
 *
 * @property Activity|null       $activity        The current activity the user is doing, or null.
 * @property Activity[] $activities      All activities the user is doing.
 * @property string                                             $status          What do you expect this to be?
 * @property ClientStatus|null   $clientStatus    The client's status on desktop/mobile/web, or null.
 * @property string                                             $userID          The user ID this presence belongs to.
 *
 * @property User|null           $user            The user this presence belongs to.
 */
class Presence extends ClientBase {
    /**
     * The user ID this presence belongs to.
     * @var string
     */
    protected $userID;
    
    /**
     * The current activity the user is doing, or null.
     * @var Activity
     */
    protected $activity;
    
    /**
     * What do you expect this to be?
     * @var string
     */
    protected $status;

    /**
     * The client's status for desktop/mobile/web or null.
     * @var ClientStatus|null
     */
    protected $clientStatus;
    
    /**
     * All activities the user is doing.
     * @var Activity[]
     */
    protected $activities = array();

	/**
	 * The manual creation of such an instance is discouraged. There may be an easy and safe way to create such an instance in the future.
	 * @param Client $client The client this instance is for.
	 * @param array $presence An array containing user (as array, with an element id), activity (as array) and status.
	 *
	 * @throws RuntimeException
	 * @throws Exception
	 */
    function __construct(Client $client, array $presence) {
        parent::__construct($client);
        $this->userID = $this->client->users->patch($presence['user'])->id;
        
        $this->_patch($presence);
    }
    
    /**
     * {@inheritdoc}
     * @return mixed
     * @throws RuntimeException
     * @internal
     */
    function __get($name) {
        if(property_exists($this, $name)) {
            return $this->$name;
        }
        
        switch($name) {
            case 'user':
                return $this->client->users->get($this->userID);
            break;
        }
        
        return parent::__get($name);
    }
    
    /**
     * @return mixed
     * @internal
     */
     function jsonSerialize() {
         return array(
             'status' => $this->status,
             'clientStatus' => $this->clientStatus,
             'game' => $this->activity
         );
     }

	/**
	 * @param array $presence
	 * @return void
	 * @throws Exception
	 * @internal
	 */
     function _patch(array $presence) {
         $this->activity = (!empty($presence['game']) ? (new Activity($this->client, $presence['game'])) : null);
         $this->status = $presence['status'];
         $this->clientStatus = (!empty($presence['client_status']) ? (new ClientStatus($presence['client_status'])) : null);
         $this->activities = (!empty($presence['activities']) ? array_map(function (array $activitiy) {
             return (new Activity($this->client, $activitiy));
         }, $presence['activities']) : array());
     }
}
