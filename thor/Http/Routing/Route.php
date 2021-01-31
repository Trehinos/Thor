<?php

namespace Thor\Http\Routing;

final class Route
{

    private array $filledParams = [];

    public function __construct(
        private string $controllerClass,
        private string $controllerMethod,
        private string $path = '/',
        private string $method = 'GET',
        private array $parameters = []
    ) {
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

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMethod(): string
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

    public function getControllerClass(): string
    {
        return $this->controllerClass;
    }

    public function getControllerMethod(): string
    {
        return $this->controllerMethod;
    }

}
