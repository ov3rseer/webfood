<?php

use common\components\pgsql\Migration;

class m190124_110200_add_verification_token_column_to_user_table extends Migration
{
    /**
     * @return bool|void
     * @throws Exception
     */
    public function up()
    {
        $this->addColumn('{{%ref_user}}', 'verification_token', $this->string()->defaultValue(null));
    }

    /**
     * @return bool|void
     * @throws Exception
     */
    public function down()
    {
        $this->dropColumn('{{%ref_user}}', 'verification_token');
    }
}