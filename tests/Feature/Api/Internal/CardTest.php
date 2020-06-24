<?php

namespace Tests\Feature\Api\Internal;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;
use Tests\ApiTestCase;
use Tests\Traits;

class CardTest extends ApiTestCase
{
    use DatabaseMigrations;
    use Traits\ArrangeThings;
    use Traits\UsesSqlite;


    public function testListCardAndRemoveOne(): void
    {
        $this->arrangeUserShop();

        Auth::loginUsingId(1);

        $this->assertSwaggerRequestResponse('GET', '/api/user/cards', 200);

        $this->assertNotEmpty($this->response->getData()->cards);

        foreach ($this->response->getData()->cards as $card) {
            $this->json('DELETE', '/api/user/cards/' . $card->id);
            $this->seeJson(['result' => 'ok']);
            break;
        }

        $this->getJson('/api/user/cards');
        $this->assertEmpty($this->response->getData()->cards);

        // FixME: byjg/php-swagger-test package needs patch or fork to correct parameters validation!
        // \ByJG\Swagger\SwaggerSchema::validateArguments:98
        // https://files.slack.com/files-pri/T4P7E9ECD-FD63SM80K/image.png
        // https://files.slack.com/files-pri/T4P7E9ECD-FD66PC9S6/image.png
        /*
        foreach ($this->response->getData()->cards as $card) {
            $this->assertSwaggerRequestResponse('DELETE', '/api/user/cards/' . $card->id, 200);
            break;
        } //*/;

        // todo: adding card test
    }
}
