<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Exceptions\APIException;
use App\Exceptions\APIBadRequestException;
use App\Exceptions\APINotFoundException;
use App\Exceptions\APINotAllowedException;

use DB;
use Mail;
use Illuminate\Http\Request;
use App\Helpers\EmailerHelpers;
use App\Models\Emailer;
use App\Models\EmailerStat;
use App\Models\Template;

use App\Http\Controllers\QuadrantController as Quadrant;
use Laravel\Lumen\Routing\Controller as BaseController;

class EmailerController extends BaseController
{
    use APIController;

    const MODEL = 'App\Models\Emailer';
    
    /**
     * POST a new emailer
     * @param  Request $request 
     */
    public function post(Request $request)
    {
        try
        {
            DB::beginTransaction();

            $emailer = new Emailer;
            $emailer->subject = $request->input('subject');
            $emailer->return_name = $request->input('return_name');
            $emailer->return_address = $request->input('return_address');
            $emailer->content = $request->input('content');
            $emailer->signature = $request->input('signature');
            $emailer->template_id = $request->input('template_id');
            $emailer->status = $request->input('status', 'UNAPPROVED');

            // convert the date from the front end, in whatever timezone it is set
            // to UTC to store in the database.
            $distribute_at = new \DateTime($request->input('distribute_at', date('Y-m-d H:i:s T')),
                    new \DateTimeZone($request->input('timezone', env('APP_TIMEZONE'))));
            $emailer->distribute_at = $distribute_at->format('Y-m-d H:i:s');

            $emailer->save();

            if($request->has('lists') && is_array($request->input('lists')))
            {
                $emailer->lists()->sync($request->input('lists'));
            }

            DB::commit();

            return response()->json(['data' => $emailer], 201);
        }
        catch(Exception $e)
        {
            DB::rollback();
            throw new APIException($e->getMessage());
        }
    }
    
    /**
     * PUT changes to an emailer by uuid
     * @param  Request $request 
     * @param  string  $id      Emailer uuid
     */
    public function put(Request $request, $id)
    {
        try
        {
            if($emailer = Emailer::find($id))
            {
                if(in_array($emailer->status, ['RUNNING','COMPLETED']))
                {
                    throw new APIBadRequestException(sprintf('%s in this state cannot be edited.',env('TITLES_EMAILERS')));
                }

                DB::beginTransaction();

                $emailer->subject = $request->input('subject', $emailer->subject);
                $emailer->return_name = $request->input('return_name', $emailer->return_name);
                $emailer->return_address = $request->input('return_address', $emailer->return_address);
                $emailer->template_id = $request->input('template_id', $emailer->template_id);
                $emailer->content = $request->input('content', $emailer->content);
                $emailer->signature = $request->input('signature', $emailer->signature);
                
                if($request->has('distribute_at'))
                {
                    // convert the date from the front end, in whatever timezone it is set
                    // to UTC to store in the database.
                    $distribute_at = new \DateTime($request->input('distribute_at', date('Y-m-d H:i:s T')),
                        new \DateTimeZone($request->input('timezone', env('APP_TIMEZONE'))));
                    $emailer->distribute_at = $distribute_at->format('Y-m-d H:i:s');
                }

                $emailer->save();
                
                // update the lists to distribute the emailer to.
                if($request->has('lists') && is_array($request->input('lists')))
                {
                    $emailer->lists()->sync($request->input('lists'));
                }

                // if the emailer is already scheduled on Quadrant's system
                // we need to push the changes to it.
                if(!empty($emailer->quadrant_uid))
                {
                    $template = $emailer->template;
                    $email = view("emails.{$template->source}", [
                        'subject' => $template->name,
                        'content' => $emailer->content,
                        'signature' => $emailer->signature,
                        'emailer_id' => $emailer->id,
                        'address_id' => null
                    ])->render();

                    $from = empty($emailer->return_name) ? $emailer->return_address :
                        "{$emailer->return_name} <{$emailer->return_address}>";

                    // TODO: validate response
                    $quadrant = new Quadrant();
                    $res = $quadrant->putEmailer($emailer->quadrant_uid, [
                        'email' => $email,
                        'to_header' => '{{first_name}}',
                        'from_header' => $from,
                        'subject_header' => $emailer->subject,
                        'start_date' => $distribute_at
                            ->setTimeZone(new \DateTimeZone(env('QUADRANT_TIMEZONE')))
                            ->format(\DateTime::ISO8601),
                        'list' => $emailer->quadrant_list_uid
                    ]);
                }

                DB::commit();
            
                return response()->json(['data' => $emailer], 200);
            }
            else
            {
                throw new APINotFoundException('Resource not found');
            }
        }
        catch(Exception $e)
        {
            DB::rollback();
            throw new APIException($e->getMessage());
        }
    }

    /**
     * DELETE an emailer by uuid
     * @param  Request $request  
     * @param  string  $id       Emailer uuid
     * @param  function  $callback Pre-delete callback
     */
    public function delete(Request $request, $id, $callback = null)
    {
        try
        {
            if($emailer = Emailer::find($id))
            {
                if(in_array($emailer->status, ['PAUSED','RUNNING','COMPLETED']))
                {
                    throw new APIBadRequestException(sprintf('%s in this state cannot be deleted', env('TITLES_EMAILERS')));
                }

                DB::beginTransaction();

                // if we already have the emailer scheduled on Quadrant's system
                if(!empty($emailer->quadrant_uid))
                {
                    // TODO: validate response
                    $quadrant = new Quadrant();
                    $res = $quadrant->deleteEmailer($emailer->quadrant_uid);
                }

                $emailer->lists()->detach();
                $emailer->delete();

                DB::commit();

                return response()->json([], 204);
            }
            else
            {
               throw new APINotFoundException('Resource not found');
            }
        }
        catch(Exception $e)
        {
            DB::rollback();
            throw new APIException($e->getMessage());
        }
    }

    /**
     * Approve an emailer by uuid
     * @param  Request $request 
     * @param  string  $id      Emailer uuid
     */
    public function approve(Request $request, $id)
    {
        try
        {
            if($emailer = Emailer::find($id))
            {
                if(in_array($emailer->status, ['PENDING','PAUSED','RUNNING','COMPLETED']))
                {
                    throw new APIBadRequestException(sprintf('%s is already approved', env('TITLE_EMAILERS')), 403);
                }

                if(!$emailer->lists()->count())
                {
                    throw new APIBadRequestException(sprintf('Cannot approve a %s with no %s',
                        env('TITLE_EMAILERS'),
                        env('TITLES_LISTS')));
                }

                $emailer->status = 'APPROVED';
                $emailer->approved = true;
                $emailer->save();

                return response()->json(['data' => $emailer], 200);
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
     * Unapprove an emailer by uuid
     * @param  Request $request 
     * @param  string  $id      Emailer uuid
     */
    public function unapprove(Request $request, $id)
    {
        try
        {
            if($emailer = Emailer::find($id))
            {

                if(in_array($emailer->status, ['PAUSED','RUNNING','COMPLETED']))
                {
                    throw new APIBadRequestException(sprintf('%s is already running and cannot be unapproved',
                        env('TITLE_EMAILERS')), 403);
                }

                DB::beginTransaction();

                $emailer->status = 'UNAPPROVED';
                $emailer->approved = false;

                // remove from Quadrant's system if we can.
                if(!empty($emailer->quadrant_uid))
                {
                    $quadrant = new Quadrant();
                    $res = $quadrant->deleteEmailer($emailer->quadrant_uid);

                    // *** disabled due to the way Quadrant's API handles lists ***
                    // 
                    // TODO: fix logic or have Quadrant make their system logical
                    // $res = $quadrant->deleteList($emailer->quadrant_list_uid);   

                    $emailer->quadrant_uid = null;
                    $emailer->quadrant_list_uid = null;
                }

                $emailer->save();

                DB::commit();

                return response()->json(['data' => $emailer], 200);
            }
            else
            {
               throw new APINotFoundException('Resource not found');
            }
        }
        catch(Exception $e)
        {
            DB::rollback();
            throw new APIException($e->getMessage());
        }
    }

    /**
     * Pause an emailer by uuid
     * @param  Request $request 
     * @param  string  $id      Emailer uuid
     */
    public function pause(Request $request, $id)
    {
        try
        {
            if($emailer = Emailer::find($id))
            {
                if(!in_array($emailer->status, ['RUNNING']))
                {
                    throw new APIBadRequestException(sprintf('%s is not currently running', env('TITLE_EMAILERS')), 403);
                }

                if(empty($emailer->quadrant_uid))
                {
                    throw new APIBadRequestException(sprintf('%s is not configured in Quadrant system', env('TITLE_EMAILERS')));
                }

                DB::beginTransaction();

                $emailer->status = 'PAUSED';

                // pause emailer on Quadrant's system if we can
                // TODO: validate response
                $quadrant = new Quadrant();
                $res = $quadrant->pauseEmailer($emailer->quadrant_uid);

                $emailer->save();

                DB::commit();

                return response()->json(['data' => $emailer], 200);
            }
            else
            {
               throw new APINotFoundException('Resource not found');
            }
        }
        catch(Exception $e)
        {
            DB::rollback();
            throw new APIException($e->getMessage());
        }
    }

    /**
     * Start an emailer by uuid
     * @param  Request $request 
     * @param  string  $id      Emailer uuid
     */
    public function start(Request $request, $id)
    {
        try
        {
            if($emailer = Emailer::find($id))
            {
                if(in_array($emailer->status, ['RUNNING','COMPLETED']))
                {
                    throw new APIBadRequestException(sprintf('%s is already running or completed', env('TITLE_EMAILERS')), 403);
                }

                if(empty($emailer->quadrant_uid))
                {
                    throw new APIBadRequestException(sprintf('%s is not configured in Quadrant system', env('TITLE_EMAILERS')));
                }

                DB::beginTransaction();

                $emailer->status = 'RUNNING';

                // start emailer on Quadrant's system if we can.
                $quadrant = new Quadrant();
                $res = $quadrant->startEmailer($emailer->quadrant_uid);

                $emailer->save();

                DB::commit();

                return response()->json(['data' => $emailer], 200);
            }
            else
            {
               throw new APINotFoundException('Resource not found');
            }
        }
        catch(Exception $e)
        {
            DB::rollback();
            throw new APIException($e->getMessage());
        }
    }

    /**
     * Retrieve all of the campaign stats for an emailer by uuid
     * @param  Request $request 
     * @param  string  $id      Emailer uuid
     */
    public function stats(Request $request, $id)
    {
        try
        {
            if($emailer = Emailer::find($id))
            {
                $this->setFilters($request);
                $this->setSearch($request);
                $this->setOrder($request);
                $this->setPagination($request);

                $stats = EmailerStat::with('address')
                    ->join('addresses', 'addresses.id', '=', 'emailer_stats.address_id')
                    ->where('emailer_id', '=', $emailer->id);

                foreach($this->filters as $filter)
                {
                    $stats = call_user_func_array([$stats, 'where'], $filter);
                }

                foreach($this->order as $condition){
                    $stats = call_user_func_array([$stats,'orderBy'], $condition);
                }

                foreach($this->search as $search)
                {
                    $stats = call_user_func_array([$stats, 'where'], ['email', 'LIKE', "%{$search}%"]);
                }

                 // build counts for pagination
                $num_entries = call_user_func_array([$stats, 'count'], []);
                $num_pages = (int) ceil($num_entries / $this->pagination['limit']);
                $current_page = (int) $this->pagination['page'] + 1;

                // apply pagination parameters
                $stats = call_user_func_array(
                    [$stats, 'skip'],
                    [$this->pagination['page'] * $this->pagination['limit']]);

                $stats = call_user_func_array(
                    [$stats, 'take'],
                    [$this->pagination['limit']]);

                return response()->json(['data' => $stats->get()], 200)
                    ->header('X-Pagination-Per-Page', $this->pagination['limit'])
                    ->header('X-Pagination-Current-Page', $current_page)
                    ->header('X-Pagination-Total-Pages', $num_pages)
                    ->header('X-Pagination-Total-Entries', $num_entries);
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
     * Generate static HTML for an emailer to preview
     * @param  object $emailer 
     * @return string          The HTML preview
     */
    public function preview(Request $request)
    {
        try
        {
            if($template = Template::find($request->input('template_id', '')))
            {
                $content = view("emails.{$template->source}", [
                    'subject' => $request->input('subject', ''),
                    'content' => $request->input('content', ''),
                    'signature' => $request->input('signature', ''),
                    'emailer_id' => null,
                    'address_id' => null
                ]);

                if($request->has('address') && !empty($request->input('address')))
                {
                    $addresses = explode(',', $request->input('address'));

                    Mail::send("emails.{$template->source}", [
                        'subject' => $request->input('subject', ''),
                        'content' => $request->input('content', ''),
                        'signature' => $request->input('signature', ''),
                        'emailer_id' => null,
                        'address_id' => null
                    ], function($mlr) use ($request, $addresses) {
                        $mlr->to($addresses)
                            ->from($request->input('return_address'), $request->input('return_name'))
                            ->subject($request->input('subject'));
                    });

                    return response()->json([], 200);
                }
                else
                {
                    return response()->json([
                        'data' => [
                            'template' => $template->name,
                            'content' => base64_encode($content)
                        ]
                    ], 200);
                }

            }
            else
            {
                throw new APINotFoundException('Resource not found');
            }
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }
}
