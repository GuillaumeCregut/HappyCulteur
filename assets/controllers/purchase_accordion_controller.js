import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['popup', 'form'];

    toggle(event) {
        const item = event.currentTarget.closest('[data-purchase-accordion-item]');
        const panel = item.querySelector('[data-purchase-accordion-target="panel"]');
        const icon = item.querySelector('[data-purchase-accordion-target="icon"]');

        panel.classList.toggle('purchase-hidden');
        icon.classList.toggle('rotate-180');
    }

    async openPopup({ params: { id } }) {
        if (!this.popupTarget.open) {
            const response = await fetch(`/purchase/${id}/edit`);
            this.formTarget.innerHTML = await response.text();
            this.popupTarget.showModal();
        }
        console.log(id);
    }
}