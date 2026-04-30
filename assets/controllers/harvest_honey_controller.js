import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["dialog", "container", "select"];
    static values  = { url: String };

    async open() {
        const response = await fetch(this.urlValue, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const html = await response.text();
        this.containerTarget.innerHTML = html;
        const form = this.containerTarget.querySelector('form');
        if(form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitForm(form);
            });
        }
        this.dialogTarget.showModal();
    }

    async submitForm(form){
        const response = await fetch(form.action, {
            method: 'POST', 
            body: new FormData(form),
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        });
        if (response.ok) {
            const honey = await response.json();
            const option = new Option(honey.name, honey.id, true, true);
            this.selectTarget.add(option);
            this.close();
        }
    }

    close() {
        this.dialogTarget.close();
        this.containerTarget.innerHTML='';
    }
}