<?php

/**
 * @package Trehinos/Thor/Http
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Http\Routing;

use Attribute;
use Thor\Http\Request\HttpMethod;
use Thor\Http\Request\Request;

#[Attribute(Attribute::TARGET_METHOD)]
final class Route
{

    private array $filledParams = [];

    public function __construct(
        private ?string $routeName = null,
        private ?string $path = null,
        private HttpMethod $method = HttpMethod::GET,
        private array $parameters = [],
        private ?string $controllerClass = null,
        private ?string $controllerMethod = null,
    ) {
    }

    /**
     * @return string|null
     */
    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    /**
     * @param string|null $controllerClass
     */
    public function setControllerClass(?string $controllerClass): void
    {
        $this->controllerClass = $controllerClass;
    }

    /**
     * @param string|null $controllerMethod
     */
    public function setControllerMethod(?string $controllerMethod): void
    {
        $this->controllerMethod = $controllerMethod;
    }

    public function matches(string $pathInfo): bool
    {
        $path = $this->path;
        foreach ($this->parameters as $pName => $pInfos) {
            $regexp = $pInfos['regex'] ?? '.*';
            $path = str_replace("\$$pName", "(?P<$pName>$regexp)", $path);
        }

        if (preg_match("!^$path$!", $pathInfo, $matches)) {
            $parameters = [];
            foreach ($matches as $mKey => $mValue) {
                if (!is_numeric($mKey)) {
                    $parameters[$mKey] = $mValue;
                }
            }
            $this->filledParams = $parameters;
            return true;
        }

        return false;
    }

    public function url(array $parameters): string
    {
        $path = $this->path;
        foreach ($parameters as $pName => $pValue) {
            $path = str_replace("\$$pName", "$pValue", $path);
        }

        return "/index.php$path";
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getMethod(): HttpMethod
    {
        return $this->method;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getFilledParams(): array
    {
        return $this->filledParams;
    }

    public function getControllerClass(): ?string
    {
        return $this->controllerClass;
    }

    public function getControllerMethod(): ?string
    {
        return $this->controllerMethod;
    }

}
