<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\HTTP;

use CharlotteDunois\Yasmin\Interfaces\RatelimitBucketInterface;
use React\Promise\ExtendedPromiseInterface;
use function array_shift;
use function array_unshift;
use function count;
use function microtime;
use const INF;

/**
 * Manages a route's ratelimit in memory.
 * @internal
 */
class RatelimitBucket implements RatelimitBucketInterface {
    /**
     * The API manager.
     * @var APIManager
     */
    protected $api;
    
    /**
     * The endpoint.
     * @var string
     */
    protected $endpoint;
    
    /**
     * The requests limit.
     * @var int
     */
    protected $limit = 0;
    
    /**
     * How many requests can be made.
     * @var int
     */
    protected $remaining = INF;
    
    /**
     * When the ratelimit gets reset.
     * @var float
     */
    protected $resetTime = 0.0;
    
    /**
     * The request queue.
     * @var APIRequest[]
     */
    protected $queue = array();
    
    /**
     * Whether the bucket is busy.
     * @var bool
     */
    protected $busy = false;
    
    /**
     * DO NOT initialize this class yourself.
     * @param APIManager $api
     * @param string                                   $endpoint
     */
    function __construct(APIManager $api, string $endpoint) {
        $this->api = $api;
        $this->endpoint = $endpoint;
    }
    
    /**
     * Destroys the bucket.
     */
    function __destruct() {
        $this->clear();
    }
    
    /**
     * Whether we are busy.
     * @return bool
     */
    function isBusy(): bool {
        return $this->busy;
    }
    
    /**
     * Sets the busy flag (marking as running).
     * @param bool  $busy
     * @return void
     */
    function setBusy(bool $busy): void {
        $this->busy = $busy;
    }
    
    /**
     * Sets the ratelimits from the response.
     * @param int|null    $limit
     * @param int|null    $remaining
     * @param float|null  $resetTime  Reset time in seconds with milliseconds.
     * @return ExtendedPromiseInterface|void
     */
    function handleRatelimit(?int $limit, ?int $remaining, ?float $resetTime) {
        if($limit === null && $remaining === null && $resetTime === null) {
            $this->remaining++; // there is no ratelimit...
            return;
        }
        
        $this->limit = $limit ?? $this->limit;
        $this->remaining = $remaining ?? $this->remaining;
        $this->resetTime = $resetTime ?? $this->resetTime;
        
        if($this->remaining === 0 && $this->resetTime > microtime(true)) {
            $this->api->client->emit('debug', 'Endpoint "'.$this->endpoint.'" ratelimit encountered, continueing in '.($this->resetTime - microtime(true)).' seconds');
        }
    }
    
    /**
     * Returns the endpoint this bucket is for.
     * @return string
     */
    function getEndpoint(): string {
        return $this->endpoint;
    }
    
    /**
     * Returns the size of the queue.
     * @return int
     */
    function size(): int {
        return count($this->queue);
    }
    
    /**
     * Pushes a new request into the queue.
     * @param APIRequest $request
     * @return $this
     */
    function push(APIRequest $request) {
        $this->queue[] = $request;
        return $this;
    }
    
    /**
     * Unshifts a new request into the queue. Modifies remaining ratelimit.
     * @param APIRequest $request
     * @return $this
     */
    function unshift(APIRequest $request) {
        array_unshift($this->queue, $request);
        $this->remaining++;
        return $this;
    }
    
    /**
     * Retrieves ratelimit meta data.
     *
     * The resolved value must be:
     * ```
     * array(
     *     'limited' => bool,
     *     'resetTime' => int|null
     * )
     * ```
     *
     * @return ExtendedPromiseInterface|array
     */
    function getMeta() {
        if($this->resetTime && microtime(true) > $this->resetTime) {
            $this->resetTime = null;
            $this->remaining = ($this->limit ? $this->limit : INF);
            
            $limited = false;
        } else {
            $limited = ($this->limit !== 0 && $this->remaining === 0);
        }
        
        return array('limited' => $limited, 'resetTime' => $this->resetTime);
    }
    
    /**
     * Returns the first queue item or false. Modifies remaining ratelimit.
     * @return APIRequest|false
     */
    function shift() {
        if(count($this->queue) === 0) {
            return false;
        }
        
        $item = array_shift($this->queue);
        $this->remaining--;
        
        return $item;
    }
    
    /**
     * Unsets all queue items.
     * @return void
     */
    function clear(): void {
        $this->remaining = 0;
        while($item = array_shift($this->queue)) {
            unset($item);
        }
    }
}
