<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\LibraryService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function __construct(private readonly LibraryService $libraryService) {}

    public function index(Request $request): View|Factory
    {
        $sort = $request->query('sort') === 'newest' ? 'newest' : 'likes';
        ['featured' => $featured, 'diagrams' => $diagrams] = $this->libraryService->getLibraryData(
            $sort,
            max(1, $request->integer('page', 1)),
        );

        return view('library', compact('featured', 'diagrams', 'sort'));
    }
}
