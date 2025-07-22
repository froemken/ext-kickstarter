# CONTRIBUTORS.md

## 👋 Welcome!

Thank you for your interest in contributing to this project! We welcome issues,
suggestions, and contributions that help improve the project for everyone.

## 💬 Before You Start a Pull Request

Before putting significant time into a pull request, **please
[open an issue](https://github.com/froemken/ext-kickstarter/issues)** or reach
out to the maintainer. This helps ensure that:

- Your proposal aligns with the project’s goals
- No one else is working on the same change
- You don’t spend time on something that might not be accepted

You can reach us in the TYPO3 Slack channel `#extension-builder`.
If you're not yet on TYPO3 Slack, see the guide here:
[How to use Slack in the TYPO3 community](https://typo3.org/community/meet/how-to-use-slack-in-the-typo3-community)

Use the [issue tracker](https://github.com/froemken/ext-kickstarter/issues) for
bug reports, feature requests, and coordination.

## 🧪 Running the Tests

Before running the tests, make sure to update the required tools:

```bash
Build/Scripts/runTests.sh -s composerUpdate
```

To run the current test suites, use the following commands:

### For `cgl`:

```bash
Build/Scripts/runTests.sh -s cgl
```
### For `phpstan`:

```bash
Build/Scripts/runTests.sh -s phpstan
```

> Note: Unit and functional tests will be introduced in a future update.

## 🙌 Thank You!

We appreciate your contributions—whether it's filing an issue, suggesting an
improvement, or submitting a pull request. Let's build something great together!

