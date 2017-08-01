<?php

namespace App\Models;

use Carbon\Carbon;

/*
 * This trait is to be used with the default $table->timestamps() schema definition
 * @author Dakoda Larlham
 * @license MIT
 */
trait TimestampedModel
{
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value, env('DB_TIMEZONE'))->format('c');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value, env('DB_TIMEZONE'))->format('c');
    }
}
