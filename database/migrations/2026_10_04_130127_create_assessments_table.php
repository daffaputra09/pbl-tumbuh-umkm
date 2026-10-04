<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
	    $table->unsignedBigInteger('business_id');
	    $table->foreign('business_id')->references('user_id')->on('businesses')->onDelete('cascade');
	    $table->foreignId('filled_by')->constrained('users');
	    $table->string('status')->default('draf');
	    $table->boolean('is_current')->default(false);
	    $table->foreignId('primary_obstacle_category_id')->nullable()->constrained('obstacle_categories');
	    $table->text('other_obstacle')->nullable();
	    $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

	    $driver = Schema::getConnection()->getDriverName();

	    if (in_array($driver, ['sqlite', 'pgsql'], true)) {
		$trueLiteral = $driver === 'pgsql' ? 'true' : '1';

	        DB::statement(
	       	    "CREATE UNIQUE INDEX assessments_business_id_current_unique "
		    ."ON assessments (business_id) "
		    ."WHERE is_current = {$trueLiteral}"
	        );
	    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
