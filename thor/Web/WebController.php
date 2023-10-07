<?php

namespace Thor\Web;

use Thor\Debug\Logger;
use Twig\Error\{SyntaxError, LoaderError, RuntimeError};
use Thor\Http\{Session, HttpController, Response\HttpStatus};

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
     */
    public function __construct(protected WebServer $webServer)
    {
        parent::__construct($webServer);
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
     * Adds a message in the current session.
     * All messages are cleared when they are retrieved.
     *
     * @param string $message
     * @param string $title
     * @param string $type
     * @param string $mutedString
     *
     * @return void
     */
    public function addMessage(string $message, string $title = '', string $type = 'info', string $mutedString = ''): void
    {
        Session::write(
            'controller.messages',
            [
                ...$this->getMessages(),
                ...[
                    [
                        'title' => $title,
                        'message' => $message,
                        'type' => $type,
                        'muted' => $mutedString,
                    ],
                ]
            ]
        );
    }

    /**
     * Gets and clears messages in the current session.
     *
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
     * @param string $fileName
     * @param array $params
     * @param HttpStatus $status
     * @param array $headers
     * @param bool $retrieveMessages
     *
     * @return TwigResponse
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function twigResponse(
        string $fileName,
        array $params = [],
        HttpStatus $status = HttpStatus::OK,
        array $headers = [],
        bool $retrieveMessages = false
    ): TwigResponse {
        if ($retrieveMessages) {
            $this->webServer->getTwig()->addGlobal('_messages', $this->getMessages());
        }
        Logger::write("     -> Twig : rendering file '$fileName'");
        return new TwigResponse($this->getServer()->getTwig(), $fileName, $params, $status, $headers);
    }

}
