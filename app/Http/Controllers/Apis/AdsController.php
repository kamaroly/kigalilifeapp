<?php
namespace App\Http\Controllers\Apis;
use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Transformers\AdTransformer;
use EllipseSynergie\ApiResponse\Contracts\Response;
use Exception;

class AdsController extends Controller
{

    /**
     * @var Response
     */
    public $response;


    function __construct(Response $response) {
        $this->response = $response;
    }

    public function all()
    {
        $ads = Ad::paginate(20);

        // Pass this array (collection) into a resource, which will also have a "Transformer"
        // This "Transformer" can be a callback or a new instance of a Transformer object
        // We type hint for array, because each item in the $ads var is an array
        
        // Turn all of that into a JSON string
        return $this->response->withPaginator($ads,new AdTransformer,'ads');
    }

    public function show($id)
    {
        try {

            $ad = Ad::where('message_number',$id)->first();
            return  $this->response->withItem($ad,new AdTransformer);

        } catch (\Exception $e) {

            return $e->getMessage();
        }
    }


    public function after($number)
    {
        $ads = Ad::whereRaw('message_number > '.$number)->paginate(20);

        // Pass this array (collection) into a resource, which will also have a "Transformer"
        // This "Transformer" can be a callback or a new instance of a Transformer object
        // We type hint for array, because each item in the $ads var is an array
       return $this->response->withPaginator($ads,new AdTransformer,'ads');
    }
}