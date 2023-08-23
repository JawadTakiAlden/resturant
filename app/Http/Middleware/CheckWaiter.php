<?php

namespace App\Http\Middleware;

use App\Status\UserType;
use App\Traits\CustomResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckWaiter
{
    use CustomResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()->type === UserType::WAITER){
            return $next($request);
        }
        return $this->customResponse(null , 'Unauthorized' , 401);
    }
}
