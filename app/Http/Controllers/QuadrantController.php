<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Exceptions\QuadrantException;

class QuadrantController extends Controller
{
    private $base_uri;
    private $username;
    private $password;
    private $version;
    private $client;

    public function __construct($version = null)
    {
        $this->base_uri = env('QUADRANT_API');
        $this->username = env('QUADRANT_USER');
        $this->password = env('QUADRANT_PASS');

        // Use the default API version if we aren't specifying one
        $this->version = ($version) ?: env('QUADRANT_API_VERSION');
        $this->client = new Client([
            'base_uri' => $this->base_uri,
            'verify' => false
        ]);
    }

    /**
     * Build an endpoint to call
     * @param  string $endpoint Base endpoint to pull from .env
     * @param  array  $segments Addition url segments
     * @return string           The full endpoint
     */
    private function endpoint($endpoint = "", $segments = [])
    {
        $base = implode('/', array_filter([
            $this->version,
            env($endpoint)
        ]));

        // Append any other segments we may have (uuid, etc)
        $segments = implode('/', array_filter($segments));

        // Make sure we have a trailing /
        return str_replace('//', '/', "{$base}/{$segments}/");
    }

    /**
     * GET all lists
     * @return array    List of subscriber lists
     */
    public function allLists()
    {
        $endpoint = $this->endpoint("QUADRANT_LISTS_ENDPOINT");
        $res = $this->client->get($endpoint,[
            'auth' => [$this->username, $this->password]
        ]);

        if($res->getStatusCode() != 200)
        {
            throw new QuadrantException('Failed to get lists using Quadrant API',
                $res->getStatusCode());
        }
        else
        {
            $data = json_decode($res->getBody());
            return $data;
        }
    }

    /**
     * GET a list
     * @param  string $uid  Subscriber list UID
     * @return mixed       The subscriber list
     */
    public function getList($uid)
    {
        $endpoint = $this->endpoint("QUADRANT_LISTS_ENDPOINT", [$uid]);
        $res = $this->client->get($endpoint,[
            'auth' => [$this->username, $this->password]
        ]);

        if($res->getStatusCode() != 200)
        {
            throw new QuadrantException('Failed to get list using Quadrant API',
                $res->getStatusCode());
        }
        else
        {
            $data = json_decode($res->getBody());
            return $data;
        }
    }

    /**
     * POST a new list
     * @param  array $data  Subscriber list
     * [
     *    {
     *        'email' : '<email address>',
     *        'data' : whatever you set for the data for the email address
     *    },
     *        ...
     * ]
     * @return mixed       List creation status
     */
    public function postList($addresses)
    {
        $endpoint = $this->endpoint("QUADRANT_LISTS_ENDPOINT");
        $res = $this->client->post($endpoint,[
            'auth' => [$this->username, $this->password],
            'json' => ['subscribers' => $addresses]
        ]);

        if($res->getStatusCode() != 200)
        {
            throw new QuadrantException('Failed to create list using Quadrant API',
                $res->getStatusCode());
        }
        else
        {
            $data = json_decode($res->getBody());
            return $data;
        }
    }

    /**
     * PUT updates to an existing list with new subscriber information
     * @param  string $uid     UID of the list to update
     * @param  array $added   Array of addresses to add
     * [
     *    {
     *        'email' : '<email address>',
     *        'data' : whatever you set for the data for the email address
     *    },
     *        ...
     * ]
     * @param  array $removed Array of addresses to remove
     * [
     *    {
     *        'email' : '<email address>'
     *    },
     *        ...
     * ]
     * @return array          Counts of added an removed addresses
     */
    public function putList($uid, $added = [], $removed = [])
    {
        $endpoint = $this->endpoint("QUADRANT_LISTS_ENDPOINT", [$uid]);
        $res = $this->client->put($endpoint,[
            'auth' => [$this->username, $this->password],
            'json' => [
                'add_subscribers' => $added,
                'remove_subscribers' => $removed,
            ]
        ]);

        if($res->getStatusCode() != 200)
        {
            throw new QuadrantException('Failed to update list using Quadrant API',
                $res->getStatusCode());
        }
        else
        {
            return json_decode($res->getBody());
        }
    }

    /**
     * DELETE an existing list
     * @param  string $uid UID of the list to remove
     * @return array       Success state of the delete call
     */
    public function deleteList($uid)
    {
        $endpoint = $this->endpoint("QUADRANT_LISTS_ENDPOINT", [$uid]);
        $res = $this->client->delete($endpoint,[
            'auth' => [$this->username, $this->password]
        ]);

        if($res->getStatusCode() != 200)
        {
            throw new QuadrantException('Failed to delete list using Quadrant API',
                $res->getStatusCode());
        }
        else
        {
            $data = json_decode($res->getBody());
            if(!$data->deleted)
            {
                throw new QuadrantException('Failed to delete list using Quadrant API',
                    $res->getStatusCode());
            }
            return $data;
        }
    }

    /**
     * GET all email campaigns
     * @return array       List of all email campaigns
     */
    public function allEmailers()
    {
        $endpoint = $this->endpoint("QUADRANT_EMAILERS_ENDPOINT");
        $res = $this->client->get($endpoint,[
            'auth' => [$this->username, $this->password]
        ]);

        if($res->getStatusCode() != 200)
        {
            throw new QuadrantException('Failed to get emailers using Quadrant API',
                $res->getStatusCode());
        }
        else
        {
            $data = json_decode($res->getBody());
            return $data;
        }
    }

    /**
     * GET an email campaign
     * @param  string $uid  Email campaign UID
     * @return mixed       The email campaign
     */
    public function getEmailer($uid)
    {
        $endpoint = $this->endpoint("QUADRANT_EMAILERS_ENDPOINT", [$uid]);
        $res = $this->client->get($endpoint,[
            'auth' => [$this->username, $this->password]
        ]);

        if($res->getStatusCode() != 200)
        {
            throw new QuadrantException('Failed to get emailer using Quadrant API',
                $res->getStatusCode());
        }
        else
        {
            $data = json_decode($res->getBody());
            return $data;
        }
    }

    /**
     * POST a new email campaign
     * @param  array $data  The email campaign data
     * {
     *    'email' : '<campaign email>',
     *    'to_header' : '<campaign to header>',
     *    'from_header' : '<campaign from header>',
     *    'subject_header' : '<campaign subject jeader>',
     *    'start_date' : '<campaign start_date (can be null)>',
     *    'test' : '<true if a test campaign (don't send emails)> default: false',
     *    'list' : '<list uid>'
     *   }
     * @return array       The email campaign status
     */
    public function postEmailer($emailer)
    {
        $endpoint = $this->endpoint("QUADRANT_EMAILERS_ENDPOINT");
        $res = $this->client->post($endpoint,[
            'auth' => [$this->username, $this->password],
            'json' => $emailer
        ]);

        if($res->getStatusCode() != 200)
        {
            throw new QuadrantException('Failed to create emailer using Quadrant API',
                $res->getStatusCode());
        }
        else
        {
            $data = json_decode($res->getBody());
            return $data;
        }
    }

    /**
     * PUT updates to an existing email campaign with new data
     * @param  string $uid     UID of the email campaign to update
     * @param  array $data     Update information for the email campaign
     * {
     *    'email' : '<campaign email>',
     *    'to_header' : '<campaign to header>',
     *    'from_header' : '<campaign from header>',
     *    'subject_header' : '<campaign subject jeader>',
     *    'start_date' : '<campaign start_date (can be null)>',
     *    'test' : '<true if a test campaign (don't send emails)> default: false',
     *    'list' : '<list uid>'
     *   }
     * @return array           The updated email campaign status
     */
    public function putEmailer($uid, $data)
    {
        $endpoint = $this->endpoint("QUADRANT_EMAILERS_ENDPOINT", [$uid]);
        $res = $this->client->put($endpoint,[
            'auth' => [$this->username, $this->password],
            'json' => $data
        ]);

        if($res->getStatusCode() != 200)
        {
            throw new QuadrantException('Failed to update emailer using Quadrant API',
                $res->getStatusCode());
        }
        else
        {
            return json_decode($res->getBody());
        }
    }

    /**
     * DELETE an existing email campaign
     * @param  string $uid  UID of the email campaign to remove
     * @return array        Success state of the delete call
     */
    public function deleteEmailer($uid)
    {
        $endpoint = $this->endpoint("QUADRANT_EMAILERS_ENDPOINT", [$uid]);
        $res = $this->client->delete($endpoint,[
            'auth' => [$this->username, $this->password]
        ]);

        if($res->getStatusCode() != 200)
        {
            throw new QuadrantException('Failed to delete emailer using Quadrant API',
                $res->getStatusCode());
        }
        else
        {
            $data = json_decode($res->getBody());
            if(!$data->deleted)
            {
                throw new QuadrantException('Failed to delete emailer using Quadrant API',
                    $res->getStatusCode());
            }
            return $data;
        }
    }

    /**
     * Pause an existing email campaign
     * @param  string $uid  UID of the email campaign to pause
     * @return array        The email campaign status
     */
    public function pauseEmailer($uid)
    {
        $endpoint = $this->endpoint("QUADRANT_EMAILERS_ENDPOINT", [$uid, 'control']);
        $res = $this->client->post($endpoint,[
            'auth' => [$this->username, $this->password],
            'json' => ['campaign_control' => 'pause']
        ]);

        if($res->getStatusCode() != 200)
        {
            throw new QuadrantException('Failed to pause emailer using Quadrant API',
                $res->getStatusCode());
        }
        else
        {
            $data = json_decode($res->getBody());
            return $data;
        }
    }

    /**
     * Start an existing email campaign
     * @param  string $uid  UID of the email campaign to start
     * @return array        The email campaign status
     */
    public function startEmailer($uid)
    {
        $endpoint = $this->endpoint("QUADRANT_EMAILERS_ENDPOINT", [$uid, 'control']);
        $res = $this->client->post($endpoint,[
            'auth' => [$this->username, $this->password],
            'json' => ['campaign_control' => 'start']
        ]);

        if($res->getStatusCode() != 200)
        {
            throw new QuadrantException('Failed to start emailer using Quadrant API',
                $res->getStatusCode());
        }
        else
        {
            $data = json_decode($res->getBody());
            return $data;
        }
    }

    /**
     * GET an existing email campaign status
     * @param  string $uid       UID of the email campaign
     * @param  string $extended  Extended data option to query
     * @return array             The email campaign status
     */
    public function emailerStatus($uid, $extended = null, $extended_fields = [])
    {

        if($extended)
        {
            $endpoint = $this->endpoint("QUADRANT_EMAILERS_ENDPOINT", [
                $uid,
                'status',
                $extended
            ]);

            $res = $this->client->get($endpoint,[
                'auth' => [$this->username, $this->password],
                'query' => ['fields' => implode(',', $extended_fields)]
            ]);
        }
        else
        {
            $endpoint = $this->endpoint("QUADRANT_EMAILERS_ENDPOINT", [
                $uid,
                'status'
            ]);

            $res = $this->client->get($endpoint,[
                'auth' => [$this->username, $this->password]
            ]);
        }


        if($res->getStatusCode() != 200)
        {
            throw new QuadrantException('Failed to get emailer status using Quadrant API',
                $res->getStatusCode());
        }
        else
        {
            $data = json_decode($res->getBody());
            return $data;
        }
    }

}
