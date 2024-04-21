<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $sql = <<<'SQL'
                    CREATE TABLE billings (
                        id                  BIGINT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
                        patch_id            BIGINT references patches(id),
                        billing_name        CITEXT,
                        doctor_name        CITEXT,
                        specialist_id       BIGINT references specialists(id),
                        status              BOOLEAN default true,
                        created_at          TIMESTAMP DEFAULT NOW(),
                        updated_at          TIMESTAMP,
                        created_by          CITEXT,
                        updated_by          CITEXT
                    );
                SQL;
        DB::statement($sql);
    }

    public function down()
    {
        Schema::dropIfExists('billings');
    }
};
