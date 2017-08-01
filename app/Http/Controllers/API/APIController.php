<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Exceptions\APIException;
use App\Exceptions\APIBadRequestException;
use App\Exceptions\APINotFoundException;
use App\Exceptions\APINotAllowedException;

trait APIController
{
    protected $filters;
    protected $order;
    protected $pagination;
    protected $search;

    /**
     * Determine the valid filters for a request
     * @param Request $request 
     */
    protected function setFilters(Request $request)
    {
        try
        {
            $this->filters = [];

            if(!property_exists(self::MODEL,'filters')) return;
            $model = self::MODEL;

            foreach($model::$filters as $filter)
            {
                if($request->has($filter))
                {
                    $value = $request->input($filter);
                    $comparator = "=";
                    if(preg_match('/^%3C/i', $value))
                    {
                        $value = substr(urldecode($value), 1);
                        $comparator = "<";
                    }
                    elseif(preg_match('/^%3E/i', $value))
                    {
                        $value = substr(urldecode($value), 1);
                        $comparator = ">";
                    }
                    elseif(preg_match('/^\^/i', $value))
                    {
                        $value = substr(urldecode($value), 1);
                        $comparator = "!=";
                    }

                    // If there is a filter set, add it to our filter array
                    if(strlen($value))
                    {
                        $this->filters[] = [$filter, $comparator, $value];
                    }
                }
            }
        }
        catch(Exception $e)
        {
            throw new APIException($e->getMessage());
        }
    }

    /**
     * Determine the valid search parameters for a request
     * @param Request $request 
     */
    protected function setSearch(Request $request)
    {
        try
        {
            $this->search = [];

            if(!property_exists(self::MODEL,'searchField')) return;

            if($request->has('q'))
            {
                $searches = explode(',', $request->input('q'));
                foreach($searches as $search){
                    $this->search[] = $search;
                }
            }
        }
        catch(Exception $e)
        {
            throw new APIException($e->getMessage());
        }
    }

    /**
     * Determine the sort order for a request
     * @param Request $request
     */
    protected function setOrder(Request $request)
    {
        try
        {
            $this->order = [];

            if($request->has('order'))
            {
                $this->order = array_map(function($column)
                {
                    $direction = 'asc';
                    if(preg_match('/^-/', $column))
                    {
                        $column = substr($column, 1);
                        $direction = 'desc';
                    }
                    return [$column, $direction];
                }, explode(',', $request->input('order')));
            }
        }
        catch(Exception $e)
        {
            throw new APIException($e->getMessage());
        }
    }

    /**
     * Determine the current page and page length for a request
     * @param Request $request 
     * @param integer $limit
     */
    public function setPagination(Request $request, $limit = 10)
    {
        try
        {
            $this->pagination = [
                'limit' => $limit,
                'page' => 0
            ];
            
            if($request->has('limit'))
            {
                $this->pagination['limit'] = (int) $request->input('limit');
            }
            
            if($request->has('page'))
            {
                $this->pagination['page'] = $request->input('page') - 1;
            }
        }
        catch(Exception $e)
        {
            throw new APIException($e->getMessage());
        }
    }

    /**
     * Return all resources for this request
     * @param  Request $request  
     * @param  string  $entities The model to return
     */
    public function all(Request $request, $entities = null)
    {
        try
        {
            if(empty($entities))
            {
                $entities = self::MODEL;
            }

            $this->setFilters($request);
            $this->setSearch($request);
            $this->setOrder($request);
            $this->setPagination($request);

            foreach($this->filters as $filter)
            {
                $entities = call_user_func_array([$entities, 'where'], $filter);
            }

            foreach($this->search as $search)
            {
                $model = self::MODEL;
                $entities = call_user_func_array(
                    [$entities, 'where'],
                    [$model::searchField(), 'LIKE', "%{$search}%"]);
            }

            foreach($this->order as $condition){
                $entities = call_user_func_array([$entities,'orderBy'],$condition);
            }

            // Build counts for pagination
            $num_entries = call_user_func_array([$entities, 'count'], []);
            $num_pages = (int) ceil($num_entries / $this->pagination['limit']);
            $current_page = (int) $this->pagination['page'] + 1;

            // Apply pagination parameters
            $entities = call_user_func_array(
                [$entities, 'skip'],
                [$this->pagination['page'] * $this->pagination['limit']]);

            $entities = call_user_func_array(
                [$entities, 'take'],
                [$this->pagination['limit']]);

            $entities = $entities->get();
            
            // If we are requesting relationships as well
            if($request->has('with'))
            {
                $with = $request->input('with');
                if(!is_array($with))
                {
                    $with = [$with];
                }

                foreach($with as $i => $relation)
                {
                    if(!method_exists($entities, $relation))
                    {
                        unset($with[$i]);
                    }
                }

                // If we still have relationships to load
                if(count($with))
                {
                    $entities = call_user_func_array([$entities, 'load'],$with);
                }
            }

            return response()->json(['data' => $entities], 200)
                ->header('X-Pagination-Per-Page', $this->pagination['limit'])
                ->header('X-Pagination-Current-Page', $current_page)
                ->header('X-Pagination-Total-Pages', $num_pages)
                ->header('X-Pagination-Total-Entries', $num_entries);
        }
        catch(Exception $e)
        {
            throw new APIException($e->getMessage());
        }
    }

    /**
     * Return a single resource by uuid/id
     * @param  Request $request 
     * @param  string  $id      The uuid or id
     */
    public function get(Request $request, $id)
    {
        try
        {
            if($entity = call_user_func_array([self::MODEL,'find'],[$id]))
            {
                if($request->has('with'))
                {
                    $with = $request->input('with');
                    if(!is_array($with))
                    {
                        $with = [$with];
                    }

                    foreach($with as $i => $relation)
                    {
                        if(!method_exists($entity, $relation))
                        {
                            unset($with[$i]);
                        }
                    }
                    call_user_func_array([$entity, 'load'],$with);
                }
                return response()->json(['data' => $entity], 200);
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

    /**
     * Remove a single resource by uuid/id
     * @param  Request $request  
     * @param  string  $id       The uuid or id
     * @param  function $callback onbefore callback
     */
    public function delete(Request $request, $id, $callback = null)
    {
        try
        {
            if($entity = call_user_func_array([self::MODEL,'find'],[$id]))
            {
                if(is_callable($callback))
                {
                    $callback($request, $entity);
                }
                $entity->delete();
                return response()->json([], 204);
            }
            else
            {
               throw new APINotFoundException('Resource not found');
            }
        }
        catch(Exception $e)
        {
            if($e instanceof APIException)
            {
                throw $e;
            }
            throw new APIException($e->getMessage());
        }
    }

    /**
     * Remove all resources of a given type.
     * @param  Request $request 
     */
    public function truncate(Request $request)
    {
        try
        {
            call_user_func([self::MODEL, 'truncate']);
            return response()->json([], 204);
        }
        catch(Exception $e)
        {
            throw new APIException($e->getMessage());
        }
    }
    
}
