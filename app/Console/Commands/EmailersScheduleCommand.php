<?php namespace App\Console\Commands;

use DB;
use Log;
use App\Models\Emailer;
use App\Helpers\MySQLBuilder;
use App\Http\Controllers\EmailerController;
use App\Http\Controllers\QuadrantController as Quadrant;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class EmailersScheduleCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'emailers:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage emailer scheduling';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $quadrant = new Quadrant();

        // find all campaigns that are active or complete
        $emailers = Emailer::whereIn('status', [
            'APPROVED',
            'PAUSED',
            'PENDING',
            'RUNNING'
        ])->get();

        foreach($emailers as $emailer)
        {
            DB::beginTransaction();

            $distribute_at = strtotime($emailer->distribute_at);
            $current_time = time();

            try
            {
                // if emailer is approved and within 60 seconds of distribution time
                // we will schedule it now to account for delays in scheduling on
                // Quadrant's system.
                if($emailer->status == 'APPROVED' && $distribute_at - $current_time <= 60 )
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

                    $quadrant_list_uid = EmailerController::createQuadrantList($emailer);
                    $distribute_at = new \DateTime($emailer->distribute_at, new \DateTimeZone(env('DB_TIMEZONE')));

                    $res = $quadrant->postEmailer([
                        'email' => $email,
                        'to_header' => '{{first_name}}',
                        'from_header' => $from,
                        'subject_header' => $emailer->subject,
                        'start_date' => $distribute_at
                            ->setTimeZone(new \DateTimeZone(env('QUADRANT_TIMEZONE')))
                            ->format(\DateTime::ISO8601),
                        'list' => $quadrant_list_uid,
                    ]);

                    $emailer->quadrant_uid = $res->uid;
                    $emailer->quadrant_list_uid = $quadrant_list_uid;
                    $emailer->status = strtoupper($res->status);
                    $emailer->save();

                    // Add placeholders to the stats page.
                    $fields = [
                        'emailer_id',
                        'address_id',
                        'status'
                    ];

                    $placeholders = [];
                    foreach($emailer->lists as $list)
                    {
                        foreach($list->addresses as $address)
                        {
                            $placeholders[] = [
                                'emailer_id' => $emailer->id,
                                'address_id' => $address->id,
                                'status' => 'UNKNOWN'
                            ];
                        }
                    }
                    $this->insertPlaceHolderStats($fields, $placeholders);

                }
                elseif($emailer->status != 'APPROVED')
                {
                    // We are simply tracking the status of the emailer in Quadrant's system
                    $res = $quadrant->getEmailer($emailer->quadrant_uid);
                    if($emailer->status != 'PAUSED' && !($emailer->status == 'RUNNING' && strtoupper($res->status) == 'PAUSED'))
                    {
                        if($res->status == 'COMPLETED')
                        {
                            // We should grab the extended status information.
                        }   
                        $emailer->status = strtoupper($res->status);
                        $emailer->save();
                    }
                }

                DB::commit();
            }
            catch(Exception $e)
            {
                DB::rollback();
                Log::warn('Failed to update schedule for ' + $emailer->name + 
                    ' - ' + $emailer->id);
            }
        }
    }

    private function insertPlaceholderStats($fields, $data){
        $query_builder = new MySQLBuilder('emailer_stats');
        $query_builder->setColumns($fields);

        $query_builder->insertOrUpdate($data);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            // ['host', null, InputOption::VALUE_OPTIONAL, 'The host address to serve the application on.', 'localhost'],
            // ['port', null, InputOption::VALUE_OPTIONAL, 'The port to serve the application on.', 8000],
        ];
    }

}
