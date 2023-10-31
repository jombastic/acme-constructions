<?php namespace Lucid\Employment\Models;

use Model;

/**
 * Model
 */
class Job extends Model
{
    use \Winter\Storm\Database\Traits\Validation;

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = true;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'lucid_employment_jobs';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $attachOne = [
        'pd' => 'System\Models\File'
    ];
}
