<?php
    /**
     * @copyright 2017 Jan-Simon Winkelmann <winkelmann@blue-metallic.de>
     * @license MIT
     */

    namespace Eth8585\ZfRestQueryParamValidation;

    use ModuleName\QueryValidation\QueryParamValidationListener;
    use Zend\EventManager\EventInterface;
    use Zend\EventManager\EventManagerInterface;
    use Zend\ModuleManager\Feature\BootstrapListenerInterface;
    use Zend\ModuleManager\Feature\ServiceProviderInterface;
    use Zend\Mvc\Application;
    use Zend\ServiceManager\ServiceLocatorInterface;

    /**
     * Module class
     */
    class Module implements BootstrapListenerInterface, ServiceProviderInterface
    {

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

            $services->get(QueryParamValidationListener::class)->attachShared($events->getSharedManager());

        }

        /**
         * @inheritdoc
         */
        public function getServiceConfig()
        {

            return [
                'factories' => [
                    QueryParamValidationListener::class => QueryParamValidationListenerFactory::class
                ]
            ];

        }

    }