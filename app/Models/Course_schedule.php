<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course_schedule extends Model
{
    use HasFactory;
    protected $guarded=[];
    public function course(): \Illuminate\Database\Eloquent\Relations\BelongsTo
{
    return $this->belongsTo(Course::class);
}

}
