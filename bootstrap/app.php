<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(web: __DIR__ . "/../routes/web.php", api: __DIR__ . "/../routes/api.php", commands: __DIR__ . "/../routes/console.php", health: "/up")
  ->withMiddleware(function (Middleware $middleware) {
    // 👇 DAFTARKAN MIDDLEWARE ALIAS
    $middleware->alias([
      "role" => \App\Http\Middleware\CheckRole::class,
    ]);
  })
  ->withExceptions(function (Exceptions $exceptions) {
    // GET nyasar ke alamat yang hanya menerima POST/PATCH/DELETE
    // (misalnya saat browser HP memuat ulang alamat awal setelah mengganti mode situs)
    $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) {
      if (app()->isProduction() && $request->isMethod("GET") && $request->is("admin/*")) {
        return redirect()->route("admin.dashboard");
      }
    });
  })
  ->create();
