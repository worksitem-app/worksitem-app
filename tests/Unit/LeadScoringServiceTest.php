<?php

namespace Tests\Unit;

use App\Services\Leads\LeadScoringService;
use PHPUnit\Framework\TestCase;

class LeadScoringServiceTest extends TestCase
{
    public function test_immediate_budget_confirmed_project_is_hot(): void
    {
        $result = (new LeadScoringService())->score([
            'phone' => '0532 000 00 00',
            'budget_confirmed' => true,
            'timeline' => 'now',
            'city' => 'istanbul',
            'area' => 250,
            'project_type' => 'commercial',
            'explicit_request' => true,
        ]);

        $this->assertSame('hot', $result['quality']);
        $this->assertTrue($result['sales_ready']);
        $this->assertGreaterThanOrEqual(80, $result['score']);
    }

    public function test_one_to_three_month_project_is_warm(): void
    {
        $result = (new LeadScoringService())->score([
            'phone' => '0532 000 00 00',
            'budget_confirmed' => true,
            'timeline' => 'soon',
            'city' => 'istanbul',
            'area' => 180,
            'project_type' => 'renovation',
            'explicit_request' => true,
        ]);

        $this->assertSame('warm', $result['quality']);
        $this->assertTrue($result['sales_ready']);
    }

    public function test_research_user_is_not_sales_ready(): void
    {
        $result = (new LeadScoringService())->score([
            'phone' => '0532 000 00 00',
            'budget_confirmed' => false,
            'timeline' => 'research',
            'city' => 'istanbul',
            'area' => 150,
            'project_type' => 'renovation',
            'explicit_request' => false,
        ]);

        $this->assertSame('research', $result['quality']);
        $this->assertFalse($result['sales_ready']);
    }

    public function test_direct_discovery_request_is_warm_without_client_score(): void
    {
        $result = (new LeadScoringService())->score([
            'phone' => '0532 000 00 00',
            'project_type' => 'Fabrika Tadilatı',
            'explicit_request' => true,
        ]);

        $this->assertSame('warm', $result['quality']);
        $this->assertTrue($result['sales_ready']);
    }
}
