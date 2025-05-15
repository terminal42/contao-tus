import './tus.scss';

import { Application, Controller } from '@hotwired/stimulus';
import * as tus from 'tus-js-client';

const application = Application.start();
application.debug = process.env.NODE_ENV === 'development';

application.register(
    'tus',
    class extends Controller {
        static targets = ['file', 'list', 'template'];
        static values = {
            endpoint: String,
        };

        connect() {
            // do something
        }

        disconnect() {
            // do something
        }

        select() {
            this.fileTarget.click();
        }

        upload(event) {
            [...event.target.files].forEach((file) => {
                this._uploadFile(file, this._createItem(file.name));
            });
        }

        remove(event) {
            const el = event.target.parentNode;

            if (el.tus) {
                el.tus.abort(true);
            }

            el.remove();
        }

        _createItem(name) {
            let template = this.templateTarget.innerHTML;
            template = template.replace('{name}', name);

            this.listTarget.insertAdjacentHTML('beforeend', template);

            return this.listTarget.lastElementChild;
        }

        _uploadFile(file, el) {
            return new Promise((resolve, reject) => {
                const progress = el.querySelector('.tus__progress');

                // Create a new tus upload
                const upload = new tus.Upload(file, {
                    endpoint: this.endpointValue,
                    retryDelays: [0, 3000, 5000, 10000, 20000],
                    metadata: {
                        filename: file.name,
                        filetype: file.type,
                    },
                    onError: function (error) {
                        progress.classList.add('error');
                        progress.innerHTML = error;
                        console.log('Failed because: ' + error);
                        reject();
                    },
                    onProgress: function (bytesUploaded, bytesTotal) {
                        var percentage = ((bytesUploaded / bytesTotal) * 100).toFixed(2);
                        progress.innerHTML = `<div></div><span>${percentage}%</span>`;
                        progress.firstElementChild.style.width = `${percentage}%`;
                        console.log(bytesUploaded, bytesTotal, percentage + '%');
                    },
                    onSuccess: function () {
                        el.querySelector('input').value = upload.file.name;
                        progress.classList.add('success');
                        progress.innerHTML = '<span>100%</span>';
                        console.log('Download %s from %s', upload.file.name, upload.url);
                        resolve();
                    },
                });

                const imageUrl = URL.createObjectURL(file);
                el.querySelector('img').src = imageUrl;
                URL.revokeObjectURL(imageUrl);

                // Check if there are any previous uploads to continue.
                upload.findPreviousUploads().then(function (previousUploads) {
                    // Found previous uploads so we select the first one.
                    if (previousUploads.length) {
                        upload.resumeFromPreviousUpload(previousUploads[0]);
                    }

                    // Start the upload
                    upload.start();
                });

                el.tus = upload;
            });
        }
    },
);
