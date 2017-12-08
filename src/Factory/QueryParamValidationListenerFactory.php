<?php
    /**
     * @copyright 2017 Jan-Simon Winkelmann <winkelmann@blue-metallic.de>
     * @license MIT
     */

    namespace Eth8585\ZfRestQueryParamValidation;

    use Eth8505\ZfRestQueryParamValidation\QueryParamValidationListener;
    use Interop\Container\ContainerInterface;
    use Zend\InputFilter\InputFilterPluginManager;
    use Zend\ServiceManager\Factory\FactoryInterface;

    /**
     * Factory for query param validation listener
     */
    class QueryParamValidationListenerFactory implements FactoryInterface
    {

        /**
         * @inheritdoc
         */
        public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
        {

            return new QueryParamValidationListener($container->get(InputFilterPluginManager::class));
        }

    }