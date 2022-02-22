<?php

namespace Thor\Http\Routing;

use Attribute;
use Thor\Tools\Strings;
use Thor\Http\Request\HttpMethod;
use Thor\Security\Authorization\Authorization;

/**
 * Describes a route.
 *
 * This class is intended to recognize a request target and let the RequestHandler known it matches it.
 *
 * @package          Thor/Http/Routing
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class Route
{

    private array $filledParams = [];

    /**
     * @param string|null             $routeName
     * @param string|null             $path
     * @param HttpMethod|HttpMethod[] $method
     * @param array                   $parameters
     * @param string|null             $controllerClass
     * @param string|null             $controllerMethod
     */
    public function __construct(
        private ?string $routeName = null,
        private ?string $path = null,
        private array|HttpMethod $method = HttpMethod::GET,
        private array $parameters = [],
        private ?string $controllerClass = null,
        private ?string $controllerMethod = null,
        public ?Authorization $authorization = null
    ) {
    }

    /**
     * The name of this route.
     *
     * @return string|null
     */
    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    /**
     * Sets the class to instantiate when this route matches the request.
     *
     * @param class-string|null $controllerClass
     */
    public function setControllerClass(?string $controllerClass): void
    {
        $this->controllerClass = $controllerClass;
    }

    /**
     * Sets the method to execute when this route matches the request.
     *
     * @param string|null $controllerMethod
     */
    public function setControllerMethod(?string $controllerMethod): void
    {
        $this->controllerMethod = $controllerMethod;
    }

    /**
     * Returns true if this route matches the specified path.
     *
     * @param string $pathInfo
     *
     * @return bool
     */
    public function matches(string $pathInfo): bool
    {
        $path = $this->path;
        foreach ($this->parameters as $pName => $pInfos) {
            $regexp = $pInfos ?? '.*';
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

    /**
     * Gets a URL corresponding this route with provided parameters.
     *
     * @param array $parameters
     *
     * @return string
     */
    public function url(array $parameters): string
    {
        $path = Strings::interpolate($this->path, $parameters, true);
        return "/index.php$path";
    }

    /**
     * Gets this route's path.
     *
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Gets the HttpMethod(s) which matches this route.
     *
     * @return HttpMethod|HttpMethod[]
     */
    public function getMethod(): HttpMethod|array
    {
        return $this->method;
    }

    /**
     * Gets the allowed methods names.
     *
     * @return string[]
     */
    public function getAllowedMethods(): array
    {
        if (is_array($this->method)) {
            return array_map(
                fn(HttpMethod $method) => $method->value,
                $this->method
            );
        }
        return [$this->method->value];
    }

    /**
     * Gets the route parameters.
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Gets the route parameters with their values from the matched Request.
     *
     * @return array
     */
    public function getFilledParams(): array
    {
        return $this->filledParams;
    }

    /**
     * Gets the class to instantiate when this route matches the request.
     *
     * @return string|null
     */
    public function getControllerClass(): ?string
    {
        return $this->controllerClass;
    }

    /**
     * Gets the method to execute when this route matches the request.
     *
     * @return string|null
     */
    public function getControllerMethod(): ?string
    {
        return $this->controllerMethod;
    }

}
