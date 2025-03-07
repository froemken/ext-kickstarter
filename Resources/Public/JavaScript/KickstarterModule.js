import ErdEditor from "erd-editor";
import AjaxRequest from "@typo3/core/ajax/ajax-request.js";

const editor = document.createElement('erd-editor');
document.querySelector('#erd-editor').appendChild(editor);

document.querySelector("button[data-ext-kickstarter='create-extension']").addEventListener('click', function() {
    let request = new AjaxRequest(TYPO3.settings.ajaxUrls.ext_kickstarter_build);
    let promise = request.post(editor.value, {
        headers: {
            'Content-Type': 'application/json; charset=utf-8'
        }
    });

    promise.then(async function (response) {
        const responseObj = await response.resolve();

        if (responseObj.status === "ok") {
            alert("Extension saved");
        } else {
            alert("Error while storing extension");
        }
    });
});
