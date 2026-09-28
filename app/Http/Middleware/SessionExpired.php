<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Auth;
use Session;

class SessionExpired
{
    protected $session;
    protected $timeout = 1800; // 1800 in seconds, = 30 minutes inactive then logout

    public function __construct(Store $session){
        $this->timeout = config('session.lifetime') * 60;
        $this->session = $session;
    }

    public function handle($request, Closure $next){

        if ($this->session->get('lastActivityTime')) {

            if(time() - $this->session->get('lastActivityTime') > $this->timeout){

                $this->session->flush();
                auth()->logout();
                return redirect('index');

            } else {

                $this->session->put('lastActivityTime', time());
            
            }
            
        }
        
        return $next($request);
    }
}
