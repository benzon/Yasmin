<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\Interfaces;

use InvalidArgumentException;
use React\Promise\ExtendedPromiseInterface;

/**
 * Something all guild news channels implement.
 */
interface GuildNewsChannelInterface extends GuildChannelInterface, TextChannelInterface {
    /**
     * Creates an invite. Resolves with an instance of Invite.
     *
     * Options are as following (all are optional).
     *
     * ```
     * array(
     *    'maxAge' => int,
     *    'maxUses' => int, (0 = unlimited)
     *    'temporary' => bool,
     *    'unique' => bool
     * )
     * ```
     *
     * @param array $options
     * @return ExtendedPromiseInterface
     */
    function createInvite(array $options = array());
    
    /**
     * Fetches all invites of this channel. Resolves with a Collection of Invite instances, mapped by their code.
     * @return ExtendedPromiseInterface
     * @see \CharlotteDunois\Yasmin\Models\Invite
     */
    function fetchInvites();
    
    /**
     * Sets the topic of the channel. Resolves with $this.
     * @param string  $topic
     * @param string  $reason
     * @return ExtendedPromiseInterface
     * @throws InvalidArgumentException
     */
    function setTopic(string $topic, string $reason = '');
}
