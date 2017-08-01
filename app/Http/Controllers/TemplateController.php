<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Helpers\EmailerHelpers;
use Illuminate\Http\Request;

class TemplateController extends Controller
{

    public static $model = 'App\Models\Template';
    
    /**
     * Render a preview of an email template
     * @param  Request $request  
     * @param  string  $template Template name
     */
    public function show(Request $request, $template)
    {
        if(view()->exists("emails.{$template}"))
        {
            if($tpl = Template::findBySource($template))
            {
                return view("emails.{$template}", [
                    'subject' => $tpl->name,
                    'content' => $tpl->default_content,
                    'signature' => null,
                    'emailer_id' => null,
                    'address_id' => null
                ]);
            }
            else
            {
                return view("emails.{$template}");
            }
        }
        else
        {
            return view("emails.not-found");
        }
    }    

}
