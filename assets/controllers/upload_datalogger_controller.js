import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
     static targets = ['uploadPreview', 'uploadFileLoader', 'filename']

     changeFile() {
        const fileLoader = this.uploadFileLoaderTarget;
        if(fileLoader.files.length > 0) {
            this.uploadPreviewTarget.classList.remove('data-load-hidden');
            this.filenameTarget.innerText = fileLoader.files[0].name;
        } else {
            this.uploadPreviewTarget.classList.add('data-load-hidden');
        }
     }
}