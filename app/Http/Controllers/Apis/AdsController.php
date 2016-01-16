<?php
namespace App\Http\Controllers\Apis;
use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Transformers\AdTransformer;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class AdsController extends Controller
{
    public $fractal;

    function __construct(Manager $fractal) {
        $this->fractal = $fractal;
    }

    public function all()
    {
        $ads = Ad::paginate(20);

        // Pass this array (collection) into a resource, which will also have a "Transformer"
        // This "Transformer" can be a callback or a new instance of a Transformer object
        // We type hint for array, because each item in the $ads var is an array
        $resource = new Collection($ads, new AdTransformer);

        // Turn all of that into a JSON string
        return $this->fractal->createData($resource)->toArray(); 
    }

    public function show($id)
    {
        try {

            $book = Ad::findOrFail($id);

            $resource = new Fractal\Resource\Item($book,new AdTransformer);

        } catch (ModelNotFoundException $e) {

            return $this->response->errorNotFound();

        }
    }


    public function after($number)
    {
        $ads = Ad::whereRaw('message_number > '.$number)->paginate(20);

        // Pass this array (collection) into a resource, which will also have a "Transformer"
        // This "Transformer" can be a callback or a new instance of a Transformer object
        // We type hint for array, because each item in the $ads var is an array
        $resource = new Collection($ads, new AdTransformer);

        // Turn all of that into a JSON string
        return $this->fractal->createData($resource)->toArray(); 
    }
}