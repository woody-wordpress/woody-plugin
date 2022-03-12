<?php
/**
 * Woody Plugin
 * @author      Léo POIROUX
 * @copyright   2022 Raccourci Agency
 */

namespace Woody\Services;

class ParameterManager
{
    private $parameters;

    public function get($key, $arguments = [])
    {
        if (strpos($key, '.') !== false) {
            $pathKeys = explode('.', $key);
            $parameters = $this->parameters;
            foreach ($pathKeys as $pathKey) {
                $parameters = $parameters[$pathKey];
            }
            if (is_array($parameters)) {
                $parameter = $parameters;
            } else {
                $parameter = vsprintf($parameters, $arguments);
            }
        } else {
            $parameter = $this->parameters[$key];
        }
        return $parameter;
    }

    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }
}
