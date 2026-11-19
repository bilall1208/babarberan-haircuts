<div class="modal" data-modal hidden>
    <div class="modal__scrim" data-modal-close></div>

    <div class="modal__dialog" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="modal__head">
            <h2 id="modal-title" data-modal-title>Konfirmasi hapus</h2>
            <button type="button" data-modal-close aria-label="Tutup"><x-icon name="x" :size="20" /></button>
        </div>

        <p class="modal__desc" data-modal-desc></p>

        <div class="modal__target">
            <p class="modal__target-name" data-modal-name></p>
            <p class="modal__target-detail" data-modal-detail></p>
            <div class="divider"></div>
            <p class="modal__target-note" data-modal-note></p>
        </div>

        <div class="form-actions">
            <button type="button" class="btn btn--primary" data-modal-ok><span data-modal-ok-label>Ya, hapus</span></button>
            <button type="button" class="btn btn--secondary" data-modal-close><span>Batal</span></button>
        </div>
    </div>
</div>