<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PartialController extends Controller
{

    /**
     * Render a partial or the default partial for a controller
     * @param  string $controller Controller name
     */
    public function showDefault($controller)
    {
            $controller = preg_replace('/\.tpl$/i', '', $controller);
            if(view()->exists("partials.$controller.default"))
            {
                return view("partials.$controller.default");
            }
            else
            {
                // Return default, let Angular handle errors
                return view('default');
            }

    }

    /**
     * Render a partial for a controller by name
     * @param  string $controller Controller name
     * @param  string $partial    Partial name
     */
    public function show($controller, $partial)
    {
            $partial = preg_replace('/\.tpl$/i', '', $partial);
            if(view()->exists("partials.$controller.$partial"))
            {
                return view("partials.$controller.$partial");
            }
            else
            {
                // Try the default partial
                return $this->showDefault($controller);
            }
    }

    /**
     * Render a partial or the default admin partial for a controller
     * @param  string $controller Controller name
     */
    public function showDefaultAdmin($controller)
    {
            $controller = preg_replace('/\.tpl$/i', '', $controller);
            if(view()->exists("partials.admin.$controller.default"))
            {
                return view("partials.admin.$controller.default");
            }
            else
            {  
                // Return default, let Angular handle errors
                return view('default');
            }
    }

    /**
     * Render an admin partial for a controller by name
     * @param  string $controller Controller name
     * @param  string $partial    Partial name
     */
    public function showAdmin($controller, $partial)
    {
            $partial = preg_replace('/\.tpl$/i', '', $partial);
            if(view()->exists("partials.admin.$controller.$partial"))
            {
                return view("partials.admin.$controller.$partial");
            }
            else
            {
                // Try the default
                return $this->showDefaultAdmin($controller);
            }
    }

}
