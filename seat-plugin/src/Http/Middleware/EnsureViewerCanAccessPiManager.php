<?php

declare(strict_types=1);

namespace DrNightmare\SeatPiManager\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureViewerCanAccessPiManager
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
