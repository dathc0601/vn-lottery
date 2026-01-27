<?php

namespace App\Http\Middleware;

use App\Services\SeoOverrideService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectSeoOverride
{
    public function __construct(
        protected SeoOverrideService $seoOverrideService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $this->seoOverrideService->resolveForPath($request->getPathInfo());

        return $next($request);
    }
}
