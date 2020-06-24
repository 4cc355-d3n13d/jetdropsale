<?php declare(strict_types=1);

namespace App\Vendor\Monolog;

use Exception;
use Monolog\Formatter\NormalizerFormatter;
use Throwable;

/**
 * Encodes whatever record data is passed to it as json
 *
 * This can be useful to log to databases or remote APIs
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class JsonFormatter extends NormalizerFormatter
{
    public const BATCH_MODE_JSON = 1;
    public const BATCH_MODE_NEWLINES = 2;

    protected $batchMode = self::BATCH_MODE_JSON;
    protected $appendNewline = true;

    /**
     * @var bool
     */
    protected $includeStacktraces = false;

    /**
     * The batch mode option configures the formatting style for
     * multiple records. By default, multiple records will be
     * formatted as a JSON-encoded array. However, for
     * compatibility with some API endpoints, alternative styles
     * are available.
     *
     * @return int
     */
    public function getBatchMode(): int
    {
        return $this->batchMode;
    }

    /**
     * True if newlines are appended to every formatted record
     *
     * @return bool
     */
    public function isAppendingNewlines(): bool
    {
        return $this->appendNewline;
    }

    /**
     * {@inheritdoc}
     * @throws \RuntimeException
     */
    public function formatBatch(array $records)
    {
        switch ($this->batchMode) {
            case static::BATCH_MODE_NEWLINES:
                return $this->formatBatchNewlines($records);

            case static::BATCH_MODE_JSON:
            default:
                return $this->formatBatchJson($records);
        }
    }

    /**
     * Use new lines to separate records instead of a
     * JSON-encoded array.
     *
     * @param  array $records
     *
     * @return string
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function formatBatchNewlines(array $records): string
    {
        $instance = $this;

        $oldNewline = $this->appendNewline;
        $this->appendNewline = false;
        \array_walk($records, function (&$value) use ($instance) {
            $value = $instance->format($value);
        });
        $this->appendNewline = $oldNewline;

        return \implode("\n", $records);
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function format(array $record)
    {
        return $this->toJson($this->normalize($record), true).($this->appendNewline ? "\n" : '');
    }

    /**
     * Normalizes given $data.
     *
     * @param mixed $data
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function normalize($data)
    {
        if (\is_array($data) || $data instanceof \Traversable) {
            $normalized = [];

            $count = 1;
            foreach ($data as $key => $value) {
                if ($count++ >= 1000) {
                    $normalized['...'] = 'Over 1000 items, aborting normalization';
                    break;
                }
                $normalized[$key] = $this->normalize($value);
            }

            return $normalized;
        }

        if ($data instanceof Exception || $data instanceof Throwable) {
            return $this->normalizeException($data);
        }

        return $data;
    }

    /**
     * Normalizes given exception with or without its own stack trace based on
     * `includeStacktraces` property.
     *
     * @param Exception|Throwable $e
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function normalizeException($e): array
    {
        // TODO 2.0 only check for Throwable
        if (! $e instanceof Exception && ! $e instanceof Throwable) {
            throw new \InvalidArgumentException('Exception/Throwable expected, got '.\gettype($e).' / '.\get_class($e));
        }

        $file = $e->getFile().':'.$e->getLine();
        $file = \str_replace(\dirname(__FILE__, 4), '...', $file);

        $data = [
            'class'   => \get_class($e),
            'message' => $e->getMessage(),
            'code'    => $e->getCode(),
            'file'    => $file,
        ];

        if ($this->includeStacktraces) {
            $trace = $e->getTrace();
            foreach ($trace as $frame) {
                if (isset($frame['file'])) {
                    $data['trace'][] = $frame['file'].':'.$frame['line'];
                } elseif (isset($frame['function']) && $frame['function'] === '{closure}') {
                    // We should again normalize the frames, because it might contain invalid items
                    $data['trace'][] = $frame['function'];
                } else {
                    // We should again normalize the frames, because it might contain invalid items
                    $data['trace'][] = $this->normalize($frame);
                }
            }
        }

        if ($previous = $e->getPrevious()) {
            $data['previous'] = $this->normalizeException($previous);
        }

        return $data;
    }

    /**
     * Return a JSON-encoded array of records.
     *
     * @param  array $records
     *
     * @return string
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function formatBatchJson(array $records): string
    {
        return $this->toJson($this->normalize($records), true);
    }

    /**
     * @param bool $include
     */
    public function includeStacktraces($include = true): void
    {
        $this->includeStacktraces = $include;
    }
}
