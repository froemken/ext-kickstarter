# TYPO3 Extension Kickstarter

`ext_kickstarter` is a TYPO3 extension that simplifies and accelerates the creation of new TYPO3 extensions by automating file generation, controller setup, and plugin registration through easy-to-use CLI commands. With a few CLI commands, it generates essential files like `LICENSE`, `ext_emconf.php`, `composer.json`, and more. Whether starting from scratch or extending existing functionality, `ext_kickstarter` helps streamline development.

---

## Features

- **Generate New TYPO3 Extensions**  
  Automates the creation of TYPO3 extensions with pre-configured files.

- **CLI Commands for Common Tasks**
    - Create a new extension
    - Generate controllers
    - Register plugins
    - Extend existing controllers with additional actions.
    - Create basic TCA for tables

---

## Installation

Install the extension using Composer:
```bash
composer req stefanfroemken/ext-kickstarter
```

For DDEV:
```bash
ddev composer req stefanfroemken/ext-kickstarter
```

## Configuration

By default, new extensions are created in `typo3temp/ext-kickstarter/[your_ext_key]`.

To change this location:

- Navigate to **TYPO3 Backend → Admin Tools → Settings → Configure Extensions → `ext_kickstarter`**.
- For Composer-based installations, the recommended directory is `packages/`.

## Usage

After installation, you can use the following CLI commands:

### Create a New Extension

```bash
vendor/bin/typo3 make:extension
```

DDEV:

```bash
ddev typo3 make:extension
```

**Warning:** This command will delete an existing extension in the target directory before creating a new one.

### Generate a Controller

```bash
vendor/bin/typo3 make:controller
```

DDEV:

```bash
ddev typo3 make:controller
```

You will be prompted to select controller actions. Existing actions will not be overwritten.

### Create and Register an Extbase Plugin

```bash
vendor/bin/typo3 make:plugin
```
DDEV:

```bash
ddev typo3 make:plugin
```

### Create a TCA table

```bash
vendor/bin/typo3 make:table
```
DDEV:

```bash
ddev typo3 make:table
```

### Create an Event Listener

```bash
vendor/bin/typo3 make:eventlistener
```
DDEV:

```bash
ddev typo3 make:eventlistener
```

### Create an Extbase Repository

```bash
vendor/bin/typo3 make:repository
```
DDEV:

```bash
ddev typo3 make:repository
```

### Create an Extbase Type Converter

```bash
vendor/bin/typo3 make:typeconverter
```
DDEV:

```bash
ddev typo3 make:typeconverter
```

### Create an Upgrade Wizard

```bash
vendor/bin/typo3 make:upgrade
```
DDEV:

```bash
ddev typo3 make:upgrade
```

---

## Why Use ext_kickstarter

Creating TYPO3 extensions often involves repetitive tasks like setting up configuration files and controllers. `ext_kickstarter` automates these steps, saving time and ensuring a consistent structure.

---

## Special Thanks

Special thanks to [LiteGraph](https://github.com/jagenjo/litegraph.js) for their awesome JS UI library we are using in TYPO3 backend.

---

## Contributions & Feedback
Feedback and contributions are always welcome! Feel free to share your ideas or report issues in the [GitHub repository](https://github.com/stefanfroemken/ext-kickstarter).

---
