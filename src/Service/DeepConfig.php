<?php

namespace App\Service;

use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DeepConfigRepository;
use App\Entity\DeepConfig as DCEntity;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * @package DeepConfig
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @link https://github.com/szczyglis-dev/symfony-deep-config
 * @license MIT
 * @version 1.2 | 2020.02.26
 */
class DeepConfig
{

    const YAML_FILE_EXTENSION = '.yaml';

    const CONFIG_DIR = 'config';


    /** @var string */
    private $projectDir;

    /**
     * @var EntityManagerInterface
     */
    private $em;    

    /**
     * @var DeepConfigRepository
     */
    private $repository;

    /**
     * @var array
     */
    private $dbCache = [];

    /**
     * @var array
     */
    private $parsed = [];

    /**
     * @var array
     */
    private $keys = [];

    /**
     * @var bool
     */
    private $useDatabase = true;

    /**
     * @var bool
     */
    private $isDatabaseCache = false;

    /**
     * @var bool
     */
    private $isFound = false;

    /**
     * @var mixed
     */
    private $tmpValue = null;

    /**
     * @var bool
     */
    private $ignoreParseErrors = false;


    /**
     * DeepConfig constructor.
     * @param string $projectDir
     * @param bool $useDatabase
     * @param DeepConfigRepository $repository
     * @param EntityManagerInterface $em
     */
    public function __construct(string $projectDir, bool $useDatabase, DeepConfigRepository $repository, EntityManagerInterface $em)
    {
        $this->projectDir = $projectDir;
        $this->useDatabase = $useDatabase;
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @return void
     */
    public function createDatabaseCache()
    {
        $this->dbCache = [];
        $items = $this->repository->findAll();
        foreach ($items as $item) {
            $key = (string)$item->getParam();
            $value = (string)$item->getValue();
            $this->dbCache[$key] = $value;
        }
        $this->isDatabaseCache = true;
    }

    /**
     * @return void
     */
    private function checkDatabaseCache()
    {
        if (!$this->isDatabaseCache) {
            $this->createDatabaseCache();
        }
    }

    /**
     * @param string $key
     * @param string $value
     * @return mixed
     */
    public function set(string $key, $value = '')
    {
        if (empty($key)) return;

        if ($this->repository->isKey($key)) {
            $this->repository->updateByKey($key, $value);
        } else {
            $config = new DCEntity;
            $config->setParam($key);
            $config->setValue($value);
            $this->em->persist($config);
            $this->em->flush();
        }
        $this->dbCache[$key] = $value;
        return $value;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function del(string $key)
    {
        if (empty($key)) return;

        if ($this->repository->isKey($key)) {
            $this->repository->deleteByKey($key);
            if (array_key_exists($key, $this->dbCache)) {
                unset($this->dbCache[$key]);
            }
            return true;
        }
        return false;
    }

    /**
     * @param array $keys
     * @param array $data
     * @return mixed|null
     */
    public function findValue(array $keys, ?array $data)
    {
        if (!is_array($data)) return;

        $value = null;
        $this->isFound = false;
        foreach ($keys as $key) {
            if (array_key_exists($key, $data)) {
                $value = $data[$key];
                $this->isFound = true;
                $this->tmpValue = $value;
            } else if (is_array($value) && array_key_exists($key, $value)) {
                $value = $value[$key];
                $this->isFound = true;
                $this->tmpValue = $value;
            } else {
                $this->isFound = false;
                $this->tmpValue = null;
            }
        }
        return $value;
    }

    /**
     * @param array $parts
     * @return bool
     */
    public function findFile(array $parts)
    {
        if (!is_array($parts)) return;

        $check = '';
        foreach ($parts as $i => $part) {
            $keys = [];
            $check .= '/' . $part;
            $check = ltrim($check, '/');
            $path = $this->projectDir . '/' . self::CONFIG_DIR . '/' . $check . self::YAML_FILE_EXTENSION;
            if (file_exists($path)) {
                $data = $this->parseFile($path);
                if (isset($parts[$i + 1])) {
                    $keys = array_slice($parts, $i + 1);
                    if (!empty($keys)) {
                        $this->findValue($keys, $data);
                        if ($this->isFound) {
                            return true;
                        }
                    }
                } else {                    
                    $this->tmpValue = $data;
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param string $path
     * @return array|null
     */
    private function parseFile($path)
    {
        $data = null;
        if (!array_key_exists($path, $this->parsed)) {

            if ($this->ignoreParseErrors) {
                try {
                    $data = Yaml::parse(
                        file_get_contents($path)
                    );
                    $this->parsed[$path] = $data;
                } catch (ParseException $e) {}
            } else {
                $data = Yaml::parse(
                    file_get_contents($path)
                );
                $this->parsed[$path] = $data;
            }
        } else {
            $data = $this->parsed[$path];
        }
        return $data;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getDb(string $key)
    {
        $this->checkDatabaseCache();
        if (array_key_exists($key, $this->dbCache)) {
            return $this->dbCache[$key];
        }
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        if (empty($key)) return;

        if ($this->useDatabase) {
            $this->checkDatabaseCache();
            if (array_key_exists($key, $this->dbCache)) {
                return $this->dbCache[$key];
            }
        }

        if (array_key_exists($key, $this->keys)) {
            return $this->keys[$key];
        }

        $this->tmpValue = null;
        if ($this->findFile(explode('.', $key)) === false) {
            return;
        }
        $this->keys[$key] = $this->tmpValue;
        return $this->tmpValue;
    }

    /**
     * @param bool $value
     */
    public function useDatabase(bool $value = true)
    {
        $this->useDatabase = $value;
    }

    /**
     * @param bool $value
     */
    public function setIgnoreParseErrors(bool $value)
    {
        $this->ignoreParseErrors = $value;
    }

    /**
     * @param string $dir
     */
    public function setProjectDir(string $dir)
    {
        $this->projectDir = $dir;
    }

    /**
     * @return array
     */
    public function getParsed()
    {
        return $this->parsed;
    }

    /**
     * @return array
     */
    public function getKeys()
    {
        return $this->keys;
    }

    /**
     * @return array
     */
    public function getDbCache()
    {
        return $this->dbCache;
    }

    /**
     * @return bool
     */
    public function isFound()
    {
        return $this->isFound;
    }
}