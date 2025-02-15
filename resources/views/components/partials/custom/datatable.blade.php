<div class="table-responsive">
    <table class="display responsive" style="width: 100%; float:center;" id="{{ $idTable ?? 'table' }}">
        <thead>
            {{ $tableHead ?? '' }}
        </thead>
        <tbody>
            {{ $tableBody ?? '' }}
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        @if ($serverSide == 1)
            {{ $cofigServerSide ?? '' }}
        @else
            $('#{{ $idTable ?? "table" }}').dataTable();
        @endif
    });
</script>
