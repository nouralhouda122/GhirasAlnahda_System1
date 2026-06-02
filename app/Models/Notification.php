<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'data',
        'read_at'
    ];

    /**
     * تحويل حقل الـ data تلقائياً من نظام JSON في قاعدة البيانات 
     * إلى Array (مصفوفة) في الـ PHP لسهولة التعامل معه وعرضه
     */
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime'
    ];

    /**
     * علاقة الإشعار بالمستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}