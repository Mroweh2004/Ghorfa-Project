<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // GET /properties/{property}/reviews
    public function index(Property $property)
    {
        $reviews = $property->reviews()
            ->with('user:id,name')
            ->latest()
            ->paginate(10);

        return view('properties.reviews.index', compact('property','reviews'));
    }

    public function store(Request $request, Property $property)
    {
        $existingReview = Review::where('property_id', $property->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existingReview) {
            return back()->with('error', 'You have already reviewed this property. You can edit your existing review instead.');
        }

        $data = $request->validate([
            'rating'  => ['required','integer','between:1,5'],
            'comment' => ['required','string','min:10','max:1000'],
        ], [
            'rating.required' => 'Please select a rating.',
            'rating.between' => 'Rating must be between 1 and 5 stars.',
            'comment.required' => 'Please write a review comment.',
            'comment.min' => 'Your review must be at least 10 characters long.',
            'comment.max' => 'Your review cannot exceed 1000 characters.',
        ]);

        Review::create(attributes: [
            'property_id' => $property->id,
            'user_id'     => $request->user()->id,
            'rating'      => $data['rating'],
            'comment'     => $data['comment'],
        ]);

        return back()->with('success', 'Thank you for your review! Your feedback helps other users make informed decisions.');
    }

    // PUT /reviews/{review}
    public function update(Request $request, Review $review)
    {
        // Check if user owns this review or is admin
        if ($review->user_id !== $request->user()->id && !$request->user()->hasRole('admin')) {
            return back()->with('error', 'You can only edit your own reviews.');
        }

        $data = $request->validate([
            'rating'  => ['required','integer','between:1,5'],
            'comment' => ['required','string','min:10','max:1000'],
        ], [
            'rating.required' => 'Please select a rating.',
            'rating.between' => 'Rating must be between 1 and 5 stars.',
            'comment.required' => 'Please write a review comment.',
            'comment.min' => 'Your review must be at least 10 characters long.',
            'comment.max' => 'Your review cannot exceed 1000 characters.',
        ]);

        $review->update($data);

        return back()->with('success', 'Your review has been updated successfully.');
    }

    // DELETE /reviews/{review}
    public function destroy(Request $request, Review $review)
    {
        // Check if user owns this review, is the property owner, or is admin
        $canDelete = $review->user_id === $request->user()->id || 
                    $review->property->user_id === $request->user()->id || 
                    $request->user()->hasRole('admin');

        if (!$canDelete) {
            return back()->with('error', 'You do not have permission to delete this review.');
        }

        $review->delete();

        return back()->with('success', 'Review has been deleted successfully.');
    }
}
