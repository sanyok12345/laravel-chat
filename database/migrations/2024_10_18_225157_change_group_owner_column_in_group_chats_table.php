<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeGroupOwnerColumnInGroupChatsTable extends Migration
{
    public function up()
    {
        Schema::table('group_chats', function (Blueprint $table) {
            $table->dropColumn('group_owner');

            // Add the foreign key column
            $table->foreignId('group_owner')->constrained('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('group_chats', function (Blueprint $table) {
            // Reverse the changes in case of rollback
            $table->dropForeign(['group_owner']); // Drop foreign key constraint
            $table->dropColumn('group_owner'); // Drop the column

            // Add the old column back if you need to revert
            $table->unsignedBigInteger('group_owner'); // You can change the type if needed
        });
    }
}

