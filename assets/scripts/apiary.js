export default class Apiary {
    constructor(pictureName) {
        this.image = new Image();
        this.ready = new Promise((resolve, reject) => {
            this.image.onload = () => {
                this.width = this.image.width;
                this.height = this.image.height;
                resolve();
            };
            this.image.onerror = () => {
                throw new Error(`Erreur de chargement de l'image "${pictureName}"`);
            };
        }
        );
        this.image.src = pictureName;
        this.hives = [];
    }

    drawApiary(context)
    {
        context.drawImage(this.image, 0, 0);
        if (this.hives.length === 0) {
            return
        }
        for (const hive of this.hives) {
            hive.drawHive(context);
        }
    }

    getWidth() {
        return this.width;
    }

    getHeight() {
        return this.height;
    }

    addHive(hive) {
        this.hives.push(hive);
    }
}