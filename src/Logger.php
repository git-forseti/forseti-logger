<?php
namespace Forseti\Logger;

use Monolog\Formatter\LineFormatter;
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
            $this->env('RECAPCHA_LOGGER_STREAM', 'php://stdout'),
            $this->env('RECAPCHA_LOGGER_LEVEL', \Monolog\Logger::INFO)
        );
        $lineFormatter = new LineFormatter(
            $this->env('RECAPCHA_LOGGER_FORMAT'),
            $this->env('RECAPCHA_LOGGER_DATEFORMAT', 'Y-m-d H:i:s.u'),
            false,
            true
        );
        $stream->setFormatter($lineFormatter);
        $this->pushHandler($stream);
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
}