import { DIRECTION } from './constants.js';

export default class Hive {
    constructor(url, x, y, z, nomR) {
        this.x = parseInt(x);
        this.y = parseInt(y);
        this.width = 120;
        this.height = 105;
        this.name = nomR;
        if (typeof (z) == 'undefined') {
            this.zoom = 0;
        } else {
            this.zoom = parseInt(z);
        }
        
        if (this.zoom != 0) {
            if (this.zoom < 0) 
            {
                let step = Math.abs(this.zoom);
                this.width = Math.round(this.width / step);
                this.height = Math.round(this.height / step);
            } else {
                let step = this.zoom;
                this.width = Math.round(this.width * step);
                this.height = Math.round(this.height * step);;
            }
        }
        
        this.image = new Image();
        this.ready = new Promise((resolve, reject) => {
            this.image.onload = () => {
                resolve();
            };
            this.image.onerror = () => {
                throw new Error(`Erreur de chargement de l'image "${image_name}"`);
            };
        });
        this.image.src = url;
    }

    drawHive(context) {
        context.drawImage(this.image, this.x, this.y, this.width, this.height);
        let labelWidth = 50;
        let xlabel = this.x + (this.width - labelWidth) / 2;
        let ylabel = this.y + this.height + 5;
        context.beginPath();
        context.rect(xlabel, ylabel, labelWidth, 20);
        context.fillStyle = 'red';
        context.fill();
        context.lineWidth = 1;
        context.stroke();
        context.fillStyle = 'white';
        context.font = "10px sans-serif";
        context.fillText(this.name, (xlabel + 5), (ylabel + 12));
    }

    zoomIn(context, step) {
        let OldX = this.x;
        let OldY = this.y;
        this.width = Math.round(this.width * step);
        this.height = Math.round(this.height * step);
        this.zoom = this.zoom + step;
        context.drawImage(this.image, OldX, OldY, this.width, this.height);
    }

    zoomOut(context, step) {
        let OldX = this.x;
        let OldY = this.y;
        this.width = Math.round(this.width / step);
        this.height = Math.round(this.height / step);
        this.zoom = this.zoom - step;
        context.drawImage(this.image, OldX, OldY, this.width, this.height);
    }

    getWidth() {
        return this.width;
    }

    getHeight() {
        return this.height;
    }

    getX() {
        return this.x;
    }

    getY() {
        return this.y;
    }

    getZoom() {
        return this.zoom;
    }

    move(direction, apiary, step) {
        step = parseInt(step);
        let nextX;
        let nextY;
        switch (direction) {
            case DIRECTION.UP:
                nextX = this.x;
                nextY = this.y - step;
                break;
            case DIRECTION.BOTTOM:
                nextX = this.x;
                nextY = this.y + step;
                break;
            case DIRECTION.RIGHT:
                nextX = this.x + step;
                nextY = this.y;
                break;
            case DIRECTION.LEFT:
                nextX = this.x - step;
                nextY = this.y;
                break;
        }
        let maxRight = nextX + this.width;
        let maxBottom = nextY + this.height;
        if ((nextX < 0) || (maxRight) > apiary.getWidth() || (nextY < 0) || (maxBottom) > apiary.getHeight()) {
            return false;
        }
        this.x = nextX;
        this.y = nextY;
        return true;
    }
}
