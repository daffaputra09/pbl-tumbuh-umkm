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
        Schema::table('follow_ups', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
        });

        Schema::table('coaching_sessions', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
        });

        $userToBusinessId = DB::table('businesses')
            ->whereNotNull('user_id')
            ->pluck('id', 'user_id');

        $this->rewriteBusinessIds('follow_ups', $userToBusinessId, 'id', 'Tindak lanjut');
        $this->rewriteBusinessIds('coaching_sessions', $userToBusinessId, 'id', 'Sesi pembinaan');

        Schema::table('follow_ups', function (Blueprint $table) {
            $table->foreign('business_id')
                ->references('id')
                ->on('businesses')
                ->cascadeOnDelete();
        });

        Schema::table('coaching_sessions', function (Blueprint $table) {
            $table->foreign('business_id')
                ->references('id')
                ->on('businesses')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('follow_ups', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
        });

        Schema::table('coaching_sessions', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
        });

        $businessToUserId = DB::table('businesses')
            ->whereNotNull('user_id')
            ->pluck('user_id', 'id');

        $this->rewriteBusinessIds('follow_ups', $businessToUserId, 'user_id', 'Tindak lanjut');
        $this->rewriteBusinessIds('coaching_sessions', $businessToUserId, 'user_id', 'Sesi pembinaan');

        Schema::table('follow_ups', function (Blueprint $table) {
            $table->foreign('business_id')
                ->references('user_id')
                ->on('businesses')
                ->cascadeOnDelete();
        });

        Schema::table('coaching_sessions', function (Blueprint $table) {
            $table->foreign('business_id')
                ->references('user_id')
                ->on('businesses')
                ->cascadeOnDelete();
        });
    }

    /**
     * @param  Collection<int|string, int|string>  $targetByCurrent
     */
    private function rewriteBusinessIds(string $tableName, $targetByCurrent, string $fallbackColumn, string $label): void
    {
        $targets = [];

        foreach ($targetByCurrent as $current => $target) {
            $targets[(int) $current] = (int) $target;
        }

        $validFallbackIds = DB::table('businesses')
            ->whereNotNull($fallbackColumn)
            ->pluck($fallbackColumn)
            ->map(fn ($id) => (int) $id)
            ->flip()
            ->all();

        $updates = [];

        foreach (DB::table($tableName)->select('id', 'business_id')->orderBy('id')->get() as $row) {
            $currentId = (int) $row->business_id;

            if (isset($targets[$currentId])) {
                $target = $targets[$currentId];

                if ($target !== $currentId) {
                    $updates[(int) $row->id] = $target;
                }
            } elseif (isset($validFallbackIds[$currentId])) {
                // Nilai sudah sesuai, biarkan
                continue;
            } else {
                throw new RuntimeException($label.' '.$row->id.' tidak punya usaha yang cocok.');
            }
        }

        foreach ($updates as $rowId => $businessId) {
            DB::table($tableName)->where('id', $rowId)->update([
                'business_id' => $businessId,
            ]);
        }
    }
};
