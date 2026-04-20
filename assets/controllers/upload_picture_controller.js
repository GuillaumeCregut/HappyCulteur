import { Controller } from '@hotwired/stimulus';
import '../styles/default/upload_file.css';

export default class extends Controller {
    static targets = ['uploadPicturePreview', 'uploadPictureFileLoader', 'uploadPictureContainer']

    connect() {
        
    }

    changeFile() {
         const fileLoader = this.uploadPictureFileLoaderTarget;
         const file = fileLoader.files[0];
         if(file.type === 'image/jpeg' || file.type === 'image/png') {
            const container = this.uploadPicturePreviewTarget;
            while(container.childElementCount > 0) {
                container.removeChild(container.firstChild);
            }
            const image = document.createElement('img');
            image.src = URL.createObjectURL(file);
            image.classList.add('upload-picture-image-preview');
            container.appendChild(image);
         }
    }
}