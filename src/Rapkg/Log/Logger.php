<?php
/**
 * User: coderd
 * Date: 2017/6/20
 * Time: 9:12
 */

namespace Rapkg\Log;


use Psr\Log\LogLevel;
use Psr\Log\AbstractLogger;
use Psr\Log\InvalidArgumentException;

class Logger extends AbstractLogger
{

    protected static $levels = [
        LogLevel::EMERGENCY => 0,
        LogLevel::ALERT => 1,
        LogLevel::CRITICAL => 2,
        LogLevel::ERROR => 3,
        LogLevel::WARNING => 4,
        LogLevel::NOTICE => 5,
        LogLevel::INFO => 6,
        LogLevel::DEBUG => 7,
    ];

    protected $level = LogLevel::INFO;

    protected $filePath;
    protected $lazyFlush = false;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Set log level for logger
     *
     * @param $level
     * @throws InvalidArgumentException
     */
    public function withLevel($level)
    {
        if (!isset(self::$levels[$level])) {
            throw new InvalidArgumentException('Unsupported log level "' . $level . '"');
        }

        $this->level = $level;
    }

    public function withLazyFlush($bool)
    {
        $this->lazyFlush = $bool;
    }


    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @throws InvalidArgumentException
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        if (!isset(self::$levels[$level])) {
            throw new InvalidArgumentException('Unsupported log level "' . $level . '"');
        }
        if (self::$levels[$level] > self::$levels[$this->level]) {
            return;
        }

        $message = '[' . date('Y-m-d H:i:s') . '] [' . strtoupper($level) . '] '
            . self::processMessage($message, $context);

        $this->addRecord($message);
    }

    private static function processMessage($message, array $context = array())
    {
        if (false === strpos($message, '{')) {
            return $message;
        }

        $replacements = [];
        foreach ($context as $key => $value) {
            if (is_scalar($value) || null === $value
                    || (is_object($value) && method_exists($value, '__toString'))) {
                $replacements['{'.$key.'}'] = $value;
            } else if (is_object($value)) {
                $replacements['{'.$key.'}'] = '[object ' . get_class($value) . ']';
            } else {
                $replacements['{'.$key.'}'] = '[' . gettype($value) . ']';
            }
        }

        return strtr($message, $replacements);
    }

    protected $recordBuf = [];

    protected function addRecord($message)
    {
        if ($this->lazyFlush) {
            $this->recordBuf[] = $message;
            return;
        }

        $this->write($message);
    }

    protected function write($message)
    {
        $dir = dirname($this->filePath);
        if (!is_dir($dir) && !mkdir($dir,0777, true)) {
            return false;
        }

        return file_put_contents($this->filePath, $message, FILE_APPEND);
    }

    public function flush()
    {
        if (empty($this->recordBuf)) {
            return;
        }
        $this->write(implode("\n", $this->recordBuf) . "\n");
        $this->recordBuf = [];
    }

    public function __destruct()
    {
        if ($this->lazyFlush) {
            $this->flush();
        }
    }
}