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
       Schema::create('contact_messages', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email');
            $t->string('phone', 30)->nullable();
            $t->string('service')->nullable();   // nom du service choisi, ou « Autre demande »
            $t->text('message');
            $t->string('ip_address', 45)->nullable();
            $t->timestamp('read_at')->nullable();
            $t->timestamp('handled_at')->nullable();
            $t->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
            $t->softDeletes();
        });
    }

    public function down(): void { 
        Schema::dropIfExists('contact_messages'); 
    }
};
