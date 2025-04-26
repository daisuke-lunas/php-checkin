<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CheckinTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CheckinTable Test Case
 */
class CheckinTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CheckinTable
     */
    protected $Checkin;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Checkin',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Checkin') ? [] : ['className' => CheckinTable::class];
        $this->Checkin = $this->getTableLocator()->get('Checkin', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Checkin);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\CheckinTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
