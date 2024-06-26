<?php

use Illuminate\Database\Migrations\Migration;
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
                    CREATE TABLE stockists (
                        id                              BIGINT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
                        marketing_representative_id     BIGINT references marketing_representatives(id),
                        name                            CITEXT,
                        email                           CITEXT,
                        status                          BOOLEAN default true,
                        created_at                      TIMESTAMP DEFAULT NOW(),
                        updated_at                      TIMESTAMP,
                        created_by                      CITEXT,
                        updated_by                      CITEXT

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
        Schema::dropIfExists('stockists');
    }
};
