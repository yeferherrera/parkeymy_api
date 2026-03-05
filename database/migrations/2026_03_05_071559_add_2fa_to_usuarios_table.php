<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('codigo_2fa')->nullable()->after('autenticacion_dos_pasos');
            $table->timestamp('codigo_2fa_expira')->nullable()->after('codigo_2fa');
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(['codigo_2fa', 'codigo_2fa_expira']);
        });
    }
};