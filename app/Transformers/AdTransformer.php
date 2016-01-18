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
	        'owner'   	=> $ad->owner,
	        'address' 	=> $ad->from_address,
	        'time'		=> $ad->udate,
	        'message_id'=> (int) $ad->message_number,
	    ];

	    $data['attachments'] = json_decode($ad->attachments);

	    return $data;
	}
}