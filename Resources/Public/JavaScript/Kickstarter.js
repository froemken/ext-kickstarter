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

        this.addWidget(
            "text",
            "Key",
            this.properties.extensionKey,
            function (newValue, graphCanvas, extensionNode, vector, event) {
                // Do not use value from input. Use the modified and cleaned value from properties
                this.value = extensionNode.properties.extensionKey;
            },
            {
                property: "extensionKey"
            }
        );
    }

    onPropertyChanged = function (propertyName, newPropertyValue, previousPropertyValue) {
        if (propertyName === "extensionKey") {
            const lowerCasedFirstLetterExtensionKey = newPropertyValue.charAt(0).toLowerCase() + newPropertyValue.slice(1);
            const lowerCasedUnderscoredExtensionKey = lowerCasedFirstLetterExtensionKey.replace(/([A-Z])/g, function (match) {
                return '_' + match.toLowerCase();
            });
            const cleanedExtensionKey = lowerCasedUnderscoredExtensionKey.replace(/[^a-z0-9]/g, "_");
            this.properties.extensionKey = cleanedExtensionKey;
            this.setProperty("extensionName", cleanedExtensionKey);

            this.updateTcaTableName();

            return true;
        }
        if (propertyName === "vendorName") {
            const upperCasedFirstLetterVendorName = newPropertyValue.charAt(0).toUpperCase() + newPropertyValue.slice(1);
            this.properties.vendorName = upperCasedFirstLetterVendorName.replace(/[^a-zA-Z0-9]/g, "");
            return true;
        }
        if (propertyName === "extensionName") {
            const upperCasedFirstLetterExtensionName = newPropertyValue.charAt(0).toUpperCase() + newPropertyValue.slice(1);
            const upperCasedLettersExtensionName = upperCasedFirstLetterExtensionName.replace(/_([a-z])/g, function (underscoreAndLetter) {
                return underscoreAndLetter[1].toUpperCase();
            });
            this.properties.extensionName = upperCasedLettersExtensionName.replace(/[^a-zA-Z0-9]/g, "");
            this.properties.title = upperCasedLettersExtensionName.replace(/([A-Z])/g, ' $1').trim();
            return true;
        }
    }

    // Inform tcaTable nodes to update the tablename
    updateTcaTableName = () => {
        const tcaTableNodes = this.graph.findNodesByType?.('Tca/Table');
        tcaTableNodes?.forEach(tcaTableNode => {
            const slotId = tcaTableNode.findInputSlot?.("extbaseRepository");
            const linkId = tcaTableNode.inputs?.[slotId]?.link;
            if (linkId !== null && linkId !== undefined && linkId >= 0) {
                const {origin_id, target_id} = tcaTableNode.graph.links?.[linkId] || {};
                tcaTableNode.updateTableName(origin_id, target_id);
            }
        });
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

class TcaTable extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "Table";

        this.addInput("extbaseRepository", "ExtbaseRepositoryTable");
        this.addOutput("tcaColumns", "TcaTableColumns");

        this.properties = {
            tableName: "tx_myext_domain_model_default",
            title: "My Table",
            label: "",
        };

        this.addWidget(
            "text",
            "Table",
            this.properties.tableName,
            function (newValue, graphCanvas, extensionNode, vector, event) {
                // Do not use value from input. Use the modified and cleaned value from properties
                this.value = extensionNode.properties.tableName;
            },
            {
                property: "tableName"
            }
        );
    }

    onPropertyChanged = function (propertyName, newPropertyValue, previousPropertyValue) {
        if (propertyName === "tableName") {
            this.properties.tableName = newPropertyValue.toLowerCase();
            return true;
        }
    }

    // We set tableName property of TcaTable automatically, if it was connected via an extbase repository
    onConnectionsChange = function (connectionType, targetSlot, isConnected, linkInfo, input) {
        if (connectionType === LiteGraph.INPUT) {
            this.updateTableName(linkInfo.origin_id, linkInfo.target_id);
        }
    }

    onConnectInput = function (inputIndex, outputType, outputSlot, outputNode, outputIndex) {
        if (outputNode.outputs
            && outputNode.outputs.length >= 1
            && outputNode.outputs[0]
            && outputNode.outputs[0].links
            && outputNode.outputs[0].links.length >= 1
        ) {
            alert('It is not possible to connect multiple table nodes with an extbase repository');
            return false;
        }
    }

    updateTableName = function (originId, targetId) {
        let linkedRepositoryNode = this.graph.getNodeById(originId);
        let linkedTcaTableNode = this.graph.getNodeById(targetId);

        if (linkedRepositoryNode && linkedTcaTableNode) {
            let tableName = linkedRepositoryNode.getTableName()
            if (tableName) {
                linkedTcaTableNode.setProperty('tableName', linkedRepositoryNode.getTableName());
            }
        }
    }
}

LiteGraph.registerNodeType("Tca/Table", TcaTable);

class TcaTypeCategory extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "TCA Category";

        this.addInput("tcaTable", "TcaTableColumns");

        this.properties = {
            columnName: "default",
            label: "Default",
            description: "",
            modelProperty: false,
            propertyDataType: 'string',
        };
    }
}

LiteGraph.registerNodeType("Tca/Type/Category", TcaTypeCategory);

class TcaTypeCheck extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "TCA Checkbox";

        this.addInput("tcaTable", "TcaTableColumns");
        this.addOutput("tcaSelectItems", "TcaSelectItems");

        this.properties = {
            columnName: "default",
            label: "Default",
            description: "",
            modelProperty: false,
            propertyDataType: 'string',
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
            columnName: "default",
            label: "Default",
            description: "",
            modelProperty: false,
            propertyDataType: 'string',
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
            columnName: "default",
            label: "Default",
            description: "",
            modelProperty: false,
            propertyDataType: 'string',
        };
    }
}

LiteGraph.registerNodeType("Tca/Type/Email", TcaTypeEmail);

class TcaTypeFile extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "TCA File";

        this.addInput("tcaTable", "TcaTableColumns");

        this.properties = {
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
            columnName: "default",
            label: "Default",
            description: "",
            modelProperty: false,
            propertyDataType: 'string',
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
            columnName: "default",
            label: "Default",
            description: "",
            modelProperty: false,
            propertyDataType: 'string',
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
            columnName: "default",
            label: "Default",
            useAsTableLabel: false,
            description: "",
            modelProperty: false,
            propertyDataType: 'string',
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
            columnName: "default",
            label: "Default",
            description: "",
            modelProperty: false,
            propertyDataType: 'string',
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
            columnName: "default",
            label: "Default",
            description: "",
            modelProperty: false,
            propertyDataType: 'string',
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
            columnName: "default",
            label: "Default",
            description: "",
            modelProperty: false,
            propertyDataType: 'string',
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
            columnName: "default",
            label: "Default",
            description: "",
            modelProperty: false,
            propertyDataType: 'string',
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
            columnName: "default",
            label: "Default",
            description: "",
        };
    }
}

LiteGraph.registerNodeType("Tca/Type/Uuid", TcaTypeUuid);

class TcaSelectItem extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "TCA Select Item";

        this.addInput("tcaCheckSelect", "TcaSelectItems");

        this.properties = {
            label: "label",
            value: "valuedefault",
        };
    }
}

LiteGraph.registerNodeType("Tca/SelectItem", TcaSelectItem);

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
            this.setProperty("pluginName", upperCasedFirstLetterVendorName.replace(/[^a-zA-Z0-9]/g, ""));
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
            this.setProperty("moduleName", upperCasedFirstLetterVendorName.replace(/[^a-zA-Z0-9]/g, ""));
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
            this.setProperty("controllerName", capitalizedControllerName + (shouldAppendController ? "Controller" : ""));
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
            this.setProperty("actionName", lowerCasedActionName + (shouldAppendAction ? "Action" : ""));
            return true;
        }
    }
}

LiteGraph.registerNodeType("Extbase/ControllerAction", ExtbaseControllerAction);

class ExtbaseRepository extends LiteGraph.LGraphNode {
    constructor() {
        super();

        this.title = "Default Repository";

        this.addInput("extbaseController", "ExtbaseControllerRepositories");
        this.addOutput("tcaTable", "ExtbaseRepositoryTable");

        this.properties = {
            repositoryName: "DefaultRepository",
            tableName: ""
        };

        this.addWidget(
            "text",
            "Repo",
            this.properties.repositoryName,
            function (newValue, graphCanvas, extensionNode, vector, event) {
                // Do not use value from input. Use the modified and cleaned value from properties
                this.value = extensionNode.properties.repositoryName;
            },
            {
                property: "repositoryName"
            }
        );
    }

    getTableName = function () {
        let tableName = this.properties.tableName;
        let extensionNodes = this.graph.findNodesByType('Main/Extension');
        if (tableName === '' && extensionNodes && extensionNodes.length > 0) {
            let extensionNode = extensionNodes[0];
            let extensionKey = extensionNode.properties.extensionKey.replace(/_/g, '');
            let repositoryName = this.properties.repositoryName.toLowerCase().slice(0, -10);
            tableName = 'tx_' + extensionKey + '_domain_model_' + repositoryName;
        }
        return tableName;
    }

    onPropertyChanged = function (propertyName, newPropertyValue, previousPropertyValue) {
        if (propertyName === "repositoryName") {
            const capitalizedRepositoryName = newPropertyValue.charAt(0).toUpperCase() + newPropertyValue.slice(1);
            const shouldAppendRepository = !capitalizedRepositoryName.endsWith("Repository");
            this.properties.repositoryName = capitalizedRepositoryName + (shouldAppendRepository ? "Repository" : "");

            this.updateTcaTableName();

            return true;
        }
        if (propertyName === "tableName") {
            this.updateTcaTableName();
        }
    }

    // Inform tcaTable nodes to update the tablename
    updateTcaTableName = () => {
        const tcaTableNodes = this.graph.findNodesByType?.('Tca/Table');
        tcaTableNodes?.forEach(tcaTableNode => {
            const slotId = tcaTableNode.findInputSlot?.("extbaseRepository");
            const linkId = tcaTableNode.inputs?.[slotId]?.link;
            if (linkId !== null && linkId !== undefined && linkId >= 0) {
                const {origin_id, target_id} = tcaTableNode.graph.links?.[linkId] || {};
                tcaTableNode.updateTableName(origin_id, target_id);
            }
        });
    }
}

LiteGraph.registerNodeType("Extbase/Repository", ExtbaseRepository);

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
            this.setProperty("controllerName", capitalizedControllerName + (shouldAppendController ? "Controller" : ""));
            return true;
        }
    }
}

LiteGraph.registerNodeType("Extbase/OverwritePluginControllerActionMapping", OverwritePluginControllerActionMapping);

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
    // Show error if node of type Extension was added twice
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

graph.add(LiteGraph.createNode('Main/Extension', 'Extension', {
    pos: [70, 100]
}));

graph.start();
