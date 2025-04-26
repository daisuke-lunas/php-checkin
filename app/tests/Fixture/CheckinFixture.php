<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CheckinFixture
 */
class CheckinFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public $table = 'checkin';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => '08f9dfbc-4f21-4cc8-b7bb-182d9e873b25',
                'customer_id' => 1,
                'customer_name' => 'Lorem ipsum dolor sit amet',
                'created_at' => '2025-04-26 09:16:10',
                'type' => 'Lorem ipsum dolor sit amet',
                'item' => 'Lorem ipsum dolor sit amet',
                'details' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'check_in_at' => '2025-04-26 09:16:10',
            ],
        ];
        parent::init();
    }
}
