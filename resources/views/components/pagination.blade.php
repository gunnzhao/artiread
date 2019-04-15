<nav aria-label="Page navigation example" class="mt-4 mr-4 pb-2">
    <ul class="pagination justify-content-center">
        <li class="page-item{{ $nowPage == 1 ? ' disabled' : '' }}">
            <a class="page-link" href="{{ $link }}{{ $nowPage > 1 ? 'p=' . ($nowPage - 1) : '' }}" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
                <span class="sr-only">Previous</span>
            </a>
        </li>
        <li class="page-item disabled"><a class="page-link" href="#">{{ $nowPage }}</a></li>
        <li class="page-item{{ $overagePage <= 1 ? ' disabled' : '' }}">
            <a class="page-link" href="{{ $link }}{{ $overagePage > 1 ? 'p=' . ($nowPage + 1) : '' }}" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
                <span class="sr-only">Next</span>
            </a>
        </li>
    </ul>
</nav>