<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Exceptions\InactiveFeatureException;
use App\Features\Book\CheapestBookFeature;
use App\Features\Book\MostExpensiveBookFeature;
use App\Http\Controllers\Controller;
use App\Http\Resources\Book\BookResource;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Pennant\Feature;

class PennantBookController extends Controller {
    public function showMostExpensiveBook() {
        $book = Book::orderBy('price', 'desc')->first();

        return new BookResource($book);
    }

    public function showCheapestBook() {
        try {
            if (!Feature::for(null)->active(CheapestBookFeature::class)) {      // use [for] to pass scope to [resolve] function inside the Feature class + by default it's the [authenticated user]
                throw new InactiveFeatureException;
            }

            $book = Book::orderBy('price', 'asc')->first();

            return new BookResource($book);
        } catch (\Exception $e) {
            abort($e->getCode(), $e->getMessage());
        }
    }

    public function updateMostExpensiveBookActivation(Request $request) {
        $user = User::find($request->user_id);

        if ($request->is_active) {
            Feature::for($user)->activate(MostExpensiveBookFeature::class);
        } else {
            Feature::for($user)->deactivate(MostExpensiveBookFeature::class);
        }
    }
}
