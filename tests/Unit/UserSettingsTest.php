<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\User\Setting;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;
use Tests\Traits\UsesSqlite;

class UserSettingsTest extends TestCase
{
    use DatabaseMigrations, UsesSqlite;

    /** @test */
    public function modelUserHaveMethodSetting(): void
    {
        /** @var User $user */
        $user = create(User::class);
        $setting = create(Setting::class, ['user_id' => $user->id]);

        $this->assertEquals($setting->value, $user->setting($setting->key));

        $settings = Setting::defaultSettings();
        $key = 'gpr_rate';
        $this->assertEquals($settings->get($key)['value'], $user->setting($key));

        $this->expectException(NotFoundHttpException::class);
        $user->setting('asd');
    }
}
