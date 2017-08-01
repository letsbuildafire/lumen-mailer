<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Exceptions\APIException;
use App\Exceptions\APIBadRequestException;
use App\Exceptions\APINotFoundException;
use App\Exceptions\APINotAllowedException;

use Illuminate\Http\Request;
use App\Models\Template;

use Laravel\Lumen\Routing\Controller as BaseController;

class TemplateController extends BaseController
{
    use APIController;

    const MODEL = 'App\Models\Template';
    
    /**
     * POST a new template
     * @param  Request $request 
     */
    public function post(Request $request)
    {
        try
        {
            $template = new Template;
            $template->name = $request->input('name');
            $template->source = $request->input('source');
            $template->default_content = $request->input('default_content');
            $template->save();

            return response()->json(['data' => $template], 201);
        }
        catch(Exception $e)
        {
            throw new APIException($e->getMessage());
        }
    }
    
    /**
     * PUT changes to a template by uuid
     * @param  Request $request 
     * @param  string  $id      Template uuid
     */
    public function put(Request $request, $id)
    {
        try
        {
            if($template = Template::find($id))
            {
                
                $template->name = $request->input('name', $template->name);
                $template->source = $request->input('source', $template->source);
                $template->default_content = $request->input('default_content', $template->default_content);
                $template->save();

                return response()->json(['data' => $template], 200);
            }
            else{
                throw new APINotFoundException('Resource not found');
            }
        }
        catch(Exception $e)
        {
            throw new APIException($e->getMessage());
        }
    }

}
