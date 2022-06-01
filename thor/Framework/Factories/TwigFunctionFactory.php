<?php

namespace Thor\Framework\Factories;

use Thor\Tools\Guid;
use Twig\TwigFunction;
use Thor\Web\WebServer;
use Thor\Web\Assets\Asset;
use Thor\Http\Routing\Router;
use Thor\Security\SecurityInterface;
use Symfony\Component\VarDumper\VarDumper;

/**
 * A factory to create twig Functions.
 *
 * @package          Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class TwigFunctionFactory
{

    private function __construct()
    {
    }

    /**
     * @return TwigFunction
     */
    public static function toast(): TwigFunction
    {
        return new TwigFunction(
            'toast',
            function (array|string $message) {
                $type = 'info';
                $muted = '';
                $title = 'Message';
                if (is_array($message)) {
                    $type = $message['type'] ?? 'info';
                    $title = $message['title'] ?? '';
                    $muted = $message['muted'] ?? '';
                    $message = $message['message'] ?? '';
                }
                [$icon, $color, $bg] = match ($type) {
                    'info' => ['info-circle', 'text-info', 'bg-light'],
                    'warning' => ['exclamation-triangle', 'text-warning', 'bg-light'],
                    'error', 'danger' => ['exclamation-triangle', 'text-danger', 'bg-danger text-light'],
                    'success', 'ok' => ['check', 'text-success', 'bg-success text-light'],
                };
                return <<<§
                    <div class="toast" role="alert">
                        <div class="toast-header">
                            <i class="fas fa-$icon $color fa-fw me-2"></i>
                            <strong class="me-auto">$title</strong>
                            <small class="text-muted">$muted</small>
                            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                        </div>
                        <div class="toast-body $bg">$message</div>
                    </div>
                    §;
            },
            ['is_safe' => ['html']]
        );
    }

    /**
     * @param Asset[] $assetsList
     *
     * @return TwigFunction
     */
    public static function asset(array $assetsList): TwigFunction
    {
        return new TwigFunction(
            'asset',
            function (string $assetName, array $attrs = []) use ($assetsList) {
                $asset = $assetsList[$assetName] ?? null;
                if ($asset === null) {
                    return '';
                }
                $attrNames = array_keys($asset->getAttributes());
                $attrValues = array_values($asset->getAttributes());
                array_map(
                    fn(string $attributes, mixed $value) => '',
                    $attrNames,
                    $attrValues
                );
                foreach ($attrs + [] as $key => $value) {
                    $asset->setAttribute($key, $value);
                }
                return $asset->getHtml();
            },
            ['is_safe' => ['html']]
        );
    }

    /**
     * @return TwigFunction
     */
    public static function option(): TwigFunction
    {
        return new TwigFunction(
            'option',
            function (?string $current, ?string $optionValue, ?string $optionLabel = null) {
                $optionLabel ??= $optionValue;
                $selected = $current === $optionValue ? 'selected' : '';
                return "<option $selected value=\"$optionValue\">$optionLabel</option>";
            },
            ['is_safe' => ['html']]
        );
    }

    /**
     * @param SecurityInterface|null $security
     *
     * @return TwigFunction
     */
    public static function authorized(?SecurityInterface $security = null): TwigFunction
    {
        return new TwigFunction(
            'authorized',
            function (string ...$permissions) use ($security): bool {
                if ($security === null) {
                    return true;
                }
                $identity = $security->getCurrentIdentity();
                foreach ($permissions as $permission) {
                    if (!$identity->hasPermission($permission)) {
                        return false;
                    }
                }
                return true;
            },
        );
    }

    /**
     * @param Router $router
     *
     * @return TwigFunction
     */
    public static function url(Router $router): TwigFunction
    {
        return new TwigFunction(
            'url',
            function (string $routeName, array $params = [], array $query = []) use ($router): string {
                return "{$router->getUrl($routeName, $params, $query)}";
            },
            ['is_safe' => ['html']]
        );
    }

    /**
     * @return TwigFunction
     */
    public static function icon(): TwigFunction
    {
        return new TwigFunction(
            'icon',
            function (string $icon, string $prefix = 'fas', bool $fixed = false, string $style = '') {
                $fw = $fixed ? 'fa-fw' : '';
                $style = ('' !== $style) ? "style='$style'" : '';
                return "<i class='$prefix fa-$icon $fw' $style></i>";
            },
            ['is_safe' => ['html']]
        );
    }

    /**
     * @param WebServer $server
     *
     * @return TwigFunction
     */
    public static function render(WebServer $server): TwigFunction
    {
        return new TwigFunction(
            'render',
            function (string $routeName, array $params = []) use ($server) {
                $route = $server->getRouter()->getRoute($routeName);
                $cClass = $route->getControllerClass();
                $cMethod = $route->getControllerMethod();

                $controller = new $cClass($server);
                return $controller->$cMethod(...$params)->getBody();
            },
            ['is_safe' => ['html']]
        );
    }

    /**
     * @return TwigFunction
     */
    public static function dump(): TwigFunction
    {
        return new TwigFunction(
            'dump',
            function ($var) {
                return VarDumper::dump($var);
            },
            ['is_safe' => ['html']]
        );
    }

    /**
     * @param int $defaultSize
     *
     * @return TwigFunction
     */
    public static function uuid(int $defaultSize = 8): TwigFunction
    {
        return new TwigFunction(
            'uuid',
            fn(?int $size = null) => Guid::hex($size ?? $defaultSize)
        );
    }

}
