<?php

declare(strict_types=1);

namespace joaomsl\punished;

use pocketmine\plugin\PluginBase;
use poggit\libasynql\ConfigException;
use poggit\libasynql\DataConnector;
use poggit\libasynql\SqlError;

class Main extends PluginBase {

    private ?DataConnector $dataConnector = null;

    protected function onLoad(): void
    {
        $this->saveDefaultConfig();
        try {
            $this->dataConnector = (new DataConnectorFactory($this->getConfig()->get('mysql'), $this))->make();
        } catch(ConfigException|SqlError|BadConfigurationException $ex) {
            $this->getLogger()->error('Database connection error: ' . $ex->getMessage());
        }
    }

    protected function onEnable(): void
    {
        if(is_null($this->dataConnector)) {
            $this->getServer()->shutdown();
            return;
        }
        
        $this->getLogger()->info('Eba, eu liguei :)))');
    }

    protected function onDisable(): void
    {
        $this->dataConnector?->waitAll();
        $this->dataConnector?->close();
    }

}