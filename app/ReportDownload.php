<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * ReportDownload Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class ReportDownload extends Model
{
    protected $table = 'report_downloads';

    protected $fillable = [
        'report_id',
        'user_id'
    ];

    /**
     * User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user() : HasOne
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    /**
     * Report
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function report() : HasOne
    {
        return $this->hasOne('App\Report', 'id', 'report_id');
    }
}
