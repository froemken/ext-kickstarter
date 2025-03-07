import AjaxRequest from "@typo3/core/ajax/ajax-request.js";

document.getElementById('buildExtension').addEventListener('click', function() {
    let graphData = graph.serialize();
    let request = new AjaxRequest(TYPO3.settings.ajaxUrls.ext_kickstarter_build);

    let promise = request.post(JSON.stringify(graphData), {
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
