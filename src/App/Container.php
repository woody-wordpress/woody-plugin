<?php
/**
 * Woody Plugin
 * @author      Léo POIROUX
 * @copyright   2022 Raccourci Agency
 */

namespace Woody\App;

class Container
{
    private $definitionServices;
    private $parameterService;
    private $instances = [];
    private static $reflectionCaches = [];

    public function __construct($definitionServices)
    {
        $this->definitionServices = $definitionServices;
        $this->parameterService = $this->get('manager.parameters');
    }

    public function get($key)
    {
        if (empty($this->instances[$key])) {
            $arguments = [];
            $definition = $this->definitionServices[$key];
            if (!empty($definition['arguments'])) {
                foreach ($definition['arguments'] as $argument) {
                    $argumentType = key($argument);
                    switch ($argumentType) {
                        case 'service':
                            $arguments[] = $this->get($argument['service']);
                            break;
                        case 'collection':
                            $arguments[] = $this->handleCollection($argument['collection']);
                            break;
                        case 'parameter':
                            $arguments[] = $this->parameterService->get($argument['parameter']);
                            break;
                        case 'value':
                            $arguments[] = $arguments[] = $argument['value'];
                            break;
                        default:
                            throw new \Exception('Dependency injection : invalid argument type', 500);
                            break;
                    }
                }
            }
            $this->instances[$key] = $this->createInstance($definition['class'], $arguments);
        }
        return $this->instances[$key];
    }

    private function handleCollection(array $services)
    {
        $results = [];
        foreach ($services as $service) {
            $serviceKey = empty($service['key']) ? null : $service['key'];
            unset($service['key']);
            if (count(array_keys($service)) !== 1) {
                throw new \Exception('Dependency injection : error handle keys', 500);
            }
            if (empty($serviceKey)) {
                $results[] = $this->get($service[key($service)]);
            } else {
                $results[$serviceKey] = $this->get($service[key($service)]);
            }
        }
        return $results;
    }


    private function createInstance($class, array $arguments)
    {
        if (empty(self::$reflectionCaches[$class])) {
            self::$reflectionCaches[$class] = new \ReflectionClass($class);
        }
        return self::$reflectionCaches[$class]->newInstanceArgs($arguments);
    }
}
