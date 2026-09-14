<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuditActivityMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $response = $next($request);

        if (
            auth()->check()
            && in_array(
                strtoupper($request->method()),
                [
                    'POST',
                    'PUT',
                    'PATCH',
                    'DELETE',
                ],
                true
            )
        ) {
            try {
                $routeName =
                    $request->route()?->getName();

                AuditLog::create([
                    'user_id' =>
                        auth()->id(),

                    'action' =>
                        $routeName
                        ?? strtoupper(
                            $request->method()
                        ),

                    'route_name' =>
                        $routeName,

                    'method' =>
                        strtoupper(
                            $request->method()
                        ),

                    'path' =>
                        $request->path(),

                    'status_code' =>
                        $response->getStatusCode(),

                    'ip_address' =>
                        $request->ip(),

                    'user_agent' =>
                        mb_substr(
                            (string)
                            $request->userAgent(),
                            0,
                            1000
                        ),
                ]);
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        return $response;
    }
}
