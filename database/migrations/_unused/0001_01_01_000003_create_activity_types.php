<?php

use App\Models\ActivityType;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40);
            $table->string('description', 500);
            $table->boolean('active')->default(true);
            $table->enum('target_period', array_keys(ActivityType::TargetPeriods));
            $table->unsignedInteger('default_target')->default(0);
            $table->unsignedInteger('weight')->default(0);

            $table->datetime('created_datetime')->nullable();
            $table->datetime('updated_datetime')->nullable();

            $table->foreignId('created_by_uid')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by_uid')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_types');
    }
};
