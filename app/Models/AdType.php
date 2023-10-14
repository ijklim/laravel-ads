<?php

namespace App\Models;

class AdType extends \Illuminate\Database\Eloquent\Model
{
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'ad_type';

    /**
     * Whether primary key auto increment. Affects primary key field value after `->save()`
     *
     * @var boolean
     */
    public $incrementing = false;

    /**
     * Table has no timestamp fields `created_at` and `updated_at`
     */
    public $timestamps = false;
}
