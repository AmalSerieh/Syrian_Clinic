<?php

namespace Tests\Feature;

use App\Http\Middleware\SetLocale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;

class LocaleMiddlewareTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    public function test_header_locale_processing()
    {
        $middleware = new SetLocale();

        // اختبار مع لغة عربية
        $request = Request::create('/', 'GET');
        $request->headers->set('Accept-Language', 'ar-SY,ar;q=0.9,en;q=0.8');

        $middleware->handle($request, fn() => response()->noContent());
        $this->assertEquals('ar', app()->getLocale());

        // اختبار مع لغة إنجليزية
        $request = Request::create('/', 'GET');
        $request->headers->set('Accept-Language', 'en-US,en;q=0.9');

        $middleware->handle($request, fn() => response()->noContent());
        $this->assertEquals('en', app()->getLocale());
    }
    public function test_user_locale_processing()
    {
        $user = User::factory()->create(['language' => 'en']);
        $this->actingAs($user); // طريقة صحيحة لمحاكاة تسجيل الدخول

        $middleware = new SetLocale();
        $request = Request::create('/', 'GET');

        $middleware->handle($request, fn() => response()->noContent());
        $this->assertEquals('en', app()->getLocale());
    }
    public function test_default_locale()
    {
        $request = Request::create('/', 'GET');
        $middleware = new SetLocale();

        $middleware->handle($request, fn() => response()->noContent());
        $this->assertEquals(config('app.fallback_locale'), app()->getLocale());
    }
}
