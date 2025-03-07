class Extension extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "My Extension";
        this.addOutput("authors", "ExtensionAuthor");
        this.addOutput("plugins", "Plugin");
        this.addOutput("modules", "Module");
        this.properties = {
            extensionKey: "my_extension",
            version: "0.0.1",
            composerName: "my-vendor/my-package",
            description: "With this extension you can...",
            homepage: "https://example.com"
        };
    }

    onPropertyChanged = function (propertyName, newPropertyValue, previousPropertyValue) {
        if (propertyName === "extensionKey") {
            const lowerCasedFirstLetterExtensionKey = newPropertyValue.charAt(0).toLowerCase() + newPropertyValue.slice(1);
            const lowerCasedExtensionKey = lowerCasedFirstLetterExtensionKey.replace(/([A-Z])/g, function(match) {
                return '_' + match.toLowerCase();
            });
            this.properties.extensionKey = lowerCasedExtensionKey.replace(/[^a-z0-9]/g, "_");
            return true;
        }
    }

    onExecute() {
        // Definieren Sie hier Ihre Logik
    }
}
LiteGraph.registerNodeType("TYPO3/Extension", Extension);

class Author extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "Author";
        this.addInput("Extension", "ExtensionAuthor");
        this.properties = {
            name: "Max Mustermann",
            email: "max.mustermann@example.com",
            company: "ACME",
            role: "Lead Developer"
        };
    }

    onExecute() {
        // Definieren Sie hier Ihre Logik
    }
}
LiteGraph.registerNodeType("TYPO3/Author", Author);

class ControllerNode extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this._controllerName = "DefaultController";
        // Fügen Sie Eingänge / Ausgänge hinzu
        this.addOutput("actions", "Action");
        // Fügen Sie Eigenschaften hinzu
        this.properties = {controllerName: ""};
    }

    // Override node title. Make sure controller name is capitalized first and ends with "Controller"
    set title(controllerName) {
        const capitalizedControllerName = controllerName.charAt(0).toUpperCase() + controllerName.slice(1);
        const shouldAppendController = !capitalizedControllerName.endsWith("Controller");
        this._controllerName = capitalizedControllerName + (shouldAppendController ? "Controller" : "");
    }

    get title() {
        return this._controllerName;
    }

    onExecute() {
        // Definieren Sie hier Ihre Logik
    }
}
LiteGraph.registerNodeType("extbase/controller", ControllerNode);

class ControllerActionNode extends LiteGraph.LGraphNode {
    constructor(){
        super();

        this._actionName = "indexAction";
        this.addInput("controller", "Controller");
        this.properties = {actionName: ""};
    }

    // Override node title. Make sure action name is lower case first and ends with "Action"
    set title(actionName) {
        const lowerCasedActionName = actionName.charAt(0).toLowerCase() + actionName.slice(1);
        const shouldAppendAction = !lowerCasedActionName.endsWith("Action");
        this._actionName = lowerCasedActionName + (shouldAppendAction ? "Action" : "");
    }

    get title() {
        return this._actionName;
    }

    onExecute() {
        // Definieren Sie hier Ihre Logik
    }
}
LiteGraph.registerNodeType("extbase/action", ControllerActionNode);

// Container auswählen
const container = document.getElementById("graph-container");

// Canvas erstellen
const canvas = document.createElement("canvas");
container.appendChild(canvas);

// Litegraph Setup
const graph = new LGraph();
const graphCanvas = new LGraphCanvas(canvas, graph);

// Automatische Größenanpassung
function resizeCanvas() {
    canvas.width = container.clientWidth;
    canvas.height = container.clientHeight;
    graphCanvas.resize();
}

// Event-Listener für Fenstergröße
window.addEventListener("resize", resizeCanvas);
resizeCanvas(); // Initiale Anpassung

graph.onNodeAdded = function (node) {
    if (node.type === "TYPO3/Extension") {
        for (let i in graph._nodes) {
            let existingNode = graph._nodes[i];

            // Wenn ein anderer Knoten vom Typ "TYPO3/Extension" gefunden wird
            if (existingNode !== node && existingNode.type === "TYPO3/Extension") {
                // Entferne den neu hinzugefügten Knoten
                graph.remove(node);

                // Gebe eine Nachricht an den Benutzer aus
                alert("Only one node of type TYPO3/Extension is allowed!");

                // Beende die Funktion
                return;
            }
        }
    }
}

graph.start();
