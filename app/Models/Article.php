<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Article extends Model {

    // filter and save these fields into the database
    protected $fillable = [
        'name',
        'author',
        'created_at'
    ];

    // format and save current time to the database when create a new article
    public function setCreatedAtAttribute($date) {
        $this->attributes['created_at'] = Carbon::createFromFormat('Y-m-d', $date);
    }

}
