<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CheckinUserMonthlySummaryFixture
 */
class CheckinUserMonthlySummaryFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public $table = 'checkin_user_monthly_summary';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'yyyymm' => '',
                'user_id' => 1,
                'user_ext_id' => 'Lorem ipsum dolor sit amet',
                'user_name' => 'Lorem ipsum dolor sit amet',
                'type' => 'fe43d7f7-7591-4361-ae96-f7abd7172f06',
                'total_count' => 1,
            ],
        ];
        parent::init();
    }
}
