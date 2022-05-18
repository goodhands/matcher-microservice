<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Repository\SearchProfileRepository;

class SearchProfileMatchTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setup();

        $this->repository = new SearchProfileRepository();
    }

    public function test_check_nullable_range()
    {
        $this->repository->checkNullableRange(array(null, 10), 5, 'rooms');
        $this->assertEquals($this->repository->getScore(), $this->repository::STRICTSCORE);
        $this->repository->addChecked('rooms');
        $this->assertContains('rooms', $this->repository->getCheckedItems());
    }

    public function test_check_nullable_range_fails_with_out_range_value()
    {
        $this->repository->checkNullableRange(array(null, 10), 15, 'rooms');
        $this->assertNotEquals($this->repository->getScore(), $this->repository::STRICTSCORE);
        $this->repository->addChecked('rooms');
        $this->assertContains('rooms', $this->repository->getCheckedItems());
    }

    public function test_check_nullable_range_accepts_any_value_as_null()
    {
        $this->repository->checkNullableRange(array(10, null), 500, 'rooms');
        $this->assertEquals($this->repository->getScore(), $this->repository::STRICTSCORE);
        $this->repository->addChecked('rooms');
        $this->assertContains('rooms', $this->repository->getCheckedItems());
    }

    public function test_check_within_range()
    {
        $this->repository->checkRange(array(20000, 50000), 25000, 'price');
        $this->assertEquals($this->repository->getScore(), $this->repository::STRICTSCORE);
        $this->repository->addChecked('price');
        $this->assertContains('price', $this->repository->getCheckedItems());
    }

    public function test_check_within_range_wont_work_for_null()
    {
        $this->repository->checkRange(array(null, 50000), 25000, 'price');
        $this->assertNotEquals($this->repository->getScore(), $this->repository::STRICTSCORE);
        $this->repository->addChecked('price');
        $this->assertContains('price', $this->repository->getCheckedItems());
    }

    public function test_assign_loose_score_when_value_within_deviation_range()
    {
        $this->repository->checkDeviationRange(array(75, 200), 230, 'price');
        $this->assertEquals($this->repository->getScore(), $this->repository::LOOSESCORE);
        $this->repository->addChecked('price');
        $this->assertContains('price', $this->repository->getCheckedItems());
    }

    public function test_all_properties_are_reset_successfully()
    {
        $this->repository->resetCounts();

        $this->assertEquals($this->repository->getScore(), 0);
        $this->assertEquals($this->repository->getLooseMatches(), 0);
        $this->assertEquals($this->repository->getStrictMatches(), 0);

        $this->assertEmpty($this->repository->getCheckedItems());
    }
}
