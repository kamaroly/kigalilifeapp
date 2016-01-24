<?php
namespace App\Http\Controllers\Apis;
use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Transformers\AdTransformer;
use EllipseSynergie\ApiResponse\Contracts\Response;
use Exception;
use Illuminate\Support\Facades\Input;

class AdsController extends Controller
{
    public function register()
    {
        $url = "http://121.241.242.114:8080/bulksms/bulksms?username=skyt-huguka&password=Kigali7&type=0&dlr=Z&destination=250722123127&source=KIGALILIFE&message=".rand;
     
        // init the resource
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData
                //,CURLOPT_FOLLOWLOCATION => true
        ));
     
     
        //Ignore SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
     
     
        //get response
        $output = curl_exec($ch);
        return [ 'error' => false,'message'=>'SMS request is initiated! You will be receiving it shortly.'];
    }


    public function verifyotp()
    {
        return [ 'error'    => false,
                     'message'  =>'User created successfully!',
                     'profile'  => [
                                    'name' =>'Test name',
                                    'email'=>'Test email',
                                    'mobile'=>'0000000000',
                                    'apikey'=>'088d196bacbe6bf08657720c9d562390',
                                    'status'=> 0,
                                    'created_at' => date('Y-m-d H:i:s')
                                    ], 
                    ];
    }
}