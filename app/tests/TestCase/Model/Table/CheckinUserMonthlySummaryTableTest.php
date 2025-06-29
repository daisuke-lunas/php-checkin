<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CheckinUserMonthlySummaryTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CheckinUserMonthlySummaryTable Test Case
 */
class CheckinUserMonthlySummaryTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CheckinUserMonthlySummaryTable
     */
    protected $CheckinUserMonthlySummary;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.CheckinUserMonthlySummary',
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('CheckinUserMonthlySummary') ? [] : ['className' => CheckinUserMonthlySummaryTable::class];
        $this->CheckinUserMonthlySummary = $this->getTableLocator()->get('CheckinUserMonthlySummary', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->CheckinUserMonthlySummary);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\CheckinUserMonthlySummaryTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\CheckinUserMonthlySummaryTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
