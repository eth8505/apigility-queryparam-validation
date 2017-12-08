<?php
    /**
     * @copyright 2017 Jan-Simon Winkelmann <winkelmann@blue-metallic.de>
     * @license MIT
     */

    namespace Eth8505\ZfRestQueryParamValidation;

    use Psr\Container\ContainerInterface;
    use Zend\EventManager\AbstractListenerAggregate;
    use Zend\EventManager\EventManagerInterface;
    use Zend\EventManager\SharedEventManagerInterface;
    use Zend\InputFilter\InputFilterInterface;
    use Zend\Router\Http\RouteMatch;
    use Zend\ServiceManager\ServiceLocatorInterface;
    use ZF\ApiProblem\ApiProblem;
    use ZF\ApiProblem\ApiProblemResponse;
    use ZF\Rest\Resource;
    use ZF\Rest\ResourceEvent;

    /**
     * Listener for query param validation
     */
    class QueryParamValidationListener extends AbstractListenerAggregate
    {

        /**
         * @var array
         */
        protected $config = [];

        /**
         * @var ServiceLocatorInterface
         */
        protected $inputFilterManager;

        /**
         * @var callable[]
         */
        protected $sharedListeners = [];

        /**
         * @param array $config
         * @param ContainerInterface $inputFilterManager
         */
        public function __construct(array $config = [], ContainerInterface $inputFilterManager)
        {

            $this->config = $config;
            $this->inputFilterManager = $inputFilterManager;
        }

        /**
         * @inheritdoc
         */
        public function attach(EventManagerInterface $events, $priority = 1)
        {

            $this->listeners[] = $events->attach('fetchAll', [$this, 'onResourceEvent'], 10);
        }

        /**
         * Attach one or more listeners
         *
         * Implementors may add an optional $priority argument; the SharedEventManager
         * implementation will pass this to the aggregate.
         *
         * @param SharedEventManagerInterface $events
         */
        public function attachShared(SharedEventManagerInterface $events): void
        {

            $this->sharedListeners[] = $events->attach(Resource::class, 'create', [$this, 'onResourceEvent'], 10);
            $this->sharedListeners[] = $events->attach(Resource::class, 'delete', [$this, 'onResourceEvent'], 10);
            $this->sharedListeners[] = $events->attach(Resource::class, 'deleteList', [$this, 'onResourceEvent'], 10);
            $this->sharedListeners[] = $events->attach(Resource::class, 'fetch', [$this, 'onResourceEvent'], 10);
            $this->sharedListeners[] = $events->attach(Resource::class, 'fetchAll', [$this, 'onResourceEvent'], 10);
            $this->sharedListeners[] = $events->attach(Resource::class, 'patch', [$this, 'onResourceEvent'], 10);
            $this->sharedListeners[] = $events->attach(Resource::class, 'patchList', [$this, 'onResourceEvent'], 10);
            $this->sharedListeners[] = $events->attach(Resource::class, 'replaceList', [$this, 'onResourceEvent'], 10);
            $this->sharedListeners[] = $events->attach(Resource::class, 'update', [$this, 'onResourceEvent'], 10);

        }

        /**
         * Detach all previously attached listeners
         *
         * @param SharedEventManagerInterface $events
         */
        public function detachShared(SharedEventManagerInterface $events): void
        {

            foreach ($this->sharedListeners as $index => $listener) {

                if ($events->detach(Resource::class, $listener)) {
                    unset($this->sharedListeners[$index]);
                }

            }

        }

        /**
         * @param ResourceEvent $e
         * @return ApiProblemResponse|null
         */
        public function onResourceEvent(ResourceEvent $e): ?ApiProblemResponse
        {

            $routeMatch = $e->getRouteMatch();
            if (!$routeMatch instanceof RouteMatch) {
                return null;
            }

            $controllerService = $routeMatch->getParam('controller', false);
            if (!$controllerService) {
                return null;
            }

            $inputFilter = $this->getInputFilter($controllerService, $e->getName());

            if ($inputFilter === null || !$inputFilter instanceof InputFilterInterface) {

                return new ApiProblemResponse(
                    new ApiProblem(
                        500,
                        sprintf(
                            'Input filter not found for controller "%s" and event "%s"',
                            $controllerService,
                            $e->getName()
                        )
                    )
                );

            }

            $inputFilter->setData($e->getQueryParams());
            if ($inputFilter->isValid()) {
                $e->getQueryParams()->fromArray($inputFilter->getValues());

                return null;
            }

            return new ApiProblemResponse(
                new ApiProblem(400, 'Failed Validation', null, null, [
                    'validation_messages' => $inputFilter->getMessages(),
                ])
            );

        }

        /**
         * Retrieve the query filter service name
         *
         * If not present, return boolean false.
         *
         * @param string $controllerService
         * @param string $resourceEventName
         * @return InputFilterInterface|null
         */
        protected function getInputFilter($controllerService, $resourceEventName): ?InputFilterInterface
        {

            if (!empty($this->config[$controllerService]['query_filter'])) {

                $inputFilter = $this->config[$controllerService]['query_filter'];

                if (is_array($inputFilter) && isset($inputFilter[$resourceEventName])) {
                    $inputFilter = $inputFilter[$resourceEventName];
                }

                if ($this->inputFilterManager->has($inputFilter)) {
                    return $this->inputFilterManager->get($inputFilter);
                }

            }

            return null;

        }

    }
