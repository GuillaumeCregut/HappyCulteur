import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static values = {url: String}
    async toggle(event) {
        const isAdmin = event.target.checked;
        const response = await fetch(this.urlValue, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({isAdmin}),
        });
        if(!response.ok) {
            event.target.checked = !isAdmin;
            console.error('Failed to update user role');
        }
    }

    connect() {
        this.element.addEventListener('change', this.toggle.bind(this));
    }
}