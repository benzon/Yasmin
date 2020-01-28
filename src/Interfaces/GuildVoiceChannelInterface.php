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
 * Something all guild voice channels implement.
 */
interface GuildVoiceChannelInterface extends GuildChannelInterface, VoiceChannelInterface {
    /**
     * Sets the topic of the channel. Resolves with $this.
     * @param string  $topic
     * @param string  $reason
     * @return ExtendedPromiseInterface
     * @throws InvalidArgumentException
     */
    function setTopic(string $topic, string $reason = '');
}
