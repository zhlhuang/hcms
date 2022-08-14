<?php

declare (strict_types=1);

namespace App\Application\Demo\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $demo_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Demo extends Model
{
    protected $primaryKey = 'demo_id';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'demo';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['demo_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}