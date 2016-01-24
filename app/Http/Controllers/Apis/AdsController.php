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

    /**
     * @var Response
     */
    public $response;


    function __construct(Response $response) {
      
        $this->response = $response;
    }

    public function all()
    {
        if (Input::has('key') == false) {
          return $this->response->errorUnauthorized();
        }
        $ads = Ad::paginate(20);

        // Pass this array (collection) into a resource, which will also have a "Transformer"
        // This "Transformer" can be a callback or a new instance of a Transformer object
        // We type hint for array, because each item in the $ads var is an array
        
        // Turn all of that into a JSON string
        return $this->response->withPaginator($ads,new AdTransformer);
    }

    public function show($id)
    {
       if (Input::has('key') == false) {
          return $this->response->errorUnauthorized();
        }
        try {

            $ad = Ad::where('message_number',$id)->first();

            return  $this->response->withItem($ad,new AdTransformer);

        } catch (\Exception $e) {

            return $e->getMessage();
        }
    }


    public function after($number)
    {
     if (Input::has('key') == false) {
          return $this->response->errorUnauthorized();
        }
        $ads = Ad::whereRaw('id > '.$number)->orderBy('message_number','DESC')->paginate(50);

        // Pass this array (collection) into a resource, which will also have a "Transformer"
        // This "Transformer" can be a callback or a new instance of a Transformer object
        // We type hint for array, because each item in the $ads var is an array
       return $this->response->withPaginator($ads,new AdTransformer);
    }
}