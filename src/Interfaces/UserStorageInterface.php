<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\Interfaces;

use CharlotteDunois\Yasmin\Models\GuildMember;
use CharlotteDunois\Yasmin\Models\User;
use InvalidArgumentException;

/**
 * Something all user storages implement. The storage also is used as factory.
 */
interface UserStorageInterface extends StorageInterface {
    /**
     * Returns the current element. From Iterator interface.
     * @return User
     */
    function current();
    
    /**
     * Fetch the key from the current element. From Iterator interface.
     * @return string
     */
    function key();
    
    /**
     * Advances the internal pointer. From Iterator interface.
     * @return User|false
     */
    function next();
    
    /**
     * Resets the internal pointer. From Iterator interface.
     * @return User|false
     */
    function rewind();
    
    /**
     * Checks if current position is valid. From Iterator interface.
     * @return bool
     */
    function valid();
    
    /**
     * Returns all items.
     * @return User[]
     */
    function all();
    
    /**
     * Resolves given data to an user.
     * @param User|GuildMember|string|int  $user  string/int = user ID
     * @return User
     * @throws InvalidArgumentException
     */
    function resolve($user);
    
    /**
     * Patches an user (retrieves the user if the user exists), returns null if only the ID is in the array, or creates an user.
     * @param array  $user
     * @return User|null
     */
    function patch(array $user);
    
    /**
     * Determines if a given key exists in the collection.
     * @param string  $key
     * @return bool
     * @throws InvalidArgumentException
    */
    function has($key);
    
    /**
     * Returns the item at a given key. If the key does not exist, null is returned.
     * @param string  $key
     * @return User|null
     * @throws InvalidArgumentException
    */
    function get($key);
    
    /**
     * Sets a key-value pair.
     * @param string                               $key
     * @param User  $value
     * @return $this
     * @throws InvalidArgumentException
     */
    function set($key, $value);
    
    /**
     * Factory to create (or retrieve existing) users.
     * @param array  $data
     * @param bool   $userFetched
     * @return User
     * @internal
     */
    function factory(array $data, bool $userFetched = false);
}
