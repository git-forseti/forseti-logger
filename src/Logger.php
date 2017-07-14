<?php
namespace Forseti\Logger;

use Monolog\ErrorHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RavenHandler;
use Monolog\Handler\StreamHandler;

class Logger extends \Monolog\Logger
{

    private static $hasErrorHandler = false;

    /**
     * Logger constructor.
     * @param string $class
     */
    public function __construct($class)
    {
        parent::__construct($class);
        $stream = $this->newStreamHandler();
        $lineFormatter = $this->newLineFormatter();
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

        if (!$sentryDNS) {
            return;
        }

        $client = new \Raven_Client($sentryDNS);
        $handler = new RavenHandler($client);
        $handler->setLevel($this->env('FORSETI_SENTRY_LOGGER_LEVEL', Logger::WARNING));
        $handler->setFormatter(new LineFormatter("%message% %context% %extra%\n"));

        $this->pushHandler($handler);
        $this->registerErrorHandler($handler);
    }

    private function registerErrorHandler($sentryHandler)
    {
        if (self::$hasErrorHandler) {
            return ;
        }

        $stream = $this->newStreamHandler();
        $lineFormatter = $this->newLineFormatter();
        $stream->setFormatter($lineFormatter);

        $errorHandlerLogger = new \Monolog\Logger('error.handler');
        $errorHandlerLogger->pushHandler($stream);
        $errorHandlerLogger->pushHandler($sentryHandler);

        //tomar cuidado para gerar apenas um error handler
        //se não todos os errors handler vão ser executados
        //gerando uma confusão de erros no monolog
        ErrorHandler::register($errorHandlerLogger);
        self::$hasErrorHandler = true;
    }

    /**
     * @return StreamHandler
     */
    protected function newStreamHandler()
    {
        $stream = new StreamHandler(
            $this->env('FORSETI_LOGGER_STREAM', 'php://stdout'),
            $this->env('FORSETI_LOGGER_LEVEL', \Monolog\Logger::INFO)
        );
        return $stream;
    }

    /**
     * @return LineFormatter
     */
    protected function newLineFormatter()
    {
        $lineFormatter = new LineFormatter(
            $this->env('FORSETI_LOGGER_FORMAT'),
            $this->env('FORSETI_LOGGER_DATEFORMAT', 'Y-m-d H:i:s.u'),
            false,
            true
        );
        return $lineFormatter;
    }
}