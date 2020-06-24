<?php

namespace App;

use ByJG\Swagger\SwaggerBody;
use ByJG\Swagger\SwaggerSchema;

class SwaggerSchemaValidator extends SwaggerBody
{
    public function __construct(
        SwaggerSchema $swaggerSchema,
        array $structure = [],
        string $name = '/api/path/to/super-power/method-call',
        bool $allowNullValues = false
    ) {
        ! empty($structure) && $structure = $this->findSchemaDefinition($structure);
        parent::__construct($swaggerSchema, $name, $structure, $allowNullValues);
    }

    public function match($body)
    {
        return $this->matchSchema($this->name, $this->structure, $body);
    }

    public function matchStructure(array $structure, array $body): bool
    {
        return $this->matchSchema($this->name, $structure, $body);
    }

    /**
     * @param string $name
     * @param array  $schema
     * @param array  $body
     * @return bool
     */
    protected function matchSchema($name, $schema, $body)
    {
        $schema = $this->findSchemaDefinition($schema);
        $schema = $this->prepareSchema($schema);
        if (isset($schema['type'])) {
            if (null === $body && !empty($schema['x-nullable']) && $schema['x-nullable']) {
                return true;
            }
            if ('object' === $schema['type']) {
                return $this->matchObject($name, $schema, $body);
            }

            return parent::matchSchema($name, $schema, $body);
        }

        return true;
    }

    private function matchObject(string $name, array $schema, array $body): bool
    {
        if (count($body) !== count($schema['properties'])) {
            throw new \RuntimeException('Number of properties does not match');
        }

        foreach ((array) $body as $key => $item) {
            if (! isset($schema['properties'])) {  // If there is no type, there is no test.
                continue;
            }
            $this->matchSchema($name, $schema['properties'][$key], $item);
        }

        return true;
    }

    private function prepareSchema(array $schema): array
    {
        $schema = $this->handleRefs($schema);
        $schema = $this->handleAllOfs($schema);

        return $schema;
    }


    public function handleRefs($haystack, $needle = '$ref')
    {
        if (is_array($haystack)) {
            foreach ($haystack as $key => $value) {
                if (is_array($haystack[$key])) {
                    $self = __FUNCTION__;
                    $haystack[$key] = static::$self($haystack[$key], $needle);
                } elseif ((string) $key === (string) $needle) {
                    $definition = $this->findSchemaDefinition($this->getDefinition($value));
                    unset($haystack[$key]);
                    if (! empty($definition['type']) && 'object' === $definition['type']) {
                        if (! empty($haystack['properties'])) {
                            $haystack['properties'] = array_merge($haystack['properties'], $definition['properties']);
                        } else {
                            $haystack = $definition;
                        }
                    }
                    $haystack = $this->prepareSchema($haystack);
                }
            }
        }

        return $haystack;
    }

    private function handleAllOfs(array $schema): array
    {
        foreach ($schema as $key => $allOfs) {
            if ('allOf' === $key) {
                foreach ($allOfs as $allOf) {
                    empty($schema['properties']) && $schema['properties'] = [];
                    $schema['properties'] = array_merge($schema['properties'], $allOf['properties']);
                }
                unset($schema[$key]);
                break;
            }
        }

        return $schema;
    }

    public function getDefinition(string $name): array
    {
        $nameParts = explode('/', $name);
        $lastName = $nameParts[count($nameParts) - 1];
        $definition = $this->swaggerSchema->getDefintion($name);
        if (! empty($definition[$lastName])) {
            return $definition[$lastName];
        }

        return $definition;
    }

    private function findSchemaDefinition(array $structure): array
    {
        // schema is here
        if (! empty($structure['schema'])) {
            return $structure['schema'];
        }

        // content schema (request body)
        if (! empty($structure['content']['application/json']['schema'])) {
            return $structure['content']['application/json']['schema'];
        }

        // this is schema
        if (! empty($structure['type']) || ! empty($structure['properties'])) {
            return $structure;
        }

        // dunno wtf
        throw new \RuntimeException('Cannot find schema definition');
    }

    public function makeSample(array $schema): array
    {
        foreach ($this->prepareSchema($schema)['properties'] as $key => $property) {
            $sample[$key] = $property['example'];
        }

        return $sample ?? [];
    }
}
