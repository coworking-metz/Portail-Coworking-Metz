<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Taskrouter\V1\Workspace;

use Twilio\ListResource;
use Twilio\Page;
use Twilio\Stream;
use Twilio\Values;
use Twilio\Version;

class TaskChannelList extends ListResource {
    /**
     * Construct the TaskChannelList
     * 
     * @param Version $version Version that contains the resource
     * @param string $workspaceSid The unique ID of the Workspace that this
     *                             TaskChannel belongs to.
     * @return TaskChannelList
     */
    public function __construct(Version $version, $workspaceSid) {
        parent::__construct($version);

        // Path Solution
        $this->solution = array('workspaceSid' => $workspaceSid, );

        $this->uri = '/Workspaces/' . rawurlencode($workspaceSid) . '/TaskChannels';
    }

    /**
     * Reads TaskChannelInstance records from the API as a list.
     * Unlike stream(), this operation is eager and will load `limit` records into
     * memory before returning.
     *
     * @param int $limit Upper limit for the number of records to return. read()
     *                   guarantees to never return more than limit.  Default is no
     *                   limit
     * @param mixed $pageSize Number of records to fetch per request, when not set
     *                        will use the default value of 50 records.  If no
     *                        page_size is defined but a limit is defined, read()
     *                        will attempt to read the limit with the most
     *                        efficient page size, i.e. min(limit, 1000)
     * @return TaskChannelInstance[] Array of results
     */
    public function read($limit = null, $pageSize = null) {
        return iterator_to_array($this->stream($limit, $pageSize), false);
    }

    /**
     * Streams TaskChannelInstance records from the API as a generator stream.
     * This operation lazily loads records as efficiently as possible until the
     * limit
     * is reached.
     * The results are returned as a generator, so this operation is memory
     * efficient.
     *
     * @param int $limit Upper limit for the number of records to return. stream()
     *                   guarantees to never return more than limit.  Default is no
     *                   limit
     * @param mixed $pageSize Number of records to fetch per request, when not set
     *                        will use the default value of 50 records.  If no
     *                        page_size is defined but a limit is defined, stream()
     *                        will attempt to read the limit with the most
     *                        efficient page size, i.e. min(limit, 1000)
     * @return Stream stream of results
     */
    public function stream($limit = null, $pageSize = null) {
        $limits = $this->version->readLimits($limit, $pageSize);

        $page = $this->page($limits['pageSize']);

        return $this->version->stream($page, $limits['limit'], $limits['pageLimit']);
    }

    /**
     * Retrieve a single page of TaskChannelInstance records from the API.
     * Request is executed immediately
     * 
     * @param mixed $pageSize Number of records to return, defaults to 50
     * @param string $pageToken PageToken provided by the API
     * @param mixed $pageNumber Page Number, this value is simply for client state
     * @return Page Page of TaskChannelInstance
     */
    public function page($pageSize = Values::NONE, $pageToken = Values::NONE, $pageNumber = Values::NONE) {
        $params = Values::of(array(
            'PageToken' => $pageToken,
            'Page' => $pageNumber,
            'PageSize' => $pageSize,
        ));

        $response = $this->version->page(
            'GET',
            $this->uri,
            $params
        );

        return new TaskChannelPage($this->version, $response, $this->solution);
    }

    /**
     * Retrieve a specific page of TaskChannelInstance records from the API.
     * Request is executed immediately
     * 
     * @param string $targetUrl API-generated URL for the requested results page
     * @return Page Page of TaskChannelInstance
     */
    public function getPage($targetUrl) {
        $response = $this->version->getDomain()->getClient()->request(
            'GET',
            $targetUrl
        );

        return new TaskChannelPage($this->version, $response, $this->solution);
    }

    /**
     * Create a new TaskChannelInstance
     * 
     * @param string $friendlyName String representing user-friendly name for the
     *                             TaskChannel
     * @param string $uniqueName String representing unique name for the TaskChannel
     * @return TaskChannelInstance Newly created TaskChannelInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function create($friendlyName, $uniqueName) {
        $data = Values::of(array('FriendlyName' => $friendlyName, 'UniqueName' => $uniqueName, ));

        $payload = $this->version->create(
            'POST',
            $this->uri,
            array(),
            $data
        );

        return new TaskChannelInstance($this->version, $payload, $this->solution['workspaceSid']);
    }

    /**
     * Constructs a TaskChannelContext
     * 
     * @param string $sid The sid
     * @return TaskChannelContext
     */
    public function getContext($sid) {
        return new TaskChannelContext($this->version, $this->solution['workspaceSid'], $sid);
    }

    /**
     * Provide a friendly representation
     * 
     * @return string Machine friendly representation
     */
    public function __toString() {
        return '[Twilio.Taskrouter.V1.TaskChannelList]';
    }
}