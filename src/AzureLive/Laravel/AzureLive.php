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

    /**
     * @var
     */
    private $config;

    /**
     * @var
     */
    private $client;

    /**
     * @var
     */
    private $base_url;

    /**
     * @var
     */
    private $token;

    /**
     * @var
     */
    private $request;

    /**
     * @param ServicesBuilder $servicesBuilder
     * @param $config
     */
    public function __construct() {
        // $this->servicesBuilder = $sb;
        // $this->config          = $config;

        // $this->base_url = 'https://wamsdubclus001rest-hs.cloudapp.net'; // @todo: might need to get this dynamically??????
        $this->base_url = 'https://wamsdubclus001rest-hs.cloudapp.net';
        // $this->base_url = 'https://media.windows.net'; 

        // @todo: might need to get this dynamically??????
        // Can be found in array(11) { ["odata.metadata"] "https://wamsdubclus001rest-hs.cloudapp.net/api/$metadata#Channels/@Element"
        // On most requests

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
        $this->client = new \Guzzle\Http\Client();
        // Get results:

        // @todo: this url should be dynamic?????
        $request = $this->client->post('https://wamsprodglobal001acs.accesscontrol.windows.net/v2/OAuth2-13'); // https://wamsprodglobal001acs.accesscontrol.windows.net/v2/OAuth2-13
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

    private function setRequestHeaders() {
    	$this->request->addHeader('x-ms-version', '2.7');
        $this->request->addHeader('Accept', 'application/json;odata=minimalmetadata');
        $this->request->addHeader('Content-Type', 'application/json;odata=minimalmetadata');
        $this->request->addHeader('Authorization', 'Bearer '.$this->token);
    }

    /**
     * CreateChannel
     *
     * @param $name Name
     * @param $description Description
     */
    public function createChannel($name, $description) {

        $this->request = $this->client->post($this->base_url.'/api/Channels');

        $this->setRequestHeaders();

        $this->request->setBody('
			{
			    "Id": null,
			    "Name": "'.$name.'",
			    "Description": "'.$description.'",
			    "Created": "0001-01-01T00:00:00",
			    "LastModified": "0001-01-01T00:00:00",
			    "State": null,
			    "Input": {
			        "KeyFrameInterval": null,
			        "StreamingProtocol": "RTMP",
			        "AccessControl": {
			            "IP": {
			                "Allow": [{
			                    "Name": "defaultOpen",
			                    "Address": "0.0.0.0",
			                    "SubnetPrefixLength": 0
			                }]
			            }
			        },
			        "Endpoints": []
			    },
			    "Preview": {
			        "AccessControl": {
			            "IP": {
			                "Allow": [{
			                    "Name": "defaultOpen",
			                    "Address": "0.0.0.0",
			                    "SubnetPrefixLength": 0
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
            // $response = $this->request->send();

        	$response = $this->request->send();

        	if($response->getStatusCode() == 202) {
        	}
            return $response->json();

            // $response_array = $response->json();
        } catch (Exception $e) {
        	// Handle??
           return $e; 
        }

        // if($response->getStatusCode() == 202) { // Success...
        	// return $response->json();
        // } else {
        // 	return 
        // }
    }
    
    /**
     * StartChannel
     *
     * @param $id Azure Channel Id
     */
    public function startChannel($id) {

        $this->request = $this->client->post($this->base_url.'/api/Channels(\''.$id.'\')/Start');

        $this->setRequestHeaders();

        try {
            // $response = $this->request->send();

        	$response = $this->request->send();

        	if($response->getStatusCode() == 202) {
        		return true;
        	} else {
        		return false;

        	}
            // return $response->json();

            // $response_array = $response->json();
        } catch (Exception $e) {
        	// Handle??
           return $e; 
        }
    }
    
    /**
     * StopChannel
     *
     * @param $id Azure Channel Id
     */
    public function stopChannel($id) {
        $this->request = $this->client->post($this->base_url.'/api/Channels(\''.$id.'\')/Stop');

        $this->setRequestHeaders();

        try {
            // $response = $this->request->send();

        	$response = $this->request->send();

        	if($response->getStatusCode() == 202) {
        		return true;
        	} else {
        		return false;

        	}
            // return $response->json();

            // $response_array = $response->json();
        } catch (Exception $e) {
        	// Handle??
           return $e; 
        }
    }

    /**
     * GetChannel
     *
     * @param $id Azure Channel Id
     */
    public function getChannel($id) {

        $this->request = $this->client->get($this->base_url.'/api/Channels(\''.$id.'\')');

        $this->setRequestHeaders();

        try {
            // $response = $this->request->send();

        	$response = $this->request->send();

        	if($response->getStatusCode() == 200) {
        		return $response->json();
        	} else {
        		return false;

        	}
            // return $response->json();

            // $response_array = $response->json();
        } catch (Exception $e) {
        	// Handle??
           return $e; 
        }
    }
    
    /**
     * ListChannels
     */
    public function listChannels() {
        $this->request = $this->client->get($this->base_url.'/api/Channels');

        $this->setRequestHeaders();

        try {
            // $response = $this->request->send();

        	$response = $this->request->send();

        	if($response->getStatusCode() == 200) {
        		return $response->json();
        	} else {
        		return false;

        	}
            // return $response->json();

            // $response_array = $response->json();
        } catch (Exception $e) {
        	// Handle??
           return $e; 
        }
    }
    
    /**
     * ResetChannel
     *
     * @param $id Azure Channel Id
     */
    public function resetChannel($id) {
        $this->request = $this->client->post($this->base_url.'/api/Channels(\''.$id.'\')/Reset');

        $this->setRequestHeaders();

        try {
            // $response = $this->request->send();

        	$response = $this->request->send();

        	if($response->getStatusCode() == 202) {
        		return true;
        	} else {
        		return false;

        	}
            // return $response->json();

            // $response_array = $response->json();
        } catch (Exception $e) {
        	// Handle??
           return $e; 
        }
    }
    
    /**
     * UpdateChannel
     *
     * @param $name Name
     * @param $description Description
     *
     * @todo 400 errors atm
     */
    public function updateChannel($id, $name = false, $description = false) {

    	// 400 error?

    //     $this->request = $this->client->patch($this->base_url.'/api/Channels(\''.$id.'\')');

    //     $this->setRequestHeaders();

    //     if($name) {
	   //      $this->request->setBody('
				// "Name": "'.$name.'"
	   //      ');
    //     }

    // //     if($description) {
	   // //      $this->request->setBody('
				// // "Description": "'.$description.'"
	   // //      ');
	   // //  }

    //     try {
    //         // $response = $this->request->send();

    //     	$response = $this->request->send();

    //     	if($response->getStatusCode() == 202) {
    //     	}
    //         return $response->json();

    //         // $response_array = $response->json();
    //     } catch (Exception $e) {
    //     	// Handle??
    //        return $e; 
    //     }

    }
    
    /**
     * DeleteChannel
     *
     * @param $id Azure Channel Id
     */
    public function deleteChannel($id) {
        $this->request = $this->client->delete($this->base_url.'/api/Channels(\''.$id.'\')');

        $this->setRequestHeaders();

        try {
            // $response = $this->request->send();

        	$response = $this->request->send();

        	if($response->getStatusCode() == 202) {
        		return true;
        	} else {
        		return false;

        	}
            // return $response->json();

            // $response_array = $response->json();
        } catch (Exception $e) {
        	// Handle??
           return $e; 
        }
    }





    /**
     * @return \WindowsAzure\Common\WindowsAzure\BLAH\BLAH\IServiceManagement
     */
    public static function fullTest()
    {
// dd('fulltest');
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
    "Name": "testchannel'.time().'",
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
                    "Name": "defaultOpen",
                    "Address": "0.0.0.0",
                    "SubnetPrefixLength": 0
                }]
            }
        },
        "Endpoints": []
    },
    "Preview": {
        "AccessControl": {
            "IP": {
                "Allow": [{
                    "Name": "defaultOpen",
                    "Address": "0.0.0.0",
                    "SubnetPrefixLength": 0
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