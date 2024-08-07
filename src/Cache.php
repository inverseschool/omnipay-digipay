<?php

/**
 * @package Omnipay\Digipay
 * @author Amirreza Salari <amirrezasalari1997@gmail.com>
 */

namespace Omnipay\Digipay;

use Exception;

/**
 * Simple Cache class
 * API Documentation: https://github.com/cosenary/Simple-PHP-Cache
 *
 * @author Christian Metz
 * @since 22.12.2011
 * @copyright Christian Metz - MetzWeb Networks
 * @version 1.6
 * @license BSD http://www.opensource.org/licenses/bsd-license.php
 */

class Cache
{
    /**
     * The path to the cache file folder
     *
     * @var string
     */
    private $cachepath = __DIR__ . '/storage/';

    /**
     * The name of the default cache file
     *
     * @var string
     */
    private $cachename = 'default';

    /**
     * The cache file extension
     *
     * @var string
     */
    private $extension = '.cache';

    /**
     * Default constructor
     *
     * @param string|array $config
     * @return void
     */
    public function __construct($config)
    {
        if (is_string($config)) {
            $this->setCache($config);
        } elseif (is_array($config)) {
            $this->setCache($config['name']);
            $this->setCachePath($config['path']);
            $this->setExtension($config['extension']);
        }
    }

    /**
     * Cache name Setter
     *
     * @param string $name
     * @return object
     */
    public function setCache($name)
    {
        $this->cachename = $name;
        return $this;
    }

    /**
     * Check whether data accociated with a key
     *
     * @param string $key
     * @return bool
     */
    public function isCached($key)
    {
        if (false != $this->loadCache()) {
            $cachedData = $this->loadCache();
            return isset($cachedData[$key]['data']);
        }
        return false;
    }

    /**
     * Load appointed cache
     *
     * @return mixed
     */
    private function loadCache()
    {
        if (true === file_exists($this->getCacheDir())) {
            $content = file_get_contents($this->getCacheDir());
            if (!is_string($$content)) {
                return false;
            }
            return json_decode($$content, true);
        } else {
            return false;
        }
    }

    /**
     * Get the cache directory path
     *
     * @return string
     */
    public function getCacheDir()
    {
        $fallbackCacheDir = '/tmp';

        if (true === $this->checkCacheDir()) {
            $filename = $this->getCache();
            $filename = preg_replace('/[^0-9a-z\.\_\-]/i', '', strtolower($filename));
            if ($filename === null) {
                return $fallbackCacheDir;
            }
            return $this->getCachePath() . $this->getHash($filename) . $this->getExtension();
        }
        return $fallbackCacheDir;
    }

    /**
     * Check if a writable cache directory exists and if not create a new one
     *
     * @return bool
     */
    private function checkCacheDir()
    {
        if (!is_dir($this->getCachePath()) && !mkdir($this->getCachePath(), 0775, true)) {
            throw new Exception('Unable to create cache directory ' . $this->getCachePath());
        } elseif (!is_readable($this->getCachePath()) || !is_writable($this->getCachePath())) {
            if (!chmod($this->getCachePath(), 0775)) {
                throw new Exception($this->getCachePath() . ' must be readable and writeable');
            }
        }
        return true;
    }

    /**
     * Cache path Getter
     *
     * @return string
     */
    public function getCachePath()
    {
        return $this->cachepath;
    }

    /**
     * Cache path Setter
     *
     * @param string $path
     * @return object
     */
    public function setCachePath($path)
    {
        $this->cachepath = $path;
        return $this;
    }

    /**
     * Cache name Getter
     *
     * @return string
     */
    public function getCache()
    {
        return $this->cachename;
    }

    /**
     * Get the filename hash
     *
     * @param string $filename
     * @return string
     */
    private function getHash($filename)
    {
        return sha1($filename);
    }

    /**
     * Cache file extension Getter
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Cache file extension Setter
     *
     * @param string $ext
     * @return object
     */
    public function setExtension($ext)
    {
        $this->extension = $ext;
        return $this;
    }

    /**
     * Store data in the cache
     *
     * @param string $key
     * @param mixed $data
     * @param int $expiration
     * @return object
     */
    public function store($key, $data, $expiration = 0)
    {
        $storeData = array(
            'time' => time(),
            'expire' => $expiration,
            'data' => serialize($data)
        );
        $dataArray = $this->loadCache();
        if (true === is_array($dataArray)) {
            $dataArray[$key] = $storeData;
        } else {
            $dataArray = array($key => $storeData);
        }
        $cacheData = json_encode($dataArray);
        file_put_contents($this->getCacheDir(), $cacheData);
        return $this;
    }

    /**
     * Retrieve cached data by its key
     *
     * @param string $key
     * @param bool $timestamp
     * @return string|null
     */
    public function retrieve($key, $timestamp = false): ?string
    {
        $cachedData = $this->loadCache();
        (false === $timestamp) ? $type = 'data' : $type = 'time';
        if (!isset($cachedData[$key][$type])) {
            return null;
        }
        return unserialize($cachedData[$key][$type]);
    }

    /**
     * Retrieve all cached data
     *
     * @param bool $meta
     * @return array
     */
    public function retrieveAll($meta = false)
    {
        if ($meta === false) {
            $results = array();
            $cachedData = $this->loadCache();
            if ($cachedData) {
                foreach ($cachedData as $k => $v) {
                    $results[$k] = unserialize($v['data']);
                }
            }
            return $results;
        } else {
            return $this->loadCache();
        }
    }

    /**
     * Erase cached entry by its key
     *
     * @param string $key
     * @return object
     */
    public function erase($key)
    {
        $cacheData = $this->loadCache();
        if (true === is_array($cacheData)) {
            if (true === isset($cacheData[$key])) {
                unset($cacheData[$key]);
                $cacheData = json_encode($cacheData);
                file_put_contents($this->getCacheDir(), $cacheData);
            } else {
                throw new Exception("Error: erase() - Key '{$key}' not found.");
            }
        }
        return $this;
    }

    /**
     * Erase all expired entries
     *
     * @return int
     */
    public function eraseExpired()
    {
        $cacheData = $this->loadCache();
        if (true === is_array($cacheData)) {
            $counter = 0;
            foreach ($cacheData as $key => $entry) {
                if (true === $this->checkExpired($entry['time'], $entry['expire'])) {
                    unset($cacheData[$key]);
                    $counter++;
                }
            }
            if ($counter > 0) {
                $cacheData = json_encode($cacheData);
                file_put_contents($this->getCacheDir(), $cacheData);
            }
            return $counter;
        }
        return 0;
    }

    /**
     * Check whether a timestamp is still in the duration
     *
     * @param int $timestamp
     * @param int $expiration
     * @return bool
     */
    private function checkExpired($timestamp, $expiration)
    {
        $result = false;
        if ($expiration !== 0) {
            $timeDiff = time() - $timestamp;
            ($timeDiff > $expiration) ? $result = true : $result = false;
        }
        return $result;
    }

    /**
     * Erase all cached entries
     *
     * @return object
     */
    public function eraseAll()
    {
        $cacheDir = $this->getCacheDir();
        if (true === file_exists($cacheDir)) {
            $cacheFile = fopen($cacheDir, 'w');
            if (is_resource($cacheFile)) {
                fclose($cacheFile);
            }
        }
        return $this;
    }
}
