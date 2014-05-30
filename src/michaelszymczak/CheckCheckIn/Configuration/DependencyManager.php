<?php
namespace michaelszymczak\CheckCheckIn\Configuration;

use michaelszymczak\CheckCheckIn\Command\Git\FilteredGitFilesRetriever;
use michaelszymczak\CheckCheckIn\Command\Git\GitFilesHarvesterFactory;
use michaelszymczak\CheckCheckIn\Validator\ValidatorFactory;
use michaelszymczak\CheckCheckIn\View\Display;

class DependencyManager {

    private $display;
    private $groups;
    private $config;
    private $gitFilesHarvesterFactory;
    private $validatorFactory;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->display = new Display($config);
        $this->gitFilesHarvesterFactory = new GitFilesHarvesterFactory();
        $this->validatorFactory = new ValidatorFactory($this);
        $this->createGroupObjectssBasedOnConfig($config);
    }

    public static function createFromParameters($parameters)
    {
        return new DependencyManager(new Config($parameters));
    }

    public function getDisplay()
    {
        return $this->display;
    }

    public function getGroups()
    {
        return $this->groups;
    }
    public function getConfig()
    {
        return $this->config;
    }

    public function getFilteredGitFilesRetriever()
    {
        if ($this->config->getCandidates() === Config::CANDIDATES_STAGED) {
            $harvester = $this->gitFilesHarvesterFactory->createForStaged();
        }
        if ($this->config->getCandidates() === Config::CANDIDATES_MODIFIED) {
            $harvester = $this->gitFilesHarvesterFactory->createForModified();
        }

        return new FilteredGitFilesRetriever($harvester, $this->config->getBlacklist());
    }

    public function getValidatorFactory()
    {
        return $this->validatorFactory;
    }

    private function createGroupObjectssBasedOnConfig($config)
    {
        foreach ($config->getGroups() as $groupConfig) {
            $this->groups[] = new Group($groupConfig);
        }
    }

}