.PHONY: help
help: ## Displays this list of targets with descriptions
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: fix ## Run all tests
fix: rector fix-code-style

.PHONY: test ## Run all tests
test: lint code-style phpstan

.PHONY: lint
lint:
	Build/Scripts/runTests.sh -s lint

.PHONY: phpstan
phpstan:
	Build/Scripts/runTests.sh -s phpstan

.PHONY: phpstanBaseline
phpstanBaseline:
	Build/Scripts/runTests.sh -s phpstanBaseline

.PHONY: code-style
code-style:
	Build/Scripts/runTests.sh -s cgl -n

.PHONY: fix-code-style
fix-code-style: ## Fix PHP coding style issues
	Build/Scripts/runTests.sh -s cgl

.PHONY: install
install: ## Fix PHP coding style issues
	Build/Scripts/runTests.sh -s composerUpdate

.PHONY: rector
rector: ## Refactor code using rector
	Build/Scripts/runTests.sh -s rector
