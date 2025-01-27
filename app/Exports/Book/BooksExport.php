<?php

namespace App\Exports\Book;

use App\Filters\Book\BookFilters;
use App\Models\Book;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BooksExport implements FromCollection, WithHeadings, WithMapping, WithStyles {
    public function __construct(private array $filters) {
    }

    public function headings(): array {
        return [
            'Title',
            'Description',
            'Published At',
            'Price',
            'Category',
            'Authors'
        ];
    }

    public function map($book): array {
        return [
            $book->title,
            $book->description,
            $book->published_at,
            '$' . $book->price,
            $book->category->name,
            $book->authors->pluck('name')->implode(', '),
        ];
    }

    public function collection(): Collection {
        $queryBuilder = Book::with(['category', 'authors'])
            ->select('*');

        $books = app(BookFilters::class)->filter([
            'queryBuilder' => $queryBuilder,
            'params' => $this->filters,
        ])->get();

        return $books;
    }

    public function styles(Worksheet $sheet) {
        return [
            // Make first row bold
            1 => ['font' => ['bold' => true]],
        ];
    }
}