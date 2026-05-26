<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('location');
            $table->text('description')->nullable();
            $table->enum('type', [
                'relief',        // إغاثية
                'awareness',     // توعوية
                'training',      // تدريبية
                'field',         // ميدانية
                'development',   // تنموية
                'charity'        // خيرية
            ]);
            $table->enum('status', [
                'pending_approval',   // بانتظار موافقة المدير العام
                'approved',           // تمت الموافقة
                'rejected',           // مرفوضة
                'ongoing',            // قيد التنفيذ
                'completed',         // مكتملة
                'archived'           // مؤرشفة
            ])->default('pending_approval');
            $table->enum('priority', [
                'low',
                'medium',
                'high'
            ])->default('medium');
            $table->foreignId('leader_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('radius')->nullable();
            $table->integer('required_volunteers')->default(0);
            $table->integer('current_volunteers')->default(0);
            $table->decimal('target_amount', 10, 2)->default(0);
            $table->decimal('current_amount', 10, 2)->default(0);
            $table->boolean('has_evaluation')->default(0);
            $table->string('image')->nullable();
            $table->string('video')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
