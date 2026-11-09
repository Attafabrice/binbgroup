<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void{
        Schema::table('quote_requests', function (Blueprint $table) {
            $table->text('decision_note')->nullable()->after('message');
            $table->foreignId('reviewed_by')->nullable()->after('assigned_to')->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
        });
    }

    public function down(): void{
        Schema::table('quote_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn(['decision_note', 'reviewed_at']);
        });
    }
};