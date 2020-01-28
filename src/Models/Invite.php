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
use CharlotteDunois\Yasmin\HTTP\APIEndpoints;
use CharlotteDunois\Yasmin\Interfaces\GuildChannelInterface;
use CharlotteDunois\Yasmin\Utils\DataHelpers;
use DateTime;
use Exception;
use React\Promise\ExtendedPromiseInterface;
use React\Promise\Promise;
use RuntimeException;
use function property_exists;

/**
 * Represents an invite.
 *
 * @property string                                                                                                  $code                The invite code.
 * @property Guild|PartialGuild|null                   $guild               The guild which this invite belongs to, or null.
 * @property GuildChannelInterface|PartialChannel $channel             The channel which this invite belongs to.
 * @property int|null                                                                                                $createdTimestamp    When this invite was created, or null.
 * @property User|null                                                                $inviter             The inviter, or null.
 * @property int|null                                                                                                $maxUses             Maximum uses until the invite expires, or null.
 * @property int|null                                                                                                $maxAge              Duration (in seconds) until the invite expires, or null.
 * @property bool|null                                                                                               $temporary           If this invite grants temporary membership, or null.
 * @property int|null                                                                                                $uses                Number of times this invite has been used, or null.
 * @property int|null                                                                                                $presenceCount       Approximate amount of presences, or null.
 * @property int|null                                                                                                $memberCount         Approximate amount of members, or null.
 *
 * @property DateTime|null                                                                                          $createdAt           The DateTime instance of the createdTimestamp, or null.
 * @property string                                                                                                  $url                 Returns the URL for the invite.
 */
class Invite extends ClientBase {
    /**
     * The invite code.
     * @var string
     */
    protected $code;
    
    /**
     * The guild this invite belongs to.
     * @var Guild
     */
    protected $guild;
    
    /**
     * The channel which this invite belongs to.
     * @var GuildChannelInterface|PartialChannel
     */
    protected $channel;
    
    /**
     * When this invite was created, or null.
     * @var int
     */
    protected $createdTimestamp;
    
    /**
     * The inviter, or null.
     * @var User|null
     */
    protected $inviter;
    
    /**
     * Maximum uses until the invite expires, or null.
     * @var int|null
     */
    protected $maxUses;
    
    /**
     * Duration (in seconds) until the invite expires, or null.
     * @var int|null
     */
    protected $maxAge;
    
    /**
     * If this invite grants temporary membership, or null.
     * @var bool
     */
    protected $temporary;
    
    /**
     * Number of times this invite has been used, or null.
     * @var int|null
     */
    protected $uses;
    
    /**
     * Approximate amount of presences, or null.
     * @var int|null
     */
    protected $presenceCount;
    
    /**
     * Approximate amount of members, or null.
     * @var int|null
     */
    protected $memberCount;

	/**
	 * @param Client $client
	 * @param array $invite
	 * @throws Exception
	 * @internal
	 */
    function __construct(Client $client, array $invite) {
        parent::__construct($client);
        
        $this->code = $invite['code'];
        $this->guild = (!empty($invite['guild']) ? ($client->guilds->get($invite['guild']['id']) ?? (new PartialGuild($client, $invite['guild']))) : null);
        $this->channel = ($client->channels->get($invite['channel']['id']) ?? (new PartialChannel($client, $invite['channel'])));
        
        $this->createdTimestamp = (!empty($invite['created_at']) ? (new DateTime($invite['created_at']))->getTimestamp() : null);
        $this->inviter = (!empty($invite['inviter']) ? $client->users->patch($invite['inviter']) : null);
        $this->maxUses = $invite['max_uses'] ?? null;
        $this->maxAge = $invite['max_age'] ?? null;
        $this->temporary = $invite['temporary'] ?? null;
        $this->uses = $invite['uses'] ?? null;
        
        $this->presenceCount = (isset($invite['approximate_presence_count']) ? ((int) $invite['approximate_presence_count']) : $this->presenceCount);
        $this->memberCount = (isset($invite['approximate_member_count']) ? ((int) $invite['approximate_member_count']) : $this->memberCount);
    }

	/**
	 * {@inheritdoc}
	 * @return mixed
	 * @throws RuntimeException
	 * @throws Exception
	 * @internal
	 */
    function __get($name) {
        if(property_exists($this, $name)) {
            return $this->$name;
        }
        
        switch($name) {
            case 'createdAt':
                if($this->createdTimestamp !== null) {
                    return DataHelpers::makeDateTime($this->createdTimestamp);
                }
                
                return null;
            break;
            case 'url':
                return APIEndpoints::HTTP['invite'].$this->code;
            break;
        }
        
        return parent::__get($name);
    }
    
    /**
     * Deletes the invite.
     * @param string  $reason
     * @return ExtendedPromiseInterface
     */
    function delete(string $reason = '') {
        return (new Promise(function (callable $resolve, callable $reject) use ($reason) {
            $this->client->apimanager()->endpoints->invite->deleteInvite($this->code, $reason)->done(function () use ($resolve) {
                $resolve();
            }, $reject);
        }));
    }
}
