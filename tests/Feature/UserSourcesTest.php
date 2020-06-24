<?php

namespace Tests\Feature;

use App\Http\Middleware\EncryptCookies;
use App\Models\User\UserSource;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Symfony\Component\HttpFoundation\Cookie;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;
use Tests\Traits\UsesSqlite;

class UserSourcesTest extends TestCase
{
    use DatabaseMigrations, UsesSqlite;

    public function setUp()
    {
        parent::setUp();
        $this->disableCookiesEncryption(UserSource::DROPWOW_UUID_COOKIE);
    }

    /**
     * @test
     */
    public function saveGuestReferer(): void
    {
        $this->get('/', ['referer' => URL::to('catalog')]) // Visiting the site - ignoring
            ->assertCount(0, UserSource::all());

        $this->get('/') // Empty referrer - saving
            ->seeCookie(UserSource::DROPWOW_UUID_COOKIE)
            ->seeInDatabase('user_sources', ['http_referrer_domain' => null, 'http_referrer_full' => null, 'user_id' => null, 'id' => 1])
            ->assertCount(1, UserSource::all());

        $ref = 'https://ya.ru/foo?bar=baz';
        $this->get('/', ['referer' => $ref]) // Came from yandex - saving
            ->seeCookie(UserSource::DROPWOW_UUID_COOKIE)
            ->seeInDatabase('user_sources', ['http_referrer_domain' => 'ya.ru', 'http_referrer_full' => $ref, 'user_id' => null, 'id' => 2])
            ->assertCount(2, UserSource::all())
        ;
    }

    /**
     * @test
     */
    public function ignoreSignedUserReferer(): void
    {
        // Its a user, not a guest - always ignoring
        $this->signIn();

        $this->get('/', ['referer' => URL::to('catalog')])
            ->assertCount(0, UserSource::all());

        $this->get('/')
            ->dontSeeInDatabase('user_sources', ['http_referrer_domain' => null, 'http_referrer_full' => null, 'user_id' => null, 'id' => 1])
            ->assertCount(0, UserSource::all());

        $ref = 'https://ya.ru/foo?bar=baz';
        $this->get('/', ['referer' => $ref])
            ->dontSeeInDatabase('user_sources', ['http_referrer_domain' => 'ya.ru', 'http_referrer_full' => $ref, 'user_id' => null, 'id' => 2])
            ->assertCount(0, UserSource::all())
        ;
    }


    /**
     * @test
     */
    public function saveUuidCookie(): void
    {
        $this->get('/')
            ->seeCookie(UserSource::DROPWOW_UUID_COOKIE);

        $uuidCookie = collect($this->response->headers->getCookies())->first(function (Cookie $cookie) {
            return $cookie->getName() === UserSource::DROPWOW_UUID_COOKIE;
        });

        // A bit weird but I cannot find the better way
        $this->call('get', '/', [], [UserSource::DROPWOW_UUID_COOKIE => $uuidCookie->getValue()]);
        $this->seeCookie($uuidCookie->getName(), $uuidCookie->getValue(), false, false);

        // Like another user
        $this->get('/')
            ->seeCookie(UserSource::DROPWOW_UUID_COOKIE);

        // 2 users, first one has 2 records and the second has 1 record
        $this->assertCount(2, UserSource::where(['cookie_hash' => $uuidCookie->getValue()])->get());
        $this->assertCount(3, UserSource::all());
    }

    /**
     * @test
     */
    public function updateRecordsWhenUserRegistered(): void
    {
        UserSource::create(['cookie_hash'=>'foo', 'user_id'=>null]);
        UserSource::create(['cookie_hash'=>'foo', 'user_id'=>null]);
        UserSource::create(['cookie_hash'=>'bar', 'user_id'=>null]);


        $this->assertCount(2, UserSource::where(['cookie_hash' => 'foo', 'user_id' => null])->get());
        $this->assertCount(3, UserSource::where(['user_id' => null])->get());

        request()->cookies->set(UserSource::DROPWOW_UUID_COOKIE, 'foo');
        $this->signIn();

        $this->assertCount(2, UserSource::where(['cookie_hash' => 'foo', 'user_id' => 1])->get());
        $this->assertCount(1, UserSource::where(['user_id' => null])->get());
    }

    /**
     * @test
     */
    public function saveUtmData(): void
    {
        $utm = 'test_utm';

        $this->json('GET', '/', ['utm_source' => $utm]);
        $this->assertCount(1, UserSource::where(['utm_source' => $utm])->get());

        $this->json('GET', '/', ['utm_medium' => $utm]);
        $this->assertCount(1, UserSource::where(['utm_medium' => $utm])->get());

        $this->json('GET', '/', ['utm_campaign' => $utm]);
        $this->assertCount(1, UserSource::where(['utm_campaign' => $utm])->get());

        $this->json('GET', '/', ['utm_content' => $utm]);
        $this->assertCount(1, UserSource::where(['utm_content' => $utm])->get());

        $this->json('GET', '/', ['utm_term' => $utm]);
        $this->assertCount(1, UserSource::where(['utm_term' => $utm])->get());
    }

    /**
     * @param $cookies
     * @return $this
     */
    protected function disableCookiesEncryption($cookies): self
    {
        $this->app->resolving(
            EncryptCookies::class,
            function ($object) use ($cookies) {
                $object->disableFor($cookies);
            }
        );

        return $this;
    }
}
