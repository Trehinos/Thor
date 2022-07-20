<?php

namespace Thor\Web;

use Thor\Debug\Logger;
use Twig\Error\{SyntaxError, LoaderError, RuntimeError};
use Thor\Http\{Routing\Route, Session, HttpController, Response\Response, Response\HttpStatus};

/**
 * Base controller for web context.
 *
 * @package          Thor/Http/Controller
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
abstract class WebController extends HttpController
{

    /**
     * @param WebServer $webServer
     * @param Route     $route
     */
    public function __construct(protected WebServer $webServer, Route $route)
    {
        parent::__construct($webServer, $route);
    }

    /**
     * @return bool
     */
    public function hasMessages(): bool
    {
        return !empty(Session::read('controller.messages', []));
    }

    /**
     * @param string $languageKey
     * @param string $hint
     *
     * @return void
     */
    public function error(string $languageKey, string $hint = ''): void
    {
        $message = $this->getServer()->getLanguage()['errors'][$languageKey] ?? $languageKey;
        $this->addMessage($message, 'Error', 'error', $hint);
    }

    /**
     * @inheritDoc
     */
    public function getServer(): WebServer
    {
        return $this->webServer;
    }

    /**
     * @param string $message
     * @param string $title
     * @param string $type
     * @param string $muted
     *
     * @return void
     */
    public function addMessage(string $message, string $title = '', string $type = 'info', string $muted = ''): void
    {
        Session::write(
            'controller.messages',
            array_merge($this->getMessages(), [
                [
                    'title'   => $title,
                    'message' => $message,
                    'type'    => $type,
                    'muted'   => $muted,
                ],
            ])
        );
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        $messages = Session::read('controller.messages', []);
        Session::remove('controller.messages');
        return $messages;
    }

    /**
     * Returns a Response with twig rendering.
     *
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function twigResponse(
        string $fileName,
        array $params = [],
        HttpStatus $status = HttpStatus::OK,
        array $headers = [],
        bool $retrieveMessages = false
    ): Response {
        if ($retrieveMessages) {
            $this->webServer->getTwig()->addGlobal('_messages', $this->getMessages());
        }
        Logger::write("     -> Twig : rendering file '$fileName'");
        return Response::create($this->webServer->getTwig()->render($fileName, $params), $status, $headers);
    }

}
