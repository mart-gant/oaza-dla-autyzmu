<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Review;
use App\Models\User;
use App\Models\Facility;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function review_belongs_to_user_and_facility()
    {
        $user = User::factory()->create();
        $facility = Facility::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id, 'facility_id' => $facility->id]);
        $this->assertInstanceOf(User::class, $review->user);
        $this->assertInstanceOf(Facility::class, $review->facility);
    }
}
