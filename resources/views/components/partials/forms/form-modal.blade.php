<div id="{{ $modalId ?? '' }}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">{{ $modalTitle ?? '' }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal calender" role="form" action="{{ $formUrl ?? '' }}"
                    id="{{ $formId ?? '' }}" method="{{ $formMethod ?? 'POST' }}" enctype="multipart/form-data">
                    @csrf
                    {{ $formBody ?? '' }}
                </form>
            </div>
        </div>
    </div>
</div>
