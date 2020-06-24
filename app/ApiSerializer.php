<?php

namespace App;

use League\Fractal\Serializer\ArraySerializer;

class ApiSerializer extends ArraySerializer
{
    /**
     * Serialize a collection.
     * @param string $resourceKey
     * @param array  $data
     * @return array
     */
    public function collection($resourceKey, array $data)
    {
        if (!$resourceKey) {
            throw new \InvalidArgumentException('resourceKey is required');
        }

        return [$resourceKey => $data];
    }

    /**
     * Serialize an item.
     * @param string $resourceKey
     * @param array  $data
     * @return array
     */
    public function item($resourceKey, array $data)
    {
        if (!$resourceKey) {
            throw new \InvalidArgumentException('resourceKey is required');
        }

        return [$resourceKey => $data];
    }

    /**
     * Serialize null resource.
     * @return array
     */
    public function null()
    {
        return ['data' => []];
    }

    public function meta(array $meta)
    {
        return $meta;
    }
}
