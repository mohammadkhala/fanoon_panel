{{--
  Template picker — used in product add/edit form.
  Props:
    $designTemplates  — collection of DesignTemplate (id, name, thumbnail, ...)
    $selectedId       — currently selected template id (or null)
    $inputId          — id of the hidden input to update (default: 'tmpl-selected-id')
--}}
@php $inputId = $inputId ?? 'tmpl-selected-id'; @endphp

@if($designTemplates->isEmpty())
    <div class="text-center py-4">
        <i class="tio-layers fa-2x text-muted mb-2 d-block"></i>
        <p class="text-muted mb-1">{{ translate('no_templates_yet') ?: 'لا توجد قوالب بعد.' }}</p>
        <a href="{{ route('admin.design-template.add-new') }}" class="btn btn-sm btn-outline-primary" target="_blank">
            <i class="fa fa-plus me-1"></i> {{ translate('add_new_template') ?: 'إضافة قالب جديد' }}
        </a>
    </div>
@else
    <style>
    .tp-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(130px,1fr)); gap:10px; }
    .tp-card {
      border:2px solid #e9ecef; border-radius:10px; overflow:hidden; cursor:pointer;
      background:#fff; transition:border-color .15s, box-shadow .15s;
    }
    .tp-card:hover { border-color:#adb5bd; box-shadow:0 2px 8px rgba(0,0,0,.08); }
    .tp-card.tp-selected { border-color:#EC2227; box-shadow:0 0 0 3px rgba(236,34,39,.15); }
    .tp-card img { width:100%; aspect-ratio:1; object-fit:cover; display:block; }
    .tp-card .tp-ph {
      width:100%; aspect-ratio:1; background:#f8f9fa;
      display:flex; align-items:center; justify-content:center; color:#ced4da;
    }
    .tp-card .tp-label { padding:6px 8px; font-size:12px; font-weight:600; color:#343a40; line-height:1.3; }
    .tp-card .tp-check { display:none; position:absolute; top:5px; inset-inline-end:5px;
      width:22px; height:22px; border-radius:50%; background:#EC2227; color:#fff;
      align-items:center; justify-content:center; font-size:11px; }
    .tp-card.tp-selected .tp-check { display:flex; }
    .tp-wrap { position:relative; }
    .tp-none-card {
      border:2px dashed #ced4da; border-radius:10px; cursor:pointer;
      background:#fafafa; transition:border-color .15s;
      display:flex; flex-direction:column; align-items:center; justify-content:center;
      min-height:130px; padding:12px; text-align:center; color:#6c757d; font-size:12px; font-weight:600;
    }
    .tp-none-card:hover { border-color:#adb5bd; }
    .tp-none-card.tp-selected { border-color:#EC2227; color:#EC2227; }
    </style>

    <div class="tp-grid" id="tp-grid-{{ $inputId }}">

        {{-- "بدون قالب" card --}}
        <div class="tp-none-card {{ !$selectedId ? 'tp-selected' : '' }}"
             onclick="tpSelect('{{ $inputId }}', null, this)">
            <i class="fa fa-ban fa-lg mb-2"></i>
            {{ translate('no_template') ?: 'بدون قالب' }}
        </div>

        @foreach($designTemplates as $tmpl)
        <div class="tp-wrap">
            <div class="tp-card {{ $selectedId == $tmpl->id ? 'tp-selected' : '' }}"
                 onclick="tpSelect('{{ $inputId }}', {{ $tmpl->id }}, this.parentElement.querySelector('.tp-card'))">
                @if($tmpl->thumbnail_fullpath)
                    <img src="{{ $tmpl->thumbnail_fullpath }}" alt="{{ $tmpl->name }}" loading="lazy">
                @else
                    <div class="tp-ph"><i class="fa fa-palette fa-2x"></i></div>
                @endif
                <div class="tp-label">{{ $tmpl->name }}</div>
            </div>
            <div class="tp-check"><i class="fa fa-check"></i></div>
        </div>
        @endforeach

    </div>

    <div class="mt-3 d-flex align-items-center justify-content-between">
        <span id="tp-label-{{ $inputId }}" class="text-muted" style="font-size:13px">
            @if($selectedId)
                {{ translate('selected_template') ?: 'القالب المختار' }}:
                <strong>{{ $designTemplates->firstWhere('id', $selectedId)?->name }}</strong>
            @else
                {{ translate('no_template_selected') ?: 'لم يتم اختيار قالب' }}
            @endif
        </span>
        <a href="{{ route('admin.design-template.add-new') }}" class="btn btn-sm btn-outline-secondary" target="_blank">
            <i class="fa fa-plus me-1"></i> {{ translate('add_new_template') ?: 'إضافة قالب جديد' }}
        </a>
    </div>

    @once
    <script>
    function tpSelect(inputId, id, clickedCard) {
        // Deselect all in this grid
        const grid = document.getElementById('tp-grid-' + inputId);
        grid.querySelectorAll('.tp-card, .tp-none-card').forEach(c => c.classList.remove('tp-selected'));

        // Select clicked
        if (clickedCard) clickedCard.classList.add('tp-selected');

        // Update hidden input
        document.getElementById(inputId).value = id !== null ? id : '';

        // Update label
        const labelEl = document.getElementById('tp-label-' + inputId);
        if (labelEl) {
            labelEl.innerHTML = id
                ? '{{ translate('selected_template') ?: 'القالب المختار' }}: <strong>' + (clickedCard?.querySelector('.tp-label, .tp-none-card')?.textContent?.trim() || '') + '</strong>'
                : '{{ translate('no_template_selected') ?: 'لم يتم اختيار قالب' }}';
        }
    }
    </script>
    @endonce
@endif
