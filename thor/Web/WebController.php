<?php

namespace Thor\Web;

use Thor\Debug\Logger;
use Twig\Error\{SyntaxError, LoaderError, RuntimeError};
use Thor\Http\{Session, HttpController, Response\Response, Response\HttpStatus};

/**
 * Base controller for web context.
 *
 * @package          Thor/Http/Controller
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
abstract class WebController extends HttpController
{

    public function __construct(protected WebServer $webServer)
    {
        parent::__construct($webServer);
    }

    public function hasMessages(): bool
    {
        return !empty(Session::read('controller.messages', []));
    }

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
