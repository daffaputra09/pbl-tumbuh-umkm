<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
        });

        $this->rewriteProductBusinessIds(
            DB::table('businesses')
                ->whereNotNull('user_id')
                ->pluck('id', 'user_id'),
        );

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('business_id')
                ->references('id')
                ->on('businesses')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
        });

        $this->rewriteProductBusinessIds(
            DB::table('businesses')
                ->whereNotNull('user_id')
                ->pluck('user_id', 'id'),
        );

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('business_id')
                ->references('user_id')
                ->on('businesses')
                ->cascadeOnDelete();
        });
    }

    /**
     * Rows created before this migration stored businesses.user_id.
     * The application now stores businesses.id.
     *
     * @param  Collection<int|string, int|string>  $targetByCurrent
     */
    private function rewriteProductBusinessIds($targetByCurrent): void
    {
        $targets = [];

        foreach ($targetByCurrent as $current => $target) {
            $targets[(int) $current] = (int) $target;
        }

        $updates = [];

        foreach (DB::table('products')->select('id', 'business_id')->orderBy('id')->get() as $product) {
            $target = $targets[(int) $product->business_id] ?? null;

            if ($target === null) {
                throw new RuntimeException('Produk '.$product->id.' tidak punya usaha yang cocok.');
            }

            if ($target !== (int) $product->business_id) {
                $updates[(int) $product->id] = $target;
            }
        }

        foreach ($updates as $productId => $businessId) {
            DB::table('products')->where('id', $productId)->update([
                'business_id' => $businessId,
            ]);
        }
    }
};
