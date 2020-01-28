<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\WebSocket\Encoding;

use CharlotteDunois\Yasmin\Interfaces\WSEncodingInterface;
use CharlotteDunois\Yasmin\WebSocket\DiscordGatewayException;
use Ratchet\RFC6455\Messaging\Frame;
use Ratchet\RFC6455\Messaging\Message;
use RuntimeException;
use function json_decode;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;
use const JSON_ERROR_NONE;

/**
 * Handles WS encoding.
 * @internal
 */
class Json implements WSEncodingInterface {
    /**
     * Returns encoding name (for gateway query string).
     * @return string
     */
    function getName(): string {
        return 'json';
    }
    
    /**
     * Checks if the system supports it.
     * @return void
     * @throws RuntimeException
     */
    static function supported(): void {
        // Nothing to check
    }
    
    /**
     * Decodes data.
     * @param string  $data
     * @return mixed
     * @throws DiscordGatewayException
     */
    function decode(string $data) {
        $msg = json_decode($data, true);
        if($msg === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new DiscordGatewayException('The JSON decoder was unable to decode the data. Error: '. json_last_error_msg());
        }
        
        return $msg;
    }
    
    /**
     * Encodes data.
     * @param mixed  $data
     * @return string
     * @throws DiscordGatewayException
     */
    function encode($data): string {
        $msg = json_encode($data);
        if($msg === false && json_last_error() !== JSON_ERROR_NONE) {
            throw new DiscordGatewayException('The JSON encoder was unable to encode the data. Error: '. json_last_error_msg());
        }
        
        return $msg;
    }
    
    /**
     * Prepares the data to be sent.
     * @param string  $data
     * @return Message
     */
    function prepareMessage(string $data): Message {
        $frame = new Frame($data, true, Frame::OP_TEXT);
        
        $msg = new Message();
        $msg->addFrame($frame);
        
        return $msg;
    }
}
