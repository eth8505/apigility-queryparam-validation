<?php
    
namespace Eth8585\ApigilityQueryStringValidation;

use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\Mvc\Application;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @author Jan-Simon Winkelmann <winkelmann@blue-metallic.de>
 */
class Module implements BootstrapListenerInterface {

    /**
     * @inheritdoc
     */
    public function onBootstrap(EventInterface $e)
    {
        /** @var Application $app */
        $app = $e->getTarget();

        /** @var ServiceLocatorInterface $services */
        $services = $app->getServiceManager();

        /** @var EventManagerInterface $events */
        $events = $app->getEventManager();

        $sharedEvents = $events->getSharedManager();

        $services->get(QueryValidationListener::class)->attachShared($sharedEvents);
    }

}