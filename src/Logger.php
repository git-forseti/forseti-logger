<?php
namespace Forseti\Logger;

use Monolog\ErrorHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RavenHandler;
use Monolog\Handler\StreamHandler;

class Logger extends \Monolog\Logger
{

    /**
     * Logger constructor.
     * @param string $class
     */
    public function __construct($class)
    {
        parent::__construct($class);
        $stream = new StreamHandler(
            $this->env('FORSETI_LOGGER_STREAM', 'php://stdout'),
            $this->env('FORSETI_LOGGER_LEVEL', \Monolog\Logger::INFO)
        );
        $lineFormatter = new LineFormatter(
            $this->env('FORSETI_LOGGER_FORMAT'),
            $this->env('FORSETI_LOGGER_DATEFORMAT', 'Y-m-d H:i:s.u'),
            false,
            true
        );
        $stream->setFormatter($lineFormatter);
        $this->pushHandler($stream);
        $this->useSentry();
    }

    /**
     * @param string $key
     * @param null $default
     * @return null
     */
    private function env($key, $default = null)
    {
        $value = getenv($key);
        $value = (trim(strtolower($value)) === 'true') ? true : $value;
        $value = (trim(strtolower($value)) === 'false') ? false : $value;
        return $value ?: $default;
    }

    private function useSentry()
    {
        $sentryDNS = $this->env('FORSETI_SENTRY_DNS');

        if ($sentryDNS) {
            $client = new \Raven_Client($sentryDNS);
            $handler = new RavenHandler($client);
            $handler->setLevel($this->env('FORSETI_SENTRY_LOGGER_LEVEL', Logger::WARNING));
            $handler->setFormatter(new LineFormatter("%message% %context% %extra%\n"));
            $this->pushHandler($handler);
            ErrorHandler::register($this);
        }
    }
}
