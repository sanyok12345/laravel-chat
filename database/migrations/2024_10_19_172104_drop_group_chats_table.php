<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('group_user');

        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['group_chat_id']);
            $table->dropColumn('group_chat_id');
        });

        Schema::dropIfExists('group_chats');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('group_chats', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('group_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('group_chat_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('group_chat_id')->constrained()->onDelete('cascade');;
        });
    }
};
