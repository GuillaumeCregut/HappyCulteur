import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    // static targets = ['panel', 'icon'];

    toggle(event) {
        const item = event.currentTarget.closest('[data-purchase-accordion-item]');
        const panel = item.querySelector('[data-purchase-accordion-target="panel"]');
        const icon = item.querySelector('[data-purchase-accordion-target="icon"]');

        panel.classList.toggle('purchase-hidden');
        icon.classList.toggle('rotate-180');
    }
}