<?php

namespace Crater\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * The app only receives traffic through the reverse proxy (Traefik/Cloudflare),
     * so its forwarded scheme and host headers are trusted.
     *
     * @var array|string
     */
    protected $proxies = '*';

    /**
     * The current proxy header mappings.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
