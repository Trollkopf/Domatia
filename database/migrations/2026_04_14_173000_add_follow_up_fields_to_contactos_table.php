<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contactos', function (Blueprint $table) {
            $table->timestamp('last_contacted_at')->nullable()->after('status');
            $table->date('next_action_at')->nullable()->after('last_contacted_at');
            $table->text('internal_notes')->nullable()->after('next_action_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contactos', function (Blueprint $table) {
            $table->dropColumn(['last_contacted_at', 'next_action_at', 'internal_notes']);
        });
    }
};
