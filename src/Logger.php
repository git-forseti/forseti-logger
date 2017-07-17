<?php
namespace Forseti\Logger;

class Logger extends \Monolog\Logger
{

    /**
     * Logger constructor.
     * @param string $class
     */
    public function __construct($class)
    {
        parent::__construct($class);
        $stream = StreamHandlerFactory::create();
        $stream->setFormatter(LineFormatterFactory::create());

        $this->pushHandler($stream);
        $this->useSentry();
    }

    private function useSentry()
    {
        if ($handler = SentryHandlerFactory::create()) {
            $this->pushHandler($handler);
        }
    }
}