<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ApprovalRequest extends Model
{
    protected $fillable = [
        'type',
        'status',
        'notes',
        'approvable_id',
        'approvable_type',
        'requested_by',
    ];
    public function approvable()
    {
        return $this->morphTo();
    }
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
