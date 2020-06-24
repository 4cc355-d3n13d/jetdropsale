<?php

namespace Tests\Feature;

use App\Models\User\Setting;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\UsesSqlite;

class UserSettingsTest extends TestCase
{
    use DatabaseMigrations, UsesSqlite;

    /**
     * @test
     */
    public function guestDontSeeSettings(): void
    {
        $this
            ->getJson('/api/user/settings')
            ->assertResponseStatus(401)
            ->seeJson(
            [
                'result' => 'error',
                'message' => 'Client is not authenticated'
            ]
        );
    }

    /**
     * @test
     */
    public function userSeeSettings(): void
    {
        $this->signIn();
        $settings = Setting::defaultSettings()->mapWithKeys(function ($value, $key) {
            return [$key=>$value['value']];
        });

        $this
            ->getJson('/api/user/settings')
            ->assertResponseStatus(200)
            ->seeJson(['result'=>'ok','settings'=> $settings->toArray()])
        ;
    }

    /**
     * @test
     */
    public function userSaveSettings()
    {
        $this->signIn();
        $settings = Setting::defaultSettings()->mapWithKeys(function ($value, $key) {
            return [$key=>$value['value']];
        });

        $this->putJson('/api/user/settings', $settings->toArray())
            ->assertResponseStatus(200)
            ->seeJson(['result'=>'ok','settings'=> $settings->toArray()])
        ;

        $settings->each(function ($value, $key) {
            $this->seeInDatabase((new Setting)->getTable(), ['key'=>$key, 'value'=>$value, 'user_id'=>auth()->id()]);
        });
    }
}
