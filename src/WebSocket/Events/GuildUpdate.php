<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\WebSocket\Events;

use CharlotteDunois\Yasmin\Client;
use CharlotteDunois\Yasmin\Interfaces\WSEventInterface;
use CharlotteDunois\Yasmin\WebSocket\WSConnection;
use CharlotteDunois\Yasmin\WebSocket\WSManager;
use function in_array;

/**
 * WS Event
 * @see https://discordapp.com/developers/docs/topics/gateway#guild-update
 * @internal
 */
class GuildUpdate implements WSEventInterface {
    /**
     * The client.
     * @var Client
     */
    protected $client;
    
    /**
     * Whether we do clones.
     * @var bool
     */
    protected $clones = false;
    
    function __construct(Client $client, WSManager $wsmanager) {
        $this->client = $client;
        
        $clones = $this->client->getOption('disableClones', array());
        $this->clones = !($clones === true || in_array('guildUpdate', (array) $clones));
    }
    
    function handle(WSConnection $ws, $data): void {
        $guild = $this->client->guilds->get($data['id']);
        if($guild) {
            if(($data['unavailable'] ?? false)) {
                $guild->_patch(array('unavailable' => true));
                $this->client->queuedEmit('guildUnavailable', $guild);
                return;
            }
            
            $oldGuild = null;
            if($this->clones) {
                $oldGuild = clone $guild;
            }
            
            $guild->_patch($data);
            
            $this->client->queuedEmit('guildUpdate', $guild, $oldGuild);
        }
    }
}
