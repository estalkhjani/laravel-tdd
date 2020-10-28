<?php

declare (strict_types=1);

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests\PostBookRequest;
use App\Http\Requests\PostBookReviewRequest;
use App\Http\Resources\BookResource;
use App\Http\Resources\BookReviewResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class BooksController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function getCollection(Request $request)
    {
        $book = Book::when($request->filled('title'), function ($builder) use ($request) {
            return $builder->where('title','like','%'.'title'.'%');
        })->when($request->filled('sortColumn'), function ($builder) use ($request) {
            $sortDirection = $request->sortDirection === 'DESC'? 'DESC' : 'ASC';
            return $builder->orderBy('title', $sortDirection);
        });
        return BookResource::collection($book->paginate());
    }

    /**
     * @param PostBookRequest $request
     * @param Book $book
     * @return BookResource
     */
    public function post(PostBookRequest $request, Book $book): BookResource
    {
        $book->title = $request->title;
        $book->isbn = $request->isbn;
        $book->description = $request->description;
        $book->save();
        $book->authors()->attach($request->authors);
        return new BookResource($book);
    }

    /**
     * @param PostBookReviewRequest $request
     * @param Book $book
     * @return BookReviewResource
     */
    public function postReview(PostBookReviewRequest $request, Book $book): BookReviewResource
    {
        $bookReview = $book->reviews()->create();
        $bookReview->review = $request->review;
        $bookReview->comment = $request->comment;
        $bookReview->user_id = Auth::id();
        $bookReview->book_id = $book->id;
        $bookReview->save();;
        return new BookReviewResource($bookReview);
    }
}
