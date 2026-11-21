@if($paginator->hasPages())
<nav class="academic-pagination" aria-label="Pagination">
    <p class="pagination-summary">Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ number_format($paginator->total()) }}</p>
    <div class="pagination-controls">
        @if($paginator->onFirstPage())
            <span class="pagination-control" aria-disabled="true">Previous</span>
        @else
            <a class="pagination-control" href="{{ $paginator->previousPageUrl() }}" rel="prev">Previous</a>
        @endif
        <span class="pagination-mobile">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
        <div class="pagination-numbers">
            @foreach($elements as $element)
                @if(is_string($element))
                    <span class="pagination-gap">{{ $element }}</span>
                @else
                    @foreach($element as $page => $url)
                        @if($page === $paginator->currentPage())
                            <span class="pagination-control" aria-current="page" aria-label="Page {{ $page }}">{{ $page }}</span>
                        @else
                            <a class="pagination-control" href="{{ $url }}" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>
        @if($paginator->hasMorePages())
            <a class="pagination-control" href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a>
        @else
            <span class="pagination-control" aria-disabled="true">Next</span>
        @endif
    </div>
</nav>
@endif
