<?php

namespace Thor\Http\Routing;

final class Route
{

    private string $path;

    private string $method;

    private array $parameters;

    private array $filledParams;

    private string $controllerClass;

    private string $controllerMethod;

    public function __construct(string $path, string $controllerClass, string $controllerMethod, string $method = 'GET', array $parameters = [])
    {
        $this->path = $path;
        $this->method = $method;
        $this->parameters = $parameters;
        $this->filledParams = [];
        $this->controllerClass = $controllerClass;
        $this->controllerMethod = $controllerMethod;
    }

    public function matches(string $pathInfo, string $method): bool
    {
        $path = $this->path;
        foreach ($this->parameters as $pName => $pInfos) {
            $regexp = $pInfos['regexp'] ?? '.*';
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
            return $this->method = $method;
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

    public function getFilledParameters(): array
    {
        return $this->filledParams;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return array
     */
    public function getFilledParams(): array
    {
        return $this->filledParams;
    }

    /**
     * @return string
     */
    public function getControllerClass(): string
    {
        return $this->controllerClass;
    }

    /**
     * @return string
     */
    public function getControllerMethod(): string
    {
        return $this->controllerMethod;
    }



}
