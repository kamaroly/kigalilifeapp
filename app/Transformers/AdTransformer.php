<?php
namespace App\Transformers;

use App\Models\Ad;
use League\Fractal;

class AdTransformer extends Fractal\TransformerAbstract
{
	public function transform(Ad $ad)
	{
	    $data =  [
	        'subject'   => $ad->subject,
	        'slug'    	=> $ad->subject,
	        'body'		=> $ad->body,
	        'time'		=> $ad->udate,
	        'owner'   	=> $ad->owner,
	        'address' 	=> $ad->from_address,
	    ];

	    $data['attachments'] = json_decode($ad->attachments);

	    return $data;
	}
}