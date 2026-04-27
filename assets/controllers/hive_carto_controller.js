import { Controller } from '@hotwired/stimulus';
import Hive from '../scripts/hive.js';
import Apiary from '../scripts/apiary.js';
import { DIRECTION } from '../scripts/constants.js';

export default class extends Controller {
    static targets = ["canvas"];

    static values = {
        x: Number,
        y: Number,
        scale: Number,
        id: Number,
        name: String,
        path: String, 
        hivePicture: String
    }
    async connect() {
        this.canvas = this.canvasTarget;
        this.ctx = this.canvasTarget.getContext("2d");
        this.apiary = new Apiary(this.pathValue);
        //Create hive
        this.hive = new Hive(this.hivePictureValue, this.xValue, this.yValue, this.zValue, this.nameValue);
        await this.hive.ready;
        //attach hive to apiary

        await this.apiary.ready;
        this.apiary.addHive(this.hive);
        this.canvas.width = this.apiary.getWidth();
        this.canvas.height = this.apiary.getHeight();
        this.loop();
       /* this.interval = setInterval(() => {
            this.draw();
        }, 40);*/
        
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

    moveLeft(){
        this.hive.move(DIRECTION.LEFT, this.apiary, 10);
    }

    moveRight(){
        this.hive.move(DIRECTION.RIGHT, this.apiary, 10);
    }

    zoomIn(){
        this.hive.zoomIn(this.ctx, 2);
    }

    zoomOut() {
        this.hive.zoomOut(this.ctx, 2);
    }
}