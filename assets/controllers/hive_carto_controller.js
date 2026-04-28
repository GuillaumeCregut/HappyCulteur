import { Controller } from '@hotwired/stimulus';
import Hive from '../scripts/hive.js';
import Apiary from '../scripts/apiary.js';
import { DIRECTION } from '../scripts/constants.js';

export default class extends Controller {
    static targets = ["canvas", "message"];

    static values = {
        x: Number,
        y: Number,
        scale: Number,
        id: Number,
        name: String,
        path: String,
        hivePicture: String,
        url: String,
        csrf: String,
    }
    async connect() {
        this.canvas = this.canvasTarget;
        this.ctx = this.canvasTarget.getContext("2d");
        this.apiary = new Apiary(this.pathValue);
        this.hive = new Hive(this.hivePictureValue, this.xValue, this.yValue, this.scaleValue, this.nameValue);
        await this.apiary.ready;
        await this.hive.ready;
        this.apiary.addHive(this.hive);
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
    }

    disconnect() {
        clearInterval(this.interval); //to remove ?
        cancelAnimationFrame(this.frameId);
    }

    moveUp() {
        this.hive.move(DIRECTION.UP, this.apiary, 10);
    }

    moveDown() {
        this.hive.move(DIRECTION.BOTTOM, this.apiary, 10);
    }

    moveLeft() {
        this.hive.move(DIRECTION.LEFT, this.apiary, 10);
    }

    moveRight() {
        this.hive.move(DIRECTION.RIGHT, this.apiary, 10);
    }

    zoomIn() {
        this.hive.zoomIn(this.ctx, 2);
    }

    zoomOut() {
        this.hive.zoomOut(this.ctx, 2);
    }

    async save() {
        const x = this.hive.getX();
        const y = this.hive.getY();
        const z = this.hive.getZoom();
        const url = this.urlValue;
        const csrf = this.csrfValue;
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ x, y, z })
            });
            const returnValue = await response.json();
            if (!response.ok) {
                this.showMessage('Erreur lors de la sauvegarde.', 'error');
            } else {
                this.showMessage('Position sauvegardée !', 'success');
            }
        } catch (e) {
            this.showMessage('Erreur réseau.', 'error');
        }
    }

    showMessage(text, type) {
        const error = 'hive-carto-message-error';
        const success = 'hive-carto-message-success';
        this.messageTarget.textContent = text;
        this.messageTarget.className = type === 'success' ? success : error;
        setTimeout(() => {
            this.messageTarget.textContent = '';
            this.messageTarget.clasName = '';
        }, 3000);
    }
}