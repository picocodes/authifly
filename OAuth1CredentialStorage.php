<?php

namespace Authifly\Storage;

/**
 * AuthiFly storage manager
 */
class OAuth1CredentialStorage implements StorageInterface
{
    /**
     * stores the OAuth1 credentials for subsequent lookups.
     */
    protected $credentials = [];

    /**
     * Initiate a new storage
     *
     * @param null|array $credentials
     */
    public function __construct($credentials = null)
    {
        if (isset($credentials)) {
            $this->credentials = $credentials;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        if (!empty($this->credentials[$key])) {
            return $this->credentials[$key];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->credentials[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->credentials = [];
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        if (isset($this->credentials[$key])) {
            unset($this->credentials[$key]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMatch($key)
    {
        if (count($this->credentials)) {
            foreach ($this->credentials as $k => $v) {
                if (strstr($k, $key)) {
                    unset($this->credentials[$k]);
                }
            }
        }
    }
}
