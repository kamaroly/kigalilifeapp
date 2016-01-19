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
	        'slug'    	=> $ad->slug,
	        'body'		=> $ad->body,
	        'owner'   	=> $ad->owner,
	        'address' 	=> $ad->from_address,
	        'time'		=> $ad->udate,
	        'message_id'=> (int) $ad->message_number,
	    ];

	    $data['profile']     = 'https://xa.yimg.com/kq/groups/23147588/tn/194346319/name/Short+hair+puppy_male.jpg';
	    $data['attachments'] = json_decode($ad->attachments);

	    return $data;
	}
}