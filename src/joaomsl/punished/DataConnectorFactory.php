<?php

declare(strict_types=1);

namespace joaomsl\punished;

use pocketmine\plugin\PluginBase;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

class DataConnectorFactory
{
    public function __construct(private readonly array $config, private readonly PluginBase $pluginBase)
    {
        if(
            !isset($config['host']) || 
            !filter_var($config['host'], FILTER_VALIDATE_IP) && 
            !filter_var($config['host'], FILTER_VALIDATE_DOMAIN)
        ) {
            throw new BadConfigurationException('Invalid host.');
        }
        if(!isset($config['user']) || !is_string($config['user']) || empty($config['user'])) {
            throw new BadConfigurationException('Invalid user.');
        }
        if(!isset($config['password']) || !is_string($config['password'])) {
            throw new BadConfigurationException('Invalid password.');
        }
        if(!isset($config['database']) || !is_string($config['database']) || empty($config['database'])) {
            throw new BadConfigurationException('Invalid database name.');
        }
        if(!isset($config['worker-limit']) || !is_int($config['worker-limit']) || $config['worker-limit'] < 1) {
            throw new BadConfigurationException('Invalid worker limit.');
        }
    }

    public function make(): DataConnector
    {
        $libAsyncConfig = [
            'type' => 'mysql',
            'sqlite' => ['file' => 'data.sqlite'],
            'mysql' => [
                'host' => $this->config['host'],
                'username' => $this->config['user'],
                'password' => $this->config['password'],
                'schema' => $this->config['database']
            ],
            'worker-limit' => $this->config['worker-limit']
        ];

        return libasynql::create($this->pluginBase, $libAsyncConfig, ['mysql' => ['sql/migrate.sql']]);
    }
}
