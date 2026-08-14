<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
Schema::create('attendances', function (Blueprint $table) {
        $table->id();
        $table->foreignId('volunteer_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
        $table->timestamp('check_in_time')->nullable();
        $table->timestamp('check_out_time')->nullable();
        
        // التعديل هنا: تحويلها لـ Decimal لتقبل كسور الساعات (مثل 1.5 ساعة)
        $table->decimal('hours', 8, 2)->nullable(); 
        
        $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
        $table->boolean('is_leader')->default(false);
        $table->boolean('is_active_session')->default(false);
        $table->timestamps();

});
}

public function down(): void
{
Schema::dropIfExists('attendances');
}
};
