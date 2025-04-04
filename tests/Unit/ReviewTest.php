<?php 
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Review;
use App\Models\User;
use App\Models\Facility;

class ReviewTest extends TestCase
{
    /** @test */
    public function review_belongs_to_user_and_facility()
    {
        $review = new Review();
        $this->assertInstanceOf(User::class, $review->user()->getRelated());
        $this->assertInstanceOf(Facility::class, $review->facility()->getRelated());
    }
}
