import { Controller } from '@hotwired/stimulus';
import Hive from '../scripts/hive.js';
import Apiary from '../scripts/apiary.js';

export default class extends Controller {
    static targets = ["canvas", "message",];
    hivesOk = true;
    static values = {
        initialized: Boolean,
        path: String,
        url: String,
        hivePicture: String,
        csrf: String,
        savePath: String
    }
    async connect() {
        const hives = await this.fetchHives();
        this.canvas = this.canvasTarget;
        this.ctx = this.canvasTarget.getContext("2d");
        if (!this.initializedValue) {
            return;
        }
        this.apiary = new Apiary(this.pathValue);
        await this.apiary.ready;
        if (hives.length === 0) {
            this.hivesOk = false;
        }
        for (const hive of hives) {
            const theHive = new Hive(this.hivePictureValue, hive.coordX, hive.coordY, hive.coordZ, hive.name);
            await theHive.ready;
            this.apiary.addHive(theHive);
        };
        this.canvas.width = this.apiary.getWidth();
        this.canvas.height = this.apiary.getHeight();
        this.loop();
    }

    loop = () => {
        this.draw();
        this.frameId = requestAnimationFrame(this.loop);
    }

    draw() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        this.apiary.drawApiary(this.ctx);
        if (!this.hivesOk) {
            this.drawEmpty();
        }
    }

    async fetchHives() {
        const response = await fetch(this.urlValue, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (response.ok) {
            const hives = await response.json();
            return hives;
        }
        return [];
    }

    drawEmpty() {
        this.ctx.font = "24px serif";
        this.ctx.strokeStyle = "white";
        this.ctx.strokeText("Aucune ruche n'a été configurée sur la cartographie", 10, 50);
    }

    async save() {
        try {
            this.canvasTarget.toBlob(async (blob) => {
                const formData = new FormData();
                formData.append("image", blob, "canvas.png");
                const url = this.savePathValue;
                const csrf = this.csrfValue;
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf
                    },
                    body: formData
                });
                 const returnValue = await response.json();
            if (!response.ok) {
                this.showMessage('Erreur lors de la sauvegarde.', 'error');
            } else {
                this.showMessage('cartographie sauvegardée !', 'success');
            }
            },
                'image/png');
        } catch (e) {
            this.showMessage('Erreur réseau.', 'error');
        }
    }

     showMessage(text, type) {
        const error = 'apiary-carto-message-error';
        const success = 'apiary-carto-message-success';
        this.messageTarget.textContent = text;
        this.messageTarget.className = type === 'success' ? success : error;
        setTimeout(() => {
            this.messageTarget.textContent = '';
            this.messageTarget.clasName = '';
        }, 3000);
    }
}