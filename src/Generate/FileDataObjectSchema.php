<?php

/**
 * This file is part of the Heystack package
 *
 * @package Heystack
 */

/**
 * Generate namespace
 */
namespace Heystack\Core\Generate;

use Heystack\Core\Exception\ConfigurationException;
use Heystack\Core\Identifier\Identifier;
use Heystack\Core\State\State;

use Heystack\Core\State\StateableInterface;

/**
 * Uses yaml files to provide a schema for dataobject class creation
 *
 * @author  Cam Spiers <cameron@heyday.co.nz>
 * @author  Stevie Mayhew <stevie@heyday.co.nz>
 * @package Heystack
 */
abstract class FileDataObjectSchema implements DataObjectGeneratorSchemaInterface, StateableInterface
{

    /**
     * @var array
     */
    private $config;

    /**
     * @var \Heystack\Core\State\State
     */
    private $stateService;

    /**
     * @var string
     */
    private $stateKey;

    /**
     * @param                                                  $file
     * @param  State                                           $stateService
     * @throws \Heystack\Core\Exception\ConfigurationException
     */
    public function __construct($file, State $stateService)
    {
        $this->stateService = $stateService;

        $this->stateKey = md5($file);

        if (!$this->restoreState() || isset($_GET['flush'])) {

            $config = $this->parseFile($file);

            if (!is_array($config)) {

                throw new ConfigurationException('Your config is empty');

            }

            if (!array_key_exists('id', $config)) {

                throw new ConfigurationException('Identifier missing');

            }

            if (!array_key_exists('flat', $config)) {

                throw new ConfigurationException('Flat config missing');

            }

            if (!array_key_exists('related', $config)) {

                throw new ConfigurationException('Related config missing');

            }

            $this->config = $config;

            $this->saveState();

        }

    }

    /**
     * @param $file
     * @return mixed
     */
    abstract protected function parseFile($file);

    /**
     *
     */
    public function saveState()
    {
        $this->stateService->setByKey($this->stateKey, $this->config);
    }

    /**
     * @return mixed
     */
    public function restoreState()
    {
        return $this->config = $this->stateService->getByKey($this->stateKey);
    }

    /**
     * @return bool|\Heystack\Core\Identifier\Identifier
     */
    public function getIdentifier()
    {
        return isset($this->config['id']) ? new Identifier($this->config['id']) : false;
    }

    /**
     * @return bool
     */
    public function getDataProviderIdentifier()
    {
        return isset($this->config['data_provider_id']) ? $this->config['data_provider_id'] : false;

    }

    /**
     * @return array
     */
    public function getFlatStorage()
    {
        return isset($this->config['flat']) ? $this->config['flat'] : [];

    }

    /**
     * @return array
     */
    public function getRelatedStorage()
    {
        return isset($this->config['related']) ? $this->config['related'] : [];

    }

    /**
     * @return array
     */
    public function getParentStorage()
    {
        return isset($this->config['parent']) ? $this->config['parent'] : [];

    }

    /**
     * @return array
     */
    public function getChildStorage()
    {
        return isset($this->config['children']) ? $this->config['children'] : [];

    }

    /**
     * @param  DataObjectGeneratorSchemaInterface $schema
     * @return void
     */
    public function mergeSchema(DataObjectGeneratorSchemaInterface $schema)
    {

        $flat = $schema->getFlatStorage();

        if (is_array($flat)) {

            foreach ($flat as $key => $value) {

                $this->config['flat'][$key] = $value;

            }

        }

        $children = $schema->getChildStorage();

        if (is_array($children)) {

            foreach ($children as $key => $value) {

                $this->config['children'][$key] = $value;

            }

        }

    }
}
