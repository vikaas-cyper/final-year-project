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
                    CREATE TABLE doctor_masters (
                        id                              BIGINT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
                        billing_id                      BIGINT references billings(id),
                        marketing_representative_id     BIGINT references marketing_representatives(id),
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
        Schema::dropIfExists('doctor_masters');
    }
};
