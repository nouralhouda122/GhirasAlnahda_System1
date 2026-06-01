<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class EvaluationTask extends Model
{
    use HasFactory;
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }
    public function employee()
    {
        return $this->belongsTo(
            User::class,
            'employee_id'
        );
    }
}
