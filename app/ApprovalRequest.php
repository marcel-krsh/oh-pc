<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * ApprovalRequest Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class ApprovalRequest extends Model
{
    protected $table = 'approval_requests';

    protected $fillable = [
        'approval_type_id',
        'link_type_id',
        'user_id',
        'due_by',
        'seen_on'
    ];

    /**
     * Approver
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function approver() : HasOne
    {
        return $this->hasOne(\App\User::class, 'id', 'user_id');
    }

    /**
     * Approval Type
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function approval_type() : HasOne
    {
        return $this->hasOne(\App\ApprovalType::class, 'id', 'approval_type_id');
    }

    /**
     * Actions
     *
     * @return mixed
     */
    public function actions() : HasMany
    {
        return $this->hasMany(\App\ApprovalAction::class, 'approval_request_id')->orderBy('id', 'DESC');
    }

    /**
     * Last Action
     *
     * @return mixed
     */
    public function last_action()
    {
        if ($this->actions) {
            return $this->actions()->first();
        }
    }
}
