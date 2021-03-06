<?php

namespace Heystack\Core;

use Heystack\Core\Console\Command\GenerateContainer;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * Provides the functionality for regenerating the container after saving/deleting a dataobject
 *
 * @copyright  Heyday
 * @author     Glenn Bautista <glenn@heyday.co.nz>
 * @author     Cam Spiers <cameron@heyday.co.nz>
 * @package    Ecommerce-Deals
 */
trait GenerateContainerDataObjectTrait
{
    /**
     * Regenerate the container
     * @return void
     */
    public function onAfterWrite()
    {
        (new GenerateContainer())->run(
            new ArrayInput([]),
            new NullOutput()
        );
    }

    /**
     * Regenerate the container
     * @return void
     */
    public function onAfterDelete()
    {
        $this->onAfterWrite();
    }
}
