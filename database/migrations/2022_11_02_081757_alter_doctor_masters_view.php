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
        CREATE or REPLACE VIEW doctor_masters_view AS

        select
        hq.location        as hq_location ,
        hq.code            as hq_code ,
        hq.status          as hq_status ,
        s.state            as state ,
        s.status           as s_status,
        bl.billing_name    as billing_name,
        bl.doctor_name     as doctor_name,
        bl.status          as bl_status,
        bl.specialist_id   as bl_specialist_id,
        p.patch            as patch ,
        p.status           as p_status,
        mr.name            as mr_name ,
        mr.email           as mr_email,
        mr.status          as mr_status,
        ar.name            as ar_name,
        ar.email           as ar_email,
        ar.status          as ar_status ,
        st.name            as st_name,
        st.status          as st_status,
        dm.id              as dm_id,
        s.id               as s_id,
        hq.id              as hq_id,
        bl.id              as bl_id,
        p.id               as p_id,
        mr.id              as mr_id,
        ar.id              as ar_id,
        st.id              as st_id
        from
        head_quarters hq
        FULL JOIN states s ON s.id = hq.state_id
        full join billings bl on bl.head_quarter_id = hq.id
        full join patches p on bl.patch_id = p.id
        full join doctor_masters dm on bl.id= dm.billing_id
        full join marketing_representatives mr ON mr.id = dm.marketing_representative_id
        full join area_managers ar ON ar.id = mr.area_manager_id

        FULL JOIN stockists st on st.marketing_representative_id = mr.id
        ;
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
        //
    }
};
