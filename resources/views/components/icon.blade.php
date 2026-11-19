@props(['name', 'size' => 16, 'stroke' => 1.5])

{{-- Ikon bergaya lucide (sama dengan vocabulary ikon di Figma UI kit) --}}
<svg {{ $attributes->merge(['class' => 'icon']) }}
     width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none"
     stroke="currentColor" stroke-width="{{ $stroke }}" stroke-linecap="round" stroke-linejoin="round"
     aria-hidden="true">
    @switch($name)
        @case('arrow-up-right') <path d="M7 7h10v10"/><path d="M7 17 17 7"/> @break
        @case('menu')           <path d="M4 12h16"/><path d="M4 6h16"/><path d="M4 18h16"/> @break
        @case('x')              <path d="M18 6 6 18"/><path d="m6 6 12 12"/> @break
        @case('plus')           <path d="M5 12h14"/><path d="M12 5v14"/> @break
        @case('chevron-down')   <path d="m6 9 6 6 6-6"/> @break
        @case('circle-check')   <circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/> @break
        @case('circle-alert')   <circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/> @break
        @case('pencil')         <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/> @break
        @case('trash-2')        <path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/> @break
        @case('printer')        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6"/><rect x="6" y="14" width="12" height="8" rx="1"/> @break
        @case('arrow-right')  <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/> @break
        @case('circle-alert') <circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/> @break
        @case('circle-check') <circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/> @break
        @case('pencil')       <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/> @break
        @case('trash-2')      <path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/> @break
        @case('info')      <circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/> @break
        @case('list-plus') <path d="M11 12H3"/><path d="M16 6H3"/><path d="M16 18H3"/><path d="M18 9v6"/><path d="M21 12h-6"/> @break
        @case('printer')     <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6"/><rect x="6" y="14" width="12" height="8" rx="1"/> @break
        @case('file-search') <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><circle cx="11.5" cy="14.5" r="2.5"/><path d="M13.3 16.3 15 18"/> @break
    @endswitch
</svg>
