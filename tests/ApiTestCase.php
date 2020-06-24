<?php

namespace Tests;

use App\SwaggerSchemaValidator;
use ByJG\Swagger\SwaggerSchema;
use Illuminate\Http\JsonResponse;

abstract class ApiTestCase extends TestCase
{
    protected $filePath = '/var/www/dropwow/storage/api-docs/api-docs.json';

    /** @var SwaggerSchema */
    protected $swaggerSchema;

    /** @var SwaggerSchemaValidator */
    protected $swaggerValidator;

    /** @var JsonResponse */
    protected $response;

    /** @var array */
    protected $structure;


    public function setUp()
    {
        parent::setUp();
        $this->swaggerSchema = new SwaggerSchema(file_get_contents($this->getFilePath()));
        $this->swaggerValidator = new SwaggerSchemaValidator($this->swaggerSchema);
    }

    protected function getFilePath()
    {
        return storage_path("api-docs/api-docs.json");
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->structure = null;
    }

    /**
     * Если в $data передать null, то данные будут взяты из доки (swagger example)
     */
    protected function assertSwaggerRequest(
        string $method = 'GET',
        string $path = '/api/user/cards',
        ?int $expectedStatus = null,
        ?array $data = [],
        array $headers = []
    ): void {
        $requestBody = $data;
        $requestParams = [];

        // find our handle (route)
        $this->structure = $this->swaggerSchema->getPathDefinition($path, $method);

        // fill path parameters
        if (! empty($this->structure['parameters'])) {
            foreach ($this->structure['parameters'] as $parameter) {
                $parameterName = $parameter['name'];
                if (! empty($requestBody[$parameterName]) && 'path' === $parameter['in']) {
                    $path = preg_replace('#{\s*' . $parameter['name'] . '\s*\}#', $data[$parameterName], $path);
                    $requestParams[$parameterName] = $data[$parameterName];
                    unset($requestBody[$parameterName]);
                }
            }
        }

        if (null === $data && ! empty($this->structure['requestBody'])) {
            $data = $this->swaggerValidator->makeSample($this->structure['requestBody']);

            $this->swaggerValidator->matchStructure($this->structure['requestBody'], $data);
        }

        // verify request data
        self::assertTrue($this->assertSwaggerRequestData($this->structure, $data));

        // do request
        $this->json($method, $path, $data, $headers);

        self::assertInstanceOf(JsonResponse::class, $this->response);

        // verify expected status code
        if (null !== $expectedStatus) {
            self::assertEquals($expectedStatus, $this->response->getStatusCode());
        }
    }

    /**
     * Если в $data передать null, то данные будут взяты из доки (example data)
     */
    protected function assertSwaggerRequestResponse(
        string $method = 'GET',
        string $path = '/api/user/cards',
        ?int $expectedStatus = null,
        ?array $data = [],
        array $headers = []
    ): void {
        $this->assertSwaggerRequest($method, $path, $expectedStatus, $data, $headers);

        // verify response body
        self::assertTrue($this->assertSwaggerResponseBody($this->structure, $this->response));
    }

    /**
     * Если в $data передать null, то данные будут взяты из доки (example)
     */
    protected function assertPreviousResponseComponent(string $componentPath): void
    {
        if (! $this->structure) {
            throw new \RuntimeException('No structure for previous requests present');
        };

        $structure = $this->swaggerValidator->getDefinition($componentPath);

        $this->assertPreviousResponseStructure($structure);
    }

    /**
     * Если в $data передать null, то данные будут взяты из доки (example)
     * @param array $structure
     */
    protected function assertPreviousResponseStructure(array $structure): void
    {
        if (! $this->response) {
            throw new \RuntimeException('No previous response data present');
        };

        // verify response body
        self::assertTrue($this->swaggerValidator->matchStructure($structure, $this->response->getOriginalContent()));
    }

    protected function assertSwaggerRequestData(array $structure, array $requestData): bool
    {
        if (empty($structure['parameters']) && empty($structure['requestBody']) && empty($requestData)) {
            return true;
        }

        if (empty($structure['requestBody'])) {
            if (empty($structure['parameters'])) {
                return true;
            }

            foreach ($structure['parameters'] as $param) {
                if ($param['in'] && ! in_array($param['in'], ['path', 'query'])) {
                    return false;
                }
            }

            return true;
        }

        if ($this->swaggerValidator->matchStructure($structure, $requestData) !== false) {
            return true;
        }

        return false;
    }

    protected function assertSwaggerResponseBody(array $structure, JsonResponse $responseJson): bool
    {
        foreach ($structure['responses'] as $responseCode => $responseData) {
            if ($responseJson->getStatusCode() === $responseCode) {
                return $this->swaggerValidator->matchStructure($responseData, $responseJson->getOriginalContent());
            }
        }

        return false;
    }
}
