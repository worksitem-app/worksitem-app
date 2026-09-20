<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Kolonlar artık create_leads_table'da tanımlı.
     * Bu migration geriye dönük uyumluluk için no-op.
     */
    public function up(): void
    {
        // no-op
    }

    public function down(): void
    {
        // no-op
    }
};
