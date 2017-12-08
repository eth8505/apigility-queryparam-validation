<?php
    /**
     * @copyright 2017 Jan-Simon Winkelmann <winkelmann@blue-metallic.de>
     * @license MIT
     */

    namespace Eth8505\ZfRestQueryParamValidation\Factory;

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

            return new QueryParamValidationListener(
                $this->getConfig($container),
                $container->get(InputFilterPluginManager::class)
            );
        }

        /**
         * Get config from container
         *
         * @param ContainerInterface $container
         * @return array
         */
        private function getConfig(ContainerInterface $container) : array
        {

            $config = [];

            if ($container->has('Config')) {
                $config = $container->get('Config')['zf-content-validation'] ?? [];
            }

            return $config;

        }

    }