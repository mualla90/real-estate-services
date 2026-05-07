<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('price_usd', 12, 2)->nullable()->after('price');
            $table->decimal('price_syp', 15, 2)->nullable()->after('price_usd');
            $table->index('price_usd');
            $table->index('price_syp');
        });

        DB::table('services')
            ->where('currency', 'USD')
            ->update([
                'price_usd' => DB::raw('price'),
            ]);

        DB::table('services')
            ->where('currency', 'SYP')
            ->update([
                'price_syp' => DB::raw('price'),
            ]);
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex(['price_usd']);
            $table->dropIndex(['price_syp']);
            $table->dropColumn(['price_usd', 'price_syp']);
        });
    }
};
