<?php

namespace App\Vendor\Monolog;

use Monolog\Formatter\ElasticaFormatter;

class ElasticFormatter extends ElasticaFormatter
{
    protected $channel;
    protected $env;

    public function __construct($channel)
    {
        $this->channel = $channel;
        $this->env = env("APP_ENV", "production");
        $index = $channel . '-log';
        $type = "record";
        parent::__construct($index, $type);
    }

    public function format(array $record)
    {
        $record['channel'] = $this->channel;
        $record['env'] = $this->env;
        $record =  parent::format($record);

        return $record;
    }
}
