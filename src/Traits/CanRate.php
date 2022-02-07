<?php

namespace Rennokki\Rating\Traits;

use Carbon\Carbon;
use Rennokki\Rating\Contracts\Rating;
use Rennokki\Rating\Contracts\Rateable;

trait CanRate
{
    /**
     * Relationship for models that this model currently rated.
     *
     * @param  null|\Illuminate\Database\Eloquent\Model  $model
     * @return mixed
     */
    public function ratings($model = null)
    {
        $modelClass = $model ? (new $model)->getMorphClass() : $this->getMorphClass();

        return $this->morphToMany($modelClass, 'rater', 'ratings', 'rater_id', 'rateable_id')
            ->withPivot('rateable_type', 'rating', 'review', 'created_at', 'meta')
            ->wherePivot('rateable_type', $modelClass)
            ->wherePivot('rater_type', $this->getMorphClass());
    }

    /**
     * Check if the current model is rating another model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return bool
     */
    public function hasRated($model): bool
    {
        if (!$model instanceof Rateable && !$model instanceof Rating) {
            return false;
        }

        return !is_null($this->ratings($model->getMorphClass())->find($model->getKey()));
    }

    /**
     * Rate a certain model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  float  $rating
     * @return bool
     */
    public function rate($model, float $rating, string $review, $meta = null): bool
    {
        if (!$model instanceof Rateable && !$model instanceof Rating) {
            return false;
        }

        // if ($this->hasRated($model)) {
        //     return false;
        // }

        $this->ratings()->attach($this->getKey(), [
            'rater_id' => $this->getKey(),
            'rateable_type' => $model->getMorphClass(),
            'rateable_id' => $model->getKey(),
            'rating' => $rating,
            'review' => $review,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'meta' => $meta
        ]);

        return true;
    }

    /**
     * Update the rating for a model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  float  $newRating
     * @return bool
     */
    public function updateRatingFor($model, $newRating, $review, $meta = null): bool
    {
        if (!$model instanceof Rateable && !$model instanceof Rating) {
            return false;
        }

        if (!$this->hasRated($model)) {
            return $this->rate($model, $newRating, $review, $meta);
        }

        $this->unrate($model);

        return $this->rate($model, $newRating, $review, $meta);
    }

    /**
     * Unrate a certain model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return bool
     */
    public function unrate($model): bool
    {
        if (!$model instanceof Rateable && !$model instanceof Rating) {
            return false;
        }

        if (!$this->hasRated($model)) {
            return false;
        }

        return (bool) $this->ratings($model->getMorphClass())->detach($model->getKey());
    }
}
