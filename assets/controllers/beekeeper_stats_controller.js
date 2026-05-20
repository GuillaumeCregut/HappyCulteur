import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["apiarySelector", "hiveSelector", "errorMessage", "hiveInfos"];
    static values = { url: String, hiveUrl: String };
    connect() {
    }

    async apiaryChanged() {
        const apiarySelector = this.apiarySelectorTarget;
        const hiveSelector = this.hiveSelectorTarget;
        const apiaryId = apiarySelector.value;
        if (!apiaryId) {
            this.hiveSelectorTarget.innerHTML = '<option value="">-- Choisir une ruche --</option>';
            return;
        }
        this.cleanInfoContainer();
        const allUrl = `${this.urlValue}?apiaryId=${apiaryId}`;
        const response = await fetch(allUrl);
        if (response.ok) {
            try {
                const hives = await response.json();
                this.cleanHiveSelector();
                const option = new Option('-- Choisir une ruche --', '');
                hiveSelector.add(option);
                hives.forEach(hive => {
                    const option = new Option(hive.name, hive.id);
                    hiveSelector.add(option);
                });
            } catch (e) {
                this.displayError('Une erreur est survenue. ', 500);
            }
        } else {
            this.displayError('Une erreur est survenue. ', response.status);
        }
    }

    async hiveChanged() {
        const hiveSelector = this.hiveSelectorTarget;
        const hiveId = hiveSelector.value;
        if (!hiveId) {
            return;
        }
        this.cleanInfoContainer();
        const allUrl = `${this.hiveUrlValue}?hiveId=${hiveId}`;
        const response = await fetch(allUrl);
        if (response.ok) {
            try {
                const hiveInfos = await response.json();
                this.buildResult(hiveInfos);
            } catch (e) {
                this.displayError('Une erreur est survenue. ', 500);
            }
        } else {
            this.displayError('Une erreur est survenue. ', response.status);
        }
    }

    cleanHiveSelector() {
        const hiveSelector = this.hiveSelectorTarget;
        while (hiveSelector.hasChildNodes()) {
            hiveSelector.removeChild(hiveSelector.firstChild);
        }
    }

    displayError(errorMessage, code) {
        const error = 'stats-beekeeper-message-error';
        let message = '';
        switch (code) {
            case 404: message = 'Element non trouvé';
                break;
            case 500: message = 'Erreur serveur';
                break;
            case 403: message = "Vous n'êtes pas autorisé à voir ces informations";
                break;
            case 422: message = 'Aucune information envoyée'
        }
        message = errorMessage + message;
        this.errorMessageTarget.textContent = message;
        this.errorMessageTarget.className = error;
        setTimeout(() => {
            this.errorMessageTarget.textContent = '';
            this.errorMessageTarget.clasName = '';
        }, 3000);
    }

    buildResult(infos) {
        const container = this.hiveInfosTarget;
        if (undefined === infos.state) {
            return;
        }
        const pState = document.createElement('p');
        pState.textContent = `Etat de la ruche : ${infos.state}`;
        const pRise = document.createElement('p');
        pRise.textContent = `Nombre de hausses : ${infos.rise}`;
        const pSwarm = document.createElement('p');
        pSwarm.textContent = `Essaim : ${infos.swarm}`;
        container.appendChild(pState);
        container.appendChild(pRise);
        container.appendChild(pSwarm);
    }

    cleanInfoContainer() {
        const container = this.hiveInfosTarget;
        while (container.hasChildNodes()) {
            container.removeChild(container.firstChild);
        }
    }
}