<?php

namespace Tests;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\BrowserKitTesting\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use Traits\CreatesApplication;

    public $baseUrl = 'http://dropwow.loc';


    protected function signIn(?User $user = null)
    {
        $user = $user ?: create('App\Models\User');

        return $this->actingAs($user);
    }

    protected function signOut()
    {
        Auth::logout();

        return $this;
    }

    public function mockJson(string $path, bool $asArray = true)
    {
        return json_decode($this->getMockFromFile($path), $asArray);
    }

    public function getMockFromFile(string $path)
    {
        return file_get_contents(base_path('tests/Mock/' . $path));
    }
}
