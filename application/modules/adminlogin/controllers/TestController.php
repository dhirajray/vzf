<?php

class Adminlogin_TestController extends Zend_Controller_Action
{

	private $options;
	
	/**
	 * Init
	 * 
	 * @see Zend_Controller_Action::init()
	 */
    public function init()
    {
       

    }

  

    public function indexAction() 
    {
        session_start();

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $OAUTH2_CLIENT_ID = '23216524140.apps.googleusercontent.com';
        $OAUTH2_CLIENT_SECRET = 'Wq-loBG_nyhRZcdC3u86uBLq';

        $client = new Google_Client();
        $client->setClientId($OAUTH2_CLIENT_ID);
        $client->setClientSecret($OAUTH2_CLIENT_SECRET);
        $client->setScopes('https://www.googleapis.com/auth/youtube');
        $redirect = filter_var('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'],
        FILTER_SANITIZE_URL);
        $client->setRedirectUri($redirect);

        // Define an object that will be used to make all API requests.
        $youtube = new Google_Service_YouTube($client);

        if (isset($_GET['code'])) {
            if (strval($_SESSION['state']) !== strval($_GET['state'])) {
                die('The session state did not match.');
            }

            $client->authenticate($_GET['code']);
            $_SESSION['token'] = $client->getAccessToken();
            header('Location: ' . $redirect);
        }

        if (isset($_SESSION['token'])) {
            $client->setAccessToken($_SESSION['token']);
        }

        // Check to ensure that the access token was successfully acquired.
        if ($client->getAccessToken()) 
        {
            try {
            // Create an object for the liveBroadcast resource's snippet. Specify values
            // for the snippet's title, scheduled start time, and scheduled end time.
            $broadcastSnippet = new Google_Service_YouTube_LiveBroadcastSnippet();
            $broadcastSnippet->setTitle('New Broadcast');
            $broadcastSnippet->setScheduledStartTime('2034-01-30T00:00:00.000Z');
            $broadcastSnippet->setScheduledEndTime('2034-01-31T00:00:00.000Z');

            // Create an object for the liveBroadcast resource's status, and set the
            // broadcast's status to "private".
            $status = new Google_Service_YouTube_LiveBroadcastStatus();
            $status->setPrivacyStatus('private');

            // Create the API request that inserts the liveBroadcast resource.
            $broadcastInsert = new Google_Service_YouTube_LiveBroadcast();
            $broadcastInsert->setSnippet($broadcastSnippet);
            $broadcastInsert->setStatus($status);
            $broadcastInsert->setKind('youtube#liveBroadcast');

            // Execute the request and return an object that contains information
            // about the new broadcast.
            $broadcastsResponse = $youtube->liveBroadcasts->insert('snippet,status',
            $broadcastInsert, array());

            // Create an object for the liveStream resource's snippet. Specify a value
            // for the snippet's title.
            $streamSnippet = new Google_Service_YouTube_LiveStreamSnippet();
            $streamSnippet->setTitle('New Stream');

            // Create an object for content distribution network details for the live
            // stream and specify the stream's format and ingestion type.
            $cdn = new Google_Service_YouTube_CdnSettings();
            $cdn->setFormat("1080p");
            $cdn->setIngestionType('rtmp');

            // Create the API request that inserts the liveStream resource.
            $streamInsert = new Google_Service_YouTube_LiveStream();
            $streamInsert->setSnippet($streamSnippet);
            $streamInsert->setCdn($cdn);
            $streamInsert->setKind('youtube#liveStream');

            // Execute the request and return an object that contains information
            // about the new stream.
            $streamsResponse = $youtube->liveStreams->insert('snippet,cdn',
            $streamInsert, array());

            // Bind the broadcast to the live stream.
            $bindBroadcastResponse = $youtube->liveBroadcasts->bind(
            $broadcastsResponse['id'],'id,contentDetails',
            array(
            'streamId' => $streamsResponse['id'],
            ));

            $htmlBody .= "<h3>Added Broadcast</h3><ul>";
            $htmlBody .= sprintf('<li>%s published at %s (%s)</li>',
            $broadcastsResponse['snippet']['title'],
            $broadcastsResponse['snippet']['publishedAt'],
            $broadcastsResponse['id']);
            $htmlBody .= '</ul>';

            $htmlBody .= "<h3>Added Stream</h3><ul>";
            $htmlBody .= sprintf('<li>%s (%s)</li>',
            $streamsResponse['snippet']['title'],
            $streamsResponse['id']);
            $htmlBody .= '</ul>';

            $htmlBody .= "<h3>Bound Broadcast</h3><ul>";
            $htmlBody .= sprintf('<li>Broadcast (%s) was bound to stream (%s).</li>',
            $bindBroadcastResponse['id'],
            $bindBroadcastResponse['contentDetails']['boundStreamId']);
            $htmlBody .= '</ul>';

            } catch (Google_ServiceException $e) {
            $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
            htmlspecialchars($e->getMessage()));
            } catch (Google_Exception $e) {
            $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
            htmlspecialchars($e->getMessage()));
            }

            $_SESSION['token'] = $client->getAccessToken();
        } else {
            // If the user hasn't authorized the app, initiate the OAuth flow
            $state = mt_rand();
            $client->setState($state);
            $_SESSION['state'] = $state;

            $authUrl = $client->createAuthUrl();
            $htmlBody = 'bad<a href="'.$authUrl.'">authorize access</a>';
        }
        echo $htmlBody;
    }
}

