<?php
    namespace ModuleName\QueryValidation;

    use Zend\EventManager\EventManagerInterface;
    use Zend\EventManager\ListenerAggregateInterface;
    use Zend\EventManager\ListenerAggregateTrait;
    use Zend\EventManager\SharedEventManagerInterface;

    use Zend\InputFilter\InputFilterInterface;
    use Zend\Router\Http\RouteMatch;
    use Zend\ServiceManager\ServiceLocatorInterface;
    use ZF\ApiProblem\ApiProblem;
    use ZF\ApiProblem\ApiProblemResponse;
    use ZF\Rest\ResourceEvent;
    use ZF\Rest\Resource;

    class QueryValidationListener implements ListenerAggregateInterface
    {
        use ListenerAggregateTrait;

        /**
         * @var array
         */
        protected $config = [];

        /**
         * @var ServiceLocatorInterface
         */
        protected $inputFilterManager;

        /**
         * Cache of input filter service names/instances
         *
         * @var array
         */
        protected $inputFilters = [];

        /**
         * @var \Zend\Stdlib\CallbackHandler[]
         */
        protected $sharedListeners = [];

        /**
         * @param array $config
         * @param null|ServiceLocatorInterface $inputFilterManager
         */
        public function __construct(array $config = [], ServiceLocatorInterface $inputFilterManager = null)
        {
            $this->config = $config;
            $this->inputFilterManager = $inputFilterManager;
        }

        /**
         * @param EventManagerInterface $events
         */
        public function attach(EventManagerInterface $events, $priority = 1)
        {
            $this->listeners[] = $events->attach('fetchAll', [$this, 'onFetchAll'], 10);
        }

        /**
         * Attach one or more listeners
         *
         * Implementors may add an optional $priority argument; the SharedEventManager
         * implementation will pass this to the aggregate.
         *
         * @param SharedEventManagerInterface $events
         */
        public function attachShared(SharedEventManagerInterface $events)
        {
            // trigger before resource listener fetchAll event
            $this->sharedListeners[] = $events->attach(Resource::class, 'fetchAll', [$this, 'onFetchAll'], 10);
        }

        /**
         * Detach all previously attached listeners
         *
         * @param SharedEventManagerInterface $events
         */
        public function detachShared(SharedEventManagerInterface $events)
        {
            foreach ($this->sharedListeners as $index => $listener) {
                if ($events->detach(Resource::class, $listener)) {
                    unset($this->sharedListeners[$index]);
                }
            }
        }

        /**
         * @param ResourceEvent $e
         * @return ApiProblemResponse
         */
        public function onFetchAll($e)
        {
            $routeMatches = $e->getRouteMatch();
            if (! $routeMatches instanceof RouteMatch) {
                return;
            }

            $controllerService = $routeMatches->getParam('controller', false);
            if (! $controllerService) {
                return;
            }

            $inputFilterService = $this->getInputFilterService($controllerService);
            if (! $inputFilterService) {
                return;
            }

            if (! $this->hasInputFilter($inputFilterService)) {
                return new ApiProblemResponse(
                    new ApiProblem(
                        500,
                        sprintf('Listed input filter "%s" does not exist; cannot validate request', $inputFilterService)
                    )
                );
            }

            $inputFilter = $this->getInputFilter($inputFilterService);

            $inputFilter->setData($e->getQueryParams());
            if ($inputFilter->isValid()) {
                $e->getQueryParams()->fromArray($inputFilter->getValues());
                return;
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
         * @param  string $controllerService
         * @return string|false
         */
        protected function getInputFilterService($controllerService)
        {
            if (isset($this->config[$controllerService]['query_filter'])) {
                return $this->config[$controllerService]['query_filter'];
            }

            return false;
        }

        /**
         * Determine if we have an input filter matching the service name
         *
         * @param string $inputFilterService
         * @return bool
         */
        protected function hasInputFilter($inputFilterService)
        {
            if (array_key_exists($inputFilterService, $this->inputFilters)) {
                return true;
            }

            if (! $this->inputFilterManager
                || ! $this->inputFilterManager->has($inputFilterService)
            ) {
                return false;
            }

            $inputFilter = $this->inputFilterManager->get($inputFilterService);
            if (! $inputFilter instanceof InputFilterInterface) {
                return false;
            }

            $this->inputFilters[$inputFilterService] = $inputFilter;
            return true;
        }

        /**
         * Retrieve the named input filter service
         *
         * @param string $inputFilterService
         * @return InputFilterInterface
         */
        protected function getInputFilter($inputFilterService)
        {
            return $this->inputFilters[$inputFilterService];
        }
    }
