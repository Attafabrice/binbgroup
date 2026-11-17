<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void{
        Schema::table('quotes', function (Blueprint $t) {
            $t->foreignId('accepted_by')->nullable()->constrained('users')->nullOnDelete(); // null = client
            $t->string('acceptance_channel')->nullable(); // portal|phone|whatsapp|paper|email
            $t->text('acceptance_note')->nullable();
            $t->string('acceptance_proof')->nullable();
            $t->text('refusal_reason')->nullable();
        });
    }

    public function down(): void{
        Schema::table('quotes', function (Blueprint $t) {
            $t->dropConstrainedForeignId('accepted_by');
            $t->dropColumn(['acceptance_channel', 'acceptance_note', 'acceptance_proof', 'refusal_reason']);
        });
    }
};
