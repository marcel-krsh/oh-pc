<script>
@if ($paginator->hasPages())
    
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            
        @else
           
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())

            
            $('#results-pagination').empty().append('<a href="{{ $paginator->nextPageUrl() }}" style="display:none;" rel="next" id="next-page">&raquo;</a>');
            window.getContentForListId = 1;
        @else
            window.getContentForListId = 0;
        @endif
    
@else
    window.getContentForListId = 0;
@endif
</script>