<?php namespace App\Console\Commands;

use DB;
use Log;

use App\Models\Emailer;
use App\Models\EmailerStat;
use App\Helpers\MySQLBuilder;
use App\Http\Controllers\QuadrantController as Quadrant;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class EmailersStatsCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'emailers:stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve emailer stats from Quadrant API';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        
        $quadrant = new Quadrant();
        $emailers = [];

        if($emailer_uuid = $this->argument('emailer_uuid'))
        {
            
            $this->info("Processing statistics for <{$emailer_uuid}>: Started");
            if($emailer = Emailer::find($emailer_uuid))
            {
                $emailers[] = $emailer;
            }
            else
            {
                $this->error("Processing statistics for <{$emailer_uuid}>: Failed");
                $this->error("Error: Emailer <{$emailer_uuid}> not found.");
                return false;
            }
        }
        else
        {
            // Find all campaigns that are active or complete
            $emailers = Emailer::whereIn('status', ['PENDING', 'RUNNING', 'COMPLETED'])
                ->where(['api_extended_status_received' => 0])
                ->get();
            $this->info('Processing statistics for all emailers: Started');
        }

        foreach($emailers as $emailer)
        {

            $this->info("Processing statistics for <{$emailer->id}>: Basic");

            // Retrieve the basic emailer status information
            $res = $quadrant->emailerStatus($emailer->quadrant_uid);
            $emailer->api_sending_status_numbers = json_encode($res);
            $emailer->save();

            // Retrieve extended data for COMPLETED emailers
            $force = $this->option('force') !== false;
            if($emailer->status == 'COMPLETED' && 
                (!$emailer->api_extended_status_received || !empty($emailer_uuid) && $force))
            {
                $distributed_at = strtotime($emailer->distribute_at);
                $current_time = time();

                // Only process if it has been 48 hours, or we are manually calling it.
                if(($current_time - $distributed_at)/60 < 2880 && empty($emailer_uuid))
                {
                    continue;
                }
                elseif(!empty($emailer_uuid) && $force)
                {
                    $this->info("Updating existing stats for <{$emailer->id}>: Started");
                }
                else
                {
                    continue;
                }
                
                $this->info("Processing statistics for <{$emailer->id}>: Bounced");

                $fields = [
                    'emailer_id',
                    'address_id',
                    'status',
                    'bounced',
                    'extended_status'
                ];

                // Grab bounced details first
                $results = [];
                $res = $quadrant->emailerStatus($emailer->quadrant_uid, 'bounced', [
                    'data',
                    'bounce_message'
                ]);
                foreach($res->bounced as $stat)
                {
                    $results[] = [
                        'emailer_id' => $emailer->id,
                        'address_id' => $stat->data->uuid,
                        'status' => 'BOUNCED',
                        'bounced' => true,
                        'extended_status' => $stat->bounced_message
                    ];
                }
                
                if(count($results))
                {
                    $this->insertResults($fields, $results);
                    // EmailerStat::insert($results);
                }

                $this->info("Processing statistics for <{$emailer->id}>: Accepted");

                // Grab accepted details
                $results = [];
                $res = $quadrant->emailerStatus($emailer->quadrant_uid, 'accepted', [
                    'data',
                    'send_date',
                    'last_deferred_date',
                    'accepted_date'
                ]);
                foreach($res->accepted as $stat)
                {
                    $results[] = [
                        'emailer_id' => $emailer->id,
                        'address_id' => $stat->data->uuid,
                        'status' => 'ACCEPTED',
                        'bounced' => false,
                        'extended_status' => json_encode([
                            'send_date' => $stat->send_date,
                            'last_deferred_date' => $stat->last_deferred_date,
                            'accepted_date' => $stat->accepted_date,
                        ])
                    ];
                }
                
                if(count($results))
                {
                    $this->insertResults($fields, $results);
                    // EmailerStat::insert($results);
                }

                $this->info("Processing statistics for <{$emailer->id}>: Deferred");

                // Grab deferred details
                $results = [];
                $res = $quadrant->emailerStatus($emailer->quadrant_uid, 'deferred', [
                    'data',
                    'send_date',
                    'last_deferred_date'
                ]);
                foreach($res->deferred as $stat)
                {
                    $results[] = [
                        'emailer_id' => $emailer->id,
                        'address_id' => $stat->data->uuid,
                        'status' => 'DEFERRED',
                        'bounced' => false,
                        'extended_status' => json_encode([
                            'send_date' => $stat->send_date,
                            'last_deferred_date' => $stat->last_deferred_date
                        ])
                    ];
                }

                if(count($results))
                {
                    $this->insertResults($fields, $results);
                    // EmailerStat::insert($results);
                }

                $emailer->api_extended_status_received = true;
                $emailer->save();

                $this->info("Processing statistics for <{$emailer->id}>: Completed");
            }
            elseif($emailer->api_extended_status_received)
            {
                $this->error("Processing statistics for <{$emailer->id}>: Failed");
                $this->error("Error: Statistics for <{$emailer->id}> already processed.");
                $this->info("Processing statistics for <{$emailer->id}>: Completed");
            }
        }
    }

    private function insertResults(array $fields, array $data){
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
            ['force', null, InputOption::VALUE_NONE, 'Force fetch new stats (remove existing).'],
        ];
    }

    protected function getArguments()
    {
        return [
            ['emailer_uuid', InputArgument::OPTIONAL, '(Optional) The uuid of the emailer to retrieve stats for.', null],
        ];
    }

}
