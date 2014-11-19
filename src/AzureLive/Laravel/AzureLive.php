<?php
/**
 * AzureLive
 */

namespace AzureLive\Laravel;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

// @todo: Do we need all these
use Guzzle\Http\Client;
use Guzzle\Http\Url;
use Guzzle\Stream;

/**
 * AzureLive
 */
class AzureLive {

    // public function test() {
    //     dd('testhgh');
    // }

    /**
     * @var
     */
    private $config;

    /**
     * @var
     */
    private $token;

    /**
     * @param ServicesBuilder $servicesBuilder
     * @param $config
     */
    public function __construct() {
        // $this->servicesBuilder = $sb;
        // $this->config          = $config;

        # Yes, the Azure SDK wants and environment variable. There is another way, but
        # I am just too lazy to figure it out at the moment.
        // putenv("StorageConnectionString={$this->config["connection_string"]["storage"]}");

        // dd($this->config);

        // $this->cs = CloudConfigurationManager::getConnectionString("StorageConnectionString");
        // $this->cs = $this->config['connection_string']['storage'];

        // @todo: get these working
        // $this->config is currently the included file for some reason??
        // $this->cs =  "DefaultEndpointsProtocol=https;AccountName=".getenv("AZURE_ACCOUNT_NAME").";AccountKey=".getenv("AZURE_PRIMARY_ACCESS_KEY");
        // $this->cssm =  "SubscriptionID=".getenv("AZURE_SUBSCRIPTION_ID").";CertificatePath=".getenv("AZURE_PATH_TO_CERTIFICATE");

        // Connect...
        $client = new \Guzzle\Http\Client();
        // Get results:

        // @todo: this url should be dynamic?????
        $request = $client->post('https://wamsprodglobal001acs.accesscontrol.windows.net/v2/OAuth2-13'); // https://wamsprodglobal001acs.accesscontrol.windows.net/v2/OAuth2-13
        $request->addHeader('x-ms-version', '2.7');
        $request->addHeader('Content-Type', 'application/x-www-form-urlencoded');
        $request->setBody('grant_type=client_credentials&client_id='.getenv('AZURE_ACCOUNT_NAME').'&client_secret='.urlencode( getenv('AZURE_PRIMARY_MEDIA_SERVICE_ACCESS_KEY') ).'&scope=urn%3aWindowsAzureMediaServices');

        // $request->setResponseBody('string');
        // dd($response);
        $response = $request->send();

        // dd( $response->getHeaders() );
        // dd( $response->getStatusCode() );

        ////// A

        // echo $response->getStatusCode() . '<hr />';
        // // "200"
        // echo $response->getHeader('content-type') . '<hr />';
        // // 'application/json; charset=utf8'
        // echo $response->getBody() . '<hr />';

        if($response->getStatusCode() == 200) {
            // {"type":"User"...'
            $response_array = $response->json();

            // dd($response_array);

            // $token = $response->getBody();

            // dd( $response->getBody() );

            $this->token = $response_array['access_token'];

        }

        // echo '<hr />';
        // Outputs the JSON decoded data
        // dd( $response->getBody() );

        // echo '<hr />';


        // dd( $token );


    }

    ///////// Media Services Live Streaming Functions

    
    /**
     * CreateChannels
     */
    public function CreateChannel() {

    }
    
    /**
     * StartChannels
     */
    public function StartChannels() {

    }
    
    /**
     * StopChannels
     */
    public function StopChannels() {

    }
    
    /**
     * ListChannels
     */
    public function ListChannels() {

    }
    
    /**
     * ResetChannels
     */
    public function ResetChannels() {

    }
    
    /**
     * UpdateChannels
     */
    public function UpdateChannels() {

    }
    
    /**
     * DeleteChannels
     */
    public function DeleteChannels() {

    }





    /**
     * @return \WindowsAzure\Common\WindowsAzure\BLAH\BLAH\IServiceManagement
     */
    public static function fullTest()
    {

        // Connect...
        $client = new \Guzzle\Http\Client();
        // Get results:

        $request = $client->post('https://wamsprodglobal001acs.accesscontrol.windows.net/v2/OAuth2-13'); // https://wamsprodglobal001acs.accesscontrol.windows.net/v2/OAuth2-13
        $request->addHeader('x-ms-version', '2.7');
        $request->addHeader('Content-Type', 'application/x-www-form-urlencoded');
        $request->setBody('grant_type=client_credentials&client_id='.getenv('AZURE_ACCOUNT_NAME').'&client_secret='.urlencode( getenv('AZURE_PRIMARY_MEDIA_SERVICE_ACCESS_KEY') ).'&scope=urn%3aWindowsAzureMediaServices');


        // $request->setResponseBody('string');
        // dd($response);
        $response = $request->send();

        // dd( $response->getHeaders() );
        // dd( $response->getStatusCode() );

        ////// A

        echo $response->getStatusCode() . '<hr />';
        // "200"
        echo $response->getHeader('content-type') . '<hr />';
        // 'application/json; charset=utf8'
        echo $response->getBody() . '<hr />';
        // {"type":"User"...'
        $response_array = $response->json();

        // dd($response_array);

        // $token = $response->getBody();

        // dd( $response->getBody() );

        $token = $response_array['access_token'];

        // echo '<hr />';
        // Outputs the JSON decoded data
        // dd( $response->getBody() );

        echo '<hr />';


        // dd( $token );






        $request = $client->post('https://wamsdubclus001rest-hs.cloudapp.net/api/Channels');

        // Moved here atm...
        $request->addHeader('x-ms-version', '2.7');
        $request->addHeader('Accept', 'application/json;odata=minimalmetadata');
        $request->addHeader('Content-Type', 'application/json;odata=minimalmetadata');
        $request->addHeader('Authorization', 'Bearer '.$token);
        $request->setBody('
{
    "Id": null,
    "Name": "testchannelRTMP",
    "Description": "Test Description",
    "Created": "0001-01-01T00:00:00",
    "LastModified": "0001-01-01T00:00:00",
    "State": null,
    "Input": {
        "KeyFrameInterval": null,
        "StreamingProtocol": "RTMP",
        "AccessControl": {
            "IP": {
                "Allow": [{
                    "Name": "testName1",
                    "Address": "1.1.1.1",
                    "SubnetPrefixLength": 24
                }]
            }
        },
        "Endpoints": []
    },
    "Preview": {
        "AccessControl": {
            "IP": {
                "Allow": [{
                    "Name": "testName1",
                    "Address": "1.1.1.1",
                    "SubnetPrefixLength": 24
                }]
            }
        },
        "Endpoints": []
    },
    "Output": {
        "Hls": {
            "FragmentsPerSegment": 1
        }
    },
    "CrossSiteAccessPolicies": {
        "ClientAccessPolicy": null,
        "CrossDomainPolicy": null
    }
}
            ');

        try {
            $response = $request->send();
        } catch (Exception $e) {
           dd($e); 
        }

        unset($request);

        echo $response->getStatusCode() . '<hr />';
        // "200"
        echo $response->getHeader('content-type') . '<hr />';
        // 'application/json; charset=utf8'
        echo $response->getBody() . '<hr />';


        dd('don');


        // Atempt to use AzureClient from guzzle-azure

        // $client = new \Guzzle\Azure\AzureClient( 'https://media.windows.net/API/', getenv("AZURE_SUBSCRIPTION_ID"), getenv("AZURE_PATH_TO_CERTIFICATE") );

        // $request = $client->get('https://media.windows.net/api/Channels');

        // // Moved here atm...
        // $request->addHeader('x-ms-version', '2.7');
        // $request->setBody('{"Id":null,"Name":"testchannel001","Description":"Test Description","Created":"0001-01-01T00:00:00","LastModified":"0001-01-01T00:00:00","State":null,"Input":{"KeyFrameInterval":null,"StreamingProtocol":"FragmentedMP4","AccessControl":{"IP":{"Allow":[{"Name":"testName1","Address":"1.1.1.1","SubnetPrefixLength":24}]}},"Endpoints":[]},"Preview":{"AccessControl":{"IP":{"Allow":[{"Name":"testName1","Address":"1.1.1.1","SubnetPrefixLength":24}]}},"Endpoints":[]},"Output":{"Hls":{"FragmentsPerSegment":1}},"CrossSiteAccessPolicies":{"ClientAccessPolicy":null,"CrossDomainPolicy":null}}');

        // $response = $request->send();

        // dd( $response->getStatusCode() );

        // dd( $response->getBody() );

        // dd($request);




        // Generic client...
        // $client = new \Guzzle\Http\Client();
        // // Get results:

        // $request = $client->get('http://stream-fingerprint.chew.tv:8080');

        // // $request->setResponseBody('string');
        // // dd($response);
        // $response = $request->send();

        // // dd( $response->getStatusCode() );
        // dd( $response->getBody() );

        // throw new Exception("Not implemented yet.");
        // return $this->servicesBuilder->listContainers($this->cs);



        // Create blob REST proxy.
        // $blobRestProxy = ServicesBuilder::getInstance()->createBlobService($this->cs);


        // try {
        //     // List blobs.
        //     $blob_list = $blobRestProxy->listBlobs($container);
        //     $blobs = $blob_list->getBlobs();

        //     foreach($blobs as $blob)
        //     {
        //         $return[] = $blob->getName().": ".$blob->getUrl();
        //     }
        // }
        // catch(ServiceException $e){
        //     // Handle exception based on error codes and messages.
        //     // Error codes and messages are here: 
        //     // http://msdn.microsoft.com/en-us/library/windowsazure/dd179439.aspx
        //     $code = $e->getCode();
        //     $error_message = $e->getMessage();
        //     echo $code.": ".$error_message."<br />";
        // }
    }
}