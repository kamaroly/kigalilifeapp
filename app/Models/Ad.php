<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    /**
     * Set unique slug attribute
     * @param  string $value
     */
    public function setSlugAttribute($value) {
		$slug = str_slug($value);

		$slugs = static::whereRaw("slug REGEXP '^{$slug}(-[0-9]*)?$'");

		if ($slugs->count() === 0) {

			return $slug;

		}

		// get reverse order and get first
		$lastSlugNumber = intval(str_replace($slug . '-', '', $slugs->orderBy('slug', 'desc')->first()->slug));

		return $slug . '-' . ($lastSlugNumber + 1);

	}
}
