<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleToasts
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        if (session()->has('toast') && !$request->expectsJson()) {
            $toast = session('toast');
            
            $script = <<<SCRIPT
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (window.Toast) {
                        window.Toast.show('{$toast['message']}', '{$toast['type']}', {$toast['duration']});
                    } else {
                        document.dispatchEvent(new CustomEvent('toast', { 
                            detail: {
                                message: '{$toast['message']}',
                                type: '{$toast['type']}',
                                duration: {$toast['duration']}
                            }
                        }));
                    }
                });
            </script>
            SCRIPT;
            
            // Get the content of the response
            $content = $response->getContent();
            
            // Insert script before closing body tag
            if (is_string($content) && str_contains($content, '</body>')) {
                $content = str_replace('</body>', $script . '</body>', $content);
                $response->setContent($content);
            }
        }
        
        return $response;
    }
}