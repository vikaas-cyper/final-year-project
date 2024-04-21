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

        $sql = <<<'SQL'
            CREATE TABLE stockist_expenses (
                id                      BIGINT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
                price_per_stockist      DECIMAL,
                expenses_date           DATE,
                stockist_id             BIGINT references stockists(id),
                status                  BOOLEAN default true,

                created_at              TIMESTAMP DEFAULT NOW(),
                updated_at              TIMESTAMP,
                created_by              CITEXT,
                updated_by              CITEXT
            );
        SQL;
        DB::statement($sql);


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stockist_expenses');
    }
};
