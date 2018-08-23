<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Report Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Report extends Model
{
    use SoftDeletes;

    protected $table = 'reports';
    
    protected $fillable = [
        'type',
        'folder',
        'filename',
        'pending_request',
        'user_id',
        'program_numbers',
        'program_processed'
    ];

    protected $dates = [
        'deleted_at'
    ];

    /**
     * User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Downloads
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function downloads() : HasMany
    {
        return $this->hasMany(\App\Models\ReportDownload::class, 'report_id', 'id');
    }

    /**
     * Download Total
     *
     * @return int
     */
    public function download_total() : int
    {
        return (int) $this->downloads()->count();
    }
}
