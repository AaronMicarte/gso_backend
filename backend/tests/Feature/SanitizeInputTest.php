<?php

namespace Tests\Feature;

use App\Http\Middleware\SanitizeInput;
use Illuminate\Http\Request;
use Tests\TestCase;

class SanitizeInputTest extends TestCase
{
    public function test_strips_script_tags()
    {
        $request = Request::create('/test', 'POST', [
            'name' => '<script>malicious()</script>Hello',
        ]);

        $middleware = new SanitizeInput();

        $middleware->handle($request, function ($request) {
            $this->assertEquals(
                'malicious()Hello',
                $request->input('name')
            );

            return response()->json();
        });
    }
}