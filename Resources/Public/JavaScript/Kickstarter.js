LiteGraph.clearRegisteredTypes();

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
            const lowerCasedExtensionKey = newPropertyValue.toLowerCase().replace(/([A-Z])/g, function (match) {
                return '_' + match.toLowerCase();
            });
            this.properties.extensionKey = lowerCasedExtensionKey.replace(/[^a-z0-9]/g, "_");
            return true;
        }
        if (propertyName === "vendorName") {
            const upperCasedFirstLetterVendorName = newPropertyValue.charAt(0).toUpperCase() + newPropertyValue.slice(1);
            this.properties.vendorName = upperCasedFirstLetterVendorName.replace(/[^a-zA-Z0-9]/g, "");
            return true;
        }
        if (propertyName === "extensionName") {
            const upperCasedFirstLetterExtensionName = newPropertyValue.charAt(0).toUpperCase() + newPropertyValue.slice(1);
            this.properties.extensionName = upperCasedFirstLetterExtensionName.replace(/[^a-zA-Z0-9]/g, "");
            return true;
        }
    }
}

LiteGraph.registerNodeType("Main/Extension", Extension);

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

LiteGraph.registerNodeType("Main/Author", Author);

class ExtbasePlugin extends LiteGraph.LGraphNode {
    pluginTypes = {
        "Plugin": "plugin",
        "Content Element": "content"
    }

    constructor() {
        super();

        this.title = "Extbase Plugin";

        this.addInput("extension", "ExtensionExtbasePlugins");
        this.addOutput("useExtbaseControllers", "ExtbasePluginControllers");

        this.properties = {
            "pluginName": "MyPlugin"
        }

        this.addProperty("pluginType", "plugin", "enum", {
            values: this.pluginTypes
        });

        this.constructor["@pluginType"] = {
            type: "enum",
            title: "Plugin Type",
            values: this.pluginTypes
        }
    }

    onPropertyChanged = function (propertyName, newPropertyValue, previousPropertyValue) {
        if (propertyName === "pluginName") {
            const upperCasedFirstLetterVendorName = newPropertyValue.charAt(0).toUpperCase() + newPropertyValue.slice(1);
            this.properties.pluginName = upperCasedFirstLetterVendorName.replace(/[^a-zA-Z0-9]/g, "");
            return true;
        }
    }
}

LiteGraph.registerNodeType("Extbase/Plugin", ExtbasePlugin);

class ExtbaseModule extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "Extbase Module";

        this.addInput("extension", "ExtensionExtbaseModules");
        this.addOutput("useExtbaseControllers", "ExtbaseModuleControllers");

        this.properties = {
            moduleName: "MyModule",
        };
    }

    onPropertyChanged = function (propertyName, newPropertyValue, previousPropertyValue) {
        if (propertyName === "moduleName") {
            const upperCasedFirstLetterVendorName = newPropertyValue.charAt(0).toUpperCase() + newPropertyValue.slice(1);
            this.properties.moduleName = upperCasedFirstLetterVendorName.replace(/[^a-zA-Z0-9]/g, "");
            return true;
        }
    }
}

LiteGraph.registerNodeType("Extbase/Module", ExtbaseModule);

class ExtbaseController extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "Extbase Controller";

        this.addInput("extbasePlugin", "ExtbasePluginControllers");
        this.addInput("extbaseModule", "ExtbaseModuleControllers");
        this.addOutput("extbaseControllerActions", "ExtbaseControllerActions");
        this.addOutput("extbaseRepositories", "ExtbaseControllerRepositories");

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
    constructor() {
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

/**
 * Only needed, if you don't want to register all actions of a controller in plugin configuration.
 * If used, it overwrites the "Controller::class -> index,update,show,list" string
 */
class OverwritePluginControllerActionMapping extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "Mapping";

        this.addInput("extbasePlugin", "ExtbasePluginControllers");
        this.addInput("extbaseModule", "ExtbaseModuleControllers");

        this.addProperty("controllerName", "DefaultController");
        this.addProperty("actionNames", "list,show");
        this.addProperty("uncached", false);

        this.addWidget("text", "Controller", this.properties.controllerName, "controllerName");
        this.addWidget("text", "Actions", this.properties.actionNames, "actionNames");
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

LiteGraph.registerNodeType("Extbase/OverwritePluginControllerActionMapping", OverwritePluginControllerActionMapping);

class ExtbaseRepository extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "Default Repository";

        this.addInput("extbaseController", "ExtbaseControllerRepositories");
        this.addOutput("tcaTable", "ExtbaseRepositoryTable");

        this.properties = {
            repositoryName: "DefaultRepository"
        };
    }

    onPropertyChanged = function (propertyName, newPropertyValue, previousPropertyValue) {
        if (propertyName === "repositoryName") {
            const capitalizedRepositoryName = newPropertyValue.charAt(0).toUpperCase() + newPropertyValue.slice(1);
            const shouldAppendRepository = !capitalizedRepositoryName.endsWith("Repository");
            this.properties.repositoryName = capitalizedRepositoryName + (shouldAppendRepository ? "Repository" : "");
            return true;
        }
    }
}

LiteGraph.registerNodeType("Extbase/Repository", ExtbaseRepository);

class TcaTable extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "Table";

        this.addInput("extbaseRepository", "ExtbaseRepositoryTable");
        this.addOutput("tcaColumns", "TcaTableColumns");

        this.properties = {
            tableName: "tx_myext_domain_model_default",
            title: "My Table",
        };
    }
}

LiteGraph.registerNodeType("Tca/Table", TcaTable);

class TcaTypeCheck extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "TCA Checkbox";

        this.addInput("tcaTable", "TcaTableColumns");

        this.properties = {
            tcaType: "check",
            columnName: "default",
            label: "Default",
            description: "",
        };
    }
}

LiteGraph.registerNodeType("Tca/Type/Check", TcaTypeCheck);

class TcaTypeColor extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "TCA Color";

        this.addInput("tcaTable", "TcaTableColumns");

        this.properties = {
            tcaType: "color",
            columnName: "default",
            label: "Default",
            description: "",
        };
    }
}

LiteGraph.registerNodeType("Tca/Type/Color", TcaTypeColor);

class TcaTypeDatetime extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "TCA Datetime";

        this.addInput("tcaTable", "TcaTableColumns");

        this.properties = {
            tcaType: "datetime",
            columnName: "default",
            label: "Default",
            description: "",
        };
    }
}

LiteGraph.registerNodeType("Tca/Type/Datetime", TcaTypeDatetime);

class TcaTypeEmail extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "TCA Email";

        this.addInput("tcaTable", "TcaTableColumns");

        this.properties = {
            tcaType: "email",
            columnName: "default",
            label: "Default",
            description: "",
        };
    }
}

LiteGraph.registerNodeType("Tca/Type/Datetime", TcaTypeDatetime);

class TcaTypeFile extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "TCA File";

        this.addInput("tcaTable", "TcaTableColumns");

        this.properties = {
            tcaType: "file",
            columnName: "default",
            label: "Default",
            description: "",
        };
    }
}

LiteGraph.registerNodeType("Tca/Type/File", TcaTypeFile);

class TcaTypeFolder extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "TCA Folder";

        this.addInput("tcaTable", "TcaTableColumns");

        this.properties = {
            tcaType: "folder",
            columnName: "default",
            label: "Default",
            description: "",
        };
    }
}

LiteGraph.registerNodeType("Tca/Type/Folder", TcaTypeFolder);

class TcaTypeGroup extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "TCA Group";

        this.addInput("tcaTable", "TcaTableColumns");

        this.properties = {
            tcaType: "group",
            columnName: "default",
            label: "Default",
            description: "",
        };
    }
}

LiteGraph.registerNodeType("Tca/Type/Group", TcaTypeGroup);

class TcaTypeInline extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "TCA Inline";

        this.addInput("tcaTable", "TcaTableColumns");

        this.properties = {
            tcaType: "inline",
            columnName: "default",
            label: "Default",
            description: "",
        };
    }
}

LiteGraph.registerNodeType("Tca/Type/Inline", TcaTypeInline);

class TcaTypeInput extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "TCA Input";

        this.addInput("tcaTable", "TcaTableColumns");

        this.properties = {
            tcaType: "input",
            columnName: "default",
            label: "Default",
            description: "",
        };
    }
}

LiteGraph.registerNodeType("Tca/Type/Input", TcaTypeInput);

class TcaTypeLink extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "TCA Link";

        this.addInput("tcaTable", "TcaTableColumns");

        this.properties = {
            tcaType: "link",
            columnName: "default",
            label: "Default",
            description: "",
        };
    }
}

LiteGraph.registerNodeType("Tca/Type/Link", TcaTypeLink);

class TcaTypeNone extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "TCA None";

        this.addInput("tcaTable", "TcaTableColumns");

        this.properties = {
            tcaType: "none",
            columnName: "default",
            label: "Default",
            description: "",
        };
    }
}

LiteGraph.registerNodeType("Tca/Type/None", TcaTypeNone);

class TcaTypeNumber extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "TCA Number";

        this.addInput("tcaTable", "TcaTableColumns");

        this.properties = {
            tcaType: "number",
            columnName: "default",
            label: "Default",
            description: "",
        };
    }
}

LiteGraph.registerNodeType("Tca/Type/Number", TcaTypeNumber);

class TcaTypePassthrough extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "TCA Passthrough";

        this.addInput("tcaTable", "TcaTableColumns");

        this.properties = {
            tcaType: "passthrough",
            columnName: "default",
            label: "Default",
            description: "",
        };
    }
}

LiteGraph.registerNodeType("Tca/Type/Passthrough", TcaTypePassthrough);

class TcaTypePassword extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "TCA Password";

        this.addInput("tcaTable", "TcaTableColumns");

        this.properties = {
            tcaType: "password",
            columnName: "default",
            label: "Default",
            description: "",
        };
    }
}

LiteGraph.registerNodeType("Tca/Type/Password", TcaTypePassword);

class TcaTypeRadio extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "TCA Radio";

        this.addInput("tcaTable", "TcaTableColumns");

        this.properties = {
            tcaType: "radio",
            columnName: "default",
            label: "Default",
            description: "",
        };
    }
}

LiteGraph.registerNodeType("Tca/Type/Radio", TcaTypeRadio);

class TcaTypeSelect extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "TCA Select";

        this.addInput("tcaTable", "TcaTableColumns");

        this.properties = {
            tcaType: "select",
            columnName: "default",
            label: "Default",
            description: "",
        };
    }
}

LiteGraph.registerNodeType("Tca/Type/Select", TcaTypeSelect);

class TcaTypeSlug extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "TCA Slug";

        this.addInput("tcaTable", "TcaTableColumns");

        this.properties = {
            tcaType: "slug",
            columnName: "default",
            label: "Default",
            description: "",
        };
    }
}

LiteGraph.registerNodeType("Tca/Type/Slug", TcaTypeSlug);

class TcaTypeText extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "TCA Text";

        this.addInput("tcaTable", "TcaTableColumns");

        this.properties = {
            tcaType: "text",
            columnName: "default",
            label: "Default",
            description: "",
        };
    }
}

LiteGraph.registerNodeType("Tca/Type/Text", TcaTypeText);

class TcaTypeUser extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "TCA User";

        this.addInput("tcaTable", "TcaTableColumns");

        this.properties = {
            tcaType: "user",
            columnName: "default",
            label: "Default",
            description: "",
        };
    }
}

LiteGraph.registerNodeType("Tca/Type/User", TcaTypeUser);

class TcaTypeUuid extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "TCA Uuid";

        this.addInput("tcaTable", "TcaTableColumns");

        this.properties = {
            tcaType: "uuid",
            columnName: "default",
            label: "Default",
            description: "",
        };
    }
}

LiteGraph.registerNodeType("Tca/Type/Uuid", TcaTypeUuid);

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
