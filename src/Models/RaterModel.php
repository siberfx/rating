<?php

namespace Rennokki\Rating\Models;

use Illuminate\Database\Eloquent\Model;

class RaterModel extends Model
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'ratings';

    /**
     * {@inheritdoc}
     */
    protected $guarded = [];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'rating' => 'float',
        'meta' => 'json'
    ];

    /**
     * The relationship function for the model who was rated.
     *
     * @return mixed
     */
    public function rateable()
    {
        return $this->morphTo();
    }

    /**
     * The relationship function for the model who rated.
     *
     * @return mixed
     */
    public function rater()
    {
        return $this->morphTo();
    }
}
