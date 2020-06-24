<?php

namespace Tests\Feature\Api\Internal;

use ByJG\Swagger\Exception\PathNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;
use Tests\ApiTestCase;
use Tests\Traits;

class ApiBasicTest extends ApiTestCase
{
    use DatabaseMigrations;
    use Traits\ArrangeThings;
    use Traits\UsesSqlite;

    private $unexistedPath = '/api/user/god-mode';
    private $unexistedError = ['result' => 'error', 'message' => 'Route not found'];
    private $unauthorizedError = ['result' => 'error', 'message' => 'Client is not authenticated'];

    public function testBasicCases(): void
    {
        $this->arrangeUserShop();

        Auth::loginUsingId(1);

        // Assert swagger request data & response body
        $this->assertSwaggerRequestResponse('GET', '/api/user/cards', 200);

        Auth::logout();

        // Swagger can generate example request data (set $data to null)
        $this->assertSwaggerRequest('GET', '/api/user/cards', 401);

        // Assert json swagger
        $this->assertPreviousResponseComponent('#/components/schemas/FailedResponse');

        // Assert json schema
        $this->seeJson($this->unauthorizedError);
        $this->seeJsonEquals($this->unauthorizedError);
        $this->seeJsonStructure(array_keys($this->unauthorizedError), $this->response->getOriginalContent());
        $this->assertEquals($this->unauthorizedError, $this->response->getOriginalContent());
        $this->assertJsonStringEqualsJsonString(json_encode($this->unauthorizedError), json_encode($this->response->getOriginalContent()));

        // Unexisted route
        $this->getJson($this->unexistedPath);
        $this->seeJson($this->unexistedError);

        // Unexisted swagger path
        $this->expectException(PathNotFoundException::class);
        $this->expectExceptionMessage('Path "' . $this->unexistedPath . '" not found');
        $this->assertSwaggerRequest('GET', $this->unexistedPath);
    }
}
