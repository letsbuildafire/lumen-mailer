<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Exceptions\APIException;
use App\Exceptions\APIBadRequestException;
use App\Exceptions\APINotFoundException;
use App\Exceptions\APINotAllowedException;

use Illuminate\Http\Request;
use App\Models\HelpArticle;

use Laravel\Lumen\Routing\Controller as BaseController;

class HelpArticleController extends BaseController
{
    use APIController;
    
    const MODEL = 'App\Models\HelpArticle';
    
    /**
     * POST a new help article
     * @param  Request $request 
     */
    public function post(Request $request)
    {
        try
        {
            $article = new HelpArticle;
            $article->title = $request->input('title');
            $article->content = $request->input('content');
            $article->section = $request->input('section');
            $article->save();

            return response()->json(['data' => $article], 201);
        }
        catch(Exception $e)
        {
            throw new APIException($e->getMessage());
        }
    }
    
    /**
     * PUT changes to a help article by uuid
     * @param  Request $request 
     * @param  string  $id      Help article uuid
     */
    public function put(Request $request, $id)
    {
        try
        {
            if($article = HelpArticle::find($id))
            {
                $article->title = $request->input('title', $article->title);
                $article->content = $request->input('content', $article->content);
                $article->section = $request->input('section', $article->section);
                $article->save();

                return response()->json(['data' => $article], 200);
            }
            else
            {
                throw new APINotFoundException('Resource not found');
            }
        }
        catch(Exception $e)
        {
            throw new APIException($e->getMessage());
        }
    }
}
