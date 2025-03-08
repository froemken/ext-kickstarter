class Extension extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "Extension";

        this.addOutput("authors", "ExtensionAuthors");
        this.addOutput("extbasePlugins", "ExtensionExtbasePlugins");
        this.addOutput("extbaseModules", "ExtensionExtbaseModules");

        this.properties = {
            extensionKey: "my_extension",
            vendorName: "MyVendor",
            extensionName: "MyExtension",
            composerName: "",
            title: "My Extension",
            description: "With this extension you can...",
            version: "0.0.1",
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
        if (propertyName === "vendorName") {
            const upperCasedFirstLetterVendorName = newPropertyValue.charAt(0).toUpperCase() + newPropertyValue.slice(1);
            this.properties.extensionKey = upperCasedFirstLetterVendorName.replace(/[^a-zA-Z0-9]/g, "");
            return true;
        }
        if (propertyName === "extensionName") {
            const upperCasedFirstLetterVendorName = newPropertyValue.charAt(0).toUpperCase() + newPropertyValue.slice(1);
            this.properties.extensionKey = upperCasedFirstLetterVendorName.replace(/[^a-zA-Z0-9]/g, "");
            return true;
        }
    }
}
LiteGraph.registerNodeType("TYPO3/Extension", Extension);

class Author extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "Author";

        this.addInput("Extension", "ExtensionAuthors");

        this.properties = {
            name: "Max Mustermann",
            email: "max.mustermann@example.com",
            company: "ACME",
            role: "Lead Developer"
        };
    }
}
LiteGraph.registerNodeType("TYPO3/Author", Author);

class ExtbasePlugin extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "Extbase Plugin";

        this.addInput("extension", "ExtensionExtbasePlugins");
        this.addOutput("extbaseControllers", "ExtbasePluginControllers");

        this.properties = {
            pluginName: "MyPlugin",
            pluginType: "plugin",
        };
    }
}
LiteGraph.registerNodeType("Extbase/Plugin", ExtbasePlugin);

class ExtbaseModule extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "Extbase Module";

        this.addInput("extension", "ExtensionExtbaseModules");
        this.addOutput("extbaseControllers", "ExtbaseModuleControllers");

        this.properties = {
            moduleName: "MyModule",
        };
    }
}
LiteGraph.registerNodeType("Extbase/Module", ExtbaseModule);

class ExtbaseController extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "Extbase Controller";

        this.addInput("extbasePlugins", "ExtbasePluginControllers");
        this.addInput("extbaseModules", "ExtbaseModuleControllers");
        this.addOutput("extbaseControllerActions", "ExtbaseControllerActions");

        this.properties = {
            controllerName: "DefaultController",
        };
    }

    onPropertyChanged = function (propertyName, newPropertyValue, previousPropertyValue) {
        if (propertyName === "controllerName") {
            const capitalizedControllerName = newPropertyValue.charAt(0).toUpperCase() + newPropertyValue.slice(1);
            const shouldAppendController = !capitalizedControllerName.endsWith("Controller");
            this.properties.controllerName = capitalizedControllerName + (shouldAppendController ? "Controller" : "");
            return true;
        }
    }
}
LiteGraph.registerNodeType("Extbase/Controller", ExtbaseController);

class ExtbaseControllerAction extends LiteGraph.LGraphNode {
    constructor(){
        super();

        this.title = "Index Action";

        this.addInput("extbaseController", "ExtbaseControllerActions");

        this.properties = {
            actionName: "indexAction",
            uncached: false,
        };
    }

    onPropertyChanged = function (propertyName, newPropertyValue, previousPropertyValue) {
        if (propertyName === "actionName") {
            const lowerCasedActionName = newPropertyValue.charAt(0).toLowerCase() + newPropertyValue.slice(1);
            const shouldAppendAction = !lowerCasedActionName.endsWith("Action");
            this.properties.actionName = lowerCasedActionName + (shouldAppendAction ? "Action" : "");
            return true;
        }
    }
}
LiteGraph.registerNodeType("Extbase/ControllerAction", ExtbaseControllerAction);

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
