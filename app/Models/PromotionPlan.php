<?php

namespace App\Models;

use App\Models\Country;
use App\Models\Promotion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class PromotionPlan extends Model
{

    protected $fillable = ['name', 'slug', 'country_id', 'views','clicks'];

    public function country(){
        return $this->belongsTo(Country::class);
    }

}
