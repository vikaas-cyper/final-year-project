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
                    CREATE TABLE product_buy_rates (
                        id                      BIGINT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
                        product_id              BIGINT references products(id),
                        price_per_unit          DECIMAL,
                        purchase_date           DATE,
                        status                  BOOLEAN default false,
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
        Schema::dropIfExists('product_buy_rates');
    }
};
