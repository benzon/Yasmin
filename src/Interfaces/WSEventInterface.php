<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\Interfaces;

use CharlotteDunois\Yasmin\Client;
use CharlotteDunois\Yasmin\WebSocket\WSConnection;
use CharlotteDunois\Yasmin\WebSocket\WSManager;

/**
 * WS Event interface.
 * @internal
 */
interface WSEventInterface {
	/**
	 * Constructor.
	 * @param Client $client
	 * @param WSManager $wsmanager
	 */
    function __construct(Client $client, WSManager $wsmanager);

	/**
	 * Handles events.
	 * @param WSConnection $ws
	 * @param $data
	 * @return void
	 */
    function handle(WSConnection $ws, $data): void;
}
