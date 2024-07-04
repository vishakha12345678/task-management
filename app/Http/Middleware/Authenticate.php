<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        $he = $request->header();
        if (isset($he['authorization'])) {
            if (empty($he['authorization'][0])) {
                $request->headers->set('accept', 'application/json', true);
            }
        } else {
            $request->headers->set('accept', 'application/json', true);
        }
       
    }
}
